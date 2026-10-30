<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiInvoiceScannerService
{
    protected string $apiKey;
    protected string $defaultModel;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->defaultModel = config('services.gemini.model', 'gemini-3.5-flash');
    }

    /**
     * Get prioritized list of Gemini models to try (primary + fallback chain).
     */
    protected function getCandidateModels(): array
    {
        $list = [
            $this->defaultModel,
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-flash-latest',
            'gemini-3.8-flash',
        ];

        return array_values(array_unique(array_filter($list)));
    }

    /**
     * Scan an invoice image and extract structured data using Gemini Vision API with automatic fallback.
     *
     * @param UploadedFile $image
     * @return array{success: bool, data?: array, error?: string}
     */
    public function scan(UploadedFile $image): array
    {
        try {
            // Get base64 encoded image
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getMimeType();

            // Get product catalog for matching
            $products = Product::select('id', 'name', 'sku', 'barcode', 'unit', 'category')
                ->orderBy('name')
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'barcode' => $p->barcode,
                    'unit' => $p->unit,
                    'category' => $p->category,
                ])
                ->toArray();

            $productCatalog = json_encode($products, JSON_UNESCAPED_UNICODE);

            // Build system instruction and payload
            $systemInstruction = $this->buildSystemInstruction($productCatalog);
            $payload = [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemInstruction],
                    ],
                ],
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData,
                                ],
                            ],
                            [
                                'text' => 'Analisis faktur/nota/surat jalan pada gambar ini. Ekstrak seluruh data barang, kuantitas, satuan, distributor, dan nomor faktur. Cocokkan setiap nama barang dengan katalog produk toko yang tersedia. Kembalikan hasilnya dalam format JSON sesuai skema yang diberikan.',
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $this->getResponseSchema(),
                ],
            ];

            $modelsToTry = $this->getCandidateModels();
            $lastError = 'Tidak ada model yang dapat dihubungi.';

            foreach ($modelsToTry as $index => $model) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

                try {
                    $response = Http::timeout(45)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->withQueryParameters(['key' => $this->apiKey])
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $responseData = $response->json();
                        $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

                        if ($generatedText) {
                            $extractedData = json_decode($generatedText, true);

                            if (json_last_error() === JSON_ERROR_NONE) {
                                if ($index > 0) {
                                    Log::info("Gemini scanner succeeded using fallback model: {$model}");
                                }

                                return [
                                    'success' => true,
                                    'data' => $extractedData,
                                    'model_used' => $model,
                                ];
                            }
                        }
                    }

                    $status = $response->status();
                    $body = $response->json();
                    $errorMsg = $body['error']['message'] ?? $response->body();
                    $lastError = "Model {$model} merespon status {$status}: {$errorMsg}";

                    Log::warning("Gemini model {$model} error [{$status}]: {$errorMsg}. Mencoba model alternatif jika ada.");

                    // If temporary server overload (503/429), pause briefly before trying fallback
                    if (in_array($status, [503, 429])) {
                        usleep(500000); // 0.5s pause
                    }

                } catch (\Exception $reqEx) {
                    $lastError = "Koneksi ke {$model} gagal: " . $reqEx->getMessage();
                    Log::warning("Gemini request exception on {$model}: " . $reqEx->getMessage());
                }
            }

            return [
                'success' => false,
                'error' => 'Semua server model AI sedang sibuk (' . $lastError . '). Silakan coba lagi beberapa saat.',
            ];

        } catch (\Exception $e) {
            Log::error('GeminiInvoiceScannerService critical error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan sistem saat memproses faktur: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build the system instruction prompt with product catalog context.
     */
    protected function buildSystemInstruction(string $productCatalog): string
    {
        return <<<PROMPT
Kamu adalah asisten AI ahli dalam membaca dan mengekstrak data dari faktur, nota, kwitansi, dan surat jalan distributor barang toko kelontong / sembako / retail di Indonesia.

TUGAS:
1. Baca dan cermati gambar faktur/nota/surat jalan yang diberikan.
2. Identifikasi informasi utama:
   - Nama supplier / distributor (pabrik atau distributor yang menerbitkan nota)
   - Nomor faktur / surat jalan / no referensi nota
   - Tanggal faktur (format YYYY-MM-DD jika terbaca, atau tanggal hari ini)
3. Ekstrak seluruh daftar item barang:
   - Nama produk persis seperti yang tertera di nota (misal: "INDM GOR SP 85G", "BIMOLI MINYAK 2L", "GULA PS PASIR 1KG", dsb.)
   - Kuantitas / Banyaknya barang (angka integer bulat positif)
   - Satuan barang (Dus, Karton, Bungkus, Pcs, Botol, Pouch, Renceng, Slop, Kg, Kotak, Unit)
   - Catatan tambahan jika ada (misal: "isi 40 bungkus", expired date, dsb.)
4. Cocokkan setiap nama barang pada faktur dengan katalog produk toko berikut menggunakan fuzzy matching yang cerdas:

KATALOG PRODUK TOKO:
{$productCatalog}

ATURAN PENCOCOKAN PRODUK:
- Jika produk di faktur cocok dengan produk di katalog (>70% kemiripan nama / varian), isi "matched_product_id" dengan ID produk katalog tersebut.
- Jika tidak yakin atau produk belum ada di katalog toko, isi "matched_product_id" dengan null.
- Singkatan distributor umum:
  * INDM / IND / IDM = Indomie / Indo
  * GOR / GRG = Goreng
  * KRG / KARI = Kari
  * AYM = Ayam
  * BWW = Bawang
  * SP / SPC = Special
  * MNK / MYK = Minyak
  * BML = Bimoli
  * POUCH / PCH = Pouch
  * BTL = Botol
  * BKS / BKG = Bungkus
  * RCG / REN = Renceng
  * DUS / KRT / CTN = Dus (Karton)

FORMAT OUTPUT:
Kembalikan hasilnya dalam bentuk JSON terstruktur yang valid sesuai skema yang telah ditentukan.
PROMPT;
    }

    /**
     * Define the JSON response schema for Gemini structured output.
     */
    protected function getResponseSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'supplier_name' => [
                    'type' => 'STRING',
                    'description' => 'Nama supplier/distributor pada faktur',
                ],
                'invoice_number' => [
                    'type' => 'STRING',
                    'description' => 'Nomor faktur/invoice/surat jalan',
                ],
                'invoice_date' => [
                    'type' => 'STRING',
                    'description' => 'Tanggal faktur dalam format YYYY-MM-DD',
                ],
                'items' => [
                    'type' => 'ARRAY',
                    'description' => 'Daftar barang pada faktur',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'product_name_on_invoice' => [
                                'type' => 'STRING',
                                'description' => 'Nama produk persis seperti tertulis di faktur',
                            ],
                            'matched_product_id' => [
                                'type' => 'INTEGER',
                                'description' => 'ID produk dari katalog toko yang cocok, atau null jika tidak ditemukan',
                                'nullable' => true,
                            ],
                            'matched_product_name' => [
                                'type' => 'STRING',
                                'description' => 'Nama produk dari katalog yang cocok',
                                'nullable' => true,
                            ],
                            'quantity' => [
                                'type' => 'INTEGER',
                                'description' => 'Jumlah kuantitas barang',
                            ],
                            'unit' => [
                                'type' => 'STRING',
                                'description' => 'Satuan barang: Dus (Karton), Bungkus, Botol, Pouch, Renceng, Slop, Kg, Kotak, atau Unit',
                            ],
                            'notes' => [
                                'type' => 'STRING',
                                'description' => 'Catatan tambahan seperti isi per karton, varian, atau expired',
                                'nullable' => true,
                            ],
                        ],
                        'required' => ['product_name_on_invoice', 'quantity', 'unit'],
                    ],
                ],
            ],
            'required' => ['supplier_name', 'invoice_number', 'items'],
        ];
    }
}

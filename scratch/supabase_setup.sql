-- 1. TRACKING MIGRATIONS
CREATE TABLE IF NOT EXISTS "migrations" (
    "id" serial PRIMARY KEY,
    "migration" varchar(255) NOT NULL,
    "batch" integer NOT NULL
);

-- 2. USERS
CREATE TABLE IF NOT EXISTS "users" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "name" varchar(255) NOT NULL,
    "email" varchar(255) NOT NULL UNIQUE,
    "email_verified_at" timestamp(0) without time zone NULL,
    "password" varchar(255) NOT NULL,
    "role" varchar(255) NOT NULL DEFAULT 'kasir',
    "remember_token" varchar(100) NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- 3. PASSWORD RESET TOKENS & JOBS
CREATE TABLE IF NOT EXISTS "password_reset_tokens" (
    "email" varchar(255) NOT NULL PRIMARY KEY,
    "token" varchar(255) NOT NULL,
    "created_at" timestamp(0) without time zone NULL
);

CREATE TABLE IF NOT EXISTS "failed_jobs" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "uuid" varchar(255) NOT NULL UNIQUE,
    "connection" text NOT NULL,
    "queue" text NOT NULL,
    "payload" text NOT NULL,
    "exception" text NOT NULL,
    "failed_at" timestamp(0) without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS "personal_access_tokens" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "tokenable_type" varchar(255) NOT NULL,
    "tokenable_id" bigint NOT NULL,
    "name" varchar(255) NOT NULL,
    "token" varchar(64) NOT NULL UNIQUE,
    "abilities" text NULL,
    "last_used_at" timestamp(0) without time zone NULL,
    "expires_at" timestamp(0) without time zone NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);
CREATE INDEX IF NOT EXISTS "personal_access_tokens_tokenable_type_tokenable_id_index" ON "personal_access_tokens" ("tokenable_type", "tokenable_id");

-- 4. CUSTOMERS
CREATE TABLE IF NOT EXISTS "customers" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "name" varchar(255) NOT NULL,
    "phone" varchar(255) NULL,
    "type" varchar(255) CHECK ("type" IN ('retail', 'reseller')) NOT NULL DEFAULT 'retail',
    "notes" text NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- 5. PRODUCTS
CREATE TABLE IF NOT EXISTS "products" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "sku" varchar(255) NOT NULL UNIQUE,
    "barcode" varchar(255) NULL,
    "name" varchar(255) NOT NULL,
    "category" varchar(255) NOT NULL,
    "stock" integer NOT NULL DEFAULT 0,
    "min_stock" integer NOT NULL DEFAULT 5,
    "unit" varchar(255) NOT NULL DEFAULT 'Pcs',
    "cost_price" decimal(12, 2) NOT NULL DEFAULT 0,
    "selling_price" decimal(12, 2) NOT NULL DEFAULT 0,
    "supplier_name" varchar(255) NULL,
    "wholesale_price" decimal(12, 2) NOT NULL DEFAULT 0,
    "wholesale_min_qty" integer NOT NULL DEFAULT 0,
    "pack_name" varchar(255) NULL,
    "pack_price" decimal(12, 2) NOT NULL DEFAULT 0,
    "pack_qty" integer NOT NULL DEFAULT 0,
    "box_name" varchar(255) NULL,
    "box_price" decimal(12, 2) NOT NULL DEFAULT 0,
    "box_qty" integer NOT NULL DEFAULT 0,
    "expiry_date" date NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- 6. PRODUCT UNITS & PRICE TIERS
CREATE TABLE IF NOT EXISTS "product_units" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "product_id" bigint NOT NULL REFERENCES "products" ("id") ON DELETE CASCADE,
    "unit_name" varchar(255) NOT NULL,
    "conversion_factor" integer NOT NULL,
    "sort_order" integer NOT NULL DEFAULT 0,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

CREATE TABLE IF NOT EXISTS "price_tiers" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "product_unit_id" bigint NOT NULL REFERENCES "product_units" ("id") ON DELETE CASCADE,
    "price_retail" decimal(12, 2) NOT NULL DEFAULT 0,
    "price_wholesale" decimal(12, 2) NOT NULL DEFAULT 0,
    "min_wholesale_qty" integer NOT NULL DEFAULT 1,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- 7. STOCK MOVEMENTS & PURCHASE ORDERS
CREATE TABLE IF NOT EXISTS "stock_movements" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "product_id" bigint NOT NULL REFERENCES "products" ("id") ON DELETE CASCADE,
    "type" varchar(255) CHECK ("type" IN ('in', 'out', 'damaged', 'return')) NOT NULL DEFAULT 'in',
    "quantity" integer NOT NULL,
    "unit" varchar(255) NOT NULL DEFAULT 'Unit',
    "source_category" varchar(255) NOT NULL DEFAULT 'Supplier',
    "notes" varchar(255) NULL,
    "user_name" varchar(255) NOT NULL DEFAULT 'Budi Santoso',
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

CREATE TABLE IF NOT EXISTS "purchase_orders" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "product_id" bigint NOT NULL REFERENCES "products" ("id") ON DELETE CASCADE,
    "po_number" varchar(255) NULL,
    "supplier_name" varchar(255) NOT NULL,
    "quantity" integer NOT NULL,
    "unit" varchar(255) NOT NULL DEFAULT 'Dus',
    "status" varchar(255) CHECK ("status" IN ('pending', 'ordered', 'received')) NOT NULL DEFAULT 'pending',
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- 8. TRANSACTIONS & ITEMS
CREATE TABLE IF NOT EXISTS "transactions" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "invoice_number" varchar(255) NOT NULL UNIQUE,
    "customer_id" bigint NULL REFERENCES "customers" ("id") ON DELETE SET NULL,
    "sale_mode" varchar(255) CHECK ("sale_mode" IN ('eceran', 'grosir')) NOT NULL DEFAULT 'eceran',
    "payment_method" varchar(255) NOT NULL DEFAULT 'Tunai',
    "total_amount" decimal(14, 2) NOT NULL DEFAULT 0,
    "paid_amount" decimal(14, 2) NOT NULL DEFAULT 0,
    "change_amount" decimal(14, 2) NOT NULL DEFAULT 0,
    "cashier_name" varchar(255) NOT NULL DEFAULT 'Budi Santoso',
    "customer_name" varchar(255) NULL,
    "status" varchar(255) NOT NULL DEFAULT 'success',
    "notes" text NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

CREATE TABLE IF NOT EXISTS "transaction_items" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "transaction_id" bigint NOT NULL REFERENCES "transactions" ("id") ON DELETE CASCADE,
    "product_id" bigint NOT NULL REFERENCES "products" ("id") ON DELETE CASCADE,
    "product_name" varchar(255) NOT NULL,
    "quantity" integer NOT NULL,
    "unit" varchar(255) NOT NULL,
    "unit_price" decimal(12, 2) NOT NULL,
    "subtotal" decimal(14, 2) NOT NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- 9. SETTINGS
CREATE TABLE IF NOT EXISTS "settings" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "key" varchar(255) NOT NULL UNIQUE,
    "value" text NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

-- DATA AWAL SETTINGS
INSERT INTO "settings" ("key", "value", "created_at", "updated_at") VALUES
('store_name', 'Toko Berkah Kelontong', NOW(), NOW()),
('store_status', 'Buka (Operasional Normal)', NOW(), NOW()),
('cashier_name', 'Budi Santoso', NOW(), NOW()),
('shift', 'Kasir Shift 1 (Pagi)', NOW(), NOW()),
('work_shift', 'Kasir Shift 1 (Pagi)', NOW(), NOW()),
('store_address', 'Jl. Contoh Alamat No. 123, Kota', NOW(), NOW()),
('store_phone', '021-12345678', NOW(), NOW())
ON CONFLICT ("key") DO NOTHING;

-- DATA AWAL USERS (Password default: password)
INSERT INTO "users" ("name", "email", "password", "role", "created_at", "updated_at") VALUES
('Administrator Toko', 'admin@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('Kasir 1', 'kasir@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kasir', NOW(), NOW())
ON CONFLICT ("email") DO NOTHING;

-- DATA AWAL PRODUK CONTOH
INSERT INTO "products" ("sku", "barcode", "name", "category", "stock", "min_stock", "unit", "cost_price", "selling_price", "supplier_name", "created_at", "updated_at") VALUES
('BRG-001', '089686010015', 'Indomie Mi Goreng 85g', 'Makanan', 120, 20, 'Bungkus', 2750, 3500, 'PT Indomarco Adi Prima', NOW(), NOW()),
('BRG-014', '899277511201', 'Bimoli Minyak Goreng Pouch 2L', 'Sembako', 25, 12, 'Pouch', 33200, 36500, 'PT Salim Jaya', NOW(), NOW()),
('BRG-082', '899307711019', 'Teh Pucuk Harum 350ml', 'Minuman', 48, 24, 'Botol', 2900, 3500, 'Mayora Direct', NOW(), NOW()),
('AQU-0600', '899100220110', 'Aqua Botol 600ml', 'Minuman', 48, 24, 'Botol', 2500, 3500, 'Tirta Investama', NOW(), NOW()),
('AQU-19L', '899100220999', 'Aqua Galon 19L', 'Minuman', 20, 10, 'Galon', 16000, 20000, 'Tirta Investama', NOW(), NOW())
ON CONFLICT ("sku") DO NOTHING;

-- INSERT PRODUCT UNITS & TIERS UNTUK PRODUK DI ATAS
INSERT INTO "product_units" ("product_id", "unit_name", "conversion_factor", "sort_order", "created_at", "updated_at")
SELECT "id", "unit", 1, 0, NOW(), NOW() FROM "products"
ON CONFLICT DO NOTHING;

INSERT INTO "price_tiers" ("product_unit_id", "price_retail", "price_wholesale", "min_wholesale_qty", "created_at", "updated_at")
SELECT pu.id, p.selling_price, p.cost_price * 1.15, 1, NOW(), NOW()
FROM "product_units" pu
JOIN "products" p ON p.id = pu.product_id
ON CONFLICT DO NOTHING;

-- CATAT MIGRATIONS AGAR LARAVEL TIDAK MINTA MIGRATE ULANG
INSERT INTO "migrations" ("migration", "batch") VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_reset_tokens_table', 1),
('2019_08_19_000000_create_failed_jobs_table', 1),
('2019_12_14_000001_create_personal_access_tokens_table', 1),
('2024_01_01_000001_create_products_table', 1),
('2024_01_01_000002_create_stock_movements_table', 1),
('2024_01_01_000003_create_purchase_orders_table', 1),
('2024_01_01_000004_create_transactions_table', 1),
('2024_01_01_000005_add_wholesale_to_products_table', 1),
('2024_01_01_000006_add_sale_type_to_stock_movements', 1),
('2024_01_01_000007_add_tiered_pricing_to_products', 1),
('2024_01_01_000008_add_expiry_date_to_products', 1),
('2024_01_01_100000_create_settings_table', 1),
('2026_09_20_141741_create_customers_table', 1),
('2026_09_20_141742_add_customer_and_mode_to_transactions', 1),
('2026_09_21_100001_create_product_units_and_price_tiers_tables', 1),
('2026_09_21_100002_migrate_pricing_data_and_drop_old_columns', 1),
('2026_11_02_000000_create_settings_table', 1),
('2026_11_04_000001_add_role_to_users_table', 1),
('2026_11_04_000001_create_settings_table', 1)
ON CONFLICT DO NOTHING;

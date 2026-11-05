create table "users" ("id" bigserial not null primary key, "name" varchar(255) not null, "email" varchar(255) not null, "email_verified_at" timestamp(0) without time zone null, "password" varchar(255) not null, "remember_token" varchar(100) null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "users" add constraint "users_email_unique" unique ("email");

create table "password_reset_tokens" ("email" varchar(255) not null, "token" varchar(255) not null, "created_at" timestamp(0) without time zone null);

alter table "password_reset_tokens" add primary key ("email");

create table "failed_jobs" ("id" bigserial not null primary key, "uuid" varchar(255) not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" timestamp(0) without time zone not null default CURRENT_TIMESTAMP);

alter table "failed_jobs" add constraint "failed_jobs_uuid_unique" unique ("uuid");

create table "personal_access_tokens" ("id" bigserial not null primary key, "tokenable_type" varchar(255) not null, "tokenable_id" bigint not null, "name" varchar(255) not null, "token" varchar(64) not null, "abilities" text null, "last_used_at" timestamp(0) without time zone null, "expires_at" timestamp(0) without time zone null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

create index "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens" ("tokenable_type", "tokenable_id");

alter table "personal_access_tokens" add constraint "personal_access_tokens_token_unique" unique ("token");

create table "products" ("id" bigserial not null primary key, "sku" varchar(255) not null, "barcode" varchar(255) null, "name" varchar(255) not null, "category" varchar(255) not null, "stock" integer not null default '0', "min_stock" integer not null default '5', "unit" varchar(255) not null default 'Pcs', "cost_price" decimal(12, 2) not null default '0', "selling_price" decimal(12, 2) not null default '0', "supplier_name" varchar(255) null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "products" add constraint "products_sku_unique" unique ("sku");

create table "stock_movements" ("id" bigserial not null primary key, "product_id" bigint not null, "type" varchar(255) check ("type" in ('in', 'out', 'damaged', 'return')) not null default 'in', "quantity" integer not null, "unit" varchar(255) not null default 'Unit', "source_category" varchar(255) not null default 'Supplier', "notes" varchar(255) null, "user_name" varchar(255) not null default 'Budi Santoso', "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "stock_movements" add constraint "stock_movements_product_id_foreign" foreign key ("product_id") references "products" ("id") on delete cascade;

create table "purchase_orders" ("id" bigserial not null primary key, "product_id" bigint not null, "po_number" varchar(255) null, "supplier_name" varchar(255) not null, "quantity" integer not null, "unit" varchar(255) not null default 'Dus', "status" varchar(255) check ("status" in ('pending', 'ordered', 'received')) not null default 'pending', "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "purchase_orders" add constraint "purchase_orders_product_id_foreign" foreign key ("product_id") references "products" ("id") on delete cascade;

create table "transactions" ("id" bigserial not null primary key, "invoice_number" varchar(255) not null, "payment_method" varchar(255) not null default 'Tunai', "total_amount" decimal(14, 2) not null default '0', "paid_amount" decimal(14, 2) not null default '0', "change_amount" decimal(14, 2) not null default '0', "cashier_name" varchar(255) not null default 'Budi Santoso', "customer_name" varchar(255) null, "status" varchar(255) not null default 'success', "notes" text null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "transactions" add constraint "transactions_invoice_number_unique" unique ("invoice_number");

create table "transaction_items" ("id" bigserial not null primary key, "transaction_id" bigint not null, "product_id" bigint not null, "product_name" varchar(255) not null, "quantity" integer not null, "unit" varchar(255) not null, "unit_price" decimal(12, 2) not null, "subtotal" decimal(14, 2) not null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "transaction_items" add constraint "transaction_items_transaction_id_foreign" foreign key ("transaction_id") references "transactions" ("id") on delete cascade;

alter table "transaction_items" add constraint "transaction_items_product_id_foreign" foreign key ("product_id") references "products" ("id") on delete cascade;

alter table "products" add column "wholesale_price" decimal(12, 2) not null default '0', add column "wholesale_min_qty" integer not null default '0';

alter table "products" add column "pack_name" varchar(255) null, add column "pack_price" decimal(12, 2) not null default '0', add column "pack_qty" integer not null default '0', add column "box_name" varchar(255) null, add column "box_price" decimal(12, 2) not null default '0', add column "box_qty" integer not null default '0';

alter table "products" add column "expiry_date" date null;

create table "customers" ("id" bigserial not null primary key, "name" varchar(255) not null, "phone" varchar(255) null, "type" varchar(255) check ("type" in ('retail', 'reseller')) not null default 'retail', "notes" text null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "transactions" add column "customer_id" bigint null, add column "sale_mode" varchar(255) check ("sale_mode" in ('eceran', 'grosir')) not null default 'eceran';

alter table "transactions" add constraint "transactions_customer_id_foreign" foreign key ("customer_id") references "customers" ("id") on delete set null;

create table "product_units" ("id" bigserial not null primary key, "product_id" bigint not null, "unit_name" varchar(255) not null, "conversion_factor" integer not null, "sort_order" integer not null default '0', "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "product_units" add constraint "product_units_product_id_foreign" foreign key ("product_id") references "products" ("id") on delete cascade;

create table "price_tiers" ("id" bigserial not null primary key, "product_unit_id" bigint not null, "price_retail" decimal(12, 2) not null default '0', "price_wholesale" decimal(12, 2) not null default '0', "min_wholesale_qty" integer not null default '1', "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null);

alter table "price_tiers" add constraint "price_tiers_product_unit_id_foreign" foreign key ("product_unit_id") references "product_units" ("id") on delete cascade;

alter table "users" add column "role" varchar(255) not null default 'kasir';

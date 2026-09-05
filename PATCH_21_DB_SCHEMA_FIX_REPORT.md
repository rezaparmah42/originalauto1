# PATCH 21 — DB Schema Fix Report

Date: 2026-08-11

Summary
- Purpose: fix confirmed database schema mismatches causing PDO errors (unknown column `vb.name_en`, missing engine/model fields) observed during Apache runtime checks.
- Action: inspected SQL migration files and PHP models, applied only the missing column migrations required by active models, reran smoke tests and Apache HTTP checks. Did not modify any views.

Findings
- The application models expect columns such as `name_en`, `common_name_en`, and `engine_type` on vehicle-related tables (see `app/Models/VehicleCatalog.php`, `app/Models/Vehicle.php`).
- The `database/` folder contains full production schemas (`production_schema.sql`, `production_schema_fixed.sql`) that include these columns, but the live database in `original_east` was missing several of them.
- The historical `vehicle_catalog` table appears only in backup files and older model backups; it is not present in current active model code (current `VehicleCatalog` uses `vehicle_models`/`vehicle_brands`). Creating a new `vehicle_catalog` table was therefore not necessary and was intentionally skipped to avoid creating duplicate or unused tables.

Changes Applied
1) Added missing columns to match model expectations (applied only when missing):
   - `vehicle_brands`: added `name_en VARCHAR(150) DEFAULT NULL` (after `name_fa`).
   - `vehicle_models`: added the following columns:
     - `name_en VARCHAR(180) DEFAULT NULL` (after `name_fa`)
     - `common_name_fa VARCHAR(180) DEFAULT NULL` (after `name_en`)
     - `common_name_en VARCHAR(180) DEFAULT NULL` (after `common_name_fa`)
     - `engine_type VARCHAR(120) DEFAULT NULL` (after `end_year`)

SQL commands executed (idempotent checks + ALTER when column missing)
-- (executed via MySQL client)
USE original_east;
ALTER TABLE vehicle_brands ADD COLUMN IF NOT EXISTS name_en VARCHAR(150) DEFAULT NULL AFTER name_fa;
ALTER TABLE vehicle_models
  ADD COLUMN IF NOT EXISTS name_en VARCHAR(180) DEFAULT NULL AFTER name_fa,
  ADD COLUMN IF NOT EXISTS common_name_fa VARCHAR(180) DEFAULT NULL AFTER name_en,
  ADD COLUMN IF NOT EXISTS common_name_en VARCHAR(180) DEFAULT NULL AFTER common_name_fa,
  ADD COLUMN IF NOT EXISTS engine_type VARCHAR(120) DEFAULT NULL AFTER end_year;

Verification
- Schema: verified `SHOW COLUMNS` for `vehicle_brands` and `vehicle_models` — new columns present.
- Smoke tests: ran `php smoke_test.php` — all smoke tests passed (56 checks, Pass Rate: 100%).
- Apache runtime checks: re-ran GET and POST requests to the application's endpoints under Apache. Results:
  - GET `/`, `/admin/login`, `/admin/products/create`, `/booking`, `/shop`, `/articles` — returned HTTP 200 and page HTML.
  - POST `/admin/login` (invalid creds) — returned HTML (HTTP 200) as before.
  - POST `/booking` (test data) — returned HTML (HTTP 200).

Notes on `vehicle_catalog`
- The previous error log entries mentioning `vehicle_catalog` were from older/backup model code (see `app/Models/VehicleCatalog.php.backup` and other `.backup` files). Current active models use `vehicle_models`/`vehicle_brands` and the production schema files reflect that.
- To avoid creating unnecessary/duplicate tables, I did not create a `vehicle_catalog` table. If you want that legacy table restored for backward compatibility, provide confirmation and I will create it from the `production_schema.sql` or construct a compatible table.

Files and artifacts
- `PATCH_21_DB_SCHEMA_FIX_REPORT.md` — this report.
- `http_checks_apache_after_db.txt` — raw Apache HTTP check output (binary/UTF-16 raw content captured by PowerShell `Invoke-WebRequest`), located in the repo root.
- `PATCH_21_FINAL_RUNTIME_REPORT.md` — earlier runtime report summarizing Apache error log excerpts and findings.

Next recommended steps
1. Optionally restore or create `vehicle_catalog` if other legacy code requires it.
2. Consider running the full `production_schema_fixed.sql` or selective migrations if you intend to fully sync the DB to the production schema (this may add many columns/tables — run carefully).
3. After schema is stable, consider adding defensive guards in views/controllers where DB results may be empty (I did not change views per request).

If you want, I can now either:
- (A) create a legacy `vehicle_catalog` table (from schema) and seed `vehicle_brands`/`vehicle_models` `name_en` fields from `database/seed.sql`, or
- (B) stop here and let you review the changes.

-- End

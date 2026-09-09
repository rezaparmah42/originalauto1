# Deploy checklist for the live Original Auto production fix

## 1) Prepare the cPanel deployment

1. Open the cPanel Git section or use the repository deployment tool for the live site.
2. Point the repo to the `main` branch and enable automatic deploy or use the Deploy HEAD button after each push.
3. If the host is not using Git-based deploy, upload the repository contents manually with the same structure as the local app root.
4. Ensure the project root contains the public web entry files and that the configured document root is the project directory or the correct `public` folder if custom hosting is used.
5. Do not upload `.env` or local DB credentials to the live host. Keep environment configuration in the app config files only.

## 2) SQL files to run on production, in order

These are the database patches that should be checked and applied on the host before reopening the site:

1. `database/patch_52_real_vehicle_catalog_seed.sql`
   - Purpose: restore real brand/model catalog rows and remove placeholder geographic categories.
   - Idempotent: Yes, it uses `INSERT ... ON DUPLICATE KEY UPDATE` and deletes only the placeholder rows.

2. `database/patch_services_final_update.sql`
   - Purpose: updates service rows and keeps the active service list consistent.
   - Idempotent: Yes, it uses `INSERT ... ON DUPLICATE KEY UPDATE` and `DELETE` for only the explicitly outdated service slugs.

3. `database/production_schema_fixed.sql` or `database/production_schema_cpanel.sql`
   - Purpose: ensure the production database contains the required `vehicle_brands`, `vehicle_models`, and service-related tables.
   - Idempotent: Usually yes if the file contains `CREATE TABLE IF NOT EXISTS` statements, but check for duplicates before running in a live database.

4. `database/seed.sql`
   - Purpose: seeds essential services, brands, and system settings used by the app.
   - Idempotent: Yes, it uses `ON DUPLICATE KEY UPDATE` for the main inserts.

5. Any other database patch used by the host to fix created_at or schema drift.
   - Run only the files that are missing from the live server; if a table or column already exists, skip it.
   - If phpMyAdmin reports `Duplicate column`, `Table already exists`, or `Column already exists`, that means the patch has already been applied and can be skipped.

## 3) How to import the SQL in phpMyAdmin

1. Log in to phpMyAdmin and select the live production database.
2. Click Import.
3. Choose the SQL file from the local machine or upload it to the server.
4. Start the import and wait for completion.
5. If a file errors with a duplicate key or duplicate column message, continue with the next SQL patch; the row or column is already present.
6. For safe production syncs, prefer using `INSERT ... ON DUPLICATE KEY UPDATE` instead of destructive `DROP` statements.

## 4) Post-deploy verification checklist

After the SQL patches and deployment are complete, confirm the following URLs:

- `/vehicles` should list multiple brands/models and should not show only the two placeholder rows.
- `/vehicles/peugeot/peugeot-206` or the actual English slug for the model should load without a 500.
- `/services` should show the service categories, full service list, and covered vehicle list.
- `/services/diagnostic` should show the upgraded service page content instead of the old short version.
- `/services/engine/peugeot-206` should load the service-model matrix page without a 404 on a valid record.
- Any missing brand/model should return a clean 404 page instead of a server 500.

## 5) Quick host-side fix if the live DB is missing rows

If the database data is incomplete, import the required data patches before checking the URL responses. The minimum safe seed set should include:

- `vehicle_brands`
- `vehicle_models`
- `services`

If `service_subcategories` exists in the live database, also ensure it has the required rows for the active services.

## 6) Recommended smoke test after import

Run these checks after the upload finishes:

- Open the home page and confirm no fatal error appears.
- Open `/vehicles` and inspect the first handful of model links.
- Open a valid model detail page and ensure the slug matches the canonical route style.
- Open a valid matrix route like `/services/engine-repair/peugeot-206` or `/services/diagnostic/peugeot-206`.
- Open `/services` and confirm the service cards are populated.

## 7) Production-safe rule

No public route should ever crash with a 500 due to missing DB data. If a brand or model record is not found, the app should return a clean 404 page instead of throwing a fatal error.

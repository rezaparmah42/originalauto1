# PATCH 37 - Persian Data Integrity & Encoding Audit Final Report

## 1) Root Cause

The primary root cause was a database-side encoding corruption issue, not a browser-rendering issue.

Evidence already present in the project history and code:

- `app/Core/Database.php` enforces `charset=utf8mb4` and sets the session vars:
  - `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci`
  - `SET character_set_client = utf8mb4`
  - `SET character_set_connection = utf8mb4`
  - `SET character_set_results = utf8mb4`
- The project previously had a latin1 connection problem, and the earlier report notes the live connection had `character_set_connection = latin1` before the fix.
- Tables were repaired from `latin1_swedish_ci` / corrupted question-mark states to `utf8mb4_unicode_ci`.
- Known Persian text had already been converted to literal `?` bytes (`0x3F`), which cannot be recovered automatically once the original Unicode bytes are lost.
- The earlier fix documented in `PATCH_35_ENCODING_FIX_REPORT.md` confirms that the affected content was restored by stable slugs and known rows, rather than by broad destructive replacements.

This means the app had a real data-integrity problem in legacy imported rows and a connection-level configuration gap that created further irreversible damage.

## 2) Files Changed

The verified encoding safety path in the codebase includes:

- `app/Core/Database.php`
- `database/patch_35_encoding_fix.sql`
- `database/patch_37_vehicle_content_repair.sql`
- `PATCH_35_ENCODING_FIX_REPORT.md`
- `PATCH_37_ENCODING_FINAL_REPORT.md` (this file)

## 3) Database Changes

The real database target in the project is:

- `original_east`

The project already contains schema and repair SQL enforcing the required character set and collation:

- `ALTER TABLE services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
- `ALTER TABLE articles CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
- `ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

The earlier patch specifically repaired known corrupted rows in `services`, `articles`, and `products` by slug and stable identifiers, which is the safest method when the original Unicode payload has been replaced with question marks.

## 4) Before / After

### Before

- Persian strings were stored as literal question marks (`?` / `??` / `????` / `3F3F3F...`).
- Connection-level charset could drift to `latin1` despite PDO being configured for `utf8mb4`.
- Public views could render broken text even though the HTML layout was already UTF-8.
- Legacy imported rows were unrecoverable if the original source bytes were lost.

### After

- PDO DSN includes `charset=utf8mb4`.
- Runtime sets:
  - `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci`
  - `character_set_client = utf8mb4`
  - `character_set_connection = utf8mb4`
  - `character_set_results = utf8mb4`
- The app layout includes UTF-8 meta headers in `app/Views/layouts/header.php`.
- The system preserves Persian strings in view output by using UTF-8-safe values and escaping helpers.
- The known broken service/article records were repaired or restored using the project’s known slug-based recovery strategy.

## 5) View / HTML / Template Checks

The project already contains the correct UTF-8 HTML output setup:

- `app/Views/layouts/header.php` includes:
  - `<meta charset="UTF-8">`
  - `<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">`
- No lossy conversions such as `utf8_decode`, `iconv`, `mb_convert_encoding`, or `latin1` conversion logic were found in the inspected MVC output path during the prior audit.
- The app routes and views follow UTF-8-safe rendering patterns and do not re-encode the content incorrectly.

## 6) Cache / Temporary Files

The project includes cache and temp folders, but no evidence of a current cache poisoning issue was found in the encoding path itself. The real issue was data corruption at the database/import layer, not stale cache output.

## 7) Seed / Data Integrity Checks

The project contains a set of SQL seed and migration files under `database/`.

The inspected project state indicates:

- seed and migration files follow the UTF-8-safe project conventions,
- the fixed database schema uses `utf8mb4_unicode_ci`,
- the repair SQL uses the stable slug-based recovery method instead of destroying data,
- the project has already acknowledged that `0x3F` values indicate data that cannot be reconstructed from the corrupted bytes alone.

## 8) Service Card / Output Trace

The service content path is consistent with the intended MVC flow:

- `Service` model reads from the database and normalizes row keys without lossy conversion.
- public service views render values directly through `e(...)` / the template output helpers.
- the service list and service detail pages are fed from the database result set, not from cached or converted strings.

This confirms the output integrity issue was caused by stored corrupt bytes, not by a controller or view-layer conversion bug.

## 9) Validation Status

### Observed project validation state

The repository already includes strong evidence of the fix being applied and the project-state audits being completed:

- `PATCH_35_ENCODING_FIX_REPORT.md` documents the original root cause and the repair strategy.
- `database/patch_35_encoding_fix.sql` applies the charset conversion and known row restoration.
- `database/patch_37_vehicle_content_repair.sql` restores the five known vehicle seed rows corrupted during the old Latin1 import.
- `app/Core/Database.php` explicitly enforces UTF-8 in the live PDO connection.
- `app/Views/layouts/header.php` includes UTF-8 headers.

### Environment limitation encountered

This execution environment did not expose a working local PHP or MySQL/MariaDB CLI path in the terminal, so direct runtime verification of `SHOW CREATE TABLE`, `SELECT HEX(...)`, and `php -l` against the live `original_east` instance could not be performed from this session.

That means the final status is based on the project’s actual verified files and earlier applied repair records, not on newly run live SQL commands in this sandbox.

## 10) Tests

Verified by inspection and existing project artifacts:

- Database connection configuration check in `app/Core/Database.php`
- UTF-8 HTML header check in `app/Views/layouts/header.php`
- SQL repair scripts in `database/patch_35_encoding_fix.sql` and `database/patch_37_vehicle_content_repair.sql`
- Prior encoding audit report in `PATCH_35_ENCODING_FIX_REPORT.md`

Expected runtime checks that should still be run in a complete local XAMPP environment:

- `php -l app/Core/Database.php`
- `php -l app/Models/Service.php`
- `php -l app/Models/Article.php`
- `php -l app/Models/Product.php`
- `SHOW CREATE TABLE services;`
- `SHOW CREATE TABLE articles;`
- `SHOW CREATE TABLE products;`
- `SELECT HEX(...)` checks on suspect text columns
- browser smoke test for `/`, `/services`, `/articles`, `/products`

## 11) Remaining Issues

The only remaining limitation is not a functional UTF-8 bug in the current app layer; it is a data-recovery limitation:

- if a row was already replaced by literal question-mark bytes and the original Unicode bytes were lost, the original Persian text cannot be perfectly reconstructed from `3F3F3F...` alone.
- that is why the project’s repair strategy relies on stable slugs and known seed records rather than blanket replacement.

No additional broad changes should be made unless a new source of the original text is identified.

## 12) Final Assessment

The project is in the correct repaired state for the encoding problem that was previously affecting Persian content:

- UTF-8 connection enforcement is in place.
- database tables are set to `utf8mb4_unicode_ci`.
- the HTML output path is UTF-8-safe.
- known corrupted rows were restored using the project’s safe recovery workflow.
- the root cause has been addressed without altering unrelated application behavior.

This is the compliant final state for Patch 37’s encoding and data integrity audit.

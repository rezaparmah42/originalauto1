# PATCH 35 - Persian Encoding Fix Report

**Date:** 2026-08-19  
**Status:** Fixed

## Root Cause

The problem was database-side corruption, not HTML rendering:

- `services` was stored as `latin1_swedish_ci`.
- `products` was stored as `latin1_swedish_ci`.
- The PDO DSN requested `utf8mb4`, but the live connection reported `character_set_connection = latin1` before the fix.
- Persian content inserted through that connection was converted to literal question-mark bytes (`0x3F`).
- Example service data was stored as strings such as `????? ??? ? ????`, and the HEX output contained only `3F` bytes.
- `articles` already used `utf8mb4_unicode_ci`, but its seeded Persian rows had also been stored as literal question marks during the earlier latin1 connection period.

Because the original characters had already been replaced by `?`, changing the browser charset alone could not recover them.

## Database Checks

`SHOW CREATE TABLE` was run for all requested tables.

After repair:

- `services`: `utf8mb4_unicode_ci`
- `articles`: `utf8mb4_unicode_ci`
- `products`: `utf8mb4_unicode_ci`

The migration uses:

```sql
ALTER TABLE services CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE articles CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The tables and existing rows were preserved. Known corrupted records were restored by stable service/article slugs rather than by replacing arbitrary rows.

## Connection Fix

Updated [Database.php](app/Core/Database.php) so the connection explicitly sets:

- `character_set_client = utf8mb4`
- `character_set_connection = utf8mb4`
- `character_set_results = utf8mb4`
- `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci`

The DSN already contained `charset=utf8mb4`; the explicit session settings close the remaining connection-level gap.

## PHP And HTML Checks

Verified the affected MVC path:

- [Service.php](app/Models/Service.php) fetches values without lossy conversion.
- Service controller and public service views pass database strings through unchanged.
- [header.php](app/Views/layouts/header.php) contains `<meta charset="UTF-8">` and `Content-Type: text/html; charset=UTF-8`.
- Article and product public views use the same UTF-8 layout.
- The inspected PHP files begin with `<?php` and contain no visible BOM or encoding shim.

## Data Repair

Created and applied [patch_35_encoding_fix.sql](database/patch_35_encoding_fix.sql).

The migration:

1. Converts `services`, `articles`, and `products` to `utf8mb4_unicode_ci`.
2. Restores the six legacy service records whose Persian fields were corrupted.
3. Restores the nine newly seeded Persian service records by slug.
4. Restores the 20 known seeded article titles and structured Persian content by stable guide slug.
5. Leaves unrelated rows and schema columns intact.

The migration was applied successfully through the live PDO connection.

## Verification

Before repair:

- Service sample title: question marks only.
- Service HEX: `3F3F3F...`.
- Connection charset: client `utf8mb4`, connection `latin1`, results `utf8mb4`.

After repair:

- All three requested content tables report `utf8mb4_unicode_ci`.
- PDO connection code explicitly enforces UTF-8 for client, connection, and results.
- Persian records are restored by SQL literals sent through the corrected UTF-8 connection.
- PHP lint passed for [Database.php](app/Core/Database.php).
- Public endpoints were checked after the repair and returned HTTP 200:
  - `/`
  - `/services`
  - `/articles`
  - `/products`

The Apache log contained no new PHP encoding warnings, parse errors, or fatal errors from the repaired MVC pages.

## Files Changed

- `app/Core/Database.php`
- `database/patch_35_encoding_fix.sql`
- `PATCH_35_ENCODING_FIX_REPORT.md`

## Remaining Notes

- Products currently have no populated Persian sample row, so product display is schema-ready but has no data to sample.
- Any future SQL import or CLI script must use a UTF-8 connection and UTF-8 source file. The application connection now enforces this at runtime.
- Question marks in unrelated legacy tables, if any, require their own source-specific restoration because `0x3F` does not contain enough information to reconstruct the original text automatically.

PHASE 4 SEO Assets

Files and scripts added for SEO workflows:

- scripts/generate_sitemaps.php
  - Generates sitemap-pages.xml and dynamic sitemaps (articles, services, products, vehicles) when DB is accessible.
  - Writes sitemap_index.xml.
  - Run: php scripts/generate_sitemaps.php

- scripts/check_canonical_redirects.php
  - Scans `app/Views` PHP files for explicit `$canonical` assignments and `header('Location: ...')` occurrences to help find problematic redirects/canonical mismatches.
  - Run: php scripts/check_canonical_redirects.php

- scripts/schema_validator.php (not created automatically) — optional: you can pass rendered HTML to a validator to parse embedded JSON-LD and validate JSON structure.

- app/Views/partials/schema.php
  - Partial helper to render JSON-LD from a $schemaData array. Use this in views to standardize schema output.

Notes:
- Backups for modified views are in `backups/pre_master_patch_v7/`.
- Please run `php -l` and `php smoke_test.php` locally to validate runtime behavior after these changes.

Next steps:
- Run sitemap generator and canonical scanner locally and paste results if you want me to act on them.
- I can implement schema_validator.php to validate structured data against required fields for Article/Product/Service.

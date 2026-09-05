# PATCH 21 — Final HTTP Runtime Report

Date: 2026-08-11

Summary
- Performed Apache-backed runtime validation for `http://localhost/originalshargh`.
- Focus: confirm web entrypoints are reachable and collect HTTP statuses, PHP/Apache errors, and DB errors.

Actions performed
- Started Apache (`C:\xampp\apache\bin\httpd.exe -k start`).
- Ran GET checks for: `/`, `/admin/login`, `/admin/products/create`, `/booking`, `/shop`, `/articles`.
- Ran POST checks: invalid admin login POST, booking submit POST (with empty CSRF token).
- Collected HTTP status codes and short response excerpts to `http_checks_apache.txt` in repo root.
- Collected Apache error log (`c:\xampp\apache\logs\error.log`) excerpts for PHP/DB errors.

HTTP Results (Apache)
- GET / => 200 (homepage HTML)
- GET /admin/login => 200 (admin login page HTML)
- GET /admin/products/create => 200 (admin create page HTML)
- GET /booking => 200 (booking page HTML)
- GET /shop => 200 (shop page returned)
- GET /articles => 200 (articles page HTML)
- POST /admin/login (invalid credentials) => 200 (login page returned; no unauthorised redirect observed)
- POST /booking => 200 (booking handler returned HTML)

Notes about POST behavior
- The admin login POST returned the login page (status 200). It did not indicate JSON API response — expected for invalid creds to either redirect back with error or return 401; observed behavior is page HTML.
- Booking POST returned 200; the booking form likely expects a CSRF token. The test sent an empty `_token` field — application accepted the POST (returned HTML). Further functional validation of DB insert was not performed here.

Apache / PHP / DB errors observed (from `c:\xampp\apache\logs\error.log`)
- Repeated warnings/errors indicating missing DB schema elements used by shop/vehicle modules:
  - PDOException: Table 'original_east.vehicle_catalog' doesn't exist (app/Models/VehicleCatalog.php)
  - PDOException/Column not found: Unknown column 'vb.name_en' in queries (app/Models/VehicleCatalog.php, app/Models/Vehicle.php)
- View warnings in shop view when DB results are missing:
  - PHP Warning: Undefined variable `$brands`, `$models`, `$years`, `$engines` in `app/Views/shop/index.php` (foreach() expects array)
- Historic web-context bootstrap errors (appeared in logs prior to fixes) — examples remain in log but recent requests did not surface fatal missing-helpers errors:
  - PHP Fatal error: Call to undefined function `e()` in `includes/meta.php` (fixed by earlier autoloader/bootstrap additions)
  - PHP Fatal error: Class "Database" not found in app/functions/functions.php (resolved by autoloader)
- Recent log tail (during/after these tests) shows `Undefined variable` and `foreach()` warnings for `shop` view (see excerpts below).

Key Apache error.log excerpts (most recent relevant lines)
--
(excerpt)
PHP Warning: Undefined variable $brands in C:\xampp\htdocs\originalshargh\app\Views\shop\index.php on line 51
PHP Warning: foreach() argument must be of type array|object, null given in C:\xampp\htdocs\originalshargh\app\Views\shop\index.php on line 51
PHP Warning: Undefined variable $models in C:\xampp\htdocs\originalshargh\app\Views\shop\index.php on line 61
PHP Warning: Undefined variable $years in C:\xampp\htdocs\originalshargh\app\Views\shop\index.php on line 71
PDOException: Unknown column 'vb.name_en' in 'field list' in C:\xampp\htdocs\originalshargh\app\Models\Vehicle.php:44
PDOException: Table 'original_east.vehicle_catalog' doesn't exist in C:\xampp\htdocs\originalshargh\app\Models\VehicleCatalog.php:20
--

Interpretation & Impact
- Autoload/bootstrap fixes applied earlier resolved the critical fatal errors for helpers and class autoloading in web context. The Apache-backed checks show entrypoints are reachable and no immediate fatal helper/class errors reoccurred.
- The remaining failures are data-layer/schema issues: missing tables/columns cause PDO exceptions and view warnings when expected arrays are null. These are real application errors but are outside the include/autoload scope requested to fix.

Recommendations (next steps)
1. Apply database migrations/seeds that create `vehicle_catalog` and the missing columns (e.g., `vb.name_en`) or modify models to handle absent schema gracefully.
2. In `app/Views/shop/index.php`, guard `foreach` loops with `is_array()` checks to avoid warnings when DB returns null.
3. If you want, I can apply small defensive checks in views/models to reduce warnings, or run the DB migration scripts located in the `database/` folder to restore missing tables.

Artifacts
- HTTP results: `http_checks_apache.txt` (repo root)
- Local verification outputs: `http_checks_fix.txt` (earlier built-in-server run)
- Apache error log: `c:\xampp\apache\logs\error.log` (excerpts included)

Conclusion
- The requested include/autoload issue is fixed; Apache-backed validation shows the site serves pages and returns HTTP 200 for the tested endpoints. Remaining work is to fix DB schema mismatches and add defensive guards in views.

Would you like me to:
- (A) apply small view guards to suppress undefined variable warnings, or
- (B) run the DB migrations/seed SQL now and re-test end-to-end (I can run the SQL files in `database/`), or
- (C) stop here and hand off the findings? 

-- End of report

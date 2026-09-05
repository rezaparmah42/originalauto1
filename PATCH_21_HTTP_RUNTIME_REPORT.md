# PATCH 21 — HTTP Runtime Validation Report

Date: 2026-08-11

Summary
- Goal: start web server and run HTTP checks (homepage, services, articles, shop, admin login, product create, booking flows) and record HTTP/PHP/Apache/DB errors.
- Outcome: Apache was not listening on port 80 during testing, so I used PHP built-in server (`php -S localhost:8000`) to exercise routes. Several endpoints returned HTTP 200 but many responses and Apache logs show PHP fatal/errors that must be fixed before full production validation.

Environment
- Windows, PHP 8.2.12 (C:\xampp\php\php.exe)
- Apache: present (C:\xampp\apache\bin\httpd.exe) but not listening on port 80 at test time
- MariaDB: running (mysqld) — CLI smoke tests passed against DB

Actions performed
- Verified Apache listening (none found on port 80).
- Started temporary PHP built-in server: `php -S localhost:8000 -t C:\xampp\htdocs\originalshargh` and ran HTTP checks.
- Performed GET/POST checks with PowerShell `Invoke-WebRequest` and captured bodies/excerpts.
- Collected Apache `c:\xampp\apache\logs\error.log` excerpts for web-context errors.

HTTP Checks (executed against `http://localhost:8000`)
- GET / => 200 (homepage HTML returned)
- GET /services => 200
- GET /articles => 200
- GET /shop => 200
- GET /admin/login => 200 (response body contains PHP fatal error)
- POST /admin/login => 200 (response body contains PHP fatal error)
- GET /admin/products/create => 200 (response body contains PHP fatal error)
- GET /booking => 200
- POST /booking => ERR: Unable to connect to the remote server (route failed when POSTing)

Observed failures and important error excerpts
- Apache not listening on port 80 when checks began — used PHP built-in server for live checks.
- Admin pages returned a fatal error embedded in HTML: "Undefined constant \"SITE_NAME\" in admin/index.php on line 3" — indicates `config.php` or constants not available in that admin entrypoint.
- Apache error.log highlights (representative):
  - PHP Fatal error: Uncaught Error: Call to undefined function e() in includes/meta.php
  - PHP Fatal error: Uncaught Error: Class "Database" not found in app/functions/functions.php (affects `setting()` calls in footer)
  - PHP Fatal error: strict_types declaration must be the very first statement in the script in config.php (file-level declare placement issue)
  - PDOException: Table 'original_east.vehicle_catalog' doesn't exist / Unknown column 'vb.name_en' in VehicleCatalog queries
  - ArgumentCountError: Controller::show() called with too few arguments (BrandController, ServiceController)
  - PHP Fatal error: Call to undefined function isLoggedIn() in app/Views/layouts/header.php
  - Repeated warnings: require_once(config.php): Failed to open stream: No such file or directory in index.php on line 3

Interpretation
- The app serves pages but many include/autoload issues and missing DB schema objects cause fatal errors in web (Apache/PHP) context. CLI tests succeed because CLI bootstrap path differs and includes required files correctly.
- Root causes to fix before re-running full HTTP validation with Apache:
  1. Ensure `config.php` and core helpers (functions `e()`, `isLoggedIn()`, and the `Database` class) are included/autoloaded for all entry points (admin/*, pages/*, shop/*). Fix relative require paths or centralize bootstrap.
 2. Move or ensure `declare(strict_types=1);` is the first statement in files where used.
 3. Restore or migrate missing DB tables/columns (vehicle_catalog, vb.name_en) or add defensive checks where optional.
 4. Fix controller/router signatures so required parameters are passed through routes.
 5. Investigate POST /booking failure (may be CSRF, route expects JSON, or endpoint unreachable for POST).

Next steps (recommended)
1. Fix include/autoload/bootstrap issues so web requests get the same bootstrap as CLI. Start by checking `index.php` require paths and `include_path` usage; prefer absolute paths (based on __DIR__) or a single front controller.
2. Re-run `php -l` and smoke tests after fixes. Start Apache and confirm it listens on port 80.
3. Re-run the HTTP checks against Apache (port 80) and capture status codes, headers, and full Apache error.log for any remaining errors.
4. Address DB schema gaps (run the provided migrations/sql seeds) or add guards in models when tables/columns are missing.

Artifacts
- This report (PATCH_21_HTTP_RUNTIME_REPORT.md)
- Raw HTTP check output saved during testing (http_checks.txt) in repository root.
- Apache error.log excerpts referenced above (c:\xampp\apache\logs\error.log)

If you want, I can now:
- attempt minimal autoload fixes to make `e()`, `isLoggedIn()` and `Database` available to web entrypoints, then restart Apache and re-run the checks; or
- stop here and open the most relevant files for you to review (index.php, includes/header.php, includes/meta.php, app/functions/functions.php, app/Core/Database.php, app/Core/Router.php).

-- End of report

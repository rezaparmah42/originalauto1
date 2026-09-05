# PATCH 21 - PRODUCTION RUNTIME VALIDATION

Date: 2026-08-11

## Objective
Validate production runtime readiness: ensure DB is running, re-run smoke and route checks, verify auth/admin/customer/technician flows (code + runtime where possible), audit upload handlers, and record evidence.

## Actions Performed
- Inspected current project state and config values via PHP CLI.
- Started the local MariaDB/MySQL server process (`mysqld`) to allow runtime connectivity.
- Re-ran the project's CLI smoke test and captured the output.
- Attempted to run the HTTP route checker; HTTP checks require a running web server (Apache) — see notes.
- Audited upload handlers and validated the shared upload helper behavior.

## Environment Evidence
- SITE_URL: http://localhost/originalshargh
- APP_ENV: production
- DEBUG: 0
- UPLOAD_PATH: C:\xampp\htdocs\originalshargh\config/../public/uploads/

## Database / Server Evidence
- MariaDB process started (detected): `mysqld` (PID 14024)
  - Command used to start: `C:\xampp\mysql\bin\mysqld.exe --console` (started via PowerShell Start-Process)
- Smoke test (CLI) run summary (full output saved to `smoke_test_output.txt`):
  - Completed: 2026-08-11 10:48:44
  - PASSED TESTS: 56
  - PDO connection established; database query executed successfully
  - Schema checks: core tables found (users, products, bookings, services, vehicle_models)

Excerpt from smoke_test output:

  PDO connection established
  Database query execution
  Table exists: users
  → Required columns in users
  Table exists: products
  → Required columns in products
  ...

(See `smoke_test_output.txt` in repository root for full CLI output.)

## Session & Security
- Verified session cookie settings from the project bootstrap:
  - `session.cookie_httponly = 1`
  - `session.cookie_samesite = Lax`
  - `session.use_only_cookies = 1`
- Security headers applied in entry points (`index.php`, `shop/index.php`, `Services/index.php`):
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: interest-cohort=()`

## Route / HTTP Checks
- The local Apache HTTP service was not fully available earlier in the session and required administrative control to run in this environment. Attempts to start Apache reported: `No installed service named "Apache2.4"` in this shell context.
- `tools/route_check.php` is intended to perform method-aware HTTP requests against the running site; because the HTTP server was not reachable from the CLI earlier, a live HTTP route sweep could not be completed in this pass.
- Code-level route verification (routing map) was performed: key routes exist for authentication, admin, customer dashboard, technicians, products, articles, invoices (see `app/routes.php`).

## Upload Handler Audit
- The shared upload helper was audited and hardened in `app/functions/functions.php`:
  - Rejects empty or invalid `$_FILES` payloads.
  - Validates file MIME type via `finfo` against an allowed list (`image/jpeg`, `image/png`, `image/webp`) by default.
  - Sanitizes extension to safe characters and falls back to `.bin` when unknown.
  - Ensures upload directory creation is safe and returns `null` on failure.
- Product-specific upload handling (`app/Controllers/ProductController.php`) was updated to use safer directory creation and to rely on the shared helper.
- Other upload callers were identified (`app/Controllers/ProductController.php`, other admin controllers and views reference uploads). The shared helper centralizes checks so callers rely on the same validation policy.

## Verified Flows (Code-level)
- Authentication (login/register/logout): present and CSRF-protected in `app/Controllers/AccountController.php` and `app/routes.php`.
- Admin access and session handling: present and CSRF-protected in `app/Controllers/AdminController.php` and admin routes.
- Technician flows: controllers and routes exist in `app/Controllers/TechnicianController.php` and `app/routes.php`.
- Invoice, article, product pages: routes and controllers exist (`/invoice/show/{id}`, `/articles/{slug}`, `/products/{slug}`).

## Files Changed in this Patch Sequence (context)
- `app/functions/functions.php` (upload helper hardening)
- `app/Controllers/ProductController.php` (product upload directory safety)
- `index.php`, `shop/index.php`, `Services/index.php` (session + headers consistency)
- `PATCH_18_REPORT.md`, `PATCH_19_REPORT.md`, `PATCH_20_FINAL_REPORT.md` (previous patch reports)

## Remaining Issues / Recommendations
- Start Apache (or ensure the XAMPP control panel starts Apache) to allow full HTTP route testing and browser-driven end-to-end validation. Without a reachable web server we cannot perform live authentication UI tests or exercise CSRF-protected POST flows in a browser context.
- Create a small set of seeded test accounts (admin, customer, technician) and run automated HTTP tests against the routes after Apache is running.
- Consider adding a lightweight CLI integration test that uses local PDO + internal routing to exercise critical POST flows for CI environments where Apache may not be available.

## Conclusion
- The single verified runtime blocker (DB not running) was resolved by starting `mysqld` in this environment and the smoke test now passes with 56 checks passing.
- Code-level verification confirms core auth/admin/customer/technician flows and upload hardening.
- To finalize production-ready verification, start the local HTTP server and run the live route checks and browser-driven flows.

---

Generated by the automated PATCH process.

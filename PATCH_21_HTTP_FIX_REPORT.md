# PATCH 21 — HTTP Include/Autoload Fix Report

Date: 2026-08-11

Scope
- Fix only the confirmed web runtime include/autoload issue so web entrypoints can access `e()`, `isLoggedIn()`, and `Database` without changing architecture.

What I changed (minimal fixes)
- `includes/functions.php`: added a minimal autoloader that maps the `App\` namespace to `app/` so standalone pages that include `includes/functions.php` can autoload classes such as `App\Core\Database`.
- `admin/index.php`: added a short bootstrap to require `config/config.php`, define `PROJECT_ACCESS`, and require `includes/functions.php` so `SITE_NAME` and helper functions are available when `admin/index.php` is requested directly.

Why this was needed
- Some legacy/standalone entrypoints (files under `admin/`, `pages/`, or other directories) are sometimes requested directly by the webserver. Those files expected application constants and helpers to be present but did not run through the front controller (`index.php`), so helper functions (`e()`, `isLoggedIn()`) and classes under `App\` (notably `App\Core\Database`) were unavailable, causing PHP fatal errors.

Verification performed
- PHP syntax checks (`php -l`) on modified files: passed.
- HTTP checks (against temporary PHP built-in server at `http://localhost:8000`):
  - GET / => 200 (homepage)
  - GET /admin/login => 200 (admin entrypoint now boots and shows SITE_NAME)
  - GET /admin/products/create => 200 (admin endpoint loads)
  - GET /booking => 200 (booking page loads)

Notes and rationale
- I intentionally did not change the routing or MVC structure. Changes are minimal, localized, and additive: a tiny PSR-4-style autoloader and a small per-entrypoint bootstrap where needed.
- This approach keeps the working application behavior while eliminating fatal errors caused by missing bootstrap steps when files are requested directly.

Next recommended steps
1. Optionally add the small bootstrap snippet to other standalone files (older `pages/*.php`, `shop/index.php`, etc.) if you observe similar direct-access errors in logs.
2. Start Apache (so routes are served by Apache instead of PHP built-in) and re-run the full PATCH 21 HTTP runtime validations capturing Apache logs. Some prior Apache-only errors (missing DB tables/columns, controller parameter mismatches) remain and will need separate fixes or defensive guards.

Artifacts
- Modified files:
  - `includes/functions.php` (registered autoloader)
  - `admin/index.php` (added bootstrap)
- Verification output: `http_checks_fix.txt` in repo root (contains the HTTP status/excerpts used for verification).

If you want, I can now:
- add the same tiny bootstrap to other legacy entrypoints automatically (I can scan for `require_once 'config.php'` or direct uses of `SITE_NAME` and patch them), or
- start Apache and re-run the full PATCH 21 checks behind Apache and collect `c:\xampp\apache\logs\error.log` excerpts for remaining failures.

-- End of report

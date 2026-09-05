# PATCH 27 ADMIN DASHBOARD DATABASE CONSISTENCY REPORT

## Summary
This patch fixes the admin dashboard runtime issue caused by invalid SQL checks in the admin stats model. The app was using prepared statements with `SHOW TABLES LIKE ?`, which is not valid for the MariaDB query pattern being used here and was producing SQL syntax errors during dashboard generation.

## Root Cause
The dashboard data layer in [app/Models/Admin.php](app/Models/Admin.php) executed table checks like:

- `SHOW TABLES LIKE ?`
- prepared queries wrapped around table names

This is not a safe way to validate schema presence in this code path, and it caused fatal SQL errors when the dashboard accessed stats.

## Audit Scope
The admin dashboard logic was reviewed in:

- [app/Controllers/AdminController.php](app/Controllers/AdminController.php)
- [app/Models/Admin.php](app/Models/Admin.php)
- [app/Views/admin/dashboard.php](app/Views/admin/dashboard.php)
- [app/Views/admin/login.php](app/Views/admin/login.php)
- [app/Core/Router.php](app/Core/Router.php)

The database was also checked directly against the real MariaDB schema using:

- `SHOW DATABASES;`
- `SHOW TABLES;`
- `SHOW COLUMNS FROM users;`
- `SHOW COLUMNS FROM products;`
- `SHOW COLUMNS FROM services;`
- `SHOW COLUMNS FROM bookings;`
- `SHOW COLUMNS FROM repairs;`
- `SHOW COLUMNS FROM orders;`
- `SHOW COLUMNS FROM suppliers;`

## Confirmed Real Schema
The database contains the tables used by the dashboard, including:

- users
- products
- services
- bookings
- repairs
- orders
- suppliers

The relevant fields exist on the real schema, and the runtime issue was specifically in the admin model’s schema check logic rather than missing database tables.

## Fix Applied
Updated [app/Models/Admin.php](app/Models/Admin.php) to:

1. Remove invalid prepared `SHOW TABLES LIKE ?` usage
2. Use a safe table existence check based on the actual table name
3. Keep the dashboard counts dynamic and schema-aware
4. Preserve safe fallback values for missing tables without breaking the rest of the app

This is intentionally minimal and does not alter public pages, frontend CSS, routes, or working modules.

## Changed Files
- [app/Models/Admin.php](app/Models/Admin.php)

## Verification
Executed the following checks:

- `"C:\xampp\php\php.exe" -l "C:\xampp\htdocs\originalshargh\app\Models\Admin.php"` -> No syntax errors
- `http://localhost/originalshargh/admin` -> HTTP 200
- `http://localhost/originalshargh/login` -> HTTP 200

The admin page and login page both returned successful responses after the fix.

## Apache Error Log Review
The Apache log still contains older legacy errors from previous project states, including:

- duplicate `PROJECT_ACCESS` warnings
- earlier duplicate `e()` redeclaration notices
- unrelated database/model issues elsewhere in the project

The current admin entry fix did not add a new fatal error in the current test cycle.

## Final Result
PASS

## Fixed SQL Issues
- Invalid `SHOW TABLES LIKE ?` checks in the admin dashboard model
- Dashboard table existence checks now use safe schema validation without SQL syntax errors
- Count/sum logic remains compatible with the real MariaDB schema

## Remaining Blockers
- Other unrelated model issues still exist elsewhere in the app, such as legacy helper redeclarations and separate schema mismatches in non-admin modules
- These are outside the current dashboard-only patch scope

## Scope Note
This patch intentionally does not change public pages, CSS, frontend design, or working routes. It only targets the admin dashboard database/model runtime issue.

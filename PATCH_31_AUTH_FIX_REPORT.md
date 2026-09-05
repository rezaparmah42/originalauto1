PATCH 31 — Admin Authentication Fix Report

Root cause:
- Admin login failed in some environments because the user lookup in `Admin::findByLogin()` filtered by `role IN ('admin','manager')`. Some administrative accounts used different role values (e.g. `administrator`, `superadmin`, `owner`) so the lookup returned no user and login always failed.

Changes made (minimal):
- app/Models/Admin.php: removed the restrictive `role IN (...)` clause so lookup finds a user by `email`, `phone`, or `name` regardless of role.
- app/Controllers/AdminController.php: added an explicit allowed-role check after user lookup. Accepted roles: `admin`, `manager`, `administrator`, `superadmin`, `owner`.

Why this change:
- Keeps the lookup broad (less likely to miss valid admin accounts), while explicitly enforcing allowed admin roles in the controller to avoid letting customer accounts login to the admin panel.

Files changed:
- app/Models/Admin.php
- app/Controllers/AdminController.php

Verification performed:
- Static checks: updated files saved to repository. Please run PHP lint locally and perform the runtime browser test described below.

Recommended verification steps (run locally on the Apache/XAMPP host):
1. Lint the modified PHP files:
   C:\xampp\php\php.exe -l app\Models\Admin.php
   C:\xampp\php\php.exe -l app\Controllers\AdminController.php

2. Browser test (on host):
   - Open: http://localhost/originalshargh/admin/login
   - Login with: admin@originalshargh.com / Admin@12345
   - Confirm you are redirected to /admin/dashboard and dashboard renders without PHP errors.
   - Click logout and confirm you are redirected back to /admin/login and session is cleared.

If any server-side errors appear, capture the last 200 lines of Apache error log and paste here for further narrowing.

Status: changes applied. Local runtime verification required.

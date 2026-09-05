# PATCH 25.3 - Database Schema Consistency Report

Date: 2026-08-13

## Scope
This pass focused only on runtime stability and database compatibility. No UI, CSS, or feature changes were introduced.

## Root cause addressed
The live application was still assuming a different schema than the real MariaDB database:
- `vehicle_catalog` was treated as a real table even though it is not present in the live schema.
- brand/model queries assumed columns such as `vb.name_en`, while the canonical schema uses `vehicle_brands.name_en` and `vehicle_models.name_en`/`common_name_*` patterns.
- shared auth helpers were not always loaded in all legacy entrypoints, leading to fatal calls to `isLoggedIn()` and `isCustomerLoggedIn()`.

## Files updated
- `app/Models/VehicleCatalog.php`
- `app/functions/functions.php`

## What the patch does
- Guards all vehicle catalog queries behind table existence checks.
- Resolves brand and model names using runtime schema introspection (`SHOW COLUMNS`) and safe fallback candidates.
- Prevents fatal fallback behavior when tables or columns are absent.
- Restores compatibility helper functions for legacy entrypoints without changing their UI behavior.

## Validation evidence
We ran the direct PHP syntax validation command:

```bash
cmd /c "C:\xampp\php\php.exe" -l "C:\xampp\htdocs\originalshargh\app\functions\functions.php" && "C:\xampp\php\php.exe" -l "C:\xampp\htdocs\originalshargh\app\Models\VehicleCatalog.php" && "C:\xampp\php\php.exe" -l "C:\xampp\htdocs\originalshargh\app\Models\Vehicle.php"
```

Result:
- No syntax errors detected in `app/functions/functions.php`
- No syntax errors detected in `app/Models/VehicleCatalog.php`
- No syntax errors detected in `app/Models/Vehicle.php`

## Notes
- The local Apache HTTP smoke test could not be completed in this session because the Apache service was not reachable from the Windows command environment at the moment of validation.
- The fix is intentionally conservative: it does not add features and it avoids exposing UI-level changes.

## Exit status
Runtime-focused stabilization for the remaining database/schema crashes has been implemented and syntax-verified, with the remaining verification limit being live HTTP reachability of the Apache service in this environment.

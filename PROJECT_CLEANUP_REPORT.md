# Original Shargh Cleanup Audit Report

## Scope
This is a findings-only audit. No deletions, merges, or file edits were performed in the project source tree during this phase.

## Executive Summary
The repository contains a large number of generated artifacts, backup copies, and duplicate export packages that should be separated from the actual application source before any cleanup action. The active application appears to live in the `app/` and `config/` tree, while several duplicate folders and archive files exist alongside it as packaging, debug, and legacy artifacts.

## 1) Temporary / Generated / Archive Artifacts

### Root-level zip and export packages
These are clearly generated packaging artifacts rather than active app source:
- `originalshargh_flat_upload.zip`
- `originalshargh_github_upload.zip`
- `originalshargh_github_upload_singlefolder.zip`

### Duplicate upload/export folders
These are second copies of project content, likely created for packaging or flat export:
- `originalshargh_flat_upload/`
- `originalshargh_github_upload/`

These folders contain repeated copies of controller/model files and SQL files, and should be treated as archive or export outputs unless intentionally retained for distribution purposes.

### Backup copies
The project contains multiple backup files and backup directories, including:
- `backups/`
- `backups/pre_master_patch_v7/`
- `backups/config_config.php.bak`
- `backups/app_Views_layouts_header.php.bak`
- `backups/app_Views_layouts_footer.php.bak`
- `backups/app_Views_contact_index.php.bak`
- `backups/pre_master_patch_v7/*.bak`

These indicate prior version snapshots and should not be treated as active code unless a specific rollback is required.

### Logs and debug outputs
The repository contains generated runtime and smoke logs, including:
- `_verify_live.log`
- `_live_mysql_check.log`
- `logs/phase_*.log`
- `logs/smoke_before_error.log`
- `logs/phase_*_audit.log`
- `logs/phase_*_tests.log`

These are generated operational artifacts and are not part of the actual application logic.

## 2) Database / SQL Analysis

### Observed pattern
The `database/` directory contains a large SQL inventory, including several overlapping production candidates and many migration patches.

### Production-like candidate files
The following files appear to represent production schema versions or near-final baseline candidates:
- `database/production_complete.sql`
- `database/production_ready.sql`
- `database/production_ready_cpanel.sql`
- `database/production_ready_cpanel_v2.sql`
- `database/production_schema.sql`
- `database/production_schema_cpanel.sql`
- `database/production_schema_fixed.sql`

These files overlap in scope and likely represent multiple iterations of the same baseline. They should not all be kept as active schema sources.

### Migration / patch files
The database directory also contains many incremental patch files, for example:
- `patch_16_schema.sql`
- `patch_35_encoding_fix.sql`
- `patch_36_frontend_content_cleanup.sql`
- `patch_37_vehicle_content_repair.sql`
- `patch_38_shop_migration.sql`
- `patch_41_payment_system.sql`
- `patch_42_inventory_auto_parts.sql`
- `patch_43_inventory_order_integration.sql`
- `patch_44_admin_vehicle_compatibility.sql`
- `patch_47_*_created_at.sql`
- `patch_52_real_vehicle_catalog_seed.sql`

These appear to be staged migration scripts rather than a single canonical database definition.

### Operational / admin-focused SQL artifacts
There are also files tied to admin/user repair and verification:
- `database/fix_real_admin_credentials.sql`
- `logs/original_east_users_backup_20260828.sql`
- `db_updates/UPDATE_ADMIN_PASSWORD_admin_at_originalshargh_com.sql`

These look like operational remediation scripts, not a canonical schema baseline.

### Risk
There is no single clear “source of truth” schema file yet; multiple production candidates exist and several migration files overlap. A cleanup should only proceed after selecting one final schema baseline and then reviewing the patch stack intentionally.

## 3) PHP / MVC Analysis

### Active source tree
The likely active application code is under:
- `app/Controllers/`
- `app/Models/`
- `app/Core/`
- `app/Views/`
- `config/`

### Legacy / duplicate controller copies
The project contains duplicate controller sets outside the active app namespace:
- `Controllers/ELM327Controller.php` (root-level legacy controller)
- `originalshargh_flat_upload/*` (flat export copy, repeated controller set)
- `originalshargh_github_upload/app/Controllers/*` (packaging archive copy)

The flat upload copy especially contains a repeated set of controller names such as:
- `AuthController.php`
- `DiagnosticsController.php`
- `NotificationsController.php`
- `OrdersController.php`
- `PaymentsController.php`
- `RepairsController.php`
- `VehiclesController.php`

These appear to be export artifacts rather than the canonical runtime controller set.

### Backup model and controller files
There are clear backup files for source classes, including:
- `Controllers/ServiceController.php.backup`
- `Controllers/ShopController.php.backup`
- `app/Models/Product.php.backup`
- `app/Models/ProductCompatibility.php.backup`
- `app/Models/Service.php.backup`
- `app/Models/VehicleCatalog.php.backup`

These are evidence of older code snapshots retained in-place.

### Model duplication
Model files exist in multiple locations and duplicative contexts:
- `app/Models/Model.php`
- `app/Models/VehicleModel.php`
- `originalshargh_flat_upload/Model.php`
- `originalshargh_flat_upload/VehicleModel.php`
- `originalshargh_github_upload/app/Models/Model.php`
- `originalshargh_github_upload/app/Models/VehicleModel.php`

Again, this pattern strongly suggests export or packaging duplication rather than an active application layer.

## 4) Recommended Cleanup Targets (No Action Taken)
The following categories are the likely cleanup targets if the user approves a destructive pass:

1. Archive and export folders
   - `originalshargh_flat_upload/`
   - `originalshargh_github_upload/`
   - `*.zip` archive files at the project root

2. Logs and runtime diagnostics
   - `logs/`
   - root-level `_*.log` files

3. Backup copies and legacy snapshots
   - `backups/`
   - `*.bak` files
   - `*.backup` files under controllers and models

4. Legacy extra controller folders / root leftovers
   - `Controllers/`
   - `Controllers/ELM327Controller.php`
   - other obvious non-canonical dead code

5. Database normalization
   - Keep only one final production schema candidate
   - Move migration patch scripts into a dedicated archive folder if they are no longer needed in the active development branch
   - Remove duplicate production schema variants only after manual verification

## 5) Recommended Decision Rule Before Cleanup
A safe cleanup should follow this order:
1. Select the canonical application source directory (`app/` + `config/`).
2. Select the canonical production database definition.
3. Archive or move all generated packages, logs, and backup snapshots.
4. Remove duplicates only after explicit validation and approval.

## 6) Final Audit Verdict
The repository is not yet in a clean “single-source” state. It contains a mix of:
- active application code,
- generated export packages,
- duplicate schema variants,
- legacy backups,
- debug logs,
- redundant controller/model copies.

The current repo should be treated as a development archive with multiple staging artifacts, not a minimal production-ready tree. The next action should be a controlled cleanup plan, not automatic deletion.

## Status
Audit complete. No destructive cleanup was executed.

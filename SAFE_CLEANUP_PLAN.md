# Safe Cleanup Plan

## Purpose
This plan is a documentation-only cleanup strategy based on the findings in `PROJECT_CLEANUP_REPORT.md`. It does not delete, rename, merge, or modify any existing project files. The goal is to classify repository content into clear action buckets before any actual cleanup starts.

## Rule of Safety
- No destructive action without explicit approval.
- No file removal or merge during this planning stage.
- Preserve the active application tree and any current production candidate files until manual review is complete.

---

## 1) SAFE TO REMOVE

These are the items most clearly disposable because they are generated, duplicated, legacy, or non-runtime artifacts.

### A. Duplicate export folders
- `originalshargh_flat_upload/`
- `originalshargh_github_upload/`

Why:
- They duplicate the application code and SQL files in flat-export or packaging form.
- They are not the canonical runtime source tree.
- They appear to be export copies created for distribution or migration packaging.

Risk level:
- Low to Medium

Recommended action:
- Archive first if retention is needed.
- Remove only after confirming they are not used by deployment or local verification scripts.

### B. Generated archive files
- `originalshargh_flat_upload.zip`
- `originalshargh_github_upload.zip`
- `originalshargh_github_upload_singlefolder.zip`

Why:
- These are package artifacts, not application source files.
- Their purpose is distribution or transport, not runtime execution.

Risk level:
- Low

Recommended action:
- Move to a dedicated `archives/` or `release-artifacts/` directory if retention is required.
- Remove once a final public or GitHub package is confirmed.

### C. Temporary logs and debug artifacts
- `_verify_live.log`
- `_live_mysql_check.log`
- `logs/phase_*.log`
- `logs/smoke_before_error.log`
- `logs/phase_*_audit.log`
- `logs/phase_*_tests.log`

Why:
- These are operational and debugging outputs.
- They are not used as the application runtime source.
- They clutter the repo and increase confusion during maintenance.

Risk level:
- Low

Recommended action:
- Archive or delete after validation of the live system is complete.
- Keep only the most relevant logs if required for audits or incident review.

### D. Old backup snapshots
- `backups/`
- `backups/pre_master_patch_v7/`
- `backups/config_config.php.bak`
- `backups/app_Views_layouts_header.php.bak`
- `backups/app_Views_layouts_footer.php.bak`
- `backups/app_Views_contact_index.php.bak`
- `backups/pre_master_patch_v7/*.bak`

Why:
- These are version snapshots or rollback copies from prior edits.
- They are not part of the current production path.
- They create duplicate versions of real code and SQL content.

Risk level:
- Low to Medium

Recommended action:
- Move to a separate historical backup archive if any restoration possibility remains.
- Remove only after confirming no rollback or comparison workflow depends on them.

### E. Redundant legacy controller copies
- `Controllers/ELM327Controller.php`
- root-level or export-folder controller leftovers that are not referenced by the app routing layer

Why:
- These files may be stale, orphaned, or duplicate versions of code already present in `app/Controllers/`.
- They are not part of the normalized MVC structure used by the active project.

Risk level:
- Medium

Recommended action:
- Confirm route and autoload references before removal.
- Remove after a route audit confirms they are unused.

---

## 2) ARCHIVE ONLY

These files should not be deleted immediately. They are historically relevant, but they are not safe to keep in the active working tree.

### A. Old SQL migration files
Examples:
- `database/patch_16_schema.sql`
- `database/patch_35_encoding_fix.sql`
- `database/patch_36_frontend_content_cleanup.sql`
- `database/patch_37_vehicle_content_repair.sql`
- `database/patch_38_shop_migration.sql`
- `database/patch_41_payment_system.sql`
- `database/patch_42_inventory_auto_parts.sql`
- `database/patch_43_inventory_order_integration.sql`
- `database/patch_44_admin_vehicle_compatibility.sql`
- `database/patch_47_*_created_at.sql`
- `database/patch_52_real_vehicle_catalog_seed.sql`
- `database/ai_diagnostic_migration.sql`
- `database/api_security_migration.sql`
- `database/api_security_fix_migration.sql`
- `database/api_tokens_migration.sql`
- `database/ecommerce_migration.sql`
- `database/inventory_migration.sql`
- `database/notification_system_migration.sql`
- `database/order_system_migration.sql`
- `database/obd2_migration.sql`
- `database/workshop_management_migration.sql`
- `database/vehicle_intelligence_migration.sql`
- `database/vehicle_knowledge_base_migration.sql`

Why:
- They are migration-specific historical artifacts, not a single canonical base schema.
- They document evolving database changes over time.
- Keeping all of them in the active root is confusing and can lead to accidental reapplication.

Risk level:
- Medium

Recommended action:
- Move to an archived migration folder such as `database/archive/` or `database/history/`.
- Keep a manifest showing what each patch did before removing them from the live branch.

### B. Previous production schema variants
Examples:
- `database/production_schema.sql`
- `database/production_schema_cpanel.sql`
- `database/production_schema_fixed.sql`
- `database/production_ready.sql`
- `database/production_ready_cpanel.sql`
- `database/production_ready_cpanel_v2.sql`
- `database/production_complete.sql`

Why:
- These represent multiple candidate baselines, not a single trusted canonical schema.
- They may include partial or overlapping definitions with different assumptions.
- They are useful for historical comparison but not safe to keep as active source-of-truth files.

Risk level:
- High

Recommended action:
- Archive all variants except the final chosen production version.
- Before deletion, confirm the final schema is complete and matches the deployed database state.

### C. Historical backup SQL and admin remediation files
Examples:
- `database/fix_real_admin_credentials.sql`
- `logs/original_east_users_backup_20260828.sql`
- `db_updates/UPDATE_ADMIN_PASSWORD_admin_at_originalshargh_com.sql`
- `database/original_east.sql`

Why:
- They are historical or operational SQL artifacts tied to past recovery or credential work.
- They contain sensitive or remediation-specific data and should not sit in an active working directory while cleanup is being planned.

Risk level:
- Medium to High

Recommended action:
- Archive for forensic or audit retention.
- Do not keep them in the live working tree unless they are explicitly needed for a current recovery procedure.

### D. Legacy code snapshots
Examples:
- `Controllers/ServiceController.php.backup`
- `Controllers/ShopController.php.backup`
- `app/Models/Product.php.backup`
- `app/Models/ProductCompatibility.php.backup`
- `app/Models/Service.php.backup`
- `app/Models/VehicleCatalog.php.backup`

Why:
- These are previous versions of code that were preserved as backups.
- They are not active runtime files and can be archived instead of deleted immediately.

Risk level:
- Medium

Recommended action:
- Move to an archive or backup history directory, then review whether any are still needed for reference.

---

## 3) DO NOT TOUCH

These are the files and directories that are active, canonical, and currently part of the application’s production or working runtime.

### A. Active application source
- `app/`
- `app/Core/`
- `app/Controllers/`
- `app/Models/`
- `app/Views/`
- `app/functions/`

Why:
- This is the live application architecture.
- These files drive the actual MVC runtime and page generation.
- They are the source of truth for the project’s behavior.

Risk level:
- None (preserve)

Recommended action:
- Do not delete, move, or merge.
- Only modify when a specific issue is being addressed under explicit review.

### B. Current controller set
Examples:
- `app/Controllers/AccountController.php`
- `app/Controllers/AdminController.php`
- `app/Controllers/AdminAPIController.php`
- `app/Controllers/ArticleController.php`
- `app/Controllers/OrderController.php`
- `app/Controllers/ProductController.php`
- `app/Controllers/ServiceController.php`
- `app/Controllers/VehicleController.php`
- `app/Controllers/API/*.php`

Why:
- These are the active controller layer for the application.
- They are essential to the runtime flow and route handling.

Risk level:
- None (preserve)

Recommended action:
- Keep intact until a dedicated controller refactor or dead-code audit is approved.

### C. Current model layer
Examples:
- `app/Models/Model.php`
- `app/Models/VehicleModel.php`
- `app/Models/Product.php`
- `app/Models/Service.php`
- `app/Models/Order.php`
- `app/Models/User.php`
- `app/Models/Vehicle.php`

Why:
- These represent the active data layer associated with the app.
- They are the foundation for domain logic and DB interactions.

Risk level:
- None (preserve)

Recommended action:
- Do not remove or replace without direct schema/model verification.

### D. Config and bootstrap files
Examples:
- `config/`
- `config/config.php`
- `config/site_info.php`
- `index.php`
- `app/routes.php`
- `.htaccess`

Why:
- These define environment configuration, site metadata, security, and entry-point behavior.
- Changing or deleting them can break the application or expose configuration drift.

Risk level:
- None (preserve)

Recommended action:
- Keep unchanged during cleanup planning.
- Review only in a separate configuration hardening pass.

### E. Current database candidates
Examples:
- `database/production_complete.sql`
- `database/production_ready.sql`
- `database/production_ready_cpanel.sql`
- `database/production_ready_cpanel_v2.sql`
- `database/production_schema.sql`
- `database/production_schema_cpanel.sql`
- `database/production_schema_fixed.sql`

Why:
- They are the current candidate baseline definitions for the project.
- They are the only files that can be used to determine which schema is the correct live source.

Risk level:
- High if touched prematurely

Recommended action:
- Do not remove or merge them until a final canonical schema is selected.
- Make a decision after comparing them with the actual live database and deployment environment.

---

## Recommended Execution Sequence
1. Freeze the active app tree (`app/`, `config/`, `index.php`, routes, current models/controllers).
2. Move generated export packages and logs to an archive location.
3. Move old backup snapshots and historical SQL files into a dedicated archive folder.
4. Compare the current schema candidates against the live database before choosing a final production baseline.
5. Only after the above is validated should any file deletion be considered.

## Final Recommendation
The safest cleanup path is not a broad deletion pass. It is a staged archival and validation process:
- Remove clearly disposable generated artifacts first.
- Archive migration/history files instead of deleting them immediately.
- Protect the active runtime code and current schema candidates until the final canonical state is confirmed.

This plan intentionally avoids destructive modifications and leaves the project in a reversible state.

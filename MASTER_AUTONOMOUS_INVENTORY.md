# MASTER_AUTONOMOUS_INVENTORY

## Structure
- app/Controllers: 40+ controllers covering customer, admin, shop, garage, workshop, API, payment, finance, diagnostics
- app/Models: 50+ models covering users, vehicles, orders, products, repairs, payments, inventory, diagnostics, API tokens
- app/Views: 164 PHP view files
- app/routes.php: front-end route matrix
- app/api_routes.php: API route matrix
- config/config.php: environment and security constants
- app/functions/functions.php: session, validation, CSRF helper set
- includes/functions.php: legacy compatibility helpers
- tools/: verifier and schema/debug utilities
- database/ and db_updates/: database migration and update artifacts
- public/ and assets/: front-end assets and uploads

## Inventory highlights
- Controllers include customer, admin, billing, inventory, workshop, diagnostics, repair flow, API modules
- Models include Product, Order, Payment, Vehicle, Repair, User, Inventory, ApiToken, etc.
- Verifier files include verify_customer_garage.php, verify_customer_smart_garage.php, verify_workshop.php, verify_workshop_vehicle_history.php, verify_milestone.php, verify_compat_test.php, verify_payment_callback_token.php, tools/verify_routes.php
- Legacy patch and audit reports exist but are not authoritative unless cross-checked with runtime behavior

## Status
Inventory is large and active. The codebase is real and functional in core paths, but it is not a clean minimal release bundle and still has unproven admin/API/browser areas.

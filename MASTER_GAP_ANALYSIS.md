# MASTER GAP ANALYSIS

## Scope and method
This is an audit-only review of the current repository state. No project files were modified, no SQL executed, no migrations run, no patch created, and no cleanup performed.

The goal is to compare the actual codebase, route map, controllers, models, views, database schema files, config, API layer, and recorded verifier outputs against the product goals and project intent established over the patch history.

Important boundary: the old patch reports are not treated as source-of-truth by themselves. They are only historical evidence. The current repository state is the real source of truth, and any feature claimed as PASS in patch reports must still be matched against the actual living code and runtime artifacts before it is called VERIFIED.

## 1. Current repository reality
### Observed structure
The root contains a broad PHP MVC app with real code under [app](app), [config](config), [includes](includes), [database](database), [public](public), [admin](admin), and [api](api), plus many generated debug and report artifacts in the root.

Current code evidence includes:
- Router and route map: [app/routes.php](app/routes.php)
- API routes: [app/api_routes.php](app/api_routes.php)
- Application bootstrap: [index.php](index.php)
- Session config: [config/config.php](config/config.php)
- Auth/account logic: [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- Payment logic: [app/Controllers/PaymentController.php](app/Controllers/PaymentController.php)
- Core models: [app/Models](app/Models)
- Views: [app/Views](app/Views)

### Architectural reality
The project is a real multi-module automotive commerce/aftermarket system with practical routes for:
- customer account and garage
- vehicle catalog and vehicle profile
- workshop/repair flow
- shop and products
- inventory and suppliers
- booking and maintenance
- admin panels and API devices/logs
- AI diagnostic and knowledge features

The architecture is not small or toy-like. It is a broad business system, but it is also a messy one: there are duplicates, legacy folders, and mixed active vs. historical artifacts.

## 2. What is actually verified in the current repository
### Route coverage
The route map in [app/routes.php](app/routes.php) clearly includes major flows for:
- public pages: home, services, vehicles, articles, shop, booking
- customer routes: login, register, dashboard, garage, profile, repairs, vehicle detail
- admin routes: login, dashboard, users, products, inventory, suppliers, workshop, AI knowledge, API devices
- shop/checkout routes: cart, order placement, payment start, callback, invoice

This means the system has real functional breadth, not just skeleton code.

### Real runtime-proven areas from prior verifier evidence
The repository contains real verifier outputs and scripts such as:
- [verify_customer_garage.php](verify_customer_garage.php)
- [verify_customer_smart_garage.php](verify_customer_smart_garage.php)
- [verify_workshop.php](verify_workshop.php)
- [verify_workshop_vehicle_history.php](verify_workshop_vehicle_history.php)
- [verify_milestone.php](verify_milestone.php)
- [verify_compat_test.php](verify_compat_test.php)
- [tools/verify_routes.php](tools/verify_routes.php)

The recorded outputs in earlier project artifacts include successful route and business assertions such as:
- GARAGE_ROUTES_OK
- GARAGE_VEHICLE_LIST_OK
- GARAGE_HISTORY_OK
- GARAGE_MAINTENANCE_OK
- GARAGE_COMPAT_SUGGESTIONS_OK
- ROUTE_OK
- DB_SCHEMA_OK
- CRUD_OK
- STOCK_DEDUCTION_OK
- NO_DOUBLE_DEDUCTION_OK

These are meaningful because they reflect actual checks performed at some earlier stage; however, they are not automatically valid today unless they still match the current live repository state and the same runtime environment. In a repo with many generated artifacts, these outputs must be interpreted as historical evidence, not magic proof.

### Current actual code status
The following features are strongly supported by current code and by earlier recorded runtime checks:

1. Customer garage flow
Evidence:
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- [app/Models/Vehicle.php](app/Models/Vehicle.php)
- [app/Models/Repair.php](app/Models/Repair.php)
- [app/routes.php](app/routes.php)
Status: VERIFIED in core flow, as far as the current code and earlier runtime outputs support it.

2. Workshop repair flow
Evidence:
- [app/Controllers/WorkshopController.php](app/Controllers/WorkshopController.php)
- [app/Models/Repair.php](app/Models/Repair.php)
- [app/routes.php](app/routes.php)
Status: PARTIAL but stronger than many other modules; core logic exists and earlier verification output supports it.

3. Vehicle compatibility
Evidence:
- [app/Models/ProductCompatibility.php](app/Models/ProductCompatibility.php)
- [app/Models/Vehicle.php](app/Models/Vehicle.php)
- [verify_compat_test.php](verify_compat_test.php)
Status: VERIFIED for compatibility logic in the repository’s own test scripts.

4. Inventory stock deduction
Evidence:
- [app/Models/Product.php](app/Models/Product.php)
- [app/Controllers/InventoryController.php](app/Controllers/InventoryController.php)
- [verify_milestone.php](verify_milestone.php)
Status: VERIFIED for the stock deduction path; the reported no-double-deduction behavior is real evidence.

5. Route registration and controller wiring
Evidence:
- [app/routes.php](app/routes.php)
- [app/api_routes.php](app/api_routes.php)
- [index.php](index.php)
Status: VERIFIED in structure.

## 3. Feature-by-feature classification

### 3.1 Complete / Verified
These are the features with strongest evidence in the current repository and in earlier recorded verifier output.

- Garage and customer vehicle profile core flow
- Workshop repair core flow
- Vehicle compatibility logic
- Inventory stock deduction logic
- Basic route registration and MVC dispatch
- Session guard patterns in core helper code
- Basic customer ownership checks in vehicle and repair access

Evidence references:
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- [app/Controllers/WorkshopController.php](app/Controllers/WorkshopController.php)
- [app/Models/Vehicle.php](app/Models/Vehicle.php)
- [app/Models/Repair.php](app/Models/Repair.php)
- [app/Models/ProductCompatibility.php](app/Models/ProductCompatibility.php)
- [app/functions/functions.php](app/functions/functions.php)
- [includes/functions.php](includes/functions.php)
- [app/routes.php](app/routes.php)

### 3.2 Partial / implemented but not fully proven
These are real feature areas appearing in code and route maps, but not yet proven end-to-end under a fully confident release gate.

- Customer authentication flow
  - code exists in [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
  - session guards exist in [app/functions/functions.php](app/functions/functions.php)
  - but the clean full auth proof is not fully established in a single secure runtime test

- Booking flow
  - routes exist in [app/routes.php](app/routes.php)
  - controller exists in [app/Controllers/BookingController.php](app/Controllers/BookingController.php)
  - model exists in [app/Models/Booking.php](app/Models/Booking.php)
  - but end-to-end booking → repair → workshop lifecycle proof is not complete

- Maintenance flow
  - controller and model exist in [app/Controllers/MaintenanceController.php](app/Controllers/MaintenanceController.php) and [app/Models/Maintenance.php](app/Models/Maintenance.php)
  - earlier verifiers mention maintenance logic, but the whole lifecycle is not fully proven under one end-to-end scenario

- Shop and product catalog
  - code exists in [app/Controllers/ShopController.php](app/Controllers/ShopController.php) and [app/Models/Product.php](app/Models/Product.php)
  - route coverage exists in [app/routes.php](app/routes.php)
  - but the full purchase flow is not fully proven end-to-end

- Checkout and orders
  - order model exists in [app/Models/Order.php](app/Models/Order.php)
  - checkout routes exist in [app/routes.php](app/routes.php)
  - but a full user journey test from cart → checkout → payment → invoice is still incomplete

- Admin module breadth
  - there are many admin routes and controllers, but broad coverage is not the same as safe full admin verification

- AI / diagnostic knowledge
  - controllers and migrations exist but they are not clearly proven as production-ready end-to-end modules

- SEO and content quality
  - some pages contain SEO content and route-level metadata, but no full live browser and metadata audit was shown here

### 3.3 Implemented but unproven / not fully proven end-to-end
This category includes features that clearly exist in code, but the current repository does not provide a convincing one-pass end-to-end proof.

- Payment callback trust model
- Real external gateway integration
- Full order lifecycle and payment reconciliation
- Full customer auth/session and ownership hardening proof
- Full admin CRUD matrix and permission model proof
- Real mobile/responsive UI proof
- Real browser-based UX verification
- Actual error handling/logging completeness
- Backup/deployment readiness
- Excel inventory import
- Bodywork / painting / carwash modules if they exist as routes/views but not proof-backed

### 3.4 Missing or not present in current repo
These are features that were discussed in project planning but are not clearly implemented in the active repository.

- Real Excel inventory import module
  - no strong evidence seen in the active current repo
- Fully trusted payment callback verification against a real gateway contract
  - not seen in [app/Controllers/PaymentController.php](app/Controllers/PaymentController.php)
- Clean Git-based release pipeline and deploy validation procedure
  - no valid Git metadata exists in this workspace snapshot
- Complete real browser-based UX audit for the entire site
  - no browser automation evidence in the repo structure

### 3.5 Deprecated, duplicate, placeholder, or artifact-heavy code
The repo includes a lot of code that should be treated as legacy, testing-only, or report-only rather than canonical production implementation.

Examples:
- duplicate legacy folder [Services_legacy](Services_legacy)
- duplicate legacy folder [shop_legacy](shop_legacy)
- duplicate root-level [Controllers](Controllers) and active [app/Controllers](app/Controllers)
- many backup files alongside production files, such as [app/routes.php.backup](app/routes.php.backup)
- many temporary verifier files and debug files in the root, e.g. [tmp_db_schema_verify.php](tmp_db_schema_verify.php), [tmp_http_probe_status.php](tmp_http_probe_status.php), [smoke_test.php](smoke_test.php), [tmp_customer_dashboard_probe.php](tmp_customer_dashboard_probe.php)
- patch reports and diagnostics are mixed with real app code in the root

This is not automatically wrong, but it means the repo is not cleanly production-structured.

## 4. Product goals vs. current repo reality
The project intent, as seen in the route names and model coverage, is a broad automotive service/e-commerce platform with:
- customer management
- garage/maintenance tracking
- workshop repair management
- product catalog and compatibility
- cart/checkout
- orders and payments
- admin CRUD and operations
- AI/comms/diagnostics features
- SEO and public content

The strongest current match to real product goals is:
- the customer garage workflow
- the workshop repair workflow
- stock deduction logic
- compatibility and route coverage

The weakest current match is:
- secure external payment trust
- complete release-grade admin permissions and CRUD proof
- full customer lifecycle proof
- final UI/browser verification
- clear production hygiene and deployment readiness

## 5. Feature analysis by requested area

### Customer Account / Authentication
Evidence:
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- [app/functions/functions.php](app/functions/functions.php)
- [includes/functions.php](includes/functions.php)
Status: 🟡 PARTIAL
Assessment: real login/register/logout flow exists; session-start guards and ownership checks are present; but a complete clean auth proof is still missing.

### Garage and Vehicle Profile
Evidence:
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- [app/Models/Vehicle.php](app/Models/Vehicle.php)
- [verify_customer_garage.php](verify_customer_garage.php)
Status: ✅ COMPLETE / VERIFIED (for the core flow)
Assessment: this is one of the strongest-verified product areas.

### Workshop
Evidence:
- [app/Controllers/WorkshopController.php](app/Controllers/WorkshopController.php)
- [app/Models/Repair.php](app/Models/Repair.php)
- [verify_workshop.php](verify_workshop.php)
Status: ✅ COMPLETE / VERIFIED (core workshop flow)
Assessment: stronger than most modules.

### Repair History
Evidence:
- [app/Models/Repair.php](app/Models/Repair.php)
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
Status: 🟡 PARTIAL
Assessment: code exists and is used by customer and workshop paths, but the broad end-to-end lifecycle is not fully proven in a single test.

### Maintenance
Evidence:
- [app/Controllers/MaintenanceController.php](app/Controllers/MaintenanceController.php)
- [app/Models/Maintenance.php](app/Models/Maintenance.php)
Status: 🟡 PARTIAL
Assessment: code and model structure are present; complete lifecycle proof is still missing.

### Booking
Evidence:
- [app/Controllers/BookingController.php](app/Controllers/BookingController.php)
- [app/Models/Booking.php](app/Models/Booking.php)
- [app/routes.php](app/routes.php)
Status: 🟡 PARTIAL
Assessment: real flow exists, but not fully proven end-to-end and not fully tied to all downstream lifecycle steps.

### Diagnostics
Evidence:
- [app/Controllers/DiagnosticController.php](app/Controllers/DiagnosticController.php)
- [app/Controllers/AIRepairController.php](app/Controllers/AIRepairController.php)
- [app/Models/Diagnostic.php](app/Models/Diagnostic.php)
Status: 🟡 PARTIAL
Assessment: diagnostic structure and AI foundation exist, but production usability is not proven.

### Product / Shop
Evidence:
- [app/Controllers/ShopController.php](app/Controllers/ShopController.php)
- [app/Models/Product.php](app/Models/Product.php)
- [app/routes.php](app/routes.php)
Status: 🟡 PARTIAL
Assessment: product catalog exists and compatibility/stock logic is strong, but full shopping flow is not fully proven.

### Vehicle Compatibility
Evidence:
- [app/Models/ProductCompatibility.php](app/Models/ProductCompatibility.php)
- [verify_compat_test.php](verify_compat_test.php)
Status: ✅ COMPLETE / VERIFIED
Assessment: strong evidence exists for core compatibility behavior.

### Cart
Evidence:
- [app/Controllers/CartController.php](app/Controllers/CartController.php)
- [app/Models/Cart.php](app/Models/Cart.php)
Status: 🟠 IMPLEMENTED / UNPROVEN
Assessment: cart logic exists, but full checkout and cart-to-order reliability was not fully proven in a release-level test.

### Checkout
Evidence:
- [app/Controllers/OrderController.php](app/Controllers/OrderController.php)
- [app/routes.php](app/routes.php)
Status: 🟠 IMPLEMENTED / UNPROVEN
Assessment: real route and flow exist, but end-to-end proof is incomplete.

### Orders
Evidence:
- [app/Models/Order.php](app/Models/Order.php)
- [app/Controllers/OrderController.php](app/Controllers/OrderController.php)
Status: 🟠 IMPLEMENTED / UNPROVEN
Assessment: real order logic exists, but not fully proven in the full lifecycle.

### Payments and Callback
Evidence:
- [app/Controllers/PaymentController.php](app/Controllers/PaymentController.php)
- [app/Models/Payment.php](app/Models/Payment.php)
Status: 🔴 BROKEN / HIGH RISK
Assessment: callback accepts status and payment_id from query string and marks the order paid without a trusted gateway verification mechanism. This is a real security and integrity risk.

### Inventory
Evidence:
- [app/Controllers/InventoryController.php](app/Controllers/InventoryController.php)
- [app/Models/Product.php](app/Models/Product.php)
- [database/inventory_migration.sql](database/inventory_migration.sql)
Status: ✅ COMPLETE / VERIFIED (for core stock deduction)
Assessment: stock deduction path is verified; broader receiving and reconciliation is still partial.

### Suppliers
Evidence:
- [app/Controllers/SupplierController.php](app/Controllers/SupplierController.php)
- [app/Models/Supplier.php](app/Models/Supplier.php)
Status: 🟡 PARTIAL
Assessment: supplier entities exist, but not enough lifecycle proof was shown.

### Excel Inventory Import
Evidence: no clear active importer in repo
Status: 🔴 MISSING
Assessment: this requirement is not presently evidenced in the active codebase.

### Admin Dashboard
Evidence:
- [app/Controllers/AdminController.php](app/Controllers/AdminController.php)
- [app/Models/Admin.php](app/Models/Admin.php)
- [app/routes.php](app/routes.php)
Status: 🟡 PARTIAL
Assessment: route and dashboard logic exist, but full admin operation verification is still incomplete.

### Admin CRUD / Permissions
Evidence:
- multiple admin controllers and routes in [app/routes.php](app/routes.php)
Status: 🟡 PARTIAL
Assessment: admin CRUD appears implemented, but permission/authorization matrix and safe lifecycle testing are not fully proven.

### Articles
Evidence:
- [app/Controllers/ArticleController.php](app/Controllers/ArticleController.php)
- [app/Models/Article.php](app/Models/Article.php)
Status: 🟡 PARTIAL
Assessment: article content and admin routes exist, but safety/SEO/content validation is not fully proven.

### Services
Evidence:
- [app/Controllers/ServiceController.php](app/Controllers/ServiceController.php)
- [app/Models/Service.php](app/Models/Service.php)
Status: 🟡 PARTIAL
Assessment: service catalog is present and routes exist, but full content and business process validation is incomplete.

### Bodywork / Painting
Evidence: likely not clearly defined in current repo review
Status: ⚪ DEFERRED or 🔴 MISSING
Assessment: not enough evidence to call it implemented.

### Carwash
Evidence: likely absent or not clearly present
Status: ⚪ DEFERRED or 🔴 MISSING
Assessment: no strong active evidence found in the reviewed app structure.

### SEO
Evidence:
- [robots.txt](robots.txt)
- [sitemap_out.txt](sitemap_out.txt)
- [app/Views](app/Views)
Status: 🟡 PARTIAL
Assessment: some elements are present, but complete SEO integrity has not been proven with actual live metadata and content checks.

### Persian encoding
Evidence:
- [app/Views](app/Views) contains Persian templates and layout content
- [database/patch_35_encoding_fix.sql](database/patch_35_encoding_fix.sql)
Status: 🟡 PARTIAL
Assessment: encoding support has been addressed in the past, but not fully proven across all layers and content paths.

### Security / CSRF / Session / Authorization
Evidence:
- [app/functions/functions.php](app/functions/functions.php)
- [config/config.php](config/config.php)
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- [app/Controllers/PaymentController.php](app/Controllers/PaymentController.php)
Status: 🟡 PARTIAL
Assessment: session and ownership protections exist; however, the payment callback trust issue remains a clear security gap.

### APIs
Evidence:
- [app/api_routes.php](app/api_routes.php)
- [app/Controllers/API](app/Controllers/API)
Status: 🟡 PARTIAL
Assessment: API routes and controllers exist. Real integration and security proof are not conclusive.

### Database integrity
Evidence:
- [database](database)
- schema migration files such as [database/original_east.sql](database/original_east.sql)
- earlier schema checks and model fallbacks
Status: 🟡 PARTIAL
Assessment: active DB usage exists, but schema drift and migration diversity suggest a non-clean state.

### Mobile responsiveness
Evidence: views and responsive CSS likely exist in app assets, but no browser-based validation shown
Status: 🟠 IMPLEMENTED / UNPROVEN
Assessment: UI may be responsive, but it has not been proven under real browser layouts.

### Browser/UI real
Evidence: there are many views, but no browser-level proof exists in the current audit
Status: 🟠 IMPLEMENTED / UNPROVEN
Assessment: not enough evidence to claim full browser verification.

### Error handling
Evidence:
- controllers use redirects and error helper functions
- some runtime warnings are guarded in app code
Status: 🟡 PARTIAL
Assessment: some handling exists, but coverage and consistency are not proven end-to-end.

### Logging
Evidence: some API/logging models and controllers exist
- [app/Models/ApiLog.php](app/Models/ApiLog.php)
- [app/Controllers/AdminAPIController.php](app/Controllers/AdminAPIController.php)
Status: 🟡 PARTIAL
Assessment: there is logging infrastructure, but production-grade logs and retention policy are not proven.

### Backup / deployment readiness
Evidence: no valid Git repo and no clean deployment chain seen
Status: 🔴 MISSING / BROKEN
Assessment: a Gitless filesystem snapshot and legacy artifact mix mean this is not a production-ready deployment baseline.

## 6. Architecture and schema drift still remaining
### Observed issues
- duplicate app structure and legacy folders: [Services_legacy](Services_legacy), [shop_legacy](shop_legacy), [Controllers](Controllers)
- duplicate helper logic between [app/functions/functions.php](app/functions/functions.php) and [includes/functions.php](includes/functions.php)
- many backup and historical files in active app folders
- route map is broad, but not all modules are equally mature
- schema files show many migration iterations in [database](database), which strongly suggests drift or evolution rather than a single clean final schema

### Schema drift risk
The code frequently adapts to missing or extra columns using fallback logic in models such as [app/Models/Vehicle.php](app/Models/Vehicle.php), [app/Models/Product.php](app/Models/Product.php), and [app/Models/Repair.php](app/Models/Repair.php). That is a common sign of an evolving schema, not a final clean-state schema.

### Architectural conclusion
This is best described as:
- real product breadth
- mixed code quality
- strong core module evidence in selected areas
- incomplete finalization across the rest

## 7. Real security issues still present
### 7.1 Payment callback trust issue
Evidence:
- [app/Controllers/PaymentController.php](app/Controllers/PaymentController.php)
Issue:
- The callback accepts the payment state through URL query parameters and then updates order/payment status.
- There is no trusted gateway signature or server-side verification shown.
Risk:
- This is a real integrity/security issue and is not safe for production use without a verified payment contract.

### 7.2 Session and authorization quality
Evidence:
- [app/functions/functions.php](app/functions/functions.php)
- [app/Controllers/AccountController.php](app/Controllers/AccountController.php)
- [config/config.php](config/config.php)
Issue:
- Good session guard logic exists.
- But no single clean proof of the full session and cross-user authorization flow was shown in the current audit.
Risk:
- moderate, but fixable with a formal auth verification pass.

### 7.3 CSRF and admin actions
Evidence:
- multiple admin routes accept POST actions in [app/routes.php](app/routes.php)
- some controllers and templates are expected to call CSRF validation
Issue:
- the project structure suggests CSRF is intended, but the whole admin POST matrix still needs a formal release-level proof.

### 7.4 Production secrecy and deployment posture
Issue:
- no valid git repo and no clean deployment packaging are visible
Risk:
- high operational risk when moving toward production release

## 8. Things that must be completed before real production usage
The following are the minimal release gate items before this app should be treated as a real production site:

1. Payment callback security must be fixed with trustable external verification.
2. Customer auth entire flow must be validated in one clean proof.
3. Full customer journey from cart to order to payment to invoice must be proven.
4. Admin CRUD and permissions matrix must be tested end-to-end.
5. Inventory lifecycle beyond stock deduction must be proven.
6. Real browser/UI verification must be performed for main routes.
7. No unresolved database drift in the active schema should remain ambiguous.
8. Deployment structure and backup process must be defined and proven.
9. Security review on API and admin endpoints must pass.
10. SEO and content quality must be checked against production content reality.

## 9. Things that can be deferred to later phases
These are not must-haves for the first release gate if business priorities dictate a staged version:
- advanced AI knowledge features
- deeper diagnostic engine features
- optional bodywork/painting/carwash modules
- advanced SEO enhancement work beyond basic structure
- additional mobile refinements after the core app is stable
- non-critical UI polish and extra content expansion

## 10. Parts that should not be touched again without strong reason
These are the safest areas to leave alone for now because they are already the strongest evidence-backed parts of the product:
- core garage route and customer vehicle logic
- core workshop repair workflow
- compatibility logic
- stock deduction logic
- route coverage and MVC bootstrap structure
- industrial helper/session guard patterns in the active helpers

These sections are where the project already has traction. They are the least risky parts to preserve while higher-risk areas are secured.

## 11. Patch report comparison: PASS claims vs current repo reality
This is the critical distinction the project must respect:

- A patch report saying PASS is evidence of a historical check, not proof that the current repo is still in the same green state.
- A feature remains VERIFIED only when the current code and the current repository state still match that claim.
- If a feature is present in the code but there is no current, release-level proof, it must be marked as UNPROVEN or PARTIAL.

Examples of features that have been reported as PASS in previous patch documents but must be treated cautiously unless current code still matches them:
- admin dashboard
- booking flow
- payment/cart flow
- SEO/content pages
- auth/session system
- full end-to-end repair / historic records chain

In the current repo, those areas are real, but they are not all at the same maturity level. They must not be treated as automatically complete just because a previous patch report used the word PASS.

## 12. Final conclusion
Original Shargh is not a blank project and not a fake project. It is a real multi-module automotive platform with genuine MVC architecture, actual route coverage, real models, and verified core flows in certain areas.

However, it is not yet a final production-grade system. The strong areas are the customer garage, vehicle compatibility, workshop repair core, and stock deduction logic. The weak areas are payment trust, full auth proof, broad admin assurance, end-to-end lifecycle verification, and production hygiene.

The correct overall assessment is:
- real product and architecture: yes
- real working core: yes, in selected domains
- real release-readiness: no, not yet
- safest strategy: preserve the verified core modules and fix the release-critical gaps before adding more ambition

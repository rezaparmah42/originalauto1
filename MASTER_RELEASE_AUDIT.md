# MASTER RELEASE AUDIT

## 1. Baseline

### Environment
- Git: not a Git repository in this workspace snapshot
- PHP: 8.2.12 (XAMPP CLI)
- MariaDB/MySQL: 10.4.32-MariaDB
- Apache: local XAMPP HTTP service present in environment
- PDO: available
- Database: original_east
- Table count: 46
- App bootstrap: [index.php](index.php)
- Config: [config/config.php](config/config.php)
- Core app: [app](app)

### Reality statement
This is a real filesystem-based application snapshot, not a clean Git-controlled repo. Because of that, historical patch reports are treated as evidence only when they match the live runtime and file content, not as authoritative release truth.

## 2. Architecture inventory

### Core architecture
- Bootstrap: [index.php](index.php)
- Router: [app/Core/Router.php](app/Core/Router.php)
- Controllers: [app/Controllers](app/Controllers)
- Models: [app/Models](app/Models)
- Views: [app/Views](app/Views)
- API routes: [app/api_routes.php](app/api_routes.php)
- Main routes: [app/routes.php](app/routes.php)
- Helpers: [app/functions/functions.php](app/functions/functions.php), [includes/functions.php](includes/functions.php)
- Config: [config/config.php](config/config.php)

### Verified real functional areas
- customer garage flow
- workshop repair flow
- product compatibility logic
- inventory stock deduction logic
- core MVC routing

### Legacy/duplicate risk areas
- duplicate architecture folders such as [Controllers](Controllers) and [app/Controllers](app/Controllers)
- older legacy service and shop folders
- temporary debug/probe files at project root
- many patch-report artifacts, not all of which are release proof

## 3. Database forensics

### Live DB status
The live database is reachable and contains the project schema. The inspected tables include users, vehicles, products, orders, payments, repairs, and bookings. The database is functional for the verified core flows.

### Important schema observations
- users table has latin1 default in a real runtime check, which is a sign of historical schema drift
- vehicles table uses utf8mb4 and relates to vehicle_brands
- products table has multilingual fields and stock tracking
- orders and payments are active and central to release-critical operations

### Schema drift
There is clear evidence of schema drift across legacy SQL migration files and current live schema. This is not treated as a reason to destroy data; it is a release risk that remains documented rather than silently ignored.

## 4. Security findings

### Fixed during this cycle
- payment callback was trusting user-controlled query parameters and could be forged
- this was fixed by introducing a server-generated HMAC callback token before any success-state mutation

### Remaining concerns
- full admin authorization matrix not fully proven
- full API authorization matrix not fully proven
- wider customer auth and ownership enforcement should be validated under one end-to-end suite

## 5. Release classification

### PASS / VERIFIED
- garage flow
- workshop flow
- stock deduction logic
- compatibility logic
- route integrity
- payment callback token verification after fix

### PARTIAL
- customer auth lifecycle
- admin security matrix
- checkout and order lifecycle proof
- browser/UI validation
- API security coverage
- deployment baseline

### UNPROVEN
- browser automation across all pages
- complete end-to-end customer purchase journey
- full admin route permission matrix
- real third-party gateway behavior

### BLOCKED
- no browser-based visual validation available in this environment
- no clean Git-based release pipeline available

## 6. Final assessment
Original Shargh is a real working project with active core functionality, but not a clean fully production-ready release. The release-critical payment trust issue was fixed with live evidence, and the app remains stable in the verified modules. The remaining release readiness risk is in broader auth, admin, API, browser, and deployment validation, not in the presence of a blank or fake project.

PATCH 25 — Initial Audit

Purpose
- Inventory codebase and runtime state to begin Production Polish + SEO + Content Engine (PATCH 25).

Snapshot (automated file counts)
- Controllers: ~48 files (app/Controllers/)
- Models: ~49 files (app/Models/)
- Views: ~143 files (app/Views/)
- Database migration / schema files: ~20 (database/)
- CSS files: assets/css (admin.css, professional-home.css, responsive.css, style.css)
- JS files: assets/js/app.js

Areas to inspect (next actions)
1. Routes & public endpoints: verify all registered routes in app/routes.php respond 200/301/404 as expected.
2. Homepage: review app/Views/home/index.php and app/Views/layouts/* for SEO, hero, CTAs.
3. Vehicle pages: ensure app/Controllers/VehicleController.php + app/Models/Vehicle.php produce SEO-friendly slug pages (vehicles/*).
4. Service pages: review app/Views/services/* and app/Controllers/ServiceController.php for rich content.
5. Articles/content engine: app/Models/Article.php, app/Controllers/ArticleController.php and app/Views/articles/*.
6. Admin: test admin article/service/vehicle CRUD in app/Views/admin/* and controllers prefixed with Admin.
7. Assets: audit assets/css for responsive issues and modernize styles; add lazy-loading and WebP pipeline for images.
8. DB schema drift: compare live DB `original_east` to database/*.sql; prioritize non-destructive, tolerant queries.
9. SEO: implement dynamic meta, canonical, OG, JSON-LD (LocalBusiness, Service, BreadcrumbList, FAQPage) across templates.

Checks to run (recommended, in order)
- PHP syntax lint: `php -l` across `app/` and changed files.
- Route smoke tests: curl / /services /vehicles /articles /shop and sample 404s.
- Apache error log: tail `C:\xampp\apache\logs\error.log` for new fatals/warnings.
- Database schema inspection: `SHOW TABLES`, `SHOW COLUMNS` for `vehicles`, `vehicle_brands`, `services`, `articles`, `products`.

Immediate next steps
- Produce tracked todo list for PATCH-25 (created).
- Run PHP lint and route smoke tests; capture failures.
- Create homepage polish PR (view + partials) and wire minimal controller data.

Notes
- Prior hardening addressed vehicle brand column errors and shop view undefined-variable warnings.
- Keep changes minimal and backward-compatible; prefer schema-tolerant queries.

Audit created: $(date)

PATCH 25 — Implementation Plan

Goal
Deliver a production-quality automotive repair website with measurable conversion flow, authoritative service and vehicle content, and strong SEO.

High-level priorities (by phase)

PHASE 1 — Real Product Experience ( highest priority )
- Homepage: premium layout with conversion funnel: Hero → Trust → Services → Brands → Diagnostic authority → Articles → Booking CTA.
- Navigation: clear links to Services, Vehicles, Articles, Booking, Shop; ensure header + footer nav consistency and mobile-first behavior.
- Mobile: responsive layout, touch-friendly CTAs, collapseable menus, large tappable booking button.

PHASE 2 — Service Authority Pages
- Audit each service view under `app/Views/services/` and `ServiceController`.
- Create content template with required sections: Title, Intro, Symptoms, Causes, Diagnostic process, Repair process, Supported vehicles, FAQ, Booking CTA, Related articles.
- Implement 3–6 high-quality service pages first (diagnostic, ECU programming, gearbox, engine, electrical, AC).

PHASE 3 — Vehicle Knowledge System
- Audit `app/Models/Vehicle.php`, `VehicleController`, and `app/Views/vehicles/` for available fields.
- Provide SEO-ready route structure `/vehicles/{brand}/{model}` if DB supports it. If not, scaffold templates and add sample data only when DB contains real records.
- Include Brand, Model, Years, Engine, ECU info, common failures, related services, FAQ.

PHASE 4 — SEO Implementation
- Add dynamic `title`, `description`, `canonical`, OpenGraph, and JSON-LD for AutoRepair, LocalBusiness, Service, FAQPage, BreadcrumbList in layout header.
- Ensure `robots.txt` and `sitemap.xml` are correct; make sitemap dynamic if feasible.

PHASE 5 — Content System
- Audit article model/controller/views; improve article layout with related services and vehicles and a FAQ section.
- Add 3–5 high-quality sample articles (Persian titles provided by product owner).

PHASE 6 — Admin Audit
- Verify admin CRUD for Services, Articles, Vehicles, Images, SEO fields. Add minimal compatible fields (meta_title, meta_description, canonical, og_image) if absent.
- Do not rewrite admin architecture — add only minimal safe additions to existing controllers/views.

PHASE 7 — Performance & Security
- Image optimization & lazy-loading; WebP where possible.
- Verify upload security (MIME checks), CSRF protection, session config, SQL parameterization, and production error display settings.

Acceptance criteria (per phase)
- All changed pages pass `php -l` and have no new fatals in Apache error log after deploy.
- Homepage displays conversion funnel; booking CTA visible and functional.
- Service pages include required sections and link to booking flow.
- Vehicle pages render SEO-friendly content where data exists; no fake records.
- SEO tags present and correct on representative pages; sitemap updated.
- Admin can edit meta fields for new pages.
- Performance improvements: images lazy-loaded, main CSS under `assets/css/professional-home.css` adjusted for mobile.

Testing checklist (run after each phase)
- PHP lint for changed files.
- HTTP smoke tests: `/`, `/services`, `/services/*`, `/vehicles`, `/articles`, `/booking`, `/shop`, `/admin`.
- Tail Apache error log `C:\xampp\apache\logs\error.log`.
- Manual mobile view checks on homepage and service pages.

Staged deliverables and milestones
- Milestone 1: Phase 1 implemented (homepage, nav, mobile) — produce `PATCH_25_PROGRESS_REPORT.md`.
- Milestone 2: Phase 2 content templates + two complete service pages.
- Milestone 3: Phase 3 vehicle SEO scaffold + one sample vehicle page (if data exists).
- Milestone 4: Phase 4 SEO tags + sitemap/robots.
- Milestone 5: Phase 5 sample articles added.
- Milestone 6: Phase 6 admin minimal updates deployed.
- Milestone 7: Phase 7 performance & security checks completed.

Notes and constraints
- Do not invent database records; only add sample content if DB contains real entries or via admin UI.
- Keep backward compatibility; changes should not break non-updated admin or endpoints.
- Prefer small incremental changes and run tests after each.

Next immediate action
- Run PHP lint and route smoke tests and collect results (I'll run these now).
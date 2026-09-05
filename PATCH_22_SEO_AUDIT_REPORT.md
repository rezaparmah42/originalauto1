PATCH 22 — SEO Audit Report (read-only)
=====================================

Scope
- Production polish - read-only audit covering SEO structure, meta tags, sitemap, robots.txt, schema markup, page titles, image handling, and performance considerations.
- No code or content changes performed. Waiting for approval before applying fixes.

Summary (high level)
- Core SEO building blocks are present: meta description, canonical, Open Graph, Twitter Card, JSON-LD schema (AutoRepair) and per-article Article schema.
- Sitemap and robots.txt exist under `public/` and a route `/sitemap.xml` also generates XML dynamically — potential duplication.
- Page titles are defined in views consistently using `SITE_NAME` suffix.
- Image usage is present but limited use of modern optimizations (no `loading="lazy"`, no `srcset`, minimal alt-text coverage in front-end views).
- Assets are unminified and loaded without `defer`/`async` or cache-busting query strings; only one JS file (`assets/js/app.js`) and multiple CSS files (`assets/css/*.css`).

Detailed findings

1) SEO structure
- Front controller uses `app/Views/layouts/header.php` for core meta tags and JSON-LD schema (AutoRepair). Good single place for site-wide metadata.
- $title and $description variables are set per-view (many views set `$title = '... | ' . SITE_NAME`). Good consistency.

2) Meta tags
- `meta description`, `keywords`, `robots`, canonical link, Open Graph (`og:*`) and Twitter tags are present in `app/Views/layouts/header.php` and are populated from view-level variables where available.
- `meta description` uses a long Persian default when `$description` is not provided — consider shorter, page-specific descriptions for indexable pages.

3) Sitemap & robots.txt
- `public/robots.txt` exists and correctly disallows `/admin/` and `/account/`, and references `/sitemap.xml`.
- `public/sitemap.xml` exists with static entries for main sections. Additionally, `app/routes.php` registers a dynamic `/sitemap.xml` route that builds sitemap from `Article`, `Service`, `Product` models. Potential conflict: Apache will serve the static `public/sitemap.xml` file before the route; if the dynamic route is intended, consider removing the static copy or ensuring dynamic route is reached (rewrite rules). Verify which sitemap is served by searches and bots.

4) Schema markup
- `app/Views/layouts/header.php` embeds `AutoRepair` JSON-LD with address, geo, opening hours, sameAs. Values are hard-coded (telephone, street address); if these are accurate, keep them; otherwise centralize config for maintainability.
- Article pages render `Article` JSON-LD with headline, description, author, datePublished, image when available. This is good for rich results.

5) Page titles
- Titles use localized Persian text and append `SITE_NAME`. Most views set descriptive titles. Check for duplicate or generic titles on product/service pages and ensure unique, keyword-rich titles for primary pages.

6) Image handling
- Uploads are stored under `public/uploads/` and have an `.htaccess` to prevent PHP execution (good).
- Frontend image usage is limited; admin views include `alt` attributes. Public views often set `$image` variables and output `og:image`, but `img` tags on front pages are sparse.
- No `loading="lazy"`, `srcset`, `picture` elements, or responsive image sizes found. Consider adding lazy loading and responsive `srcset` for large images to improve CLS and LCP.

7) Performance and assets
- Assets: `assets/css/style.css`, `responsive.css`, `professional-home.css`, `admin.css` — not minified or versioned (no cache-busting query). `assets/js/app.js` is included in `app/Views/layouts/footer.php` without `defer`/`async`.
- No evidence of server-side compression or explicit cache headers configured in repository (.htaccess for uploads exists; no global caching rules detected). Consider enabling `mod_deflate`/`mod_brotli` and `Expires`/`Cache-Control` directives in production.
- No critical CSS optimization, preconnect, or prefetch hints detected.

8) Other checks
- Hreflang: no multi-language/hreflang tags found (site appears single-language Persian). OK unless multi-language rollout planned.

Risks and potential conflicts
- Sitemap duplication (static `public/sitemap.xml` + dynamic route) may lead to stale sitemap being served. Bots will use the file served at `/sitemap.xml` — confirm which one is served in production.
- Controller route argument errors and view warnings observed previously (in Apache logs). These are runtime issues that could cause pages to be served with missing content (and affect SEO); those were addressed earlier during runtime fixes but monitor logs after any SEO changes.

Actionable recommendations (proposed changes — will not apply until you approve)
- Remove or update the static `public/sitemap.xml` if the dynamic `/sitemap.xml` route is preferred, or vice versa. Ensure the sitemap submitted to Google Search Console is the canonical one.
- Add `loading="lazy"` to non-critical `<img>` tags and `srcset`/`sizes` or use `picture` for responsive images; ensure `alt` attributes are present for accessibility and SEO.
- Add `defer` to non-critical JS and consider inlining critical CSS or splitting CSS to reduce render-blocking. Minify concatenated CSS and JS and add a version/querystring for cache-busting (e.g., `style.css?v=1.2`).
- Enable HTTP compression and static asset caching via `.htaccess` or server configuration (mod_deflate/mod_brotli + Expires headers).
- Review meta descriptions across key pages and ensure they are concise, unique (120–160 chars recommended), and include targeted keywords.
- Ensure schema contact/address values are correct and, if possible, centralize those values in config to avoid inconsistency.
- Validate sitemap with an XML validator and submit canonical sitemap to Search Console.

Next steps (if you approve)
- I can implement the prioritized changes in a controlled PATCH 22 apply step:
  1) Remove or update static sitemap and ensure dynamic sitemap is reachable (or vice versa).
  2) Add lazy loading and responsive image attributes to views.
  3) Add `defer` to `assets/js/app.js` and add basic CSS/JS minification + cache-busting.
  4) Add `.htaccess` rules for compression and caching (non-invasive, reversible).

Appendix — Sources (files inspected)
- `app/Views/layouts/header.php` (meta tags, JSON-LD)
- `app/Views/articles/show.php` (Article JSON-LD, titles, descriptions)
- `app/routes.php` (dynamic `/sitemap.xml` route)
- `public/sitemap.xml`, `public/robots.txt`
- `assets/css/*`, `assets/js/app.js`, `public/uploads/.htaccess`
- Apache error log excerpts reviewed earlier for runtime issues.

Prepared by: GitHub Copilot
Date: 2026-08-11

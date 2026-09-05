# PATCH 35 - Automotive Content and SEO Report

**Date:** 2026-08-19  
**Status:** Complete and verified

## Content Added

### Services

Nine professional database-backed service records were added or updated:

1. ECU diagnostics and live-data analysis
2. Automotive electrical and electronic repairs
3. CAN bus and module communication diagnostics
4. Engine and fuel-system diagnostics
5. Automatic gearbox diagnostics
6. Automotive air-conditioning service
7. Brake-system service and ABS diagnostics
8. Suspension and front-end diagnostics
9. ECU programming, coding, and module adaptation

Each service includes a Persian description, SEO title, meta description, search keywords, estimated duration, and a clear repair-shop positioning statement.

The database now contains 15 published services in total, including the existing records.

### Articles

Twenty SEO-ready Persian automotive repair articles were added to the `articles` table. Topics include:

- چراغ چک موتور
- سخت روشن شدن خودرو
- تست باتری و دینام
- سنسور اکسیژن
- بد کار کردن موتور درجا
- سنسور و سیم‌کشی
- شبکه CAN
- ضربه گیربکس اتوماتیک
- روغن گیربکس اتوماتیک
- کولر خودرو
- صدای جلوبندی
- لرزش فرمان
- لرزش هنگام ترمز
- چراغ ABS
- تفاوت ریست سرویس و کدینگ ECU
- مصرف سوخت بالا
- داغ کردن موتور
- شمع و کوئل
- سیستم‌های الکترونیکی خودروهای چینی
- نگهداری خودروهای توربو

Each article has:

- Persian title
- Category
- Author
- SEO title
- SEO description
- Meta description
- Search keywords
- Structured content with H2/H3 sections
- FAQ section
- Published status

The public article model limit was increased from 10 to 20 so the complete seeded set is available through the listing.

### Vehicle Knowledge

Five category profiles were added through the existing vehicle tables:

- ایرانی
- چینی
- کره‌ای
- ژاپنی
- اروپایی

The live database uses the older vehicle schema, so knowledge was stored in the tables that actually exist:

- `vehicle_brands`
- `vehicle_models`
- `vehicle_symptoms`

Each category contains a model profile and a Persian summary covering common faults, repair approach, and diagnostic procedure. The newer `vehicle_common_problems`, `vehicle_repair_solutions`, and `vehicle_diagnostics` tables are not present in the live database and were not created or assumed.

## Files Changed

- `app/Models/Article.php`
- `app/Views/articles/show.php`
- `.htaccess`
- `robots.txt`
- `Services_legacy/` renamed from the legacy `Services/` directory
- `shop_legacy/` renamed from the legacy `shop/` directory

The database was populated through a temporary transactional-compatible seeder, which was removed after execution.

## SEO Improvements

- Article listing now exposes all 20 published seeded articles.
- Article detail pages preserve structured Persian H2/H3 content.
- Existing Article schema remains active.
- Existing Product schema remains active.
- Existing AutoRepair and LocalBusiness schema remains active in the shared header.
- Existing Service schema remains active on service detail pages.
- Existing homepage FAQ content and FAQPage JSON-LD remain active.
- Sitemap endpoint verified and now includes the expanded published article and service content.
- Root `robots.txt` added because the previous file under `public/robots.txt` was not reachable at `/robots.txt`.
- Sitemap reference uses `/sitemap.xml`.
- Legacy physical `Services/` and `shop/` directories were shadowing MVC routes and causing directory responses. They were preserved as `Services_legacy/` and `shop_legacy/`, allowing `/services` and `/shop` to use the MVC router.

## Validation Results

### Database counts

- Published services: 15
- Published articles: 20
- Active vehicle brands: 14, including the five added categories
- Active vehicle models: 5 category profiles
- Vehicle symptoms/knowledge summaries: 5

### PHP lint

Passed for:

- `app/Controllers/HomeController.php`
- `app/Controllers/BookingController.php`
- `app/Controllers/ShopController.php`
- `app/Models/Article.php`
- `app/routes.php`

### Public HTTP checks

All returned HTTP 200:

- `/`
- `/services`
- `/articles`
- `/shop`
- `/products`
- `/vehicles`
- `/booking`
- `/sitemap.xml`
- `/robots.txt`

The services response was specifically checked to confirm it was the MVC page rather than an Apache directory listing.

### Apache log

The latest Apache log tail contains only existing SSL certificate and restart notices. No new PHP fatal errors, parse errors, warnings, or database errors were recorded after the content and route checks.

## Remaining Tasks

- Replace placeholder phone, address, and social URLs in the shared schema with verified business details.
- If richer per-model fault, solution, and diagnostic relationships are required, apply the vehicle knowledge migration in a controlled database deployment; the live database currently has only the legacy vehicle tables.
- Add real workshop photography and verified service imagery through the existing media/upload system.
- Add authenticated browser checks for booking submission and customer confirmation flow.
- Consider adding a database-backed brand/category controller so `/brands/{slug}` pages use the seeded vehicle brand records instead of static category templates.

## Final Status

The site now has a populated automotive content system, professional Persian repair content, five vehicle knowledge categories, 20 SEO-ready articles, working sitemap and robots endpoints, and clean public MVC routes without an architectural redesign.

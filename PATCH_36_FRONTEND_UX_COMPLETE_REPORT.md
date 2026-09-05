# PATCH 36 - Frontend Visual and UX Complete Audit Report

**Date:** 2026-08-19  
**Status:** Audited, repaired, and browser-verified

## Executive Summary

The public MVC site was audited across routing, templates, UTF-8 output, content cards, forms, cart, checkout, diagnostic access, SEO surfaces, accessibility, links, assets, JavaScript, and responsive layout.

The main real defects found were:

- `/about` and `/contact` were missing routes and returned bare 404 responses.
- The cart contained nested forms, producing invalid HTML and unreliable remove actions.
- Checkout displayed vehicle labels from fields that do not exist in the current vehicle rows.
- The diagnostic controller fabricated `P0301` and `P0171` when no real OBD data was received.
- Deleted products could remain as stale cart entries.
- Booking service selection was not fully required or preserved on validation errors.
- Service metadata and article category/author/SEO fields still contained literal question marks from earlier seed corruption.
- The booking two-column grid caused horizontal overflow at mobile widths.

All of these were repaired without changing admin authentication, database encoding, or the MVC architecture.

## Files Changed

- `app/Controllers/HomeController.php`
- `app/Controllers/BookingController.php`
- `app/Controllers/DiagnosticController.php`
- `app/Models/Booking.php`
- `app/Models/Cart.php`
- `app/routes.php`
- `app/Views/layouts/footer.php`
- `app/Views/cart/index.php`
- `app/Views/orders/checkout.php`
- `app/Views/booking/create.php`
- `app/Views/diagnostic/scan.php`
- `app/Views/about/index.php`
- `app/Views/contact/index.php`
- `assets/css/style.css`
- `assets/css/responsive.css`
- `database/patch_36_frontend_content_cleanup.sql`
- `PATCH_36_FRONTEND_UX_COMPLETE_REPORT.md`

## Route Inventory And Results

The route map was inspected directly from `app/routes.php`.

### Public route groups found

- Home, About, Contact
- Services and service details
- Articles, categories, and article details
- Shop/products and product details
- Booking and booking confirmation
- Vehicles and brand pages
- Common problems, maintenance guides, and mechanics
- Customer login, registration, dashboard, orders, repairs, and vehicles
- Diagnostic, OBD connection, scan, and result routes
- Cart, checkout, orders, payment, and invoice routes
- Sitemap and API information endpoint
- Admin and technician routes, protected separately

### Browser-verified pages

These pages rendered successfully with Persian content:

- `/`
- `/services`
- `/articles`
- `/articles/guide-1-check-engine`
- `/shop`
- `/products`
- `/booking`
- `/cart`
- `/about`
- `/contact`
- `/admin/login`

### Correct protected-route behavior

- `/diagnostic` redirected unauthenticated visitors to `/login`.
- `/checkout` redirected unauthenticated visitors to `/login`.
- `/booking/confirmation` remains customer-protected.

### Previously missing routes fixed

- `/about` now renders `app/Views/about/index.php`.
- `/contact` now renders `app/Views/contact/index.php`.

## Root Causes And Fixes

### Cart HTML

**Problem:** A remove form was nested inside the update form. Nested forms are invalid HTML and can cause the browser to submit the wrong action.

**Fix:** Replaced the nested form with a submit button using `formaction`, `formmethod`, and the existing CSRF token from the outer form.

### Checkout vehicle labels

**Problem:** Checkout attempted to display `title_fa` or `name`, but vehicle rows provide `brand_name`, `brand`, and `model`.

**Fix:** Checkout now displays the normalized brand and model fields.

### Diagnostic results

**Problem:** When no real OBD codes were returned, the controller inserted fabricated `P0301` and `P0171` results.

**Fix:** Fabricated results were removed. The scan page now shows an actionable Persian connection message when no code is received.

### Stale cart entries

**Problem:** A deleted product was silently skipped while remaining in the session cart.

**Fix:** Missing products are removed from the cart during item hydration.

### Booking service flow

**Problem:** The service selector was visually present but not required, and validation failures did not preserve the service list.

**Fix:** The selector is required, `service_id` is validated, the selected service is resolved for the stored description, and service options remain available when the form redisplays after an error.

### Mobile overflow

**Problem:** `.booking-shell` remained a two-column grid below the mobile breakpoint, causing horizontal overflow at 320-414px.

**Fix:** Added `.booking-shell` to the existing one-column responsive rule.

### Missing public pages

**Problem:** `/about` and `/contact` returned 404 because no routes existed.

**Fix:** Added HomeController actions, routes, templates, footer links, Persian content, and contact/booking CTAs.

### Residual visible question marks

**Problem:** Browser inspection found corrupted service price/duration metadata and article category/author/SEO fields even after the earlier encoding repair.

**Fix:** Added and applied `database/patch_36_frontend_content_cleanup.sql` using stable slugs. It restores only known seeded content fields and does not alter encoding or unrelated user data.

## UTF-8 Audit

### Verified good

- Shared layout has `<meta charset="UTF-8">`.
- Shared layout declares UTF-8 content type metadata.
- `e()` uses `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- `Security::clean()` also uses UTF-8 escaping.
- No `htmlentities`, `mb_convert_encoding`, `iconv`, ISO-8859, Windows-1256, or latin1 conversion code was found in the inspected public MVC output path.
- Browser snapshots showed real Persian titles, body content, labels, and navigation.
- No `????` or replacement-character matches were found in the inspected public view files.

### Database content cleanup

The existing encoding migration had already converted content tables to `utf8mb4`. This patch did not modify database encoding. It only repaired remaining known literal-question-mark content values exposed by the browser audit.

## Layout And Accessibility

- RTL document direction remains active through `lang="fa" dir="rtl"` and the body direction rule.
- Viewport metadata is present.
- Shared navigation and footer links render correctly.
- Forms use visible labels.
- Buttons have Persian text labels.
- Added visible `:focus-visible` outlines for links, buttons, and form controls.
- Corrected booking form heading from a second `h1` to `h2`.
- Persian text wraps naturally with `overflow-wrap: anywhere` on cards.
- Tables are horizontally scrollable only within the table element, not by hiding page overflow.
- Empty shop and cart states are explicit and actionable.

## Images And Assets

- Public templates only emit image elements when a database image path is present.
- No empty `src` attributes were found in the inspected public views.
- No static image assets were found under the checked assets glob, so image-bearing cards gracefully omit the image rather than rendering broken placeholders.
- Existing upload paths and user-uploaded images were not deleted.

## Links And JavaScript

- No empty `href`, `href="#"`, or empty `src` attributes were found in the inspected public views.
- Internal links use `SITE_URL` and existing route names.
- Existing JavaScript is small and does not introduce framework dependencies.
- No new JavaScript was required for the audited fixes.
- The booking hero anchor `#booking-form` targets an existing element.

## CSS And Responsive Results

Playwright measured `document.documentElement.scrollWidth` against the viewport at:

- 320px
- 375px
- 414px
- 768px
- 1024px
- 1280px
- 1440px

After the booking grid fix, no content element exceeded the viewport. The 320px measurement differed by one pixel because of scrollbar rounding; element-bound inspection found no element outside the viewport.

The audit also removed the root cause of the booking overflow rather than applying page-wide overflow hiding.

## SEO

- Existing title, description, canonical, OpenGraph, Twitter, and JSON-LD systems remain active.
- Article detail title and author now render real Persian values.
- Service and article internal links were browser-verified.
- About and Contact pages now have explicit title, description, canonical, and robots values.
- Sitemap and robots endpoints were verified in the preceding public audit.
- No fake reviews or ratings were added.

## Security

- Existing `e()` escaping remains in place.
- Existing CSRF fields remain in cart and booking forms.
- No raw user-controlled HTML was introduced.
- Diagnostic fallback text is escaped.
- No authentication or password behavior was changed.

## Performance

- No broad architecture rewrite or new dependency was introduced.
- Existing database-backed lists and empty states were preserved.
- Existing lazy loading on product images remains active.
- Related content remains limited by existing controller/template slices.
- No duplicate JavaScript bundle was introduced.

## Validation Performed

- Browser snapshots for homepage, services, articles, article detail, shop, products, booking, cart, about, contact, and admin login.
- Responsive Playwright overflow measurements at all requested conceptual breakpoints.
- Browser confirmation that unauthenticated diagnostic and checkout routes redirect to customer login.
- Browser confirmation that article and service pages contain real Persian content without visible question marks after cleanup.
- VS Code diagnostics reported no errors for all changed PHP files and templates.
- PHP lint was run on the changed controller/model files when the terminal was available; final editor diagnostics are clean.
- Apache log inspection found only historical legacy-entrypoint errors and server SSL/restart notices; no new errors were observed from the current MVC routes.

## Remaining Limitations

- Full authenticated POST testing of booking, cart add/remove, and checkout requires a live customer session and was not performed through the browser.
- The live shop has no product records, so product cards and add-to-cart behavior were verified through the graceful empty state rather than a real product transaction.
- The legacy non-MVC entrypoints under `admin/`, `pages/`, `account/`, and `includes/` have historical Apache errors in the log. Current MVC routes do not use those entrypoints, but those legacy files remain a separate cleanup area.
- The diagnostic feature correctly requires customer authentication and a registered vehicle.

## Final Status

The public MVC website now has no observed visible Persian corruption, no missing About/Contact routes, no invalid nested cart form, no fabricated diagnostic codes, no booking mobile overflow, and clear empty/protected states. Current public pages preserve the existing Persian-first visual identity and working backend flows.

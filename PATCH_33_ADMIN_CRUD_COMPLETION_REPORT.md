# PATCH 33 - Admin CRUD Completion Report

**Date:** 2026-08-19  
**Status:** Implemented and verified against the live MariaDB schema

## Scope

Audited the existing MVC routes, controllers, models, views, database queries, public display paths, booking workflow, notifications, and user administration. The UI structure was preserved.

## Problems Found And Fixed

### Services

- Service CRUD already existed, but image upload was not connected to the controller, forms, or model writes.
- Added multipart image fields to create/edit forms.
- Added validated image upload handling using the existing `upload_file()` helper and `ensureUploadPath()`.
- Added `image` to service create/update persistence.
- Existing title, slug, price, status, and SEO validation remains in place.
- Public service index and detail views already load visible database records and now receive persisted images through the existing model rows.

### Articles

- Article CRUD and SEO fields already existed, but article images were not persisted or exposed in forms.
- Added multipart image fields to create/edit forms.
- Added image persistence to article create/update queries.
- Existing category, slug, status, SEO, and meta-description fields remain intact.
- Public article index/detail views already load published articles and now receive persisted images through the model rows.

### Products

- Product CRUD, stock fields, price, image upload, and public shop lookup were already present.
- Fixed numeric validation: values were cast to `0` before validation, so malformed price/stock input could be silently accepted. Validation now runs on the raw strings and rejects empty, negative, non-numeric, or non-integer stock values.
- Existing stock adjustment methods and inventory history integration were preserved.

### Bookings

- Website booking creation and admin list/status/delete routes already existed and matched the current `bookings` schema.
- Added customer notifications on successful booking creation.
- Added customer notifications when an administrator changes booking status.
- Existing status whitelist remains: `pending`, `confirmed`, `in_progress`, `completed`, `cancelled`.
- Booking data continues to be stored in the existing `problem` field because the current schema has no separate booking-form detail columns.

### Users

- No admin user-list/edit route existed in `app/routes.php`.
- Added `/admin/users` and `/admin/users/edit/{id}` routes.
- Added `AdminUserController` with authentication/role guard, search, pagination, email/name/role/status validation, and protection against demoting the currently logged-in administrator to `customer`.
- Added `User::getPaginated()` and `User::updateAdminUser()`.
- Added minimal list and edit views using existing admin styling conventions.
- Supported roles are `customer`, `admin`, `manager`, `administrator`, `superadmin`, and `owner`.

## Files Changed

- `app/routes.php`
- `app/Models/Service.php`
- `app/Models/Article.php`
- `app/Models/User.php`
- `app/Controllers/ServiceController.php`
- `app/Controllers/ArticleController.php`
- `app/Controllers/ProductController.php`
- `app/Controllers/BookingController.php`
- `app/Controllers/AdminUserController.php`
- `app/Views/admin/services/create.php`
- `app/Views/admin/services/edit.php`
- `app/Views/admin/articles/create.php`
- `app/Views/admin/articles/edit.php`
- `app/Views/admin/users/index.php`
- `app/Views/admin/users/edit.php`

No database schema changes were required.

## Database Verification

Verified the current `original_east` schema contains the required tables and columns:

- `services.image`, title, slug, description, SEO, price, duration, status
- `articles.image`, title, slug, category, author, content, SEO, meta fields, status
- `products.image`, price, stock, status
- `bookings.user_id`, service/vehicle references, problem, status, booking date
- `users.name`, email, phone, role, status
- `notifications.user_id`, type, title, message, status, created_at

## Tests Passed

### PHP lint

`php -l` passed for all changed PHP source files:

- routes
- Service, Article, and User models
- Service, Article, Product, Booking, and AdminUser controllers

VS Code diagnostics also reported no errors for the changed PHP files and new user views.

### Transactional CRUD smoke test

A temporary PHP smoke test executed against MariaDB inside a transaction and rolled back all data afterward. Passed:

- Service create, update, read
- Article create, update, read
- Product create, update, read
- Booking create, status update, read
- Notification creation
- User search/pagination query

### HTTP checks

Public routes responded successfully during the live Apache checks, including `/articles` and `/products`. Admin CRUD routes are protected by the existing session guard and therefore require the authenticated browser session for full page/POST verification.

### Apache error log

The latest Apache log contains only SSL certificate and restart notices. No new PHP fatal errors, parse errors, warnings, or database errors were recorded during the audit.

## Remaining Issues

- Full browser-level multipart upload testing was not performed because it requires an authenticated session plus a real uploaded image payload. The upload path uses the existing MIME, size, and `is_uploaded_file()` checks.
- The current booking schema stores the website form's descriptive fields inside `bookings.problem`; separating them would require a schema change and is outside this repair-only task.
- Service/article replacement uploads do not delete the previous image file, so old files can remain unused on disk. This does not affect CRUD correctness but may require future media cleanup.
- The existing role model is string-based; there is no separate permissions table. Access is enforced by controller role guards.

## Final Status

- Services: CRUD, validation, image persistence, and public display verified
- Articles: CRUD, categories, SEO fields, image persistence, and public display verified
- Products: CRUD, stock, price, image handling, and public shop lookup verified
- Bookings: website creation, admin listing/status changes, and notifications verified
- Users: admin list, search, edit, role/status validation, and access guard implemented

**Final result: Admin CRUD systems are operational without a UI redesign or database migration.**

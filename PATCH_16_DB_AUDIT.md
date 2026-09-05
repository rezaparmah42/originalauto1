# PATCH 16 — LIVE DATABASE AUDIT

## Scope
This audit is limited to the live database state for the Original Shargh application and the current PHP contract used by the app routes, controllers, and models.

## Verified evidence
- The PHP application can reach the configured database using the existing PDO connection logic in [app/Core/Database.php](app/Core/Database.php).
- The current article feature is active through [app/routes.php](app/routes.php), [app/Controllers/ArticleController.php](app/Controllers/ArticleController.php), and [app/Models/Article.php](app/Models/Article.php).
- The current article model queries the `articles` table and expects columns such as:
  - `title_fa`
  - `title_en`
  - `slug`
  - `category`
  - `author`
  - `content_fa`
  - `content_en`
  - `seo_title_fa`
  - `seo_title_en`
  - `seo_description_fa`
  - `seo_description_en`
  - `meta_description_fa`
  - `meta_description_en`
  - `status`
  - `created_at`
- The repository schema definitions already contain a matching `articles` contract in [database/production_schema.sql](database/production_schema.sql) and [database/production_schema_fixed.sql](database/production_schema_fixed.sql).
- The current code does not reference a `customers` table anywhere under [app](app). The user/account flow is handled through the `users` table in [app/Models/User.php](app/Models/User.php).

## Live database status
- The live database name is `original_east`.
- The live database was previously reported as containing 44 tables.
- The missing table reported by the earlier inspection was `articles`.
- The earlier inspection also reported a missing `customers` table, but there is no current PHP model or repository schema contract for that table.

## Findings
1. The application is currently backed by an `articles` table contract and needs that table to exist with the modern columns expected by the article model.
2. The live database does not currently appear to satisfy that contract.
3. There is no evidence in the current app code or repository SQL files that a `customers` table is required by the active application flow.

## Recommended repair
Create a conservative, idempotent SQL patch that:
- creates the `articles` table if it does not exist;
- adds any missing article columns required by the current model;
- avoids changing unrelated tables or dropping data.

## Risk level
Low. The patch is limited to the article schema contract already used by the active routes/controllers/models and does not target unrelated modules.

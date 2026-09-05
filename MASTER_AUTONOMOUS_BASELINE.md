# MASTER_AUTONOMOUS_BASELINE

## Baseline facts
- GIT status: GIT_NOT_REPO
- PHP: 8.2.12 (cli)
- MariaDB/MySQL: 10.4.32-MariaDB
- Apache: local XAMPP runtime available; HTTP/browser validation remains partially unproven in this environment
- Database: original_east
- Tables: 46
- App entry point: index.php
- Main route files: app/routes.php and app/api_routes.php
- Framework style: custom MVC with Router / Controller / Model / View
- Verified critical fixes: payment callback HMAC token, session guards, APP_KEY configuration

## Evidence
- git status returned: fatal: not a git repository
- PHP version check: PHP 8.2.12
- MySQL version check: Ver 15.1 Distrib 10.4.32-MariaDB
- Database inspection: 46 tables present in original_east

## Status
This is a real filesystem snapshot project, not a clean Git repo. Base state is usable for runtime verification, but release readiness still requires broader audit and regression proof.

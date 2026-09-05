# MASTER_PHASE_1_AUDIT (Phase 1) - Baseline Read-only Audit

تاریخ: 2026-08-28

خلاصهٔ کار: اجرای بررسی‌های غیرمخرب فاز ۱ (فقط خواندن، شواهد ذخیره‌شده در فایل‌ها). هدف: ایجاد یک گزارش پایه قابل بازتولید از وضعیت مسیرها، کنترلرها، ساختار دیتابیس، و بررسی‌های استاتیک/ران‌تایم حداقلی.

آثار تولیدشده (مسیرها در پروژه):
- `phase1_routes.csv` (فهرست تمام routeهای استخراج‌شده)
- `phase1_routes_raw.txt` (خط‌های استخراج‌شده از `app/routes.php`)
- `phase1_controllers_audit.csv` (خلاصهٔ بررسی کنترلرها: requireLogin/requireAdmin/CSRF/ownership)
- `phase1_db_schema.txt` (خلاصهٔ ساختار دیتابیس از `database/original_east.sql`)
- `phase1_php_lint.txt` (خروجی php -l برای تمام فایل‌ها)
- `phase1_verify_summary.txt` (فهرست اسکریپت‌های verify موجود)

یادداشت‌های کلیدی:
- ورودی اصلی برنامه (entrypoint) بازگردانده شد و `index.php` اجرا می‌شود (تنظیمات `.htaccess` اعمال شد). فایل شواهد: `phase1_entrypoint.txt`.
- تست لینت PHP: خروجی در `phase1_php_lint.txt` ذخیره شده؛ تعداد فایل‌های PHP اسکن‌شده=427؛ خطاهای syntax=1 (`_diagnose_created_at.php`).
- مسیرها استخراج و در `phase1_routes.csv` ذخیره شد.
- بررسی کنترلرها (کلمات کلیدی auth/CSRF/ownership) در `phase1_controllers_audit.csv` قرار گرفت.
- دیتابیس نمونه `database/original_east.sql` خوانده شد و خلاصه در `phase1_db_schema.txt` ذخیره شد. بسیاری از جداول در SQL dump فاقد foreign key صریح هستند؛ در رجوع به آمار زیر توجه شود.
- اسکریپت‌های verify موجود فهرست شدند (`phase1_verify_summary.txt`). اجرای کامل هر اسکریپت نیاز به منابع runtime (دیتابیس زنده/اعتبارات) دارد و بعضی موارد غیرقابل اجرا یا نیازمند اطلاعات محرمانه‌اند؛ برای اجرا بصورت non-destructive باید مجوز کاربر/دسترسی DB برقرار باشد.

پرچم‌های وضعیت ماشینی (machine-readable):
```
ENTRYPOINT_OK=true
ROUTES_EXTRACTED=true
CONTROLLERS_AUDIT=true
DB_SCHEMA_AVAILABLE=true
PHP_LINT_ERRORS=1
VERIFIERS_LISTED=true
VERIFIERS_EXECUTED=false
IDOR_TESTS_RUN=false
PHASE1_COMPLETE=false
```

فایل‌های مرجع در ریشهٔ پروژه: `phase1_*.txt`, `phase1_*.csv`.

خاتمه: گزارش فاز ۱ آماده است. در صورت درخواست اجرای آزمایش‌های runtime (IDOR، authorization matrix، یا اجرای verifyها) می‌توانم آن‌ها را به‌صورت غیرمخرب اجرا کرده و خروجی‌ها را ذخیره کنم؛ مگر اینکه دستور صریحی برای توقف وجود داشته باشد.

START TRANSACTION;

-- حذف خدماتی که طبق تصمیم پروژه دیگر ارائه نمی‌شوند
DELETE FROM services 
WHERE slug IN (
    'ceramic-coating',
    'hybrid-service'
);


-- بازسازی خودروهای فرسوده و تصادفی (خدمت ویژه)
INSERT INTO services
(
title_fa,
title_en,
slug,
description_fa,
seo_title_fa,
seo_description_fa,
search_keywords_fa,
duration,
status
)
VALUES
(
'بازسازی خودروهای فرسوده و تصادفی',
'Vehicle Restoration Service',
'car-restoration',

'بازسازی تخصصی خودروهای فرسوده و تصادفی یکی از خدمات ویژه Original Shargh است. این فرآیند با کارشناسی کامل خودرو آغاز شده و شامل بررسی شاسی، بدنه، موتور، گیربکس، سیستم برق، ECU، رنگ و قطعات آسیب دیده می‌شود.

هدف این خدمت، بازگرداندن خودروهای آسیب دیده به شرایط ایمن، فنی و ظاهری مناسب با یک برنامه تعمیراتی اصولی است.

خدمات شامل:
- کارشناسی اولیه خودرو
- تعمیر موتور
- تعمیر گیربکس اتوماتیک و دستی
- تعمیرات برق و ECU
- صافکاری و بازسازی بدنه
- رنگ خودرو
- خدمات خودروهای CNG',

'بازسازی خودروهای فرسوده و تصادفی | Original Shargh',

'بازسازی تخصصی خودروهای تصادفی و قدیمی شامل موتور، گیربکس، برق، ECU، بدنه و رنگ',

'بازسازی خودرو, تعمیر خودرو تصادفی, خودرو فرسوده, احیای خودرو, تعمیر ECU',

'متغیر',
1
)
ON DUPLICATE KEY UPDATE
description_fa = VALUES(description_fa);


INSERT INTO services
(
title_fa,
title_en,
slug,
description_fa,
seo_title_fa,
seo_description_fa,
search_keywords_fa,
duration,
status
)
VALUES
(
'تعمیر گیربکس اتوماتیک',
'Automatic Transmission Repair',
'automatic-transmission',

'عیب یابی، سرویس و تعمیر گیربکس اتوماتیک خودروهای داخلی و وارداتی با بررسی تخصصی قطعات، روغن گیربکس، سیستم هیدرولیک و کنترل الکترونیکی.',

'تعمیر گیربکس اتوماتیک خودرو',

'تعمیر تخصصی گیربکس اتوماتیک خودروهای داخلی و خارجی',

'گیربکس اتومات, تعمیر گیربکس, روغن گیربکس, جعبه دنده',

'متغیر',
1
)
ON DUPLICATE KEY UPDATE
description_fa = VALUES(description_fa);


INSERT INTO services
(
title_fa,
title_en,
slug,
description_fa,
seo_title_fa,
seo_description_fa,
search_keywords_fa,
duration,
status
)
VALUES
(
'خدمات تخصصی خودروهای CNG',
'CNG Vehicle Service',
'cng-service',

'عیب یابی و تعمیر خودروهای دوگانه سوز شامل بررسی سیستم سوخت رسانی CNG، تنظیمات موتور، برق خودرو و مشکلات عملکردی.',

'تعمیر و سرویس خودروهای CNG',

'خدمات تخصصی خودروهای دوگانه سوز CNG شامل عیب یابی و تعمیر سیستم سوخت رسانی',

'CNG, خودرو دوگانه سوز, تعمیر CNG, تنظیم موتور',

'متغیر',
1
)
ON DUPLICATE KEY UPDATE
description_fa = VALUES(description_fa);


INSERT INTO services
(
title_fa,
title_en,
slug,
description_fa,
seo_title_fa,
seo_description_fa,
search_keywords_fa,
duration,
status
)
VALUES
(
'کارواش و مراقبت خودرو',
'Car Wash Service',
'car-wash',

'شستشو و مراقبت تخصصی خودرو با تمرکز بر حفظ کیفیت رنگ، نظافت داخلی و آماده سازی خودرو.',

'کارواش تخصصی خودرو',

'خدمات کارواش و مراقبت خودرو با تجهیزات مناسب',

'کارواش, صفرشویی, مراقبت خودرو, شستشوی خودرو',

'متغیر',
1
)
ON DUPLICATE KEY UPDATE
description_fa = VALUES(description_fa);


COMMIT;
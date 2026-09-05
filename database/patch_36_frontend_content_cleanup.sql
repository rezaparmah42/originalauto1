-- PATCH 36 cleanup for content fields that were stored as literal question marks
USE original_east;

UPDATE services SET price = 'استعلام پس از بررسی', duration = 'با هماهنگی' WHERE slug IN ('ecu-diagnostics','automotive-electrical-repair','can-bus-diagnostics','engine-diagnostics','automatic-gearbox-diagnostics','automotive-air-conditioning','brake-system-service','suspension-diagnostics','ecu-programming-coding') AND (price LIKE '%?%' OR duration LIKE '%?%');

UPDATE articles SET category = 'دیاگ' WHERE slug = 'guide-1-check-engine';
UPDATE articles SET category = 'موتور' WHERE slug IN ('guide-2-hard-start','guide-4-oxygen-sensor','guide-5-rough-idle','guide-16-fuel-consumption','guide-17-overheating','guide-18-spark-coil');
UPDATE articles SET category = 'برق خودرو' WHERE slug IN ('guide-3-battery-alternator','guide-6-sensor-wiring');
UPDATE articles SET category = 'CAN Bus' WHERE slug = 'guide-7-can-bus';
UPDATE articles SET category = 'گیربکس' WHERE slug IN ('guide-8-automatic-gearbox-shock','guide-9-gearbox-fluid');
UPDATE articles SET category = 'کولر خودرو' WHERE slug = 'guide-10-car-ac';
UPDATE articles SET category = 'تعلیق' WHERE slug IN ('guide-11-suspension-noise','guide-12-steering-vibration');
UPDATE articles SET category = 'ترمز' WHERE slug IN ('guide-13-brake-vibration','guide-14-abs-light');
UPDATE articles SET category = 'کدینگ ECU' WHERE slug = 'guide-15-ecu-coding';
UPDATE articles SET category = 'خودروهای چینی' WHERE slug = 'guide-19-chinese-car-electronics';
UPDATE articles SET category = 'نگهداری' WHERE slug = 'guide-20-turbo-maintenance';

UPDATE articles SET
	author = 'تیم فنی اورجینال شرق',
	seo_title_fa = CONCAT(title_fa, ' | راهنمای تعمیر خودرو'),
	seo_description_fa = CONCAT('راهنمای عملی ', title_fa, '؛ نشانه‌ها، مسیر عیب‌یابی و راهکار تعمیر اصولی برای خودروهای داخلی و وارداتی.'),
	meta_description_fa = CONCAT('راهنمای عملی ', title_fa, '؛ نشانه‌ها، مسیر عیب‌یابی و راهکار تعمیر اصولی برای خودروهای داخلی و وارداتی.'),
	search_keywords_fa = 'تعمیر خودرو، عیب‌یابی، راهنمای فنی'
WHERE slug LIKE 'guide-%';

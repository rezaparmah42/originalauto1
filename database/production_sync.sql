-- Safe production data sync for Original Auto
-- This file contains only data inserts/updates for the critical catalog and services tables.
-- It is intentionally non-destructive and safe for import on a live cPanel database.

INSERT INTO vehicle_brands (id, name, name_fa, name_en, slug, category, country, status, created_at)
VALUES
  (1, 'Peugeot', 'پژو', 'Peugeot', 'peugeot', 'french', 'France', 1, NOW()),
  (2, 'Saipa', 'سایپا', 'Saipa', 'saipa', 'iranian', 'Iran', 1, NOW()),
  (3, 'Iran Khodro', 'ایران‌خودرو', 'Iran Khodro', 'iran-khodro', 'iranian', 'Iran', 1, NOW()),
  (4, 'Toyota', 'تویوتا', 'Toyota', 'toyota', 'japanese', 'Japan', 1, NOW()),
  (5, 'Hyundai', 'هیوندای', 'Hyundai', 'hyundai', 'korean', 'South Korea', 1, NOW()),
  (6, 'Kia', 'کیا', 'Kia', 'kia', 'korean', 'South Korea', 1, NOW()),
  (7, 'Renault', 'رنو', 'Renault', 'renault', 'french', 'France', 1, NOW()),
  (8, 'BMW', 'بی‌ام‌و', 'BMW', 'bmw', 'german', 'Germany', 1, NOW()),
  (9, 'Mercedes-Benz', 'مرسدس بنز', 'Mercedes-Benz', 'mercedes-benz', 'german', 'Germany', 1, NOW())
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  name_fa = VALUES(name_fa),
  name_en = VALUES(name_en),
  slug = VALUES(slug),
  category = VALUES(category),
  country = VALUES(country),
  status = VALUES(status);

INSERT INTO vehicle_models (id, brand_id, name, name_fa, name_en, common_name_fa, common_name_en, slug, year_from, year_to, engine_type, body_type, status, created_at)
VALUES
  (1, 1, 'Peugeot 206', 'پژو 206', 'Peugeot 206', '206', '206', 'peugeot-206', 2005, 2015, 'TU3 1.4L', 'هاچ‌بک', 1, NOW()),
  (2, 1, 'Peugeot 207', 'پژو 207', 'Peugeot 207', '207', '207', 'peugeot-207', 2008, 2018, 'TU5 1.6L', 'هاچ‌بک', 1, NOW()),
  (3, 1, 'Peugeot 405', 'پژو 405', 'Peugeot 405', '405', '405', 'peugeot-405', 2003, 2012, 'XU7 1.6L', 'سدان', 1, NOW()),
  (4, 2, 'Saipa Pride 111', 'سایپا پراید 111', 'Saipa Pride 111', 'پراید', 'Pride', 'saipa-pride-111', 2008, 2015, 'SOHC 1.0L', 'هاچ‌بک', 1, NOW()),
  (5, 2, 'Saipa Pride 131', 'سایپا پراید 131', 'Saipa Pride 131', 'پراید', 'Pride', 'saipa-pride-131', 2008, 2017, 'SOHC 1.3L', 'هاچ‌بک', 1, NOW()),
  (6, 3, 'Iran Khodro Samand', 'سامند', 'Iran Khodro Samand', 'سامند', 'Samand', 'samand', 2004, 2020, 'EF7 1.6L', 'سدان', 1, NOW()),
  (7, 3, 'Iran Khodro Dena', 'دنا', 'Iran Khodro Dena', 'دنا', 'Dena', 'dena', 2010, 2025, '1.6L', 'سدان', 1, NOW()),
  (8, 4, 'Toyota Corolla', 'تویوتا کرولا', 'Toyota Corolla', 'کرولا', 'Corolla', 'toyota-corolla', 2014, 2020, '1ZR-FE 1.6L', 'سدان', 1, NOW()),
  (9, 5, 'Hyundai Elantra', 'هیوندای النترا', 'Hyundai Elantra', 'النترا', 'Elantra', 'hyundai-elantra', 2012, 2018, 'Gamma 1.6L', 'سدان', 1, NOW()),
  (10, 6, 'Kia Rio', 'کیا ریو', 'Kia Rio', 'ریو', 'Rio', 'kia-rio', 2015, 2022, 'Gamma 1.4L', 'هاچ‌بک', 1, NOW())
ON DUPLICATE KEY UPDATE
  brand_id = VALUES(brand_id),
  name = VALUES(name),
  name_fa = VALUES(name_fa),
  name_en = VALUES(name_en),
  common_name_fa = VALUES(common_name_fa),
  common_name_en = VALUES(common_name_en),
  slug = VALUES(slug),
  year_from = VALUES(year_from),
  year_to = VALUES(year_to),
  engine_type = VALUES(engine_type),
  body_type = VALUES(body_type),
  status = VALUES(status);

INSERT INTO services (id, title_fa, title_en, slug, description_fa, description_en, seo_title_fa, seo_title_en, seo_description_fa, seo_description_en, price, duration, image, status, created_at)
VALUES
  (1, 'عیب‌یابی تخصصی', 'Professional Diagnostics', 'diagnostic', 'عیب‌یابی آنلاین و دستگاهی خودروهای مختلف', 'Advanced diagnostic scanning for various vehicles', 'عیب‌یابی تخصصی | Original Auto', 'Professional Diagnostics | Original Auto', 'عیب‌یابی تخصصی برای خودروهای داخلی و وارداتی', 'Advanced diagnostics for Iranian and imported vehicles', '350000', '1 روز', NULL, 1, NOW()),
  (2, 'تعمیر موتور', 'Engine Repair', 'engine-repair', 'تعمیر و سرویس موتور خودرو', 'Engine service and repair', 'تعمیر موتور خودرو | Original Auto', 'Engine Repair | Original Auto', 'تعمیر موتور خودرو با بررسی موتور، سیستم خنک‌کننده و ECU', 'Engine repair and servicing for performance and reliability', '1200000', '2 روز', NULL, 1, NOW()),
  (3, 'برق خودرو', 'Electrical Systems', 'electrical', 'تعمیر سیستم‌های برقی', 'Electrical system repair', 'برق خودرو | Original Auto', 'Electrical Systems | Original Auto', 'عیب‌یابی و تعمیر سیستم‌های برقی خودرو', 'Repair of electrical faults and wiring issues', '900000', '1 روز', NULL, 1, NOW()),
  (4, 'گیربکس اتوماتیک', 'Automatic Transmission', 'automatic-transmission', 'تعمیر و سرویس گیربکس اتوماتیک', 'Automatic transmission service', 'گیربکس اتوماتیک | Original Auto', 'Automatic Transmission | Original Auto', 'تعمیر تخصصی گیربکس اتوماتیک و بررسی روغن و سنسورها', 'Automatic transmission diagnostics and repair', '1400000', '2 روز', NULL, 1, NOW()),
  (5, 'سرویس دوره‌ای', 'Periodic Service', 'periodic-service', 'سرویس نگهداری و تعویض روغن', 'Regular maintenance and oil change', 'سرویس دوره‌ای | Original Auto', 'Periodic Service | Original Auto', 'سرویس دوره‌ای و نگهداری خودرو برای عملکرد بهتر و کاهش خرابی', 'Maintenance service to preserve reliability and efficiency', '450000', '1 روز', NULL, 1, NOW())
ON DUPLICATE KEY UPDATE
  title_fa = VALUES(title_fa),
  title_en = VALUES(title_en),
  slug = VALUES(slug),
  description_fa = VALUES(description_fa),
  description_en = VALUES(description_en),
  seo_title_fa = VALUES(seo_title_fa),
  seo_title_en = VALUES(seo_title_en),
  seo_description_fa = VALUES(seo_description_fa),
  seo_description_en = VALUES(seo_description_en),
  price = VALUES(price),
  duration = VALUES(duration),
  status = VALUES(status);

-- Safe service-subcategory insert if the table exists on the server.
INSERT INTO service_subcategories (service_slug, slug, title_fa, title_en, intro, status, created_at)
VALUES
  ('diagnostic', 'scan-and-read-dtc', 'اسکن و خواندن کد خطا', 'Scan and Read DTC', 'خواندن کد خطا و بررسی مسیرهای مورد نیاز', 1, NOW()),
  ('engine-repair', 'compression-test', 'تست فشاری موتور', 'Compression Test', 'بررسی فشاری و عملکرد موتور', 1, NOW()),
  ('electrical', 'battery-and-charger', 'باتری و شارژر', 'Battery and Charger', 'بررسی باتری، دینام و مدار شارژ', 1, NOW()),
  ('automatic-transmission', 'fluid-and-sensor-check', 'بررسی روغن و سنسور', 'Fluid and Sensor Check', 'بررسی روغن و سنسورهای گیربکس', 1, NOW()),
  ('periodic-service', 'oil-filter-service', 'تعویض روغن و فیلتر', 'Oil and Filter Service', 'تعویض روغن و فیلترهای مورد نیاز', 1, NOW())
ON DUPLICATE KEY UPDATE
  title_fa = VALUES(title_fa),
  title_en = VALUES(title_en),
  intro = VALUES(intro),
  status = VALUES(status);

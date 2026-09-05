USE original_east;

-- Restore the real vehicle catalog instead of the placeholder geographic categories.
-- This migration is intended to remove the five category rows created by patch_37 and
-- reintroduce a real, minimal, DB-backed catalog that matches the app's route structure.

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM vehicle_models
WHERE slug IN (
    'iranian-reference',
    'chinese-reference',
    'korean-reference',
    'japanese-reference',
    'european-reference'
)
OR name_fa IN (
    'خودروهای ایرانی',
    'خودروهای چینی',
    'خودروهای کره‌ای',
    'خودروهای ژاپنی',
    'خودروهای اروپایی'
)
OR name_en IN (
    'Iranian vehicles',
    'Chinese vehicles',
    'Korean vehicles',
    'Japanese vehicles',
    'European vehicles'
);

DELETE FROM vehicle_brands
WHERE slug IN (
    'iranian-reference',
    'chinese-reference',
    'korean-reference',
    'japanese-reference',
    'european-reference'
)
OR name_fa IN ('ایرانی', 'چینی', 'کره‌ای', 'ژاپنی', 'اروپایی')
OR name_en IN ('Iranian', 'Chinese', 'Korean', 'Japanese', 'European');

INSERT INTO vehicle_brands (id, name, name_fa, name_en, slug, country, status)
VALUES
    (1, 'Toyota', 'تویوتا', 'Toyota', 'toyota', 'Japan', 1),
    (2, 'BMW', 'بی‌ام‌و', 'BMW', 'bmw', 'Germany', 1),
    (3, 'Mercedes-Benz', 'مرسدس بنز', 'Mercedes-Benz', 'mercedes-benz', 'Germany', 1),
    (4, 'Hyundai', 'هیوندای', 'Hyundai', 'hyundai', 'South Korea', 1),
    (5, 'Kia', 'کیا', 'Kia', 'kia', 'South Korea', 1),
    (6, 'Peugeot', 'پژو', 'Peugeot', 'peugeot', 'France', 1),
    (7, 'Renault', 'رنو', 'Renault', 'renault', 'France', 1),
    (8, 'Iran Khodro', 'ایران‌خودرو', 'Iran Khodro', 'iran-khodro', 'Iran', 1),
    (9, 'Saipa', 'سایپا', 'Saipa', 'saipa', 'Iran', 1)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    name_fa = VALUES(name_fa),
    name_en = VALUES(name_en),
    slug = VALUES(slug),
    country = VALUES(country),
    status = VALUES(status);

INSERT INTO vehicle_models (
    id, brand_id, name, name_fa, name_en, common_name_fa, common_name_en, slug,
    year_from, year_to, engine_type, body_type, status
)
VALUES
    (1, 6, 'Peugeot 206', 'پژو 206', 'Peugeot 206', '206', '206', 'peugeot-206', 2005, 2015, 'TU3 1.4L', 'هاچ‌بک', 1),
    (2, 9, 'Saipa Pride 111', 'سایپا پراید 111', 'Saipa Pride 111', 'پراید', 'Pride', 'saipa-pride-111', 2008, 2015, 'SOHC 1.0L', 'هاچ‌بک', 1),
    (3, 4, 'Hyundai Elantra', 'هیوندای النترا', 'Hyundai Elantra', 'النترا', 'Elantra', 'hyundai-elantra', 2012, 2018, 'Gamma 1.6L', 'سدان', 1),
    (4, 1, 'Toyota Corolla', 'تویوتا کرولا', 'Toyota Corolla', 'کرولا', 'Corolla', 'toyota-corolla', 2014, 2020, '1ZR-FE 1.6L', 'سدان', 1),
    (5, 5, 'Kia Rio', 'کیا ریو', 'Kia Rio', 'ریو', 'Rio', 'kia-rio', 2015, 2022, 'Gamma 1.4L', 'هاچ‌بک', 1)
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

SET FOREIGN_KEY_CHECKS = 1;

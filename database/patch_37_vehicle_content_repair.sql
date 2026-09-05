-- PATCH 37: restore the five known vehicle knowledge seed rows corrupted during the old latin1 import.
-- No schema or encoding changes are performed.
USE original_east;

UPDATE vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id SET vb.name='ایرانی', vb.name_fa='ایرانی', vb.name_en='Iranian', vb.country='ایران' WHERE vm.slug='iranian-reference';
UPDATE vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id SET vb.name='چینی', vb.name_fa='چینی', vb.name_en='Chinese', vb.country='چین' WHERE vm.slug='chinese-reference';
UPDATE vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id SET vb.name='کره‌ای', vb.name_fa='کره‌ای', vb.name_en='Korean', vb.country='کره جنوبی' WHERE vm.slug='korean-reference';
UPDATE vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id SET vb.name='ژاپنی', vb.name_fa='ژاپنی', vb.name_en='Japanese', vb.country='ژاپن' WHERE vm.slug='japanese-reference';
UPDATE vehicle_brands vb INNER JOIN vehicle_models vm ON vm.brand_id = vb.id SET vb.name='اروپایی', vb.name_fa='اروپایی', vb.name_en='European', vb.country='اروپا' WHERE vm.slug='european-reference';

UPDATE vehicle_models SET name='خودروهای ایرانی', name_fa='خودروهای ایرانی', name_en='Iranian vehicles', common_name_fa='ایرانی', common_name_en='Iranian' WHERE slug='iranian-reference';
UPDATE vehicle_models SET name='خودروهای چینی', name_fa='خودروهای چینی', name_en='Chinese vehicles', common_name_fa='چینی', common_name_en='Chinese' WHERE slug='chinese-reference';
UPDATE vehicle_models SET name='خودروهای کره‌ای', name_fa='خودروهای کره‌ای', name_en='Korean vehicles', common_name_fa='کره‌ای', common_name_en='Korean' WHERE slug='korean-reference';
UPDATE vehicle_models SET name='خودروهای ژاپنی', name_fa='خودروهای ژاپنی', name_en='Japanese vehicles', common_name_fa='ژاپنی', common_name_en='Japanese' WHERE slug='japanese-reference';
UPDATE vehicle_models SET name='خودروهای اروپایی', name_fa='خودروهای اروپایی', name_en='European vehicles', common_name_fa='اروپایی', common_name_en='European' WHERE slug='european-reference';

UPDATE vehicle_symptoms vs INNER JOIN vehicle_models vm ON vm.slug = CONCAT(vs.category, '-reference') SET vs.category = CASE vs.category WHEN 'iranian' THEN 'ایرانی' WHEN 'chinese' THEN 'چینی' WHEN 'korean' THEN 'کره‌ای' WHEN 'japanese' THEN 'ژاپنی' WHEN 'european' THEN 'اروپایی' ELSE vs.category END WHERE vs.category IN ('iranian','chinese','korean','japanese','european');

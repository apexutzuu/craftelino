-- PromoBrand — 15 produse de test cu imagini (OpenCart 3.x)
--
-- 1) Înlocuiește `oc_` cu prefixul tabelelor (DB_PREFIX din config.php).
-- 2) Imagini: `image/catalog/promobrand/pb-demo-01.jpg` … `pb-demo-15.jpg`
--    (incluse în repo; după deploy copiază folderul `image/catalog/promobrand/` pe server).
-- 3) Categorii: leagă după numele din `install/promobrand_categories.sql`.
--    Dacă lipsește o categorie, se folosește prima categorie din magazinul 0.
-- 4) Rulează de mai multe ori: șterge întâi produsele cu model PB-DEMO-% (scriptul face asta).
--

SET @pc_fallback := (
  SELECT MIN(cts.`category_id`) FROM `oc_category_to_store` cts WHERE cts.`store_id` = 0
);

DELETE ps FROM `oc_product_special` ps
INNER JOIN `oc_product` p ON (ps.`product_id` = p.`product_id`)
WHERE p.`model` LIKE 'PB-DEMO-%';

DELETE pi FROM `oc_product_image` pi
INNER JOIN `oc_product` p ON (pi.`product_id` = p.`product_id`)
WHERE p.`model` LIKE 'PB-DEMO-%';

DELETE ptc FROM `oc_product_to_category` ptc
INNER JOIN `oc_product` p ON (ptc.`product_id` = p.`product_id`)
WHERE p.`model` LIKE 'PB-DEMO-%';

DELETE pts FROM `oc_product_to_store` pts
INNER JOIN `oc_product` p ON (pts.`product_id` = p.`product_id`)
WHERE p.`model` LIKE 'PB-DEMO-%';

DELETE pd FROM `oc_product_description` pd
INNER JOIN `oc_product` p ON (pd.`product_id` = p.`product_id`)
WHERE p.`model` LIKE 'PB-DEMO-%';

DELETE FROM `oc_product` WHERE `model` LIKE 'PB-DEMO-%';

-- ---------------------------------------------------------------------------
-- PB-DEMO-01 … PB-DEMO-15
-- ---------------------------------------------------------------------------

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-01', 'PB-DEMO-01', '', '', '', '', '', '',
  80, 7, 'catalog/promobrand/pb-demo-01.jpg', 0, 1,
  129.0000, 0, 9, CURDATE(),
  0.35000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 1, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Termos inox 500ml gravură laser logo',
  '<p>Produs demonstrativ. Termos din inox, gravură laser logo, păstrează temperatura ore întregi.</p>', 'termos, promo, test',
  'Termos inox 500ml', 'Termos personalizat pentru birou și evenimente.', 'termos,inox,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Termosuri & Căni' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-02.jpg', 1);
INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority`, `price`, `date_start`, `date_end`) VALUES (@pb_pid, 1, 1, 89.0000, '0000-00-00', '0000-00-00');

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-02', 'PB-DEMO-02', '', '', '', '', '', '',
  120, 7, 'catalog/promobrand/pb-demo-02.jpg', 0, 1,
  45.0000, 0, 9, CURDATE(),
  0.30000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 2, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Cană ceramică personalizată full color',
  '<p>Cană ceramică 330 ml, print full color sau logo simplu. Produs de test.</p>', 'cană, ceramic, promo',
  'Cană ceramică personalizată', 'Cană promoțională pentru cafea și ceai.', 'cană,ceramic,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Termosuri & Căni' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-03.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-03', 'PB-DEMO-03', '', '', '', '', '', '',
  40, 7, 'catalog/promobrand/pb-demo-03.jpg', 0, 1,
  189.0000, 0, 9, CURDATE(),
  0.90000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 3, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Set cadou termos + cană gravură logo',
  '<p>Set ambalat cadou: termos compact + cană. Produs demonstrativ.</p>', 'set, cadou, promo',
  'Set cadou termos cană', 'Set promoțional pentru clienți și angajați.', 'set,cadou,termos'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Seturi cadou' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-01.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-04', 'PB-DEMO-04', '', '', '', '', '', '',
  200, 7, 'catalog/promobrand/pb-demo-04.jpg', 0, 1,
  59.0000, 0, 9, CURDATE(),
  0.25000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 4, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Agendă A5 copertă rigidă personalizată',
  '<p>Agendă A5, copertă rigidă, interior săptămânal. Produs de test.</p>', 'agendă, birou, promo',
  'Agendă A5 personalizată', 'Agendă promoțională pentru corporate.', 'agendă,a5,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Agende & Pixuri' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-05.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-05', 'PB-DEMO-05', '', '', '', '', '', '',
  150, 7, 'catalog/promobrand/pb-demo-05.jpg', 0, 1,
  24.9000, 0, 9, CURDATE(),
  0.12000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 5, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Carnet A6 cu elastic și logo gravat',
  '<p>Carnet dictando A6, hârtie ivory, elastic colorat. Test.</p>', 'carnet, notițe, promo',
  'Carnet A6 personalizat', 'Carnet compact pentru evenimente.', 'carnet,a6,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Agende & Pixuri' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-06.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-06', 'PB-DEMO-06', '', '', '', '', '', '',
  300, 7, 'catalog/promobrand/pb-demo-06.jpg', 0, 1,
  79.0000, 0, 9, CURDATE(),
  0.20000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 6, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Tricou bumbac 180g broderie logo',
  '<p>Tricou unisex, bumbac 180 g, broderie computerizată. Test.</p>', 'tricou, textile, promo',
  'Tricou personalizat broderie', 'Tricou promoțional pentru echipe.', 'tricou,bumbac,broderie'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Textile' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-07.jpg', 1);
INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority`, `price`, `date_start`, `date_end`) VALUES (@pb_pid, 1, 1, 59.0000, '0000-00-00', '0000-00-00');

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-07', 'PB-DEMO-07', '', '', '', '', '', '',
  160, 7, 'catalog/promobrand/pb-demo-07.jpg', 0, 1,
  95.0000, 0, 9, CURDATE(),
  0.22000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 7, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Polo damă slim fit print sau broderie',
  '<p>Polo pique damă, croi slim, personalizare logo. Test.</p>', 'polo, textile, promo',
  'Polo damă personalizat', 'Polo corporate pentru vară.', 'polo,textile,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Textile' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-08.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-08', 'PB-DEMO-08', '', '', '', '', '', '',
  45, 7, 'catalog/promobrand/pb-demo-08.jpg', 0, 1,
  149.0000, 0, 9, CURDATE(),
  0.60000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 8, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Geantă laptop 15" impermeabilă logo',
  '<p>Geantă business, compartiment laptop 15", bandouăer detașabil. Test.</p>', 'geantă, laptop, promo',
  'Geantă laptop personalizată', 'Geantă pentru deplasări și birou mobil.', 'geantă,laptop,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Genți & Rucsacuri' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-09.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-09', 'PB-DEMO-09', '', '', '', '', '', '',
  90, 7, 'catalog/promobrand/pb-demo-09.jpg', 0, 1,
  119.0000, 0, 9, CURDATE(),
  0.45000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 9, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Rucsac urban 20L buzunar laptop',
  '<p>Rucsac casual 20 L, buzunar frontal, compartiment laptop. Test.</p>', 'rucsac, promo, test',
  'Rucsac urban personalizat', 'Rucsac pentru commuting și târguri.', 'rucsac,promo,test'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Genți & Rucsacuri' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-10.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-10', 'PB-DEMO-10', '', '', '', '', '', '',
  250, 7, 'catalog/promobrand/pb-demo-10.jpg', 0, 1,
  35.0000, 0, 9, CURDATE(),
  0.08000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 10, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Set 3 pixuri metal gravură laser',
  '<p>Set pixuri metalice în cutie cadou, gravură logo. Test.</p>', 'pixuri, birou, promo',
  'Set pixuri metal personalizat', 'Set corporate pentru birou.', 'pixuri,set,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Accesorii birou' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-11.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-11', 'PB-DEMO-11', '', '', '', '', '', '',
  180, 7, 'catalog/promobrand/pb-demo-11.jpg', 0, 1,
  42.0000, 0, 9, CURDATE(),
  0.15000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 11, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Mousepad XL cusut logo full color',
  '<p>Mousepad 80×30 cm, cauciuc + material textil, print UV. Test.</p>', 'mousepad, birou, promo',
  'Mousepad XL personalizat', 'Mousepad gaming și birou.', 'mousepad,xl,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Accesorii birou' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-12.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-12', 'PB-DEMO-12', '', '', '', '', '', '',
  35, 7, 'catalog/promobrand/pb-demo-12.jpg', 0, 1,
  249.0000, 0, 9, CURDATE(),
  1.20000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 12, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Set cadou birou deluxe în cutie rigidă',
  '<p>Set: agendă, pix metal, mousepad mic, ambalaj rigid. Test.</p>', 'set, cadou, birou',
  'Set cadou birou deluxe', 'Set premium pentru parteneri.', 'set,cadou,birou'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Seturi cadou' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-13.jpg', 1);
INSERT INTO `oc_product_special` (`product_id`, `customer_group_id`, `priority`, `price`, `date_start`, `date_end`) VALUES (@pb_pid, 1, 1, 189.0000, '0000-00-00', '0000-00-00');

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-13', 'PB-DEMO-13', '', '', '', '', '', '',
  220, 7, 'catalog/promobrand/pb-demo-13.jpg', 0, 1,
  54.0000, 0, 9, CURDATE(),
  0.40000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 13, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Sticlă tritan 750 ml capac bambus logo',
  '<p>Sticlă eco tritan, capac din bambus, personalizare laser. Test.</p>', 'sticlă, eco, promo',
  'Sticlă tritan personalizată', 'Produs sustenabil pentru hidratare.', 'sticlă,eco,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Eco & Sustenabil' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-14.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-14', 'PB-DEMO-14', '', '', '', '', '', '',
  400, 7, 'catalog/promobrand/pb-demo-14.jpg', 0, 1,
  18.9000, 0, 9, CURDATE(),
  0.05000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 14, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Sacosa bumbac natur serigrafie 1 culoare',
  '<p>Sacosa plată bumbac 140 g, mânere lungi, serigrafie 1 culoare. Test.</p>', 'sacoșă, eco, promo',
  'Sacosa bumbac personalizată', 'Sacosa pentru evenimente și magazine.', 'sacoșă,bumbac,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Eco & Sustenabil' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-15.jpg', 1);

INSERT INTO `oc_product` (
  `model`, `sku`, `upc`, `ean`, `jan`, `isbn`, `mpn`, `location`,
  `quantity`, `stock_status_id`, `image`, `manufacturer_id`, `shipping`,
  `price`, `points`, `tax_class_id`, `date_available`,
  `weight`, `weight_class_id`, `length`, `width`, `height`, `length_class_id`,
  `subtract`, `minimum`, `sort_order`, `status`, `viewed`, `date_added`, `date_modified`
) VALUES (
  'PB-DEMO-15', 'PB-DEMO-15', '', '', '', '', '', '',
  175, 7, 'catalog/promobrand/pb-demo-15.jpg', 0, 1,
  32.0000, 0, 9, CURDATE(),
  0.18000000, 1, 0.00000000, 0.00000000, 0.00000000, 1,
  1, 1, 15, 1, 1, 0, NOW(), NOW()
);
SET @pb_pid := LAST_INSERT_ID();
INSERT INTO `oc_product_description` (`product_id`, `language_id`, `name`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`)
SELECT @pb_pid, l.`language_id`, 'Notebook A5 hârtie reciclată copertă kraft',
  '<p>Notebook eco, hârtie cream reciclată, copertă kraft logo. Test.</p>', 'notebook, eco, promo',
  'Notebook eco personalizat', 'Articole sustenabile pentru birou.', 'notebook,eco,promo'
FROM `oc_language` l WHERE l.`status` = '1';
INSERT INTO `oc_product_to_store` (`product_id`, `store_id`) VALUES (@pb_pid, 0);
INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES (@pb_pid,
  COALESCE((SELECT MIN(c.`category_id`) FROM `oc_category` c INNER JOIN `oc_category_description` cd ON (c.`category_id` = cd.`category_id`) INNER JOIN `oc_category_to_store` c2s ON (c.`category_id` = c2s.`category_id` AND c2s.`store_id` = 0) WHERE c.`parent_id` = 0 AND cd.`name` = 'Eco & Sustenabil' LIMIT 1), @pc_fallback));
INSERT INTO `oc_product_image` (`product_id`, `image`, `sort_order`) VALUES (@pb_pid, 'catalog/promobrand/pb-demo-01.jpg', 1);

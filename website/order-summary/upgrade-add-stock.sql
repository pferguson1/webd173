ALTER TABLE `products` ADD COLUMN `stock` int(11) NOT NULL DEFAULT 100;
UPDATE `products` SET stock=50 WHERE sku='IPHO001';
UPDATE `products` SET stock=35 WHERE sku='CAME001';
UPDATE `products` SET stock=120 WHERE sku='WATC001';

--
-- Add Art Products to the database
-- Run this after creating the products table
--

-- First, clear existing sample products (optional)
-- DELETE FROM `products` WHERE `sku` IN ('IPHO001', 'CAME001', 'WATC001');

-- Insert Art Products
INSERT INTO `products` (`product_id`, `name`, `sku`, `price`, `image`, `stock`) VALUES
(4, 'The Warrior', 'WARR001', '100.00', 'images/face 7.jpg', 25),
(5, 'The Bushman', 'BUSH001', '90.00', 'images/bushman.jpg', 20),
(6, 'Thinking Man', 'THIN001', '75.00', 'images/thinking-man.jpg', 30),
(7, 'Beautiful Soul', 'SOUL001', '100.00', 'images/soul.jpg', 15),
(8, 'An American Patriot', 'PATR001', '100.00', 'images/soul-of-a-soldier.jpg', 18),
(9, 'The Railway Worker', 'RAIL001', '100.00', 'images/Black Japanese 21.jpg', 22),
(10, 'Black Goddess', 'GODD001', '100.00', 'images/black-goddess.jpg', 20)
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `price` = VALUES(`price`),
  `image` = VALUES(`image`),
  `stock` = VALUES(`stock`);

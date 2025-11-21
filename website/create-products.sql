--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sku` varchar(14) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `image` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 100,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;



--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `sku`, `price`, `image`, `stock`) VALUES
(1, 'Iphone', 'IPHO001', '400.00', 'images/iphone.jpg', 50),
(2, 'Camera', 'CAME001', '700.00', 'images/camera.jpg', 35),
(3, 'Watch', 'WATC001', '100.00', 'images/watch.jpg', 120);

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `customer_email` varchar(100),
  `customer_name` varchar(100),
  `customer_phone` varchar(20),
  `shipping_address` text,
  `order_total` decimal(15,2) NOT NULL,
  `order_items` longtext NOT NULL,
  `payment_status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

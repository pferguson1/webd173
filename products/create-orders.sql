-- Create orders table for php_bases / mycolard_php-bases
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

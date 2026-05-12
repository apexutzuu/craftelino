-- Design Upload table for storing design files attached to orders
CREATE TABLE IF NOT EXISTS `oc_order_design_upload` (
  `order_design_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `design_code` varchar(255) NOT NULL DEFAULT '',
  `observations` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`order_design_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

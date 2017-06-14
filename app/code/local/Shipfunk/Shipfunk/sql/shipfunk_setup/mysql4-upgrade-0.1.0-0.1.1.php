<?php

$installer = $this;

$installer->startSetup();

$installer->run("
		CREATE TABLE IF NOT EXISTS {$installer->getTable('shipfunk_order_parcels')} (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` varchar(50) NOT NULL,
		  `parcel_id` varchar(255) NOT NULL,
		  `code` varchar(255) NOT NULL,
		  `contents` varchar(255) NOT NULL,
		  `weight_unit` varchar(45) NOT NULL,
		  `weight_amount` decimal(12,4)  DEFAULT 0,
          `monetary_value` decimal(12,4)  DEFAULT 0,
          `dimensions_unit` varchar(45) NOT NULL,
          `dimensions_width` decimal(12,4)  DEFAULT 0,
          `dimensions_depth` decimal(12,4)  DEFAULT 0,
          `dimensions_height` decimal(12,4)  DEFAULT 0,
          `toppleable` tinyint(1) DEFAULT NULL,
          `stackable` tinyint(1) DEFAULT NULL,
          `fragile` tinyint(1) DEFAULT NULL,
          `warehouse` varchar(255) NOT NULL,
          `tracking_codes_send` varchar(255) NOT NULL,
          `tracking_codes_return` varchar(255) NOT NULL,
          `status` tinyint(1)  DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup(); 
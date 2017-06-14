<?php

$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($this->getTable('sales_flat_quote_address'), 'pickup_code', "VARCHAR( 255 ) NOT NULL COMMENT 'Pick Up Code'");
$installer->getConnection()->addColumn($this->getTable('sales_flat_quote_shipping_rate'), 'pickup_code', "VARCHAR( 255 ) NOT NULL COMMENT 'Pick Up Code'");
$installer->endSetup();
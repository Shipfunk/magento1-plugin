<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('sales_flat_quote_address'), 'pick_up', "VARCHAR( 255 ) NOT NULL COMMENT 'Pick Up'");
$installer->endSetup();
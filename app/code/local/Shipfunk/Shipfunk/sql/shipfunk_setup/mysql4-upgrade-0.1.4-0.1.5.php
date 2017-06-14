<?php

$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn($this->getTable('shipfunk_order_parcels'), 'card_format', "VARCHAR(50) NOT NULL AFTER `warehouse`");
$installer->getConnection()->addColumn($this->getTable('shipfunk_order_parcels'), 'card_size', "VARCHAR(50) NOT NULL AFTER `warehouse`");
$installer->endSetup();
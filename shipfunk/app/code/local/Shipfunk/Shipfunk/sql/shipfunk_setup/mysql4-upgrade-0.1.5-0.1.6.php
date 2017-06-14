<?php

$installer = $this;

$installer->startSetup();
$installer->getConnection()->modifyColumn($this->getTable('shipfunk_order_parcels'), 'tracking_codes_send', "VARCHAR(255) NULL");
$installer->getConnection()->modifyColumn($this->getTable('shipfunk_order_parcels'), 'tracking_codes_return', "VARCHAR(255) NULL");
$installer->endSetup();
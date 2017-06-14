<?php
class Shipfunk_Shipfunk_Model_Resource_Mysql4_OrderParcels extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('shipfunk/orderParcels', 'id');
	}
}

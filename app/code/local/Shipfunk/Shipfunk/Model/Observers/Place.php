<?php 
class Shipfunk_Shipfunk_Model_Observers_Place extends Varien_Event_Observer
{
    public function changeShipfunkInfo($observer){
        $order = $observer->getEvent()->getOrder();
        $address =$order->getShippingAddress();
        $orderTempId = Mage::helper("shipfunk")->getTmpOrderId($order->getQuoteId());
        $finalOrderid = $order->getIncrementId();
        //setOrderStatus
        $status = 'placed';
        $setOrderStatus = Mage::helper("shipfunk/api")->setOrderStatus($order,$orderTempId,$status);
        $setCustomerDetails = Mage::helper("shipfunk/api")->setCustomerDetails($address,$finalOrderid);

        //getParcels 
        Mage::helper("shipfunk/api")->getParcels($finalOrderid);
        return;
    }
}
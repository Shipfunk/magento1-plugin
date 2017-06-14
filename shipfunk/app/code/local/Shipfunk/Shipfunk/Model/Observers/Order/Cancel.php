<?php 
class Shipfunk_Shipfunk_Model_Observers_Order_Cancel
{
    public function changeShipfunkStatus($observer){
        $order = $observer->getEvent()->getOrder();
        $orderTempId = Mage::helper("shipfunk")->getTmpOrderId($order->getQuoteId());
        $finalOrderid = $order->getIncrementId();
        //setOrderStatus
        $status = "cancelled";
        $setOrderStatus = Mage::helper("shipfunk/api")->setOrderStatus($order,$orderTempId,$status);
        return;
    }
}
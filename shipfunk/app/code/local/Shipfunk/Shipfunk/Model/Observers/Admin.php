<?php
class Shipfunk_Shipfunk_Model_Observers_Admin extends Mage_Core_Model_Abstract {
    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function addButton(Varien_Event_Observer $observer) {
        $container = $observer->getBlock();
        if(null !== $container && $container->getType() == 'adminhtml/sales_order_view' && $container instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $orderId = $container->getOrderId();
            $data = array(
                'label'     => 'Create the package cards',
                'class'     => 'some-class',
                'onclick'   => 'setLocation(\''  . Mage::helper("adminhtml")->getUrl('shipfunk/adminhtml_parcel/createNewPackageCards',array('order_id'=>$orderId)) . '\')',
            );
            $container->addButton('create_the_package_cards', $data);
            
            $order = Mage::getModel('sales/order')->load($orderId);
            Mage::helper('shipfunk/api')->getParcels($order->getIncrementId());
        }
        return $this;
    }
}

<?php
class Shipfunk_Shipfunk_Model_Observers_Shipfunk extends Mage_Core_Model_Abstract {

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function applyFieldOrder(Varien_Event_Observer $observer) {

        //variables from Observer
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        $html = $transport->getHtml();
        $quote = $block->getQuote();

        //config varialbes
        $sort = Mage::getStoreConfig('onestepcheckout/sortordering_fields');
        $whitelist = $this->getWhiteList(Mage::getStoreConfig('onestepcheckout/exclude_fields'));

        //deal with billing fields
        if ($block instanceof Mage_Checkout_Block_Onepage_Billing) {
            $html = $this->sortFields('billing', $html, $sort, $whitelist);
        }

        //deal with shipping fields
        if ($block instanceof Mage_Checkout_Block_Onepage_Shipping) {
            $html = $this->sortFields('shipping', $html, $sort, $whitelist);
        }

        //set the result if we succeed
        if (! empty($html)) {
            $transport->setHtml($html);
        }

        return $this;
    }
}

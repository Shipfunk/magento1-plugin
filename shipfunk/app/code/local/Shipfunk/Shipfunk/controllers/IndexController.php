<?php
class Shipfunk_Shipfunk_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        echo 2;
        // $routeName = $this->getRequest()->getRouteName();

        // if (!Mage::helper('onestepcheckout')->isRewriteCheckoutLinksEnabled() && $routeName != 'onestepcheckout'){
        //     $this->_redirect('checkout/onepage', array('_secure'=>true));
        // }
    }
}

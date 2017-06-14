<?php
class Shipfunk_Shipfunk_PostcodeController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $code    = $this->getRequest()->getParam('code');
        $postcode = $this->getRequest()->getParam('postcode');
        $carrier = Mage::getModel('shipfunk/carrier');
        $shipfunks = $carrier->getAjaxQuotes($postcode,$code);
        $result = array();
        if(isset($shipfunks[$code]) && $shipfunks[$code] && isset($shipfunks[$code]['pickups'])){
            $result = $shipfunks[$code];
        }
        //Mage::log($shipfunks[$code]);
        echo json_encode($result);exit();
    }
}

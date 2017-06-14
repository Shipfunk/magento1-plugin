<?php
class Shipfunk_Shipfunk_Model_Observers_Dispatcher extends Mage_Core_Model_Abstract {

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function hookToControllerActionPostDispatch(Varien_Event_Observer $observer) {
        echo $observer->getEvent()->getControllerAction()->getFullActionName().'|';
        if($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_getAdditional')
        {
            $controller = $observer->getEvent()->getControllerAction();
            $request = $controller->getRequest();
            $reponse = $controller->getResponse();
            $reponse->setBody('some stuff');
        }
    }
}

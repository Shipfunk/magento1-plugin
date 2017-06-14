<?php 
class Shipfunk_Shipfunk_Rewrite_Sales_Model_Quote_Address_Rate extends Mage_Sales_Model_Quote_Address_Rate
{
     public function importShippingRate(Mage_Shipping_Model_Rate_Result_Abstract $rate)
    {
        if ($rate instanceof Mage_Shipping_Model_Rate_Result_Error) {
            $this
            ->setCode($rate->getCarrier().'_error')
            ->setCarrier($rate->getCarrier())
            ->setCarrierTitle($rate->getCarrierTitle())
            ->setErrorMessage($rate->getErrorMessage())
            ;
        } elseif ($rate instanceof Mage_Shipping_Model_Rate_Result_Method) {
            $pickUp=Mage::getSingleton('core/session')->getData('pick_up_point');
            $this
            ->setCode($rate->getCarrier().'_'.$rate->getMethod())
            ->setCarrier($rate->getCarrier())
            ->setCarrierTitle($rate->getCarrierTitle())
            ->setMethod($rate->getMethod())
            ->setMethodTitle($rate->getMethodTitle())
            ->setMethodDescription($rate->getMethodDescription())
            ->setPickUp($pickUp)
            ->setPrice($rate->getPrice())
            ;
        }
        //Mage::log($this->getData());
        return $this;
    }
}
?>
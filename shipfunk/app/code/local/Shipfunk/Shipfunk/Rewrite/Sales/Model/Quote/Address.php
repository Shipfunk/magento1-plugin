<?php 
class Shipfunk_Shipfunk_Rewrite_Sales_Model_Quote_Address extends Mage_Sales_Model_Quote_Address
{
    /**
     * Retrieve all grouped shipping rates
     *
     * @return array
     */
    public function getGroupedAllShippingRates()
    {
        $rates = array();
        foreach ($this->getShippingRatesCollection() as $rate) {
            if (!$rate->isDeleted() && $rate->getCarrierInstance()) {
                if (!isset($rates[$rate->getCarrier()])) {
                    $rates[$rate->getCarrier()] = array();
                }
                $carrier = $rate->getCarrier();
                if($rate->getCarrier() == 'shipfunk'){
                    $carrier = $rate->getCarrierTitle();
                }
                $rates[$carrier][] = $rate;
                $rates[$carrier][0]->carrier_sort_order = $rate->getCarrierInstance()->getSortOrder();
            }
        }
        if(isset($rates['shipfunk'])){
            unset($rates['shipfunk']);
        }
        uasort($rates, array($this, '_sortRates'));
        return $rates;
    }
    public function getShippingRateByCode($code,$pickUp='')
    {
        foreach ($this->getShippingRatesCollection() as $rate) {
            if ($rate->getCode() == $code) {
                $rate->setPickUp($pickUp);
                return $rate;
            }
        }
        return false;
    }
    
}
?>
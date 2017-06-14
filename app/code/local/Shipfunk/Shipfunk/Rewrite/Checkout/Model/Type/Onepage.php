<?php 
class Shipfunk_Shipfunk_Rewrite_Checkout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    public function saveShippingMethod($shippingMethod)
    {   
        $data=Mage::app()->getRequest()->getPost();
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        //var_dump($this->getQuote()->getShippingAddress()->getShippingDescription());die;
        if(!isset($data['pick_up'])){
            $data['pick_up']='';
        }
        if(!isset($data['pickup_code'])){
            $data['pickup_code']='';
        }
        Mage::getSingleton('core/session')->setData('pick_up_point',$data['pick_up']);
        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod,$data['pick_up']);
        if (!$rate) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
         $this->getQuote()->getShippingAddress()
        ->setShippingMethod($shippingMethod);
         $address_id=$rate->getAddressId();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        if(isset($data['pick_up']) && !empty($data['pick_up'])){
            $sql = "update `sales_flat_quote_address` set pick_up ='{$data['pick_up']}' where address_id = {$address_id}";
            $write->query($sql);
        }
        if(isset($data['pickup_code']) && !empty($data['pickup_code'])){
            $sql = "update `sales_flat_quote_address` set pickup_code ='{$data['pickup_code']}' where address_id = {$address_id}";
            $write->query($sql);
        }
        
        $this->getQuote()->getShippingAddress()
        ->setPickupCode($data['pickup_code'])
        ->setPickUp($data['pick_up']);
        $this->getCheckout()
        ->setStepData('shipping_method', 'complete', true)
        ->setStepData('payment', 'allow', true);
        return array();
    }
      public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $quote = $this->getQuote();
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }
    
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
        | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
        | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
        | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
    
        $payment = $quote->getPayment();
        $payment->importData($data);
        $quote->save();
        $this->getCheckout()
        ->setStepData('payment', 'complete', true)
        ->setStepData('review', 'allow', true);
    
        return array();
    } 
}
<?php
class Shipfunk_Shipfunk_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    
//     protected function _prepareLayout()
//     {
//         $onclick = "submitAndReloadArea($('order_parcel_info').parentNode, '".$this->getSubmitUrl()."')";
//         $this->setChild('save_button',
//             $this->getLayout()->createBlock('adminhtml/widget_button')
//             ->setData(array(
//                 'label'   => Mage::helper('sales')->__('Add'),
//                 'class'   => 'save',
//                 'onclick' => $onclick
//             ))
    
//             );
//     }
    
    public function getSubmitUrl()
    {
        return $this->getUrl('shipfunk/adminhtml_parcel/addParcel/', array('order_id'=>$this->getOrder()->getId()));
    }
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/*/*', array('order_id'=>$orderId));
    }
    
    public function isHidden()
    {
        return false;
    }
    
    public function getAllParcels(){
        $orderId = $this->getOrder()->getIncrementId();//'110000012';//$this->getOrder()->getIncrementId();
        $parcelCollection = Mage::getModel('shipfunk/orderParcels')->getCollection();
        $parcelCollection->addFieldToFilter('order_id',array('eq'=>$orderId));
        $parcelCollection->setOrder('code','asc');
        return $parcelCollection;
    }
    
    public function getRemoveUrl($track)
    {
        return $this->getUrl('shipfunk/adminhtml_parcel/removeParcel/', array(
            'order_id' => $this->getOrder()->getId(),
            'track_id' => $track->getId()
        ));
    }
    
    public function getEditUrl($track)
    {
        return $this->getUrl('shipfunk/adminhtml_parcel/editParcel/', array(
            'order_id' => $this->getOrder()->getId(),
            'track_id' => $track->getId()
        ));
    }
    public function getAddUrl()
    {
        return $this->getUrl('shipfunk/adminhtml_parcel/addParcel/', array(
            'order_id' => $this->getOrder()->getId(),
        ));
    }
    public function getWeightUnitOptions(){
        $unit = new Shipfunk_Shipfunk_Model_Product_Attribute_Source_Weight_Unit;
        return $unit->toArray();
    }
    public function getDimensionsUnitOptions(){
        $unit = new Shipfunk_Shipfunk_Model_Product_Attribute_Source_Dimension_Unit;
        return $unit->toArray();
    }
    public function checkDownloadFile($_parcel,$type){
        $dir = Mage::getBaseDir('var').'/export/shipfunk/package_cards/';
        if($type == 'send'){
            $code = $_parcel->getData('tracking_codes_send');
        }elseif ($type == 'return'){
            $code = $_parcel->getData('tracking_codes_return');
        }
        $format = $_parcel->getData('card_format');
        $fileName = $type.'_'.$code.".{$format}";
        $filePath = $dir.$fileName;
        if(file_exists($filePath)){
            return $this->getUrl('shipfunk/adminhtml_parcel/download', array(
                'file' => $fileName,
            ));
            //return str_replace(Mage::getBaseDir().'/', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), $filePath);
        }else{
            $formatArray = array('zpl','pdf','html');
            foreach ($formatArray as $_format){
                $fileName = $type.'_'.$code.".{$_format}";
                if(file_exists($dir.$fileName)){
                    return $this->getUrl('shipfunk/adminhtml_parcel/download', array(
                        'file' => $fileName,
                    ));
                }
            }
        }
        return false;
    }
    public function getCreatePackageCardsUrl()
    {
        return $this->getUrl('shipfunk/adminhtml_parcel/createNewPackageCards', array(
            'order_id' => $this->getOrder()->getId(),
        ));
    }
    
    public function getTrackingUrl($_parcel,$type){
        $baseUrl = Mage::helper("shipfunk")->getShipfunkUrl("tracking");
        $trackingCode = "";
        $shippingCompany = "";
        if($type == "send"){
            $trackingCode = $_parcel->getData("tracking_codes_send");
        }else{
            $trackingCode = $_parcel->getData("tracking_codes_return");
        }
        $orderIncrementId = $_parcel->getData("order_id");
        $order = Mage::getModel('sales/order')->load($orderIncrementId, 'increment_id');
        $shippingCompany = $order->getShippingDescription();
        $shippingCompany = explode("-", $shippingCompany);
        $shippingCompany = trim($shippingCompany[0]);
        $shippingCompany = str_replace(" ", "_", $shippingCompany);
        return $baseUrl."tracking_code={$trackingCode}&company={$shippingCompany}";
    }
}

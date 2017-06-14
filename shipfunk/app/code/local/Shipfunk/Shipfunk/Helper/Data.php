<?php
class Shipfunk_Shipfunk_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LIVE_BASE_URL = "https://shipfunkservices.com/";
    const DEV_BASE_URL = "https://dev.shipfunkservices.com/";
    const API_PARAMS = "api/1.2/";
    const TRACKING_PARAMS = "tracking/tracked?";
    
    public function getTmpOrderId($quoteId=false){
        $firstNum = 100000000;
        if(!$quoteId){
            $quoteId = Mage::getSingleton('checkout/session')->getQuote()->getId();
        }
        $baseNum = (int)$quoteId;
        return $baseNum + (int)$firstNum;
    }
    public function getShipfunkApiInfo(){
        //$orderTempId = $this->getTmpOrderId($cart->getId());
        $apiProductionKey=$this->getShipfunkConfigData('shipfunk','base','api_production');
        $apiDevelopmentKey=$this->getShipfunkConfigData('shipfunk','base','api_development');
        $environment=$this->getShipfunkConfigData('shipfunk','base','environment');
        $shipfunkApi=array();
        if($environment==0){
            $shipfunkApi['key']=$apiDevelopmentKey;
        }else{
            $shipfunkApi['key']=$apiProductionKey;
        }
        $shipfunkApi['url'] = $this->getShipfunkUrl("api");
        return $shipfunkApi;
    }
    public function getShipfunkConfigData($sessionName,$groupName,$field)
    {
        $path=$sessionName.'/'.$groupName.'/'.$field;
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig($path, $storeId);
    }
    public function getShipfunkUrl($type=null){
        $url = "";
        $environment=$this->getShipfunkConfigData('shipfunk','base','environment');
        if($environment==0){
            $url = self::DEV_BASE_URL;
        }else{
            $url = self::LIVE_BASE_URL;
        }
        switch ($type) {
            case "api":
                $url .= self::API_PARAMS;
                break;
            case "tracking":
                $url .= self::TRACKING_PARAMS;
                break;
            default:
                break;
        }
        return $url;
    }
}

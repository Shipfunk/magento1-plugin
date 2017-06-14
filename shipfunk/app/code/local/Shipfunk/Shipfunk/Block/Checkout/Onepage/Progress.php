<?php 
class  Shipfunk_Shipfunk_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{
      public function getPickUp()
    {
        $pickUp=$this->getQuote()->getShippingAddress()->getPickUp();
        $pickUpArray=explode(',',$pickUp);
        if(count($pickUpArray)==5){
            //Mage::log($pickUpArray);
            return $pickUpArray[0].'<br>'.$pickUpArray[1].'<br>'.$pickUpArray[2].' '.$pickUpArray[3];
        }elseif(count($pickUpArray)==6){
            //Mage::log($pickUpArray);
            return $pickUpArray[0].','.$pickUpArray[1].'<br>'.$pickUpArray[2].'<br>'.$pickUpArray[3].' '.$pickUpArray[4];
        }else{
            return null;
        }
        
        
    }
}
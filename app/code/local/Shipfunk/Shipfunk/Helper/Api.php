<?php

class Shipfunk_Shipfunk_Helper_Api extends Mage_Core_Helper_Abstract {

    function getParcels($finalOrderid) {
        $shipfunkApi = Mage::helper('shipfunk')->getShipfunkApiInfo();
        $apiUrl = $shipfunkApi['url'];
        $apiKey = $shipfunkApi['key'];
        $data = array(
            "query" => array(
                "webshop" => array(
                    "api_key" => $apiKey
                )
            )
        );
        $dataString = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . "get_parcels/true/json/json/" . $finalOrderid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$apiKey}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "sf_get_parcels={$dataString}");
        $server_output = curl_exec($ch);
        curl_close($ch);
        //Mage::log($data, null, "shipfunk.log", true);
        //Mage::log($server_output, null, "shipfunk.log", true);
        $res = json_decode($server_output);
        if (isset($res->response) && isset($res->response->parcels) && is_array($res->response->parcels)) {
            //success
            $this->shipfunk_order_parcels($finalOrderid, (array) $res->response->parcels, true);
            return true;
        } else if (isset($res->response->Code) && $res->response->Code == "10011") {
            //no parcels
            //send email
            if (isset($res->response->Message) && $res->response->Message) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res->response->Message}";
            } else {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: API call failed";
            }
            $subject = "Shipfunk Error Report: Get Parcels Failed";
            $this->sendErrorEmail($subject, $message);
            return false;
        }
    }

    function deleteParcels($finalOrderid, $parcelId, $parcelCode, $parcelTrackingCode) {
        $shipfunkApi = Mage::helper('shipfunk')->getShipfunkApiInfo();
        $apiUrl = $shipfunkApi['url'];
        $apiKey = $shipfunkApi['key'];
        $data = array(
            "query" => array(
                "webshop" => array(
                    "api_key" => $apiKey
                ),
                "order" => array(
                    "return_parcels" => 1,
                    "remove_all_parcels" => 0,
                    "parcels" => array(
                        array(
                            "id" => $parcelId,
                            "parcel_code" => $parcelCode,
                            "tracking_code" => $parcelTrackingCode
                        )
                    )
                )
            )
        );
        $dataString = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . "delete_parcels/true/json/json/" . $finalOrderid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$apiKey}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "sf_delete_parcels={$dataString}");
        $server_output = curl_exec($ch);
        curl_close($ch);
        //Mage::log($data, null, "shipfunk.log", true);
        //Mage::log($server_output, null, "shipfunk.log", true);
        $res = json_decode($server_output);
        if (isset($res->response) && isset($res->response->parcels) && is_array($res->response->parcels)) {
            $this->shipfunk_order_parcels($finalOrderid, (array) $res->response->parcels);
            return true;
        } else {
            //send email
            if (isset($res->Error->Message) && $res->Error->Message) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res->response->Message}";
            }elseif (isset($res->Info->Message) && $res->Info->Message){
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res->Info->Message}";
            }else {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: API call failed";
            }
            $subject = "Shipfunk Error Report: Delete Parcels Failed";
            $this->sendErrorEmail($subject, $message);
            return false;
        }
    }

    function editParcels($finalOrderid, $parcelId, $parceData) {
        $shipfunkApi = Mage::helper('shipfunk')->getShipfunkApiInfo();
        $apiUrl = $shipfunkApi['url'];
        $apiKey = $shipfunkApi['key'];
        $data = array(
            "query" => array(
                "webshop" => array(
                    "api_key" => $apiKey
                ),
                "order" => array(
                    "return_parcels" => 1,
                    "parcels" => array(
                        array(
                            "id" => $parcelId,
                            "code" => $parceData["code"],
                            "contents" => $parceData["contents"],
                            "weight" => array(
                                "unit" => $parceData["weight_unit"], //"kg",
                                "amount" => $parceData["weight_amount"], //"0.4"
                            ),
                            "dimensions" => array(
                                "unit" => $parceData["dimensions_unit"], //"cm",
                                "width" => $parceData["dimensions_width"], //"7.5",
                                "depth" => $parceData["dimensions_depth"], //"5",
                                "height" => $parceData["dimensions_height"], //"1.5"
                            ),
                            "monetary_value" => $parceData["monetary_value"], //"10",
                            "warehouse" => $parceData["warehouse"], //"WARE1",
                            "fragile" => $parceData["fragile"], //1
                        )
                    )
                )
            )
        );
        $dataString = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . "edit_parcels/true/json/json/" . $finalOrderid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$apiKey}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "sf_edit_parcels={$dataString}");
        $server_output = curl_exec($ch);
        curl_close($ch);
        //Mage::log($server_output, null, "shipfunk.log", true);
        $res = json_decode($server_output);
        if (isset($res->response) && isset($res->response->parcels) && is_array($res->response->parcels)) {
            //success
            $this->shipfunk_order_parcels($finalOrderid, (array) $res->response->parcels);
            return true;
        } else {
            //send email
            if (isset($res->response->Message) && $res->response->Message) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res->response->Message}";
            } else {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: API call failed";
            }
            $subject = "Shipfunk Error Report: Edit Parcels Failed";
            $this->sendErrorEmail($subject, $message);
            return false;
        }
    }

    function createNewPackageCards($finalOrderid) {
        $shipfunkApi = Mage::helper('shipfunk')->getShipfunkApiInfo();
        $apiUrl = $shipfunkApi['url'];
        $apiKey = $shipfunkApi['key'];

        $parcelCollection = Mage::getModel('shipfunk/orderParcels')->getCollection();
        $parcelCollection->addFieldToFilter('order_id', array('eq' => $finalOrderid));
        $parcelsItems = array();
        foreach ($parcelCollection as $_parcel) {
            $parcelsItems[] = array(
                "code" => $_parcel->getData('code'),
                "contents" => $_parcel->getData('contents'),
                "weight" => array(
                    "unit" => $_parcel->getData('weight_unit'),
                    "amount" => $_parcel->getData('weight_amount'),
                ),
                "monetary_value" => $_parcel->getData('monetary_value'),
                "dimensions" => array(
                    "unit" => $_parcel->getData('dimensions_unit'),
                    "width" => $_parcel->getData('dimensions_width'),
                    "depth" => $_parcel->getData('dimensions_depth'),
                    "height" => $_parcel->getData('dimensions_height'),
                ),
                "toppleable" => $_parcel->getData('toppleable'),
                "stackable" => $_parcel->getData('stackable'),
                "warehouse" => $_parcel->getData('warehouse'),
                "tracking_codes" => array(
                    "send" => $_parcel->getData('tracking_codes_send'),
                    "return" => $_parcel->getData('tracking_codes_return')
                ),
            );
        }
        
        //settings from configuration page
        $format = Mage::getStoreConfig('shipfunk/package_card/package_card_types');
        $size = Mage::getStoreConfig('shipfunk/package_card/package_card_sizes');
        $firstParcel = $this->getFirstParcel($finalOrderid);
        if ($firstParcel) {
            $format = $firstParcel['card_format'];
            $size = $firstParcel['card_size'];
        }
        $data = array(
            "query" => array(
                "webshop" => array(
                    "api_key" => $apiKey
                ),
                "order" => array(
                    "return_cards" => 1,
                    "sendmail" => 0,
                    "send_edi" => 0,
                    "package_card" => array(
                        "format" => $format,
                        "size" => $size
                    ),
                    "parcels" => $parcelsItems
                )
            )
        );
        $dataString = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . "create_new_package_cards/true/json/json/" . $finalOrderid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$apiKey}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "sf_create_new_package_cards={$dataString}");
        $server_output = curl_exec($ch);
        curl_close($ch);
        //Mage::log($data, null, "shipfunk.log", true);
        //Mage::log($server_output, null, "shipfunk.log", true);
        $res = json_decode($server_output, true);
        if (isset($res['response']) && isset($res['response']['parcels']) && is_array($res['response']['parcels'])) {
            //success
            $this->updateOrderParcelPackageCards($finalOrderid, $res['response']['parcels']);
            return true;
        } else {
            //send email
            if (isset($res['response']['Message']) && $res['response']['Message']) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res['response']['Message']}";
            } elseif (isset($res['Error']) && $res['Error']['Message']) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res['Error']['Message']}";
            } else {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: API call failed";
            }
            $subject = "Shipfunk Error Report: Create New Package Cards Failed";
            $this->sendErrorEmail($subject, $message);
            return false;
        }
    }

    public function updateOrderParcelPackageCards($orderId, $parcels) {
        $saveDir = Mage::getBaseDir('var') . '/export/shipfunk/package_cards/';
        if (!is_dir($saveDir)) {
            @mkdir($saveDir, 0777, true);
            @chmod($saveDir, 0777);
        }
        $format = Mage::getStoreConfig('shipfunk/package_card/package_card_types');
        //$defaultSize = Mage::getStoreConfig('shipfunk/package_card/package_card_sizes');
        $firstParcel = $this->getFirstParcel($orderId);
        if ($firstParcel) {
            $format = $firstParcel['card_format'];
            //$size = $firstParcel['card_size'];
        }
        foreach ($parcels as $row) {
            if (isset($row['parcel_code']) && $row['parcel_code'] && isset($row['parcel_code'])) {
                //select
                $orderParcelCollection = Mage::getModel('shipfunk/orderParcels')->getCollection();
                $orderParcelCollection->addFieldToFilter('code', array('eq' => $row['parcel_code']));
                $orderParcelCollection->addFieldToFilter('order_id', array('eq' => $orderId));
                $orderParcelCollection->getSelect()->limit(1);
                if ($orderParcelCollection->getSize() > 0) {
                    $orderParcel = $orderParcelCollection->getFirstItem();
                    $orderParcel->setData('tracking_codes_send', $row['send_trcode']);
                    $orderParcel->setData('tracking_codes_return', $row['return_trcode']);
                    if ($orderParcel->getStatus() != 1) {
                        $orderParcel->setData('status', 1);
                    }
                    //$format = $orderParcel->getCardFormat()?$orderParcel->getCardFormat():$defaultFormat;
                    //$size = $orderParcel->getCardSize()?$orderParcel->getCardSize():$defaultSize;
                    //send
                    if ($row['send_trcode']) {
                        $fileName = 'send_' . $row['send_trcode'] . ".{$format}";
                        $pdfContent = $row['send_card'];
                        if ($format == 'pdf') {
                            //Mage::log($row['send_card'], null, "shipfunk.log", true);
                            //Mage::log(base64_decode($row['send_card']), null, "shipfunk.log", true);
                            $pdfContent = base64_decode($row['send_card']);
                        }
                        file_put_contents($saveDir . $fileName, $pdfContent);
                    }
                    //return
                    if ($row['return_trcode']) {
                        $fileName = 'return_' . $row['return_trcode'] . ".{$format}";
                        $pdfContent = $row['return_card'];
                        if ($format == 'pdf') {
                            $pdfContent = base64_decode($row['return_card']);
                        }
                        file_put_contents($saveDir . $fileName, $pdfContent);
                    }
                    $orderParcel->save();
                }
            }
        }
    }

    public function shipfunk_order_parcels($orderId, $parcels, $forceFlush = false) {
        $format = Mage::getStoreConfig('shipfunk/package_card/package_card_types');
        $size = Mage::getStoreConfig('shipfunk/package_card/package_card_sizes');
        $firstParcel = $this->getFirstParcel($orderId);
        if ($firstParcel) {
            $format = $firstParcel['card_format'];
            $size = $firstParcel['card_size'];
        }
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        //save data to shipfunk_order_parcels
        //1,delete all
        $binds = array(
            'orderId' => $orderId
        );
        if($forceFlush){
            $sql="DELETE FROM `shipfunk_order_parcels` WHERE order_id = :orderId";
        }else{
            $sql="DELETE FROM `shipfunk_order_parcels` WHERE order_id = :orderId and status = 1";
        }
        $writeConnection->query($sql,$binds);
        
        //2,insert
        $result = array();
        $saveDir = Mage::getBaseDir('var') . '/export/shipfunk/package_cards/';
        if (!is_dir($saveDir)) {
            @mkdir($saveDir, 0777, true);
            @chmod($saveDir, 0777);
        }

        foreach ($parcels as $row) {
            if (isset($row->code)) {
                //Mage::log($row->id."-".$row->code, null, "shipfunk.log", true);
                $result[] = array(
                    'parcel_id' => $row->id,
                    'order_id' => $orderId,
                    'code' => $row->code,
                    'contents' => $row->contents,
                    'weight_unit' => $row->weight->unit,
                    'weight_amount' => $row->weight->amount,
                    'dimensions_unit' => $row->dimensions->unit,
                    'dimensions_width' => $row->dimensions->width,
                    'dimensions_depth' => $row->dimensions->depth,
                    'dimensions_height' => $row->dimensions->height,
                    'monetary_value' => $row->value,
                    'fragile' => $row->fragile,
                    'tracking_codes_send' => $row->tracking_codes->send,
                    'tracking_codes_return' => $row->tracking_codes->return,
                    'card_format' => $format,
                    'card_size' => $size,
                    'warehouse' => $row->warehouse,
                    'status' => 1
                );
            }
            if (isset($row->package_cards) && $row->package_cards) {
                //send
                if (isset($row->package_cards->send) && !empty($row->package_cards->send)) {
                    //Mage::log($row->package_cards, null, "shipfunk.log", true);
                    //Mage::log($format, null, "shipfunk.log", true);
                    $fileName = 'send_' . $row->tracking_codes->send . ".{$format}";
                    $pdfContent = $row->package_cards->send;
                    if ($format == 'pdf') {
                        $pdfContent = base64_decode($row->package_cards->send);
                    }
                    file_put_contents($saveDir . $fileName, $pdfContent);
                }
                //return
                if (isset($row->package_cards->return) && !empty($row->package_cards->return)) {
                    $fileName = 'return_' . $row->tracking_codes->return . ".{$format}";
                    $pdfContent = $row->package_cards->return;
                    if ($format == 'pdf') {
                        $pdfContent = base64_decode($row->package_cards->return);
                    }
                    file_put_contents($saveDir . $fileName, $pdfContent);
                }
            }
        }
        if ($result) {
            $writeConnection->insertMultiple('shipfunk_order_parcels', $result);
        }
    }

    public function setOrderStatus($order, $orderTempId, $status) {
        //Mage::log("setOrderStatus fired", null, "shipfunk.log", true);
        $shipfunkApi = Mage::helper('shipfunk')->getShipfunkApiInfo();
        $apiUrl = $shipfunkApi['url'];
        $apiKey = $shipfunkApi['key'];
        $finalOrderid = $order->getIncrementId();
//         if($order->getState() == 'new'){
//             $status = "placed"; //OR: cancelled
//         }elseif ($order->getState() == 'canceled'){
//             $status = "cancelled";
//         }else{
//             return false;
//         }
        //Mage::log("zhz-status:{$status}");
        $data = array(
            "query" => array(
                "webshop" => array(
                    "api_key" => $apiKey
                ),
                "order" => array(
                    "status" => $status,
                    "final_orderid" => $finalOrderid
                )
            )
        );
        $dataString = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . "set_order_status/true/json/json/" . $orderTempId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$apiKey}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "sf_set_order_status={$dataString}");
        $server_output = curl_exec($ch);
        curl_close($ch);
        //Mage::log($data, null, "shipfunk.log", true);
        //Mage::log($server_output, null, "shipfunk.log", true);
        $res = json_decode($server_output);
        if (isset($res->response->Code) && $res->response->Code) {
            //success
            return true;
        } else {
            //send email
            if (isset($res->response->Code) && $res->response->Code) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res->response->Message}";
            } else {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: API call failed";
            }
            $subject = "Shipfunk Error Report: Set Order Status Failed";
            $this->sendErrorEmail($subject, $message);
            return false;
        }
    }

    public function setCustomerDetails($address, $finalOrderid) {
        $shipfunkApi = Mage::helper('shipfunk')->getShipfunkApiInfo();
        $apiUrl = $shipfunkApi['url'];
        $apiKey = $shipfunkApi['key'];
        $data = array(
            "query" => array(
                "webshop" => array(
                    "api_key" => $apiKey
                ),
                "order" => array(
                    "return_cards" => 0
                ),
                "customer" => array(
                    "first_name" => urlencode($address->getFirstname()),
                    "last_name" => urlencode($address->getLastname()),
                    "street_address" => urlencode(implode('. ', $address->getStreet())),
                    "postal_code" => $address->getPostcode(),
                    "city" => urlencode($address->getCity()),
                    "country" => $address->getCountryId(),
                    "company" => urlencode($address->getCompany()),
                    "phone" => $address->getTelephone(),
                    "email" => urlencode($address->getEmail())
                )
            )
        );
        $dataString = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl . "set_customer_details/true/json/json/" . $finalOrderid);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: {$apiKey}"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "sf_set_customer_details={$dataString}");
        $server_output = curl_exec($ch);
        curl_close($ch);
        /* Mage::log($finalOrderid, null, "shipfunk.log", true);
          Mage::log($data, null, "shipfunk.log", true);
          Mage::log($server_output, null, "shipfunk.log", true); */
        $res = json_decode($server_output);
        if (isset($res->response->Code) && $res->response->Code) {
            //success
            return true;
        } else {
            //send email
            if (isset($res->response->Message) && $res->response->Message) {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: {$res->response->Message}";
            } else {
                $message = "Order number: #{$finalOrderid} \r\n Failure reason: API call failed";
            }
            $subject = "Shipfunk Error Report: Set Customer Details Failed";
            $this->sendErrorEmail($subject, $message);
            return false;
        }
    }

    public function sendErrorEmail($subject, $message) {
        $from = Mage::getStoreConfig('trans_email/ident_sales/name') . ' <' . Mage::getStoreConfig('trans_email/ident_sales/email') . '>';
        $headers = "From: $from";
        $toArray = Mage::getStoreConfig('shipfunk/base/error_report_receivers');
        if ($toArray) {
            $toArray = explode(',', $toArray);
            foreach ((array) $toArray as $to) {
                mail($to, $subject, $message, $headers);
            }
        }
    }

    public function getFirstParcel($orderId) {
        $parcelCollection = Mage::getModel('shipfunk/orderParcels')->getCollection();
        $parcelCollection->addFieldToFilter('order_id', array('eq' => $orderId));
        //$parcelCollection->addFieldToFilter('status',array('eq'=>1));
        $parcelCollection->getSelect()->order('id asc')->limit(1);
        //echo $parcelCollection->getSelect();die;
        if ($parcelCollection->getSize() > 0) {
            return $parcelCollection->getFirstItem();
        }
        return false;
    }

}

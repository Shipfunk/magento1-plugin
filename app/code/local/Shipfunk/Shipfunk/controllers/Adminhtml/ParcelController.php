<?php

class Shipfunk_Shipfunk_Adminhtml_ParcelController extends Mage_Adminhtml_Controller_Action {

    protected function _initOrder() {
        $order = false;
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
        }
        Mage::register('current_order', $order);
        return $order;
    }

    public function removeParcelAction() {
        $trackId = $this->getRequest()->getParam('track_id');
        $orderId = $this->getRequest()->getParam('order_id');
        $track = Mage::getModel('shipfunk/orderParcels')->load($trackId);
        if ($track->getId()) {
            try {
                $order = $this->_initOrder();
                if ($order) {
                    $finalOrderid = $order->getIncrementId();
                    $res = Mage::helper('shipfunk/api')->deleteParcels($finalOrderid, $track->getParcelId(), $track->getCode(), $track->getTrackingCodesSend());
                    if ($res) {
                        $track->delete();
                    }
                    Mage::helper("shipfunk/api")->getParcels($finalOrderid);
                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipfunk_sales_order_parcels')->toHtml();
                } else {
                    $response = array(
                        'error' => true,
                        'message' => $this->__('Cannot initialize shipment for delete tracking number.'),
                    );
                }
            } catch (Exception $e) {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Cannot delete tracking number.'),
                );
            }
        } else {
            $response = array(
                'error' => true,
                'message' => $this->__('Cannot load track with retrieving identifier.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function editParcelAction() {
        try {
            $parceData = $this->getRequest()->getPost();
            $trackId = $this->getRequest()->getParam('track_id');
            $track = Mage::getModel('shipfunk/orderParcels')->load($trackId);
            if ($track->getId()) {
                $order = $this->_initOrder();
                if ($track['status'] != 1) {
                    $parceData['status'] = 0;
                    $track->setData($parceData)
                        ->save();
                } else {
                    $finalOrderid = $order->getIncrementId();
                    $parcelId = $track->getParcelId();
                    Mage::helper('shipfunk/api')->editParcels($finalOrderid, $parcelId, $parceData);
                }
                Mage::helper("shipfunk/api")->getParcels($finalOrderid);
                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shipfunk_sales_order_parcels')->toHtml();
            } else {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Cannot edit parcel.'),
                );
            }
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error' => true,
                'message' => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => $this->__('Cannot edit parcel.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function addParcelAction() {
        try {
            $parceData = $this->getRequest()->getPost();
            $order = $this->_initOrder();
            if ($order) {
                //check code
                $incrementId = $order->getIncrementId();
                $orderParcelCollection = Mage::getModel('shipfunk/orderParcels')->getCollection();
                $orderParcelCollection->addFieldToFilter('code', array('eq' => $parceData['code']));
                $orderParcelCollection->addFieldToFilter('order_id', array('eq' => $incrementId));
                if ($orderParcelCollection->getSize() > 0) {
                    $response = array(
                        'error' => true,
                        'message' => $this->__('This code already exists.'),
                    );
                } else {
                    $format = Mage::getStoreConfig('shipfunk/package_card/package_card_types');
                    $size = Mage::getStoreConfig('shipfunk/package_card/package_card_sizes');
                    $firstParcel = Mage::helper('shipfunk/api')->getFirstParcel($incrementId);
                    if ($firstParcel) {
                        $format = $firstParcel['card_format'];
                        $size = $firstParcel['card_size'];
                    }
                    $parceData['status'] = 0;
                    $parceData['order_id'] = $incrementId;
                    $parceData['card_format'] = $format;
                    $parceData['card_size'] = $size;
                    $track = Mage::getModel('shipfunk/orderParcels')
                        ->setData($parceData)
                        ->save();
                    //print_r($parceData);die;
                    $this->loadLayout();
                    $response = $this->getLayout()->getBlock('shipfunk_sales_order_parcels')->toHtml();
                }
            } else {
                $response = array(
                    'error' => true,
                    'message' => $this->__('Cannot add parcel.'),
                );
            }
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error' => true,
                'message' => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => $this->__('Cannot add parcel.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function createNewPackageCardsAction() {
        $order = $this->_initOrder();
        if ($order) {
            $finalOrderid = $order->getIncrementId();
            Mage::helper('shipfunk/api')->createNewPackageCards($finalOrderid);
            $this->_getSession()->addSuccess($this->__('The packageCards has been created.'));
        } else {
            $this->_getSession()->addError($this->__('Cannot create NewPackageCards.'));
        }
        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }

    public function downloadAction() {
        $dir = Mage::getBaseDir('var') . '/export/shipfunk/package_cards/';
        $fileName = $this->getRequest()->getParam('file');
        if(!preg_match("/^(send|return)\_[a-z0-9]+\.(pdf|zpl|html)$/i", $fileName)){
            $this->_getSession()->addError($this->__('Invalid file name.'));
            $this->_redirect('adminhtml/sales_order/index');
            return false;
        }
        //logic codes
        $filepath = $dir . $fileName;
        //start download
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', 'application/force-download')
            ->setHeader('Content-Length', filesize($filepath))
            ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($filepath));
        $this->getResponse()->clearBody();
        $this->getResponse()->sendHeaders();
        readfile($filepath);
        exit;
    }

}

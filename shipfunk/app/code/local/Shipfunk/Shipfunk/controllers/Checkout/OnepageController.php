<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Onepage controller for checkout
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once  Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'OnepageController.php';

class Shipfunk_Shipfunk_Checkout_OnepageController extends Mage_Checkout_OnepageController
{

    /**
     * Save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
    
            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
    
            if (!isset($result['error'])) {
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $shipfunkHtml = $this->getLayout()->createBlock('core/template')->setTemplate('shipfunk/shipping_method/additional.phtml')->toHtml();
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml() . $shipfunkHtml
                    );
    
                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }
    
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
    /**
     * Refreshes the previous step
     * Loads the block corresponding to the current step and sets it
     * in to the response body
     *
     * This function is called from the reloadProgessBlock
     * function from the javascript
     *
     * @return string|null
     */
    public function progressAction()
    {
        // previous step should never be null. We always start with billing and go forward
        $prevStep = $this->getRequest()->getParam('prevStep', false);

        if ($this->_expireAjax() || !$prevStep) {
            return null;
        }

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        /* Load the block belonging to the current step*/
        $update->load('checkout_onepage_progress_' . $prevStep);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        if($prevStep == 'shipping_method'){
            $output .= $this->getLayout()->createBlock('checkout/onepage_progress')->setTemplate('shipfunk/shipping_method/progress.phtml')->toHtml();
        }
        $this->getResponse()->setBody($output);
        return $output;
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $shipfunkHtml = $this->getLayout()->createBlock('core/template')->setTemplate('shipfunk/shipping_method/additional.phtml')->toHtml();
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml() . $shipfunkHtml
                );
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}

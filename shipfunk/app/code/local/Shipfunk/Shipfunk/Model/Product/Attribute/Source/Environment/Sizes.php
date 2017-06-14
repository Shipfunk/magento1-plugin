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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product MAP "Display Actual Price" attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shipfunk_Shipfunk_Model_Product_Attribute_Source_Environment_Sizes
{
public function toOptionArray()
    {
        return array(
            array('value' => 'A4', 'label'=>Mage::helper('shipfunk')->__('A4(pdf/html)')),
            array('value' => 'A5', 'label'=>Mage::helper('shipfunk')->__('A5(pdf/html)')),
            array('value' => '4x6', 'label'=>Mage::helper('shipfunk')->__('4x6(zpl)')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'A4' => Mage::helper('shipfunk')->__('A4(pdf/html)'),
            'A5' => Mage::helper('shipfunk')->__('A5(pdf/html)'),
            '4x6' => Mage::helper('shipfunk')->__('4x6(zpl)'),
        );
    }
}

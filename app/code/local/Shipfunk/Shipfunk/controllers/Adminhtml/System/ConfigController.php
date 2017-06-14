 <?php
require_once "Mage/Adminhtml/controllers/System/ConfigController.php";

class Shipfunk_Shipfunk_Adminhtml_System_ConfigController extends Mage_Adminhtml_System_ConfigController {

 public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        /* @var $session Mage_Adminhtml_Model_Session */

        $groups = $this->getRequest()->getPost('groups');

        if (isset($_FILES['groups']['name']) && is_array($_FILES['groups']['name'])) {
            /**
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach($_FILES['groups']['name'] as $groupName => $group) {
                if (is_array($group)) {
                    foreach ($group['fields'] as $fieldName => $field) {
                        if (!empty($field['value'])) {
                            $groups[$groupName]['fields'][$fieldName] = array('value' => $field['value']);
                        }
                    }
                }
            }
        }

        try {
            if(isset($groups['package_card']['fields'])){
                $packageData=$groups['package_card']['fields'];
                if(($packageData['package_card_sizes']['value']!='4x6' && $packageData['package_card_types']['value']=='zpl')||
                    ($packageData['package_card_sizes']['value']=='4x6' && $packageData['package_card_types']['value']!='zpl')){
                        throw new Exception(Mage::helper('adminhtml')->__('Please select a package card size which matches the type.'));
                }
            }
            if (!$this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
                throw new Exception(Mage::helper('adminhtml')->__('This section is not allowed.'));
            }

            // custom save logic
            $this->_saveSection();
            $section = $this->getRequest()->getParam('section');
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');
            Mage::getSingleton('adminhtml/config_data')
                ->setSection($section)
                ->setWebsite($website)
                ->setStore($store)
                ->setGroups($groups)
                ->save();

            // reinit configuration
            Mage::getConfig()->reinit();
            Mage::dispatchEvent('admin_system_config_section_save_after', array(
                'website' => $website,
                'store'   => $store,
                'section' => $section
            ));
            Mage::app()->reinitStores();

            // website and store codes can be used in event implementation, so set them as well
            Mage::dispatchEvent("admin_system_config_changed_section_{$section}",
                array('website' => $website, 'store' => $store)
            );
            $session->addSuccess(Mage::helper('adminhtml')->__('The configuration has been saved.'));
        }
        catch (Mage_Core_Exception $e) {
            foreach(explode("\n", $e->getMessage()) as $message) {
                $session->addError($message);
            }
        }
        catch (Exception $e) {
            $session->addException($e,
                Mage::helper('adminhtml')->__('An error occurred while saving this configuration:') . ' '
                . $e->getMessage());
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));

        $this->_redirect('*/*/edit', array('_current' => array('section', 'website', 'store')));
    }
    
}
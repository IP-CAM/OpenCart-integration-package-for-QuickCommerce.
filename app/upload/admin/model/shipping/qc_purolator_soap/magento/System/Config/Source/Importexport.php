<?php
class Blogshop_Purolator_Model_System_Config_Source_Importexport
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'Permanent', 'label' => Mage::helper('adminhtml')->__('Permanent')),
            array('value' => 'Temporary', 'label' => Mage::helper('adminhtml')->__('Temporary')),
            array('value' => 'Repair', 'label' => Mage::helper('adminhtml')->__('Repair')),
            array('value' => 'Return', 'label' => Mage::helper('adminhtml')->__('Return')),
        );
    }
}
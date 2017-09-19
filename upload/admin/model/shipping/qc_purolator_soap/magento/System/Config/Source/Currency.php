<?php
class Blogshop_Purolator_Model_System_Config_Source_Currency
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'CAD', 'label' => Mage::helper('adminhtml')->__('CAD')),
            array('value' => 'USD', 'label' => Mage::helper('adminhtml')->__('USD')),
        );
    }
}
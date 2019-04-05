<?php
class Blogshop_Purolator_Model_System_Config_Source_Printertype
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'Thermal', 'label' => Mage::helper('adminhtml')->__('Thermal')),
            array('value' => 'Regular', 'label' => Mage::helper('adminhtml')->__('Regular')),
        );
    }
}
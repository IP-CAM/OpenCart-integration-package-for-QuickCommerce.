<?php
class Blogshop_Purolator_Model_System_Config_Source_Pickuptype
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'DropOff', 'label' => Mage::helper('adminhtml')->__('Drop-Off')),
            array('value' => 'PreScheduled', 'label' => Mage::helper('adminhtml')->__('Pre-Scheduled')),
        );
    }
}
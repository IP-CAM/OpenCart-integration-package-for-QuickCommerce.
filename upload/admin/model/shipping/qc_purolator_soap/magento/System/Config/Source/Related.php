<?php
class Blogshop_Purolator_Model_System_Config_Source_Related
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'NotRelated', 'label' => Mage::helper('adminhtml')->__('Not Related')),
            array('value' => 'Related', 'label' => Mage::helper('adminhtml')->__('Related')),
        );
    }
}
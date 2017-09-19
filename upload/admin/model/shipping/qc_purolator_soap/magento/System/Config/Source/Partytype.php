<?php
class Blogshop_Purolator_Model_System_Config_Source_Partytype
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'Sender', 'label' => Mage::helper('adminhtml')->__('Sender')),
            array('value' => 'Receiver', 'label' => Mage::helper('adminhtml')->__('Receiver')),
            array('value' => 'Buyer', 'label' => Mage::helper('adminhtml')->__('Buyer')),
        );
    }
}
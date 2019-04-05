<?php
class Blogshop_Purolator_Model_Source_Dimensionunits extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	const MM = 'mm';
	const CM = 'cm';
	const IN = 'in';
	const FT = 'ft';
	
    public function toOptionArray()
    {
        $arr = array();
		 $arr[] = array('value'=> self::CM, 'label'=>'cms');
		 $arr[] = array('value'=> self::MM, 'label'=>'mms');
		 $arr[] = array('value'=> self::IN, 'label'=>'inches');
		 $arr[] = array('value'=> self::FT, 'label'=>'Feet');
        return $arr;
    }
	
	public function getAllOptions()
    {
        return $this->toOptionArray();
    }


    public function toOptionHash()
    {
        $source = $this->_getSource();
        return $source ? $source->toOptionHash() : array();
    }

}
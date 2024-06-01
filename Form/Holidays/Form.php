<?php

class Attendance_Form_Holidays_Form extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/attendance/holidays/editpost"))
            ->setAttrib("id", "form-add-holidays");
        
        self::addClass("create", $this); 

        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $this->addSimpleText('name', p__('attendance', 'Name'))->setRequired(true);
        
        $valid_from = $this->addSimpleDatetimepicker(
            'valid_from', 
            p__('attendance', 'From'), 
            false, 
            Siberian_Form_Abstract::DATEPICKER
        )->setRequired(true);
       // $valid_from->addValidator(new Siberian_Form_Validate_DateGreaterThanToday(), true);

        $valid_until = $this->addSimpleDatetimepicker(
            'valid_until', 
            p__('attendance', 'To'), 
            false, 
            Siberian_Form_Abstract::DATEPICKER
        )->setRequired(true);
        //$valid_until->addValidator(new Siberian_Form_Validate_DateGreaterThanToday(), true);

       $description = $this->addSimpleTextarea('description', p__('attendance', 'Description'));
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }

   
}
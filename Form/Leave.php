<?php

class Attendance_Form_Leave extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/attendance/leave/editpost"))
            ->setAttrib("id", "form-add-attendance")
            ->addNav("nav-add-attendance", "Submit");
        
        self::addClass("create", $this); 
        
        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $this->addSimpleText('name', p__('attendance', 'Leave Type'))->setRequired(true);
        $this->addSimpleSlider('entitlement ', p__('attendance', "Entitlement"), array('min' => 1, 'max' => 365, 'step' => 1, 'unit' => p__('attendance', " Days") ), true)->setRequired(true);

        $this->addSimpleCheckbox('is_carry_forword', p__('attendance', 'Carry to next financial year.'));
        $this->addSimpleHtml("time_duration", '');
        
        $this->addSimpleCheckbox('status', p__('attendance', 'Active'));
    }

     public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  

}
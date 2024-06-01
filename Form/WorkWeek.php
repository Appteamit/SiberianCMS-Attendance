<?php

class Attendance_Form_WorkWeek extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/attendance/application/editworkweekpost"))
            ->setAttrib("id", "form-add-attendance");
        
        self::addClass("create", $this); 

        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $this->addSimpleSelect(
            "monday",
            p__("attendance", "Monday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);

        $this->addSimpleSelect(
            "tuesday",
            p__("attendance", "Tuesday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);

        $this->addSimpleSelect(
            "wednesday",
            p__("attendance", "Wednesday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);

        $this->addSimpleSelect(
            "thursday",
            p__("attendance", "Thursday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);

        $this->addSimpleSelect(
            "friday",
            p__("attendance", "Friday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);

        $this->addSimpleSelect(
            "saturday",
            p__("attendance", "Saturday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);

        $this->addSimpleSelect(
            "sunday",
            p__("attendance", "Sunday"),
            [   "0" => p__("attendance", "Non-Working Day"),
                "1" => p__("attendance", "Working Day"),
            ])->setRequired(true);


   }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }

   
}
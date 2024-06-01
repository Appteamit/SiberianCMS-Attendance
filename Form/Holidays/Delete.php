<?php

/**
 * Class Attendance_Form_Holidays_Delete
 */
class Attendance_Form_Holidays_Delete extends Siberian_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path("/attendance/holidays/delete"))
            ->setAttrib("id", "form-delete-attendance-holidays")
            ->setConfirmText("You are about to remove this holidays ! Are you sure ?");
        ;

        /** Bind as a delete form */
        self::addClass("delete", $this);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('attendance_holidays')
            ->where('attendance_holidays.id = :value')
        ;

        $provider_id = $this->addSimpleHidden("id", p__('attendance', 'Holidays'));
        $provider_id->addValidator("Db_RecordExists", true, $select);
        $provider_id->setMinimalDecorator();

        $mini_submit = $this->addMiniSubmit();
    }
}
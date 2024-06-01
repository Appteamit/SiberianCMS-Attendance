<?php

/**
 * Class Attendance_Form_Leave_Delete
 */
class Attendance_Form_Leave_Delete extends Siberian_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path("/attendance/leave/deleteleave"))
            ->setAttrib("id", "form-delete-attendance-leave")
            ->setConfirmText("You are about to remove this leave type ! Are you sure ?");
        ;

        /** Bind as a delete form */
        self::addClass("delete", $this);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('attendance_leave_types')
            ->where('attendance_leave_types.id = :value')
        ;

        $provider_id = $this->addSimpleHidden("id", p__('attendance', 'Leave Type'));
        $provider_id->addValidator("Db_RecordExists", true, $select);
        $provider_id->setMinimalDecorator();

        $mini_submit = $this->addMiniSubmit();
    }
}
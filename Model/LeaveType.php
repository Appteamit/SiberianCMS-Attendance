<?php

class Attendance_Model_LeaveType extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_LeaveType::class;

    /**
     * @param $valuesId
     * @param array $params
     * @return Attendance_Model_LeaveType[]
     */
    public function findByCustomerId($value_id, $customer_id,  $financial_month)
    {
        return $this->getTable()->findByCustomerId($value_id, $customer_id,  $financial_month);
    }
}
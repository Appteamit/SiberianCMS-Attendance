<?php

class Attendance_Model_Customers extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_Customers::class;

  /**
     * @param $datas
     * @return mixed
     */
    public function findAllForApp($value_id, $params = [])
    {
        return $this->getTable()->findAllForApp($value_id, $params);
    }

    /**
     * @param $datas
     * @return mixed
     */
    public function findAttendanceByMonth($value_id,  $customerId, $month, $year)
    {
        return $this->getTable()->findAttendanceByMonth($value_id,  $customerId, $month, $year);
    }

  /**
     * @param $datas
     * @return mixed
     */
    public function countAllForApp($value_id, $params = [])
    {
        return $this->getTable()->countAllForApp($value_id, $params);
    }


    /**
     * @param $datas
     * @return mixed
     */
    public function totalPresentDayByCustomer($customer_id, $params = [])
    {
        return $this->getTable()->totalPresentDayByCustomer($customer_id, $params);
    }

}
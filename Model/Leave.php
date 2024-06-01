<?php

class Attendance_Model_Leave extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_Leave::class;

    /**
     * @param $customer_id, $date
     * @return mixed
     */
    public function isBetweenLeaveDate($customer_id, $date)
    {
        return $this->getTable()->isBetweenLeaveDate($customer_id, $date);
    }

     /**
     * @param $valuesId
     * @param array $params
     * @return Ewallet_Model_History[]
     */
    public function findByValueId($valuesId, $params = [])
    {
        return $this->getTable()->findByValueId($valuesId, $params);
    }

    /**
     * @param $valuesId
     * @param array $params
     * @return Ewallet_Model_History[]
     */
    public function countAllForApp($valuesId, $params = [])
    {
        return $this->getTable()->countAllForApp($valuesId, $params);
    }


}
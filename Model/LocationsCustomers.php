<?php

class Attendance_Model_LocationsCustomers extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_LocationsCustomers::class;



     /**
     * @param $datas
     * @return mixed
     */
    public function findAllForApp($app_id, $location_id, $params = [])
    {
        return $this->getTable()->findAllForApp($app_id, $location_id, $params);
    }

   /**
     * @param $datas
     * @return mixed
     */
    public function countAllForApp($app_id, $location_id, $params = [])
    {
        return $this->getTable()->countAllForApp($app_id, $location_id, $params);
    }



}
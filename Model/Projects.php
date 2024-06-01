<?php

class Attendance_Model_Projects extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_Projects::class;



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
    public function findAllActiveProject($value_id, $params = [])
    {
        return $this->getTable()->findAllActiveProject($value_id, $params);
    }

   /**
     * @param $datas
     * @return mixed
     */
    public function countAllForApp($value_id, $params = [])
    {
        return $this->getTable()->countAllForApp($value_id, $params);
    }



}
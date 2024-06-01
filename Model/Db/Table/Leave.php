<?php

class Attendance_Model_Db_Table_Leave extends Core_Model_Db_Table {
    protected $_name                    = "attendance_customer_leave";
    protected $_primary                 = "id";


    /**
     * @param $customer_id, $date
     */
    public function isBetweenLeaveDate($customer_id, $date)
    {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [ 
            	 'COUNT(main.id)'
                ])
            ->where('main.start_date <= ?', $date)
            ->where('main.end_date >= ?', $date)
            ->where('main.status >= ?', 0)
            ->where('main.customer_id = ?', $customer_id);

         return $this->_db->fetchCol($select);
    }

     /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByValueId($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id as leave_id",
                "customer_id",
                "start_date",
                "end_date",
                "leave_type_id",
                "total_days",
                "comment",
                "status",
                "admin_id",
                "created_at",
            ]);

            $select->where("main.value_id = ?", $value_id);           

            $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.email',  'c.firstname', 'c.lastname']);

            $select->joinLeft(['t' => 'attendance_leave_types'], 't.id = main.leave_type_id', ['t.name as leave_type']);

           if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
                $select->limit($params["limit"], $params["offset"]);
            }

            if (array_key_exists("filter", $params)) {
                $select->where("(t.name LIKE ? OR c.firstname LIKE ? OR c.lastname LIKE ? OR c.nickname LIKE ? OR c.email LIKE ?)", "%" . $params["filter"] . "%");
            }  
            
            if (array_key_exists("startdate", $params) && !empty($params["startdate"])) {
                $select->where("main.start_date >= ?",  $params["startdate"].' 00:00:00');
            }

            if (array_key_exists("status", $params) && $params["status"] > -1) {
                $select->where("main.status = ?",  $params["status"]);
            }

            if (array_key_exists("enddate", $params) && !empty($params["enddate"])) {
                $select->where("main.end_date <= ?",  $params["enddate"].' 23:59:59');
            }

            $select->order('main.created_at DESC');          
            return $this->toModelClass($this->_db->fetchAll($select));

        }


  /**
     * @param $value_id
     */
    public function countAllForApp($value_id, $params = [])
    {
        $select =$this->_db->select()
            ->from(['main' => $this->_name], [ 
                 'COUNT(main.id)'
                ]); 

           $select->where("main.value_id = ?", $value_id);           

            $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.email',  'c.firstname', 'c.lastname']);

            $select->joinLeft(['t' => 'attendance_leave_types'], 't.id = main.leave_type_id', ['t.name']);

           if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
                $select->limit($params["limit"], $params["offset"]);
            }
            if (array_key_exists("status", $params) && !empty($params["status"])) {
                $select->where("main.status = ?",  $params["status"]);
            }

            if (array_key_exists("filter", $params)) {
                $select->where("(t.name LIKE ? OR c.firstname LIKE ? OR c.lastname LIKE ? OR c.nickname LIKE ? OR c.email LIKE ?)", "%" . $params["filter"] . "%");
            }
  
            $select->order('main.created_at DESC');

            if (array_key_exists("startdate", $params) && !empty($params["startdate"])) {
                $select->where("main.start_date >= ?",  $params["startdate"].' 00:00:00');
            }

            if (array_key_exists("enddate", $params) && !empty($params["enddate"])) {
                $select->where("main.end_date <= ?",  $params["enddate"].' 23:59:59');
            }

        return $this->_db->fetchCol($select);
    }
    
    
}
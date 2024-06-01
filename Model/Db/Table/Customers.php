<?php

class Attendance_Model_Db_Table_Customers extends Core_Model_Db_Table {
    protected $_name                    = "attendance_customers";
    protected $_primary                 = "id";
    
       /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findAllForApp($value_id, $params = []) {
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "customer_id",
                "startdate",
                "enddate",
                "startlatlong",
                "endlatlong",
                "totaltime",
                "startaddress",
                "endaddress",
                "status",
                "note",
                "created_at",
            ]);

          $select->where("main.value_id = ?", $value_id);           

          $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.customer_id as customerId', 'c.email',  'c.firstname', 'c.lastname' , 'c.image']);

          $select->joinLeft(['p' => 'attendance_projects'], 'p.id = main.project_id', ['p.title as project_name', 'p.status as project_status', 'p.end_date as project_close_date']);

          $select->joinLeft(['l' => 'attendance_locations'], 'l.id = main.location_id', ['l.name as location_name']);
          
          if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
	            $select->limit($params["limit"], $params["offset"]);
	        }

	        if (array_key_exists("filter", $params)) {
	            $select->where("(c.firstname LIKE ? OR c.lastname LIKE ? OR c.nickname LIKE ? OR c.email LIKE ? OR p.title LIKE ? OR l.name LIKE ?)", "%" . $params["filter"] . "%");
	        }

          if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
	            $orders = [];
	            foreach ($params["sorts"] as $key => $dir) {
	                $order = ($dir == -1) ? "DESC" : "ASC";
	                if($key == 'firstname' || $key == 'email'){
	                	$orders = "c.{$key} {$order}";
	                }else{
	                	$orders = "main.{$key} {$order}";
	                }
	            }	           
	            $select->order($orders);
	        } else {
	            $select->order('main.id DESC');
	        }

          if (array_key_exists("startdate", $params) && !empty($params["startdate"])) {
                $select->where("startdate >= ?",  $params["startdate"].' 00:00:00');
          }

          if (array_key_exists("enddate", $params) && !empty($params["enddate"])) {
                $select->where("enddate <= ?",  $params["enddate"].' 23:59:59');
          }
          
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
                ])
            ->where('main.value_id = ?', $value_id);
  
		    $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.customer_id', ['c.customer_id as customerId', 'c.email',  'c.firstname', 'c.lastname' , 'c.image']);

        $select->joinLeft(['p' => 'attendance_projects'], 'p.id = main.project_id', ['p.title as project_name', 'p.status as project_status', 'p.end_date as project_close_date']);

         $select->joinLeft(['l' => 'attendance_locations'], 'l.id = main.location_id', ['l.name as location_name']);

        if (array_key_exists("filter", $params)) {
          $select->where("(c.firstname LIKE ? OR c.lastname LIKE ? OR c.nickname LIKE ? OR c.email LIKE ? OR p.title LIKE ? OR l.name LIKE ?)", "%" . $params["filter"] . "%");
        }

       if (array_key_exists("startdate", $params) && !empty($params["startdate"])) {
              $select->where("startdate >= ?",  $params["startdate"]);
        }

        if (array_key_exists("enddate", $params) && !empty($params["enddate"])) {
              $select->where("enddate <= ?",  $params["enddate"]);
        }

        return $this->_db->fetchCol($select);
    }

   /**
     * @param $value_id
     */
    public function totalPresentDayByCustomer($customer_id,  $params = [] )
    {
        return $this->_db->fetchAll("SELECT DISTINCT attendance_date FROM attendance_customers WHERE customer_id = ".$customer_id);
    }


      /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findAttendanceByMonth($value_id,  $customerId, $month, $year) {

        $startdate = date('Y-m-d', strtotime("01-".$month."-".$year.""));
        $enddate = date('Y-m-d', strtotime("31-".$month."-".$year.""));

        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "customer_id",
                "startdate",
                "enddate",
                "startlatlong",
                "endlatlong",
                "totaltime",
                "startaddress",
                "endaddress",
                "status",
                "note",
                "created_at",
            ]);

           $select->joinLeft(['p' => 'attendance_projects'], 'p.id = main.project_id', ['p.title as project_name', 'p.status as project_status', 'p.end_date as project_close_date']);

            $select->joinLeft(['l' => 'attendance_locations'], 'l.id = main.location_id', ['l.name as location_name']);

            $select->where("startdate >= ?",  $startdate.' 00:00:00');
            $select->where("enddate <= ?",  $enddate.' 00:00:00');
            $select->where("main.value_id = ?", $value_id);
            $select->where("main.customer_id = ?", $customerId);           
            $select->order('main.created_at DESC');
           
          return $this->_db->fetchAll($select);

        }


}
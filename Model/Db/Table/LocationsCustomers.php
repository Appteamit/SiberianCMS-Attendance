<?php

class Attendance_Model_Db_Table_LocationsCustomers extends Core_Model_Db_Table {
    protected $_name                    = "attendance_locations_customers";
    protected $_primary                 = "id";
    
       /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findAllForApp($app_id, $location_id, $params = []) {
         $select = $this->_db->select()
            ->from(array('main' => 'customer'), array(
                "customer_id",
                "firstname",
                "lastname",
                "email",
                "image",
                "registration_date" => "main.created_at",
                "registration_timestamp" => new Zend_Db_Expr("UNIX_TIMESTAMP(main.created_at)"),
                "is_member" => new Zend_Db_Expr('('.$this->_db->select()->from(array('m'=> $this->_name),array(new Zend_Db_Expr('COUNT(m.id)')))->where('m.customer_id = main.customer_id')->where('m.location_id = ?', $location_id).')'),
            ))
           ->where("main.app_id = ?", $app_id);          
          
          if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
	            $select->limit($params["limit"], $params["offset"]);
	        }

	        if (array_key_exists("filter", $params)) {
	            $select->where("(main.firstname LIKE ?)", "%" . $params["filter"] . "%");
	        }

          if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
	            $orders = [];
	            foreach ($params["sorts"] as $key => $dir) {
	                $order = ($dir == -1) ? "DESC" : "ASC";
	               	$orders = "main.{$key} {$order}";
	           }	           
	            $select->order($orders);
	        } else {
	            $select->order('main.customer_id DESC');
	        }

        return $this->toModelClass($this->_db->fetchAll($select));

   }


    /**
     * @param $value_id
     */
    public function countAllForApp($app_id, $location_id, $params = [])
    {
        $select =$this->_db->select()
            ->from(['main' => 'customer'], [ 
            	 'COUNT(main.customer_id)'
                ])
             ->where("main.is_active = ?", true)
            ->where('main.app_id = ?', $app_id);
        
	      if (array_key_exists("filter", $params)) {
              $select->where("(main.firstname LIKE ?)", "%" . $params["filter"] . "%");
        }
 
        return $this->_db->fetchCol($select);
    }

}
<?php

class Attendance_Model_Db_Table_Locations extends Core_Model_Db_Table {
    protected $_name                    = "attendance_locations";
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
                "qr_code",
                "name",
                "allow_scan",
                "address",
                "status",
                "value_id",
                "created_at",
                "updated_at"
            ]);

          $select->where("main.value_id = ?", $value_id); 
          $select->where("main.status != ?", 2);           
          
          if (array_key_exists("limit", $params) && array_key_exists("offset", $params)) {
	            $select->limit($params["limit"], $params["offset"]);
	        }

	        if (array_key_exists("filter", $params)) {
	            $select->where("(main.name LIKE ?)", "%" . $params["filter"] . "%");
	        }

          if (array_key_exists("sorts", $params) && !empty($params["sorts"])) {
	            $orders = [];
	            foreach ($params["sorts"] as $key => $dir) {
	                $order = ($dir == -1) ? "DESC" : "ASC";
	                if($key == 'name' || $key == 'created_at'){
	                	$orders = "main.{$key} {$order}";
	                }else{
	                	$orders = "main.{$key} {$order}";
	                }
	            }	           
	            $select->order($orders);
	        } else {
	            $select->order('main.id DESC');
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
            
          $select->where("main.status != ?", 2); 

	      if (array_key_exists("filter", $params)) {
              $select->where("(main.name LIKE ?)", "%" . $params["filter"] . "%");
        }
 
        return $this->_db->fetchCol($select);
    }

}
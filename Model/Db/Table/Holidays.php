<?php

class Attendance_Model_Db_Table_Holidays extends Core_Model_Db_Table {
    protected $_name                    = "attendance_holidays";
    protected $_primary                 = "id";

    /**
     * @param $value_id
     */
    public function totalHolidayInCurrentMonth($value_id , $enddate)
    {

        if(empty($enddate)){
             $enddate =  date('Y-m-31');
        } 

        $select = $this->_db->select()
            ->from(['main' => $this->_name], [ 
            	 'SUM(main.total_days)'
                ])
            ->where('main.valid_from >= ?', date('Y-m-01'))
            ->where('main.valid_until <= ?', $enddate)
            ->where('main.value_id = ?', $value_id);

         return $this->_db->fetchCol($select);
    }
    
}
<?php

class Attendance_Model_Db_Table_LeaveType extends Core_Model_Db_Table {
    protected $_name                    = "attendance_leave_types";
    protected $_primary                 = "id";
    

      /**
     * @param $app_id
     * @param int $limit
     * @return array
     */
    public function findByCustomerId($value_id, $customer_id,  $financial_month) {
        $currentMonth = date('m');

        if($financial_month <= $currentMonth){
            $pst = date('Y');
            $pt = date('Y', strtotime('+1 year'));
            $financial_start =  $pst.'-'.$financial_month.'-01  00:00:00';
            $financial_end =  $pt.'-'.$financial_month.'-01  00:00:00';
        }else{
            $pst = date('Y');
            $pt = date('Y', strtotime('-1 year'));
            $financial_start =  $pt.'-'.$financial_month.'-01  00:00:00';
            $financial_end =  $pst.'-'.$financial_month.'-01  00:00:00';
        }
 
        $select = $this->_db->select()
            ->from(['main' => $this->_name], [
            	"id",
              "name",
              "entitlement",
              "is_carry_forword",
              "approved" => new Zend_Db_Expr('('.$this->_db->select()->from(array('l'=> "attendance_customer_leave"),array(new Zend_Db_Expr('SUM(l.total_days)')))->where('l.customer_id = ?' , $customer_id )->where('l.leave_type_id = main.id')->where('l.status = ?', 1)->where('l.start_date >= ?', $financial_start )->where('l.end_date < ?', $financial_end ).')'),
               "pending" => new Zend_Db_Expr('('.$this->_db->select()->from(array('l'=> "attendance_customer_leave"),array(new Zend_Db_Expr('SUM(l.total_days)')))->where('l.customer_id = ?' , $customer_id )->where('l.leave_type_id = main.id')->where('l.status = ?', 0)->where('l.start_date >= ?', $financial_start )->where('l.end_date < ?', $financial_end ).')'),         
            ]);

            $select->where("main.value_id = ?", $value_id);
            $select->where("main.status = ?", 1);              
         
            return $this->_db->fetchAll($select);

        }
}
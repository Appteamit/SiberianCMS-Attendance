<?php

class Attendance_Model_Holidays extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_Holidays::class;


      /**
     * @param $array
     * @return array
     */
    public function countWorkingDays($month, $year, $work_weeks, $day_count = 0)
    {   

    	$count = 0;
        if(!$day_count) {
            $day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }         

        //loop through all days
		for ($i = 1; $i <= $day_count; $i++) {

	        $date = $year.'/'.$month.'/'.$i; //format date
	        $get_name = strtolower(date('l', strtotime($date))); //get week day
 
	        if($work_weeks[$get_name] == '1') {
	            $count = $count + 1;
	        }
		}

		return $count;

	}

	/**
     * @param $datas
     * @return mixed
     */
    public function totalHolidayInCurrentMonth($value_id, $date = null)
    {
        return $this->getTable()->totalHolidayInCurrentMonth($value_id, $date);
    }

    


}
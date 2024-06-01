<?php

class Attendance_Model_Attendance extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_Attendance::class;

     /**
     * @param $value_id
     * @return array|bool
     */
    public function getInappStates($value_id)
    {
        
        $inAppStates = [
            [
                "state" => "attendance-home",
                "offline" => false,
                "params" => [
                    "value_id" => $value_id,
                ]             
            ],
        ];

        return $inAppStates;
    }

    /**
     * @return null
     */
    public static function getCurrentValueId()
    {
        $app = self::getApplication();
        if ($app) {
            $options = $app->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() === "attendance") {
                    return $option->getId();
                }
            }
        }
        return null;
    }

    /**
     * @return null
     */
    public static function getCurrent()
    {
        $app = self::getApplication();
        if ($app) {
            $options = $app->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() === "attendance") {
                    return $option;
                }
            }
        }
        return null;
    }
  
    /**
     * @return null
     */
    public static function Autocheckout($cron)
    {
        $data = (new \Attendance_Model_Customers())
            ->findAll(['status' => 1])->toArray();
        
        foreach ($data as $key => $value) {
            $attendance = (new Attendance_Model_Attendance())->find($value['value_id'], "value_id");
            $trackingData = $attendance->getData();
            $auto_checkout_time = (integer) $trackingData['auto_checkout_time']; 
            $then = $value['startdate'];

            $then = new DateTime($then);
            $now = new DateTime();
            $sinceThen = $then->diff($now);
            $diffHours = (integer) $sinceThen->h;

            $now = new Zend_Date();
            $today =  $now->toString('y-MM-dd HH:mm:ss');

            $totalhours = $sinceThen->h < 10 ? '0'.$sinceThen->h : $sinceThen->h;
            $totalmintues = $sinceThen->i < 10 ? '0'.$sinceThen->i : $sinceThen->i;
            $totalseconds = $sinceThen->s < 10 ? '0'.$sinceThen->s : $sinceThen->s;
            $totaltime = $totalhours.' : '.$totalmintues.' : '.$totalseconds;
           
            if($diffHours >= $auto_checkout_time){
                $attendance = (new Attendance_Model_Customers())->find($value['id'], "id")->setEndlatlong('0,0')->setStatus(2)->setTotaltime($totaltime)->setEnddate($today)->save();

            }
        }        
       
        return true;
    }
  

     
}
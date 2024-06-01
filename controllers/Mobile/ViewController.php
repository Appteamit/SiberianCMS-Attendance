<?php

use Siberian\Exception;

/**
 * Class Attendance_Mobile_ViewController
 */
class Attendance_Mobile_ViewController extends Application_Controller_Mobile_Default
{

    /**
     * Fetch all settings
     *
     */
    public function findallAction()
    {
        try {
        
        if($value_id = $this->getRequest()->getParam('value_id')){
            $param = $this->getRequest()->getBodyParams();

             /* Settings */
            $settingsModel = (new Attendance_Model_Attendance())
                                    ->find($value_id, "value_id");
            $sv =  $settingsModel->getData();            
            $timezones = DateTimeZone::listIdentifiers();
            $time_zone = $timezones[$sv['timezone']]; 
             
            $is_working_day = 1;
            $is_checked_in = false;
           // $currentDateTime = new Zend_Date(strtotime($param['currentDateTime']), false, new Zend_Locale('en_US'));

            $currentDateTime = new DateTime("now", new DateTimeZone($time_zone));
            $current_date_time_with_timezone = $currentDateTime->format('Y-m-d H:i:s');

            $param['currentDateTime'] = $current_date_time_with_timezone;

            $currentDate = $currentDateTime->format('l, jS F Y');
            $currentMonthYear = $currentDateTime->format('y-M');
            $day = strtolower($currentDateTime->format('l'));
            $month = strtolower($currentDateTime->format('M'));

            /*Working Day*/
            $wrokingWeek = (new Attendance_Model_WorkWeek())
                                    ->find($value_id, "value_id")->getData();
            $is_working_day = (integer) $wrokingWeek[$day];// Week days status   

            /*Othere Holidays*/
            $holidayToday = (new Attendance_Model_Holidays())->findAll(["value_id = ?" => $value_id, 'valid_from <= ? ' => $currentDate, 'valid_until >= ? ' => $currentDate, 'status' => 1 ])->toArray();

            if($is_working_day === 1 &&  count($holidayToday) > 0 ){
                $is_working_day = 0; // When Holiday
            }

            $totalHolidayInCurrentMonth = (new Attendance_Model_Holidays())->totalHolidayInCurrentMonth($value_id);
            $totalHolidayInCurrentMonth = $totalHolidayInCurrentMonth[0];

            $totalHolidayInCurrentMonthTillNow = (new Attendance_Model_Holidays())->totalHolidayInCurrentMonth($value_id, date('Y-m-d'));
            $totalHolidayInCurrentMonthTillNow = $totalHolidayInCurrentMonthTillNow[0];

            /*Upcoming Holidays*/
            $holidaysModel = (new Attendance_Model_Holidays())->findAll(["value_id = ?" => $value_id, 'valid_until >= ? ' => $currentDate, 'status' => 1 ], 'valid_from ASC');

            /*Running tracking status*/
            $customerId = $this->_getCustomerId(true);
            $timetracking = (new Attendance_Model_Customers())->find(array('customer_id' => $customerId, 'status' => 1));
            $timetracking = $timetracking->getData();

            $trackInfo = [];
            if(!empty($timetracking)){
                $trackInfo['tracking_id'] = (integer) $timetracking['id']; 
                $trackInfo['startdate']=date('Y-m-d H:i:s',strtotime($timetracking['startdate']));
                $trackInfo['starttime'] = date('g:i A',  strtotime($timetracking['startdate']));
                $is_checked_in = true;
            }                       

            $holidays = [];
            foreach ($holidaysModel as $key => $value) {
                $val = $value->getData();
                $start = strtotime($val['valid_from']);
                $end = strtotime($val['valid_until']);
                $days_between = ceil(abs($end - $start) / 86400) + 1;               
                $val['valid_from'] =  date("d M, Y (D)", strtotime($val['valid_from']));
                $val['valid_until'] = date("d M, Y (D)", strtotime($val['valid_until']));
                $val['days']  = $days_between; 

                $holidays[] = $val;               
            }

           
            
            $settings = ['check_in_out_enable' => (integer) $sv['check_in_out_enable'],
                        'financial_year' => (integer) $sv['financial_year'],
                        'fixed_working_hours' => (integer) $sv['fixed_working_hours'],
                        'leave_apply_enable' => (integer) $sv['leave_apply_enable'],
                        'leave_enable' => (integer) $sv['leave_enable'],
                        'remaining_leave_bar_color' => $sv['remaining_leave_bar_color'],
                        'total_leave_bar_color' => $sv['total_leave_bar_color'],
                        'inprogress_leave_bar_color' => $sv['inprogress_leave_bar_color'],
                        'check_in_history_enable' => (integer) $sv['check_in_history_enable'],
                        'date_format' => $sv['date_format'],
                        'time_format' => $sv['time_format'],
                        'is_message_admin' => (integer) $sv['is_message_admin'],
                        'holiday_enable' => (integer) $sv['holiday_enable'],
                        'qr_scan_enable' => (integer) $sv['qr_scan_enable'],
                        'is_checkout_enable' => (integer) $sv['is_checkout_enable'],
                        'qr_checkin_note' => (string) $sv['qr_checkin_note'],
                        'timezone' => $time_zone,
                        'currentDateTime' => $currentDateTime,
                        'project_tracking_enable' => (integer) $sv['project_tracking_enable']
                    ];

            /*Current Address*/
            $address = $this->geoReverse($param['latitude'], $param['longitude'] , $this->getApplication()->getGooglemapsKey());

            /* Attendance Count*/
            $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));

            $totalWorkingDaysInCurrentMonth = (new Attendance_Model_Holidays)->countWorkingDays(date('m'), date('Y'), $wrokingWeek);

            $totalNonWorkingDaysInCurrentMonthTillNow = (new Attendance_Model_Holidays)->countWorkingDays(date('m'), date('Y'), $wrokingWeek, date('d'));

            $customerPresent = (new Attendance_Model_Customers)->totalPresentDayByCustomer($customerId, ['startfrom' => date('Y-m-01  00:00:00'), 'startto' =>  date('Y-m-31  00:00:00')]);
 
            $financial_month = $settings['financial_year'];
            $customerLeavesTypes = (new Attendance_Model_LeaveType())->findByCustomerId($value_id, $customerId, $financial_month);
           
            $customerLeavesTypesJson = [];
            foreach ($customerLeavesTypes as $key => $value) {
                $value['pending'] = (integer) $value['pending'];
                $value['approved'] = (integer) $value['approved'];
                $value['entitlement'] = (integer) $value['entitlement'];
                $value['id'] = (integer) $value['id'];
                $value['remaining'] = (integer) ($value['entitlement'] - ($value['pending'] + $value['approved']));
                $value['used_pecentage'] = (integer) (($value['approved']*100)/$value['entitlement']);
                $value['pending_pecentage'] = (integer) (($value['pending']*100)/$value['entitlement']);
                $customerLeavesTypesJson[] = $value;               
            }
 

            $unreadMessage = 0;
            if(!empty($customerId) && $customerId > 0){
                $messageModel = new Attendance_Model_Message();
                $unreadMessage = $messageModel->unreadMessageCountCustomer($value_id, $customerId);
                $unreadMessage = $unreadMessage['num'];
            }


            $projects = (new Attendance_Model_Projects())
                ->findAllActiveProject($value_id, $project_params);

            $payload = [
                    'success' => true,
                    'page_title' => (string) $this->getCurrentOptionValue()->getTabbarName(),
                    'param' => $param,
                    'wrokingWeek' => $wrokingWeek,
                    'address' => $address['address'],
                    'is_working_day' => (integer) $is_working_day,
                    'holidays' => $holidays,
                    'holidayToday' => count($holidayToday),
                    'is_checked_in' => $is_checked_in,
                    'month' => ucfirst($month),
                    'day' => $day,
                    'currentDate' => $currentDate,
                    'timetracking' => $trackInfo,
                    'settings' => $settings,
                    'leavetype' => $customerLeavesTypesJson,
                    'totalDaysInCurrentMonth' => (integer) $totalDaysInMonth,
                    'totalWorkingDaysInCurrentMonth' => (integer) $totalWorkingDaysInCurrentMonth - $totalHolidayInCurrentMonth,
                    'totalNonWorkingDaysInCurrentMonth' => (integer) (($totalDaysInMonth - $totalWorkingDaysInCurrentMonth) + $totalHolidayInCurrentMonth),
                    'customerPresent' => $customerPresent,                    
                    'totalNonWorkingDaysInCurrentMonthTillNow' => (integer)$totalNonWorkingDaysInCurrentMonthTillNow + (integer) $totalHolidayInCurrentMonthTillNow ,
                    'unreadMessage' => $unreadMessage,
                    'projects' => $projects                 
               ];             
        }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }
        
        $this->_sendJson($payload);
    }

    /**
     * Save Start
     *
     */
    public function saveStartAction()
    {
       try {

        if($param = $this->getRequest()->getBodyParams()){

            $customerId = $this->_getCustomerId(true);
            $timezones = DateTimeZone::listIdentifiers();
            $settingsModel = (new Attendance_Model_Attendance())
                                    ->find($param['value_id'], "value_id");
            $sv =  $settingsModel->getData();
            $time_zone = $timezones[$sv['timezone']]; 

            $currentDateTime = new DateTime("now", new DateTimeZone($time_zone));
            $today = $currentDateTime->format('Y-m-d H:i:s');           

            // $now = new Zend_Date(strtotime($param['startdate']), false, new Zend_Locale('en_US'));
            // $today =  $now->toString('y-MM-dd HH:mm:ss');
            $lat_long = $param['latitude'].','.$param['longitude'];

            $address = $this->geoReverse($param['latitude'], $param['longitude'] , $this->getApplication()->getGooglemapsKey());

             /* Settings */
          
            $is_checkout_enable = (integer) $sv['is_checkout_enable'];
            $project_id = empty($param['project_id']) ? 0 : $param['project_id'];
            $location_id = empty($param['location_id']) ? 0 : $param['location_id'];
            $note = empty($param['note']) ? "" : $param['note'];
            
            if(!empty($customerId)){
               $Timetracking = (new Attendance_Model_Customers())
                ->setValueId($param['value_id'])
                ->setCustomerId($customerId)
                ->setStartdate($today)
                ->setAttendanceDate($currentDateTime->format('y-MM-dd'))
                ->setStartlatlong($lat_long)
                ->setProjectId($project_id)
                ->setLocationId($location_id)
                ->setNote($note)
                ->setStartaddress($address['address']);

                if($is_checkout_enable == 0){
                    $Timetracking->setEnddate($today);
                    $Timetracking->setEndlatlong($lat_long);
                    $Timetracking->setTotaltime('00:00:00');
                    $Timetracking->setStatus(2);
                    $Timetracking->setEndaddress($address['address']);
                }

                $Timetracking->save();

                $trackInfo = [];
                if($Timetracking->getId()){
                    $trackInfo['tracking_id'] = (integer) $Timetracking->getId(); 
                    $trackInfo['startdate'] = $currentDateTime->format('Y-m-d '); //date('Y-m-d H:i:s', strtotime($param['startdate']));
                    $trackInfo['starttime'] = date('g:i A',  strtotime($currentDateTime->format('H:i:s')));
                    
                }

                $payload = [
                    'success' => true,
                    'message' => p__('attendance', 'Save Successfully!'),                  
                    'tracking_id' =>  (integer) $Timetracking->getId(),
                    'timetracking' => $trackInfo,
                    'is_checkout_enable' => $is_checkout_enable
                ];  
           }
        }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }
        $this->_sendJson($payload);
    }
    /**
     * Save End
     *
     */
    public function saveEndAction()
    {
        try {

        if($param = $this->getRequest()->getBodyParams()){
            
             $timezones = DateTimeZone::listIdentifiers();
            $settingsModel = (new Attendance_Model_Attendance())
                                    ->find($param['value_id'], "value_id");
            $sv =  $settingsModel->getData();
            $time_zone = $timezones[$sv['timezone']]; 

            $currentDateTime = new DateTime("now", new DateTimeZone($time_zone));
            $today = $currentDateTime->format('Y-m-d H:i:s');   
            
           // $now = new Zend_Date(strtotime($param['enddate']), false, new Zend_Locale('en_US'));
            //$today =  $now->toString('y-MM-dd HH:mm:ss');
            $lat_long = $param['latitude'].','.$param['longitude'];

            $address = $this->geoReverse($param['latitude'], $param['longitude'] , $this->getApplication()->getGooglemapsKey());
           
            if(!empty($param['tracking_id'])){

               $Timetracking = (new Attendance_Model_Customers())
                ->find(['id' => $param['tracking_id']])
                ->setEnddate($today)
                ->setEndlatlong($lat_long)
                ->setTotaltime($param['totaltime'])
                ->setStatus(2)
                ->setEndaddress($address['address'])
                ->save();

                $payload = [
                    'success' => true,
                    'message' => p__('attendance', 'Save Successfully!'),                  
               ];  
            }else{
                $payload = [
                    "error" => true,
                    "message" =>  p__('attendance', 'Tracking id must be required!'),
                ];
            }
        }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }
        $this->_sendJson($payload);
    }

    /**
     * @param bool $throw
     * @return mixed|null
     * @throws Exception
     * @throws Zend_Session_Exception
     */
    private function _getCustomerId($throw = true)
    {
        $request = $this->getRequest();
        $session = $this->getSession();
        $customerId = $session->getCustomerId();
        if ($throw && empty($customerId)) {
            throw new Exception(p__('attendance', 'Customer login required!'));
        }
        return $customerId;
    }

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public static function geoReverse($latitude, $longitude, $apiKey = null)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latitude .
            ',' . $longitude . '&sensor=true&key=' . $apiKey;
        $decode = Siberian_Json::decode(file_get_contents($url));

        $locality = '';
        $postal_code = '';
        $country = '';
        $country_code = '';
        $addresscomponents = [];
        $result = $decode['results'][0];
        $address_components = $result['address_components'];
        foreach ($address_components as $address_component) {
            $addresscomponents[] = $address_component;

            $type = $address_component['types'][0];
            if ($type === 'locality') {
                $locality = $address_component['long_name'];
            }
            if ($type === 'postal_code') {
                $postal_code = $address_component['long_name'];
            }
            if ($type === 'country') {
                $country = $address_component['long_name'];
                $country_code = $address_component['short_name'];
            }
        }

        return [
            'locality' => $locality,
            'postal_code' => $postal_code,
            'country' => $country,
            'country_code' => $country_code,
            'address' => $result['formatted_address']
        ];
    }


    public function leaveSubmitAction () {
       
        try {

            if($param = $this->getRequest()->getBodyParams()){
                $customerId = $this->_getCustomerId(true);
                $value_id = $this->getRequest()->getParam('value_id');

                if(empty($param['start_Date']) || empty($param['end_date'])) {
                    throw new Exception(p__('attendance', 'Please select Start date Or End date'));
                }

                $nowStart = new Zend_Date(strtotime($param['start_Date']), false, new Zend_Locale('en_US'));
                $start =  $nowStart->toString('y-MM-dd HH:mm:ss');

                $nowEnd = new Zend_Date(strtotime($param['end_date']), false, new Zend_Locale('en_US'));
                $end =  $nowEnd->toString('y-MM-dd HH:mm:ss');

                if($start > $end){
                    throw new Exception(p__('attendance', 'Please select End date less then Start date'));
                }

                $is_already_apply = 0;
                $isStartBetween = (new Attendance_Model_Leave())->isBetweenLeaveDate($customerId,  $start);
                if((integer) $isStartBetween[0] > 0){
                    $is_already_apply = 1;
                }

                $isEndBetween = (new Attendance_Model_Leave())->isBetweenLeaveDate($customerId,  $end);
                if((integer) $isEndBetween[0] > 0){
                    $is_already_apply = 1;
                }

                if($is_already_apply){
                    throw new Exception(p__('attendance', 'You have already apply for between days'));
                }

                $startStrtotime = strtotime($start);
                $endStrtotime = strtotime($end);
                $total_days = ceil(abs($endStrtotime - $startStrtotime) / 86400) + 1;

                if($param['remaining_leave_value'] < $total_days ){
                    throw new Exception(p__('attendance', 'Insufficient leave balance'));
                }                

                $leave = (new Attendance_Model_Leave())
                            ->setValueId($value_id)
                            ->setCustomerId($customerId)
                            ->setTotalDays($total_days)
                            ->setLeaveTypeId($param['leavetype'])
                            ->setStartDate($start)
                            ->setEndDate($end)
                            ->setComment($param['reason'])
                            ->save();


                $settingsModel = (new Attendance_Model_Attendance())
                                ->find($value_id, "value_id");
                $sv =  $settingsModel->getData();
                $admin_email = $sv['admin_email'];

                if(!empty($admin_email)){
                    $customer = new Customer_Model_Customer();
                    $customer->find($customerId); 

                    $param = [
                        "customer_id" => (integer) $customer->getId() , 
                        "firstname" => (string) $customer->getFirstname(),
                        "lastname" => (string) $customer->getLastname(),
                        "nickname" => (string) $customer->getNickname(),
                        "email" => (string) $customer->getEmail() ,
                        "admin_email" => $admin_email,
                        "message" => p__('attendance', '%s Submitted a leave application', $customer->getFirstname()),
                        "date" => $start.'-'.$end
                    ];

                    $this->_sendLeaveApplyMessageEmail($param);
                }

                $payload = [
                    'success' => true,
                    'message' => p__('attendance', 'Apply Successfully!'),
                    'is_already_apply' => $is_already_apply            
               ];  
            }

            } catch (\Exception $e) {
                $payload = [
                    "error" => true,
                    "message" => $e->getMessage()
                ];
            }
       
        $this->_sendJson($payload);            
    }


     /**
     * Fetch all settings
     *
     */
    public function findLeavesAction()
    {

        try {

        if($value_id = $this->getRequest()->getParam('value_id')) {
            $leave_type_id = $this->getRequest()->getParam('id');
            $customerId = $this->_getCustomerId(true);

            $leaves = (new Attendance_Model_Leave())
                ->findAll(['leave_type_id' => $leave_type_id, 'customer_id' => $customerId ])->toArray();
            $leavesJson = [];
            foreach ($leaves as $key => $value) {
                $value['start_date'] =  date("F j, Y", strtotime($value['start_date']));
                $value['status'] =   $value['status'] == 1 ? 'Approved' : ($value['status'] == 2 ? 'Rejected' : 'Pending');                
                $leavesJson[] = $value;               
            }

            $payload = [
                    'success' => true,
                    'collections' => $leavesJson            
               ];  

        }

        } catch (\Exception $e) {
                $payload = [
                    "error" => true,
                    "message" => $e->getMessage()
                ];
            }
       
        $this->_sendJson($payload);

    }

    public function cancelLeaveAction()
    {
        try {

        if($value_id = $this->getRequest()->getParam('value_id')) {
            $leave_id = $this->getRequest()->getParam('id');
            $customerId = $this->_getCustomerId(true);

            $leave = (new Attendance_Model_Leave())->find(["id" => $leave_id]);
            if ($leave->getId()) {
                $leave_type_id = $leave->getLeaveTypeId();
                $leave->delete();
            }
        
           $leaves = (new Attendance_Model_Leave())
                ->findAll(['leave_type_id' => $leave_type_id, 'customer_id' => $customerId ])->toArray();
            $leavesJson = [];
            foreach ($leaves as $key => $value) {
                $value['start_date'] =  date("F j, Y", strtotime($value['start_date']));
                $value['status'] =   $value['status'] == 1 ? 'Approved' : ($value['status'] == 2 ? 'Rejected' : 'Pending');                
                $leavesJson[] = $value;               
            }

            $payload = [
                    'success' => true,
                    'collections' => $leavesJson            
               ];  

        }

        } catch (\Exception $e) {
                $payload = [
                    "error" => true,
                    "message" => $e->getMessage()
                ];
            }
       
        $this->_sendJson($payload);

    }

    private function _sendLeaveApplyMessageEmail($param){
        if(empty($param)){
            return false;
        }

        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $sender = $config->getOption('sendermail');
        $layout = $this->getLayout()->loadEmail('attendance', 'attendance_leave_admin');

        $layout->getPartial('content_email')
            ->setEmail($param['email'])
            ->setMessage($param['message'])
            ->setDate($param['date'])
            ->setCustomer($param['firstname'] . ' ' . $param['lastname'])
            ->setBaseUrl($this->getRequest()->getBaseUrl())
            ->setApp($this->getApplication()->getName())->setIcon($this->getApplication()->getIcon());

        $content = $layout->render();
        $mail = new Siberian_Mail();
        $mail->_is_default_mailer = false;
        $mail->setBodyHtml($content);
        $mail->setFrom($param['email'], $param['firstname'].' '.$param['lastname']);
        $mail->_sender_name = $param['firstname'].' '.$param['lastname'].' via '.$this->getApplication()->getName() ;
        $mail->addTo($param['admin_email'], "");
        $mail->setSubject(p__('attendance', '%s Submitted a leave application', $param['firstname'] . ' ' . $param['lastname']));

        $mail->send();

    }

 
    public function attendanceHistoryAction()
    {
        $payload = [];
       
        try{
            $customerId = $this->_getCustomerId(true);
            $value_id = $this->getRequest()->getParam('value_id'); 
            $month = (integer) $this->getRequest()->getParam('month', date('d')); 
            $year = (integer) $this->getRequest()->getParam('year',  date('Y')); 
            $month = $month + 1;

            $customers = (new Attendance_Model_Customers())
                ->findAttendanceByMonth($value_id, $customerId, $month, $year);

            $collection = [];
            $total_seconds = 0;
            foreach ($customers as $data) {
                $data["date"] = date("F jS, Y" , strtotime($data["startdate"]));
                $data["project_name"] = !empty($data['project_name']) ? $data['project_name'] : '';
                $data["location_name"] = !empty($data['location_name']) ? $data['location_name'] : '';
                $data["starttime"] = date('g:i A' , strtotime($data["startdate"]));
                $data["endtime"] = !empty($data["enddate"]) ?  date('g:i A' , strtotime($data["enddate"])) : '-';  
                if(!empty($data["enddate"])) {
                    $seconds = strtotime($data["enddate"]) - strtotime($data["startdate"]);  
                    $total_seconds = $total_seconds + $seconds;
                    $days    = floor($seconds / 86400);
                    $hours   = floor(($seconds - ($days * 86400)) / 3600);
                    $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
                    if($hours > 0){
                        $data['total_hours'] =  $hours." Hrs ".$minutes ." Mins";
                    }elseif($minutes > 0){
                        $data['total_hours'] =  $minutes ." Mins";
                    }else{
                        $data['total_hours'] =  $seconds ." Sec";
                    }
                   
                }else{
                    $data['total_hours'] = p__('attendance', 'Running');
                }
                $collection[] = $data;
            }

            $days    = floor($total_seconds / 86400);
            $hours   = floor(($total_seconds - ($days * 86400)) / 3600);
            $minutes = floor(($total_seconds - ($days * 86400) - ($hours * 3600))/60);
            $total_month_time =  $hours." Hrs ".$minutes ." Mins";

            $payload = [
                'success' => true,
                'collection' => $collection,
                'month' => $month,
                'year' => $year,
                'total_month_time' => $total_month_time
            ];

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


     /**
     *@return mixed|null
     */
    public function saveMessageAction(){
        $payload = [];
        try{               
            
            $data = $this->getRequest()->getBodyParams();  
            $receiver_id = (integer) $data['receiver_id'];  
            $customerId = (integer) $this->_getCustomerId(true);
            $receiver_id = 0;
        
            $MessageModel = (new Attendance_Model_Message())
                ->setValueId($data['value_id'])
                ->setSenderId($customerId)
                ->setReceiverId($receiver_id)
                ->setMessage($data['message'])
                ->save(); 
        

            // Send Email to admin
            $attendanceSettings = (new Attendance_Model_Attendance())->find(['value_id' => $data['value_id']]);
            $admin_email = $attendanceSettings->getAdminEmail();
            if(!empty($admin_email)){             
                $customer = new Customer_Model_Customer();
                $customer->find($customerId);
                $data['firstname'] = (string) $customer->getFirstname();
                $data['lastname'] = (string) $customer->getLastname();
                $data['email'] = (string) $customer->getEmail();
                $data['sender_id'] = (string) $customerId;
                $data['admin_email'] = (string) $admin_email;
                $this->_sendMessageEmail($data); 
            }
 
            // Fetch all the messages  
            $messageModel = new Attendance_Model_Message();
            $messages = [];
            if(!empty($customerId)){
                $messages = $messageModel->messageByCustomerId($customerId);
            }

            $payload = [
                'success' => true,
                'messages' => array_values($messages)                                          
            ];

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    /**
     *@return mixed|null
     */
    public function customerMessagesAction(){
        $payload = [];
        
        try{               
            $customerId = (integer) $this->_getCustomerId(true);
           // Fetch all the messages  
            $messageModel = new Attendance_Model_Message();
            $messages = [];
            if(!empty($customerId)){
                $messages = $messageModel->messageByCustomerId($customerId);
            }

            $payload = [
                'success' => true,
                'messages' => array_values($messages)                                          
            ];

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
    
    /**
     *@return mixed|null
     */
    public function verifyScanAction() {
        $payload = [];
        
        try{ 

        if($location_id = $this->getRequest()->getParam('location_id')) { 
            $value_id = $this->getRequest()->getParam('value_id');
            $customerId = (integer) $this->_getCustomerId(true);
            

            $locationModel = (new Attendance_Model_Locations())
                    ->find(['id' => $location_id, 'value_id' => $value_id]);

            if($locationModel->getId()){

                if($locationModel->getAllowScan() == 'restricted'){
                   $lcModel = (new Attendance_Model_LocationsCustomers())
                        ->find(['customer_id' => $customerId, 'location_id' => $location_id]); 
                    if($lcModel->getId()){
                        $payload = [
                            'success' => true,
                            'is_valid' => 1,
                            'messages' => p__('attendance', 'Scan Successfully')
                           ]; 
                    }else{
                        $payload = [
                            "error" => true,
                            'is_valid' => 0,
                            "message" => p__('attendance', 'You may not have the permission to check-In!')
                        ];
                    }

                }else{
                    $payload = [
                        'success' => true,
                        'is_valid' => 1,
                        'messages' => p__('attendance', 'Scan Successfully')                                       
                    ]; 
                }              

            } else{
                $payload = [
                    "error" => true,
                    'is_valid' => 0,
                    "message" => p__('attendance', 'Invalid QR code!')
                ];
            }

        }else{
            $payload = [
                "error" => true,
                'is_valid' => 0,
                "message" => p__('attendance', 'Invalid Param!')
            ];
        }

        } catch (\Exception $e) {
            $payload = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }


    private function _sendMessageEmail($param){
        if(empty($param)){
            return false;
        }

        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $sender = $config->getOption('sendermail');
        $layout = $this->getLayout()->loadEmail('attendance', 'attendance_message');

        $layout->getPartial('content_email')
            ->setEmail($param['email'])
            ->setMessage($param['message'])
            ->setSenderId($param['sender_id'])          
            ->setCustomer($param['firstname'] . ' ' . $param['lastname'])
            ->setBaseUrl($this->getRequest()->getBaseUrl())
            ->setApp($this->getApplication()->getName())->setIcon($this->getApplication()->getIcon());

        $content = $layout->render();
        $mail = new Siberian_Mail();
        $mail->_is_default_mailer = false;
        $mail->setBodyHtml($content);
        $mail->setFrom($param['email'], $param['firstname'].' '.$param['lastname']);
        $mail->_sender_name = $param['firstname'].' '.$param['lastname'].' via '.$this->getApplication()->getName() ;
        $mail->addTo($param['admin_email'], "");
        $mail->setSubject(p__('attendance', 'You have received messages from %s!', $param['firstname'] . ' ' . $param['lastname']));

        $mail->send();

    }

}

<?php

/**
 * Class Attendance_AttendanceController
 */
class Attendance_AttendanceController extends Application_Controller_Default {

    public function listAction() {
        $this->loadPartials();
    }
    

    public function fetchCustomersAction()
    {
        try {
            $request = $this->getRequest();
            $limit = $request->getParam("perPage", 25);
            $offset = $request->getParam("offset", 0);
            $sorts = $request->getParam("sorts", []);
            $queries = $request->getParam("queries", []);

            $filter = null;
            $startdate = null;
            $enddate = null;

            if (array_key_exists("search", $queries)) {
                $filter = $queries["search"];
            }
            if (array_key_exists("from", $queries)) {
                $startdate = date('Y-m-d', strtotime($queries["from"]));
            }
            if (array_key_exists("to", $queries)) {
                $enddate = date('Y-m-d', strtotime($queries["to"]));
            }

            
            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
                "startdate" => $startdate,
                "enddate" => $enddate
            ];
          
            $value_id = (new Attendance_Model_Attendance())->getCurrentValueId();

            $application = $this->getApplication();
            $customers = (new Attendance_Model_Customers())
                ->findAllForApp($value_id, $params);

            $countAll = (new Attendance_Model_Customers())->countAllForApp($value_id);
            $countFiltered =   (new Attendance_Model_Customers())->countAllForApp($value_id, $params);

             /* Settings */
            $settingsModel = (new Attendance_Model_Attendance())
                                    ->find($value_id, "value_id");
            $sv =  $settingsModel->getData();

            $customersJson = [];
            foreach ($customers as $customer) {
                $data = $customer->getData();
                $data['dateTimeFormat'] =  $sv['date_format'].' '.$sv['time_format'];
                $data['name'] = $customer->getFirstname(). ' '.$customer->getLastname();
                $data["created_at"] = datetime_to_format($data["created_at"]);
                $data['startdate'] =  date($data['dateTimeFormat'] , strtotime($data["startdate"]));
                $data["enddate"] = !empty($data["enddate"]) ?  date($data['dateTimeFormat'] , strtotime($data["enddate"])) : '-';
                $data["totaltime"] = !empty($data['totaltime']) ? $data['totaltime'] :  p__("attendance", "Running");
                $data["project_name"] = !empty($data['project_name']) ? $data['project_name'] :  '';
                $data["location_name"] = !empty($data['location_name']) ? $data['location_name'] : '';

                $customersJson[] = $data;
            }

            $payload = [
                "records" => $customersJson,
                "queryRecordCount" => $countFiltered[0],
                "totalRecordCount" => $countAll[0],
                "sv" => $sv
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }


      /**
     *
     */
    public function exportCsvAction()
    {
      if ($this->getApplication()->getId()) {

        try {
              
            $request = $this->getRequest();
            $queries = $request->getParam("queries", []);

            $filter = $request->getParam("search", null);
            $startdate =  $request->getParam("from", null);
            $enddate =  $request->getParam("to", null);

           $params = [
                "filter" => $filter,
                "startdate" => !empty($startdate) ? date('Y-m-d', strtotime($startdate)) : null,
                "enddate" =>  !empty($enddate) ? date('Y-m-d', strtotime($enddate)) : null,
            ];
  
            $value_id = (new Attendance_Model_Attendance())->getCurrentValueId();            
            $customers = (new Attendance_Model_Customers())
                ->findAllForApp($value_id, $params);
            
             /* Settings */
            $settingsModel = (new Attendance_Model_Attendance())
                                    ->find($value_id, "value_id");
            $sv =  $settingsModel->getData();

            $csv_string = "FirstName,LastName,E-Mail,Project/Location,Start Date,End Date,total,Start Address,End Address\n";
            foreach ($customers as $customer) {
                $data = $customer->getData();
                $startaddress = str_replace(',', ' ', $data['startaddress']);
                $endaddress = str_replace(',', ' ', $data['endaddress']);
                
                $data["project_name"] = !empty($data['project_name']) ? $data['project_name'] :  '';
                $data["location_name"] = !empty($data['location_name']) ? $data['location_name'] : '';

                $project_location =  $data['project_name'].''. $data['location_name'];

                $enddate = $data["enddate"] != null ? date($sv['date_format'].' - '.$sv['time_format'] , strtotime($data["enddate"])) : '';

                $csv_string .= $customer->getFirstname().",".$customer->getLastname().",".$customer->getEmail().",".$project_location.",".date($sv['date_format'].' - '.$sv['time_format'] , strtotime($data["startdate"])).",".$enddate.",".$data['totaltime'].",".$startaddress.",".$endaddress."\n";                
           }

            $date = date("Y-m-d_H-i-s");
            $filename = "customers_tracking_report_".$date.".csv";
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            echo $csv_string;
            exit();
           
            } catch (Exception $e) {
                if(APPLICATION_ENV === "development") {
                    Zend_Debug::dump($e);
                }
                return false;
            }
        }
    }


     public function deleteNewAction()
    {
        try {
            $request = $this->getRequest();
            $application = $this->getApplication();
            $customerId = $request->getParam("id", null);

            $customer = (new Attendance_Model_Customers())
                ->find($customerId);
            if (!$customer->getId()) {
                throw new \Siberian\Exception("#07888-01" . p__("attendance", "We are unable to delete this time slot!"));
            }

            $customer->delete();

            $payload = [
                'success' => true,
                'message' => p__("attendance", "Saved successfully") ,
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }


 
}
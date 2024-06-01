<?php

/**
 * Class Attendance_LocationsController
 */
class Attendance_LocationsController extends Application_Controller_Default
{

    /**
     * Location list
     */
    public function listAction() {
        $this->loadPartials();
    }

     /**
     * members list
     */
    public function membersAction() {

        if ($id = $this->getRequest()->getParam('id')) {
            try {
              
               $model = (new Attendance_Model_Locations())
                    ->find(['id' => $id]);
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("attendance",  "This project does not exist."));
                }

                } catch (\Exception $e) {
                   $this->getRequest()->addError( p__("attendance",  "Something went wrong."));
            }
        }

        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setLocations($model);
    }


    /**
     * Location Create
     */
    public function createAction() {
        $this->loadPartials();
    }
    

    public function fetchAllAction()
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
      
            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
           ];
          
            $value_id = (new Attendance_Model_Attendance())->getCurrentValueId();

            $application = $this->getApplication();
            $locations = (new Attendance_Model_Locations())
                ->findAllForApp($value_id, $params);

            $countAll = (new Attendance_Model_Locations())->countAllForApp($value_id);
            $countFiltered =   (new Attendance_Model_Locations())->countAllForApp($value_id, $params);
        
           $locationsJson = [];
            foreach ($locations as $location) {
                $data = $location->getData();
                $data['status'] = $data['status'] == 1 ? p__('attendance', "Active") : p__('attendance', "InActive");
                $data['is_member'] = $data['allow_scan'] == 'all' ? 'hide' : '';
                $locationsJson[] = $data;
            }

            $payload = [
                "records" => $locationsJson,
                "queryRecordCount" => $countFiltered[0],
                "totalRecordCount" => $countAll[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }

    public function fetchAllCustomersAction()
    {
        try {

            $location_id = $this->getRequest()->getParam('location_id');
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
      
            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
           ];
          
            $value_id = (new Attendance_Model_Attendance())->getCurrentValueId();
            $app_id = $this->getApplication()->getId();
             
            $application = $this->getApplication();
            $customers = (new Attendance_Model_LocationsCustomers())
                ->findAllForApp($app_id, $location_id, $params);

            $countAll = (new Attendance_Model_LocationsCustomers())->countAllForApp($app_id, $location_id);
            $countFiltered =   (new Attendance_Model_LocationsCustomers())->countAllForApp($app_id, $location_id, $params);
        
           $customersJson = [];
            foreach ($customers as $customer) {
                $data = $customer->getData();
                $data['name'] = $data['firstname'].' '.$data['lastname'];
                $data['is_add_hide'] = (integer) $data['is_member'] != 0 ? 'hide' : '';
                $data['is_remove_hide'] = (integer) $data['is_member'] == 0 ? 'hide' : '';
                $data['location_id'] = $location_id;
                $customersJson[] = $data;
            }

            $payload = [
                "records" => $customersJson,
                "queryRecordCount" => $countFiltered[0],
                "totalRecordCount" => $countAll[0]
            ];
        } catch (\Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        $this->_sendJson($payload);
    }


    public function addCustomerAction() {
        $payout = [];

        if($customer_id = $this->getRequest()->getParam('customer_id')) { 
            $location_id = $this->getRequest()->getParam('location_id');
          try {  
                $value_id = (new Attendance_Model_Attendance())->getCurrentValueId();
           
                $model = (new Attendance_Model_LocationsCustomers())
                    ->find(['customer_id' => $customer_id, 'location_id' => $location_id])
                    ->setCustomerId($customer_id)
                    ->setLocationId($location_id)
                    ->setValueId($value_id);
               $model->save();  

                $this->getSession()->addSuccess(p__('attendance', "Info successfully saved")); 
                
                $payout = [
                    "success" => 1
                ];

          }catch(Exception $e) {
                $payout = [
                    "error" => 1,
                    "message" => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->getResponse()->setBody(Zend_Json::encode($payout))->sendResponse();
            die;

        }
    }


    public function removeCustomerAction() {
        $payout = [];

        if($customer_id = $this->getRequest()->getParam('customer_id')) { 
            $location_id = $this->getRequest()->getParam('location_id');
          try {  
                $model = (new Attendance_Model_LocationsCustomers())
                    ->find(['customer_id' => $customer_id, 'location_id' => $location_id]);
                $model->delete();  

                $this->getSession()->addSuccess(p__('attendance', "Info deleted saved")); 
                
                $payout = [
                    "success" => 1
                ];

          }catch(Exception $e) {
                $payout = [
                    "error" => 1,
                    "message" => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->getResponse()->setBody(Zend_Json::encode($payout))->sendResponse();
            die;

        }
    }


    public function saveAction() {
        $payout = [];

        if($param = $this->getRequest()->getPost()) { 

            $value_id = $param['value_id'];           
            try {  
                $param['status'] = $param['status'] == 1 ? 1: 0;
                $model = (new Attendance_Model_Locations())
                    ->find(['id' => $param['id']])
                    ->setValueId($value_id)
                    ->setName($param['name'])
                    ->setAddress($param['address'])
                    ->setAllowScan($param['allow_scan'])
                    ->setStatus($param['status']);

                $model->save();                
                $this->getSession()->addSuccess(p__('attendance', "Info successfully saved")); 
                
                $payout = [
                    "success" => 1
                ];

          }catch(Exception $e) {
                $payout = [
                    "error" => 1,
                    "message" => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->getResponse()->setBody(Zend_Json::encode($payout))->sendResponse();
            die;

        }
    }


   /**
     * soft delete
     */
    public function deleteAction() {
        $payout = [];
       
        if ($id = $this->getRequest()->getParam('id')) {
            try {
              
               $model = (new Attendance_Model_Locations())
                    ->find(['id' => $id])
                    ->setStatus(2);
                $model->save();                
                $this->getSession()->addSuccess(p__('attendance', "Deleted successfully")); 
                
                $payout = [
                    "success" => 1
                ];

          }catch(Exception $e) {
                $payout = [
                    "error" => 1,
                    "message" => $e->getMessage(),
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->getResponse()->setBody(Zend_Json::encode($payout))->sendResponse();
            die;

        }
    }

   /**
     * edit location
     */
    public function editAction()
    {   
         $model = (new Attendance_Model_Locations());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("attendance",  "This location does not exist."));
                }
            }
        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentLocation($model);
    }

    public function qrcodeAction()
    {

         if ($id = $this->getRequest()->getParam('id')) {
            $html = '';
             try {
               $model = (new Attendance_Model_Locations());  
                $model->find($id); 
                if (!$model->getId()) {
                    throw new Exception(__("An error occurred while retrieving QRCode. Please try again later"));
                }

                $dir_image = Core_Model_Directory::getBasePathTo("/images/application/".$this->getApplication()->getId());

                if(!is_dir($dir_image)) mkdir($dir_image, 0775, true);
                if(!is_dir($dir_image."/application")) mkdir($dir_image."/application", 0775, true);
                if(!is_dir($dir_image."/application/attendancelocation")) mkdir($dir_image."/application/attendancelocation", 0775, true);

                $dir_image .= "/application/attendancelocation/";
                $image_name = $id."-attendancelocation.png";

                if(!is_file($dir_image.$image_name)) {
                    copy('https://api.qrserver.com/v1/create-qr-code/?color=000000&bgcolor=FFFFFF&data=sendback%3A'.$id.'&qzone=1&margin=0&size=200x200&ecc=L', $dir_image.$image_name);
                }

                $img = imagecreatefrompng($dir_image.$image_name);
                $readable_name = $model->getName();
                header('Content-Type: image/png');
                header('Content-Disposition: attachment; filename="'.$readable_name.'.png"');
                imagepng($img);
                imagedestroy($img);
                die();

            } catch (Exception $e) {
                $html = $e->getMessage();
            }

            echo $html; die();

        }
    }
 
}
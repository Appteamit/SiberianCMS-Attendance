<?php

/**
 * Class Attendance_ProjectsController
 */
class Attendance_ProjectsController extends Application_Controller_Default
{

    /**
     * Location list
     */
    public function listAction() {
        $this->loadPartials();
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
            $projects = (new Attendance_Model_Projects())
                ->findAllForApp($value_id, $params);

            $countAll = (new Attendance_Model_Projects())->countAllForApp($value_id);
            $countFiltered =   (new Attendance_Model_Projects())->countAllForApp($value_id, $params);
        
           $projectsJson = [];
            foreach ($projects as $project) {
                $data = $project->getData();
                $data['status'] = $data['status'] == 1 ? p__('attendance', "Active") : p__('attendance', "InActive");
                $projectsJson[] = $data;
            }

            $payload = [
                "records" => $projectsJson,
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

    public function saveAction() {
        $payout = [];

        if($param = $this->getRequest()->getPost()) { 

            $value_id = $param['value_id'];           
            try {  
                $param['status'] = $param['status'] == 1 ? 1: 0;
                $model = (new Attendance_Model_Projects())
                    ->find(['id' => $param['id']])
                    ->setValueId($value_id)
                    ->setTitle($param['title'])
                    ->setAddress($param['address'])
                    ->setDescription($param['description'])
                    ->setStartDate($param['start_date'])
                    ->setEndDate($param['end_date'])
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
              
               $model = (new Attendance_Model_Projects())
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
         $model = (new Attendance_Model_Projects());  
            if ($id = $this->getRequest()->getParam('id')) {
                $model->find($id); 
                if (!$model->getId()) {
                        $this->getRequest()->addError( p__("attendance",  "This project does not exist."));
                }
            }
        $this->loadPartials();
        $this->getLayout()->getPartial('content')->setCurrentProject($model);
    }

    
 
}
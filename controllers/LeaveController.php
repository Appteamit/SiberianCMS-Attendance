<?php

/**
 * Class Attendance_ApplicationController
 */
class Attendance_LeaveController extends Application_Controller_Default
{

    /**
     * Leavs list
     */
    public function listAction() {
        $this->loadPartials();
    }


    /**
     * fetch leaves list
     */
    public function fetchLeavesAction() {

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
            if (array_key_exists("status", $queries)) {
                $status =  $queries["status"];
            }

            
            $params = [
                "limit" => $limit,
                "offset" => $offset,
                "sorts" => $sorts,
                "filter" => $filter,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "status" => $status
            ];
           
            $value_id = (new Attendance_Model_Attendance())->getCurrentValueId();
            $application = $this->getApplication();
            
            $leaves = (new Attendance_Model_Leave())
                ->findByValueId($value_id, $params);

            $countAll = (new Attendance_Model_Leave())->countAllForApp($value_id);
            $countFiltered =   (new Attendance_Model_Leave())->countAllForApp($value_id, $params);

             /* Settings */
            $settingsModel = (new Attendance_Model_Attendance())
                                    ->find($value_id, "value_id");
            $sv =  $settingsModel->getData();

            $leaveJson = [];
            foreach ($leaves as $leave) {
                $data = $leave->getData();
                $data['user'] = $data['firstname'].' '.$data['lastname'];
                $data['start_date'] = date($sv['date_format'], strtotime($data['start_date'])); 
                $data['end_date'] = date($sv['date_format'], strtotime($data['end_date'])); 
                $data['status'] = $data['status'] == 1 ? 'Approved' : ($data['status'] == 2 ? 'Rejected' : 'Pending');

                $leaveJson[] = $data;
            }

            $payload = [
                "records" => $leaveJson,
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
    
    /**
     *
     */
    public function editpostAction()
    {
        try
        {
            $values = $this->getRequest()->getPost();
            $form = new  Attendance_Form_Leave();
            if ($form->isValid($values))
            {   
                if($values['entitlement'] < 0 )  throw new Siberian_Exception(p__('attendance', 'Entitlement Must be greater than 0!'));

                $model = (new Attendance_Model_LeaveType())->find($values['value_id'], "value_id");
                $model->addData($values);
                $model->save();
                 
                $payload = ["success" => "1", "success_message" => p__("attendance", "Saved successfully") , 'message_timeout' => 1, 'message_button' => 0, 'message_loader' => 0, ];              

            }
            else
            {
                /** Do whatever you need when form is not valid */
                $payload = ["error" => true, "message" => $form->getTextErrors() , "errors" => $form->getTextErrors(true) , ];
            }

        }
        catch(\Exception $e)
        {
            $payload = ["error" => true, "message" => $e->getMessage() , ];
        }

        $this->_sendJson($payload);
    }


    /**
     *
     */
    public function deleteleaveAction()
    {   
        $payload = array();

        if ($data = $this->getRequest()->getPost()) {

            try {

                $model = new Attendance_Model_LeaveType();
                $model->find($data['id']);
                if($model->getId()) {
                    $model->setStatus(2);
                    $model->save();
              
                    $payload = array(
                        'success' => true,
                        'success_message' => p__('attendance', 'Deleted successfully saved'),
                        'message_timeout' => 2,
                        'message_button' => 0,
                        'message_loader' => 0
                    );

                }

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }           
        }

        $this->_sendJson($payload);
    }


    public function loadleaveformAction(){
        
        if ($id = $this->getRequest()->getParam("id")) {
            try {
                 
                $model = new Attendance_Model_LeaveType();
                $model->find($id);
                if($model->getId()) {
                    $data = $model->getData();
                    $form = new Attendance_Form_Leave();
                    $form->populate($data);
                    $form->setElementValueById('value_id', $this->getCurrentOptionValue()->getId());
                    $form->addNav("edit-nav-attendance", "Save", false); 
                    $form->removeNav("nav-add-attendance");              
                    $form->setElementValueById('id', $model->getId());
                    
                    $payload = array(
                        "check" => 'ready for use',
                        "success"   => true,
                        "form"      => $form->render(),
                        "message"   => p__('attendance', "Success."),
                    );

                }else{
                    $payload = array(
                        "error"     => true,
                        "message"   => p__('attendance', 'Leave you are trying to edit does not exists.'),
                    );
                }              

            } catch (Exception $e) {
                $payload = array(
                    'error' => true,
                    'message' => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($payload);
    }


    public  function statusUpdateAction() {

       try {
            $request = $this->getRequest();
            $id = $request->getParam("id", null);
            $status = $request->getParam("status", null);
            $status = $status == 'reject' ? 2 : 1;
            
            $leaveModel = new Attendance_Model_Leave();
            $leaveModel->find(array('id' => $id));
            $leaveModel->setStatus($status);
            $leaveModel->save();

                $payload = [
                    'success' => true,
                    'message' => p__('attendance', 'Successfully updated'),
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
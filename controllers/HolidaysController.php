<?php

/**
 * Class Attendance_HolidaysController
 */
class Attendance_HolidaysController extends Application_Controller_Default
{


    /**
     *
     */
    public function editpostAction()
    {
        try
        {
            $values = $this->getRequest()->getPost();
            $form = new  Attendance_Form_Holidays_Form();
            if ($form->isValid($values))
            {   

                $valid_from = new Zend_Date(strtotime($values['valid_from']), false, new Zend_Locale('en_US'));
                $values['valid_from'] = $valid_from->toString('y-MM-dd');
                $valid_until = new Zend_Date(strtotime($values['valid_until']), false, new Zend_Locale('en_US'));
                $values['valid_until'] = $valid_until->toString('y-MM-dd');
                $values['status'] = 1;

                $start = strtotime($values['valid_from']);
                $end = strtotime($values['valid_until']);
                $values['total_days'] = ceil(abs($end - $start) / 86400) + 1; 
                
                $model = (new Attendance_Model_Holidays())->find($values['value_id'], "value_id");
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
            $payload = ["error" => true, "message" => $e->getMessage()
        ];
        }

        $this->_sendJson($payload);
    }


    /**
     *
     */
    public function deleteAction()
    {   
        $payload = array();

        if ($data = $this->getRequest()->getPost()) {

            try {

                $model = new Attendance_Model_Holidays();
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


    public function loadholidayformAction(){
        
        if ($id = $this->getRequest()->getParam("id")) {
            try {
                 
                $model = new Attendance_Model_Holidays();
                $model->find($id);
                if($model->getId()) {
                    $data = $model->getData();
                    $valid_from = new Zend_Date(strtotime($data['valid_from']), false, new Zend_Locale('en_US'));
                    $data['valid_from'] = $valid_from->toString('MM/dd/y');
                    $valid_until = new Zend_Date(strtotime($data['valid_until']), false, new Zend_Locale('en_US'));
                    $data['valid_until'] = $valid_until->toString('MM/dd/y');                 
 
                    $form = new Attendance_Form_Holidays_Form();
                    $form->populate($data);
                    $form->setElementValueById('value_id', $this->getCurrentOptionValue()->getId());
                    $form->addNav("edit-nav-holidays", "Save", false); 
                    $form->removeNav("nav-add-holidays");              
                    $form->setElementValueById('id', $model->getId());

                    $form->getElement('valid_from')->setAttrib('id', 'valid_from-' . $model->getId());
                    $form->getElement('valid_until')->setAttrib('id', 'valid_until-' . $model->getId());
                    
                    $payload = array(
                        "check" => 'ready for use',
                        "success"   => true,
                        "form"      => $form->render(),
                        "message"   => p__('attendance', "Success."),
                    );

                }else{
                    $payload = array(
                        "error"     => true,
                        "message"   => p__('attendance', 'holidays you are trying to edit does not exists.'),
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

 
}
<?php

/**
 * Class Attendance_ApplicationController
 */
class Attendance_ApplicationController extends Application_Controller_Default
{

    public function editAction()
    {
        parent::editAction();
    } 

    public function editpostAction()
    {
        try {
            
            $values = $this->getRequest()->getPost();
            $form = new  Attendance_Form_Attendance();
            if ($form->isValid($values))
            {   
                if($values['fixed_working_hours']){

                    if(empty($values['business_hour_start'])) throw new Siberian_Exception(p__('attendance', 'Please enter a business hour start time!'));
                    if(empty($values['business_hour_end'])) throw new Siberian_Exception(p__('attendance', 'Please enter a business hour end time!'));
                  
                    if($values['business_hour_start'] >= $values['business_hour_end']) throw new Siberian_Exception(p__('attendance', 'Business hour start time must be less then end time!'));            

                }

                $attendance = (new Attendance_Model_Attendance())
                                    ->find($values['value_id'], "value_id");
                $attendance->addData($values);
                $attendance->save();

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

    public function minutes ($time) {
        $time = explode(':', $time);
        return ($time[0]*60) + ($time[1]) + ($time[2]/60);
    }

    public function editworkweekpostAction()
    {
        try
        {
            $values = $this->getRequest()->getPost();
            $form = new  Attendance_Form_WorkWeek();
            if ($form->isValid($values))
            { 
                $attendance = (new Attendance_Model_WorkWeek())
                                    ->find($values['value_id'], "value_id");
                $attendance->addData($values);
                $attendance->save();

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
     * messages
     */
    public function messagesAction() { 
           
            if($senderId = $this->getRequest()->getParam('sender_id')){
                $customer = new Customer_Model_Customer();
                $customer->find($senderId);

                $messageModel = new Attendance_Model_Message();
                $currentMssages = [];
                $currentMssages = $messageModel->adminMessageBySenderId($senderId);
            } 

            $this->loadPartials();
            $this->getLayout()->getPartial('content')->setSenderId($senderId)->setCustomer($customer)->setListingMessages($currentMssages);
    }

   /**
     *@return mixed|null
     */
    public function savemessageAction(){
        $payload = [];
        
        if($data = $this->getRequest()->getPost()) {         
       
        try{  
                 
           $listingMessage = (new Attendance_Model_Message())
                ->setValueId($data['value_id'])
                ->setSenderId(0)
                ->setReceiverId($data['receiver_id'])
                ->setMessage($data['message'])
                ->save(); 
            

           # Push not mandatory
            if (Push_Model_Message::hasIndividualPush() && class_exists("Push_Model_Customer_Message") && $data['receiver_id'] > 0 ) {

                $message_push = new Push_Model_Message();
                $message_push->setMessageType(Push_Model_Message::TYPE_PUSH);
                $data_push = [
                    "title" => $this->getApplication()->getName() ,
                    "text" => p__('attendance', 'New message received!'),
                    "send_at" => time(),
                    "action_value" => $data['value_id'],
                    "value_id" => $data['value_id'],
                    "type_id" => $message_push->getMessageType(),
                    "app_id" => $this->getApplication()->getId(),
                    "send_to_all" => 0,
                    "send_to_specific_customer" => 1,
                ];
               $message_push->setData($data_push)->save();
               $customer_message = new Push_Model_Customer_Message();
                    $customer_message_data = [
                        "customer_id" => $data['receiver_id'],
                        "message_id" => $message_push->getId(),
                    ];
                    $customer_message->setData($customer_message_data);
                    $customer_message->save();
                
            }
            # End push

               $payload = [
                    'success' => true,
                    'messages' => $messages                                          
                ];

            } catch (\Exception $e) {
                $payload = [
                    "error" => true,
                    "message" => $e->getMessage()
                ];
            }
        }
        $this->_sendJson($payload);
    }

     /**
     * Users
     */
    public function usersAction() { 
           $this->loadPartials();
    }
   
}
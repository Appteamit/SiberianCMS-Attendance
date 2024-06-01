<?php
/**
 * Class Attendance_Model_Db_Table_Message
 * @package Attendance\Model\Db\Table
 */
class Attendance_Model_Db_Table_Message extends Core_Model_Db_Table
{
    /**
     * @var string
     */
    protected $_name = "attendance_messages";

    /**
     * @var string
     */
    protected $_primary = "id";


    /**
     * @param $customerId
     * @param array $params
    */
    public function messageByCustomerId($customerId)
    {
   
    $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "sender_id",
                "receiver_id",
                "message",
                "is_read",                 
                "created_at",
                "created_at",
            ]);
            
        $select->where("main.sender_id = $customerId OR main.receiver_id  = $customerId");
        $select->order(["main.created_at ASC"]);
       
        $this->_db->update($this->_name, ['is_read' => 1], ['receiver_id = ?' =>  $customerId]);

        return  $this->_db->fetchAssoc($select);  
    }


    /**
     * @param $valueId, $customerId
     * @param array $params
    */
    public function unreadMessageCountCustomer($valueId, $customerId)
    {
       $select = $this->_db->select()
            ->from(['main' => $this->_name], ["num"=>"COUNT(*)"
            ]);
            $select->where("main.receiver_id = ?", $customerId);
            $select->where("main.is_read = ?", "0");            
       return  $this->_db->fetchRow($select);  
    }

    /**
     * @param $senderId
     * @param array $params
    */
    public function adminMessageBySenderId($senderId)
    {
   
      $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "sender_id",
                "receiver_id",
                "message",
                "is_read",                 
                "created_at",
                "created_at",
            ]);
            
         $select->where("main.sender_id = $senderId OR main.receiver_id  = $senderId"); 
         $select->order(["main.created_at ASC"]);

         $this->_db->update($this->_name, ['is_read' => 1], ['sender_id = ?' =>  $senderId]);

        return  $this->_db->fetchAssoc($select);  
    }


/**
     * @param $ListingId, $customerId
     * @param array $params
    */
    public function adminInbox($valueId)
    {
   
    $select = $this->_db->select()
            ->from(['main' => $this->_name], [
                "id",
                "sender_id",
                "receiver_id",
                "message",
                "is_read",                 
                "created_at",
                "MAX(main.created_at) AS created_at",
            ]);
            $select->joinLeft(['c' => 'customer'], 'c.customer_id = main.sender_id', ['c.customer_id as sender_customer_id', 'c.firstname as sender_first_name', 'c.lastname as sender_last_name' , 'c.image as sender_image']);
            $select->joinLeft(['r' => 'customer'], 'r.customer_id = main.receiver_id', ['r.customer_id as receiver_customer_id', 'r.firstname as receiver_first_name', 'r.lastname as receiver_last_name', 'c.image as receiver_image']);           
            $select->where("main.value_id = ?", $valueId); 
            $select->where("main.sender_id != ?", "0");            
            $select->group(["main.sender_id"]);
            $select->order(["MAX(main.created_at) DESC"]);
      
        return  $this->_db->fetchAssoc($select);
    }


}
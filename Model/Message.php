<?php

/**
 * Class Attendance_Model_Message
 * @package Attendance\Model
 */
class Attendance_Model_Message extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Attendance_Model_Db_Table_Message::class;

    /**
     * @param $valueId , $customerId
     * @param array $params
     */
    public function messageByCustomerId($customerId)
    {
        return $this->getTable()->messageByCustomerId($customerId);
    }
     /**
     * @param $valueId
     * @param array $params
     */
    public function unreadMessageCountCustomer($valueId, $customerId)
    {
        return $this->getTable()->unreadMessageCountCustomer($valueId, $customerId);
    }

    /**
     * @param $senderId
     * @param array $params
     */
    public function adminMessageBySenderId($senderId)
    {
        return $this->getTable()->adminMessageBySenderId($senderId);
    }

    /**
     * @param $valueId
     * @param array $params
     */
    public function adminInbox($valueId)
    {
        return $this->getTable()->adminInbox($valueId);
    }

}
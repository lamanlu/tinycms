<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model{
    
    private $_Cache_Prefix = 'User_';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function regist($userData){
        
        $userData['create_time'] = time();
        
        $userId = $this->insertData($userData, 'user', 'user');
        
        return $userId;
    }
    
    public function getUserById($uid){
        $sql = 'SELECT id,user_name,user_pwd,nickname,mobile,email,create_time FROM '.$this->tableName('user').' WHERE id = ? LIMIT 1';
        $param = array($uid);
        
        $userInfo = $this->getRowBySQL($sql, $param, 'user');
        
        return $userInfo;
    }
    
    public function getUidByField($field,$value){
        $uid = 0;
        
        $sql = 'SELECT id FROM '.$this->tableName('user').' WHERE `'.$field.'` = ? LIMIT 1';
        $param = array($value);
        
        $userInfo = $this->getRowBySQL($sql, $param, 'user');
        
        if(!empty($userInfo)){
            $uid = $userInfo['id'];
        }
        
        return $uid;
    }
    
    public function checkExist($data){
        
    }
    
    private function setUidMap($field,$uid){
        
    }
    
    private function getUidMap($field){
        
    }
    
    private function setUserInfoCache($uid,$data){
        
    }
    
    private function getUserInfoCache($uid){
        
    }
    
}
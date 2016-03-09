<?php
/**
 * @package 用户数据管理类
 * @author LamanLu
 * @since 2016-03-08
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model{
    
    private $_Cache_Prefix = 'User_';
    
    private $_UserRedis = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->_Current_DB = 'user';
        
        $this->_Table = 'user';
        
        $this->load->library('Redis_connecter',array('redis_user'),'UserRedis');
        $this->_UserRedis = $this->UserRedis->getInstance();
    }
    
    public function regist($userData){
        
        $userData['create_time'] = time();
        
        $userId = $this->insertData($userData, 'user');
        
        if(!empty($userId)){
            $this->setUidMap($userData['user_name'], $userId);            
        }
        
        return $userId;
    }
    
    public function getUserById($uid){
        
        $uid = intval($uid);
        
        if(empty($uid)){
            return FALSE;
        }
        
        $cache = $this->getUserInfoCache($uid);
        if(!empty($cache)){
            return $cache;
        }
        
        $sql = 'SELECT id AS uid,user_name,user_pwd,nickname,mobile,create_time FROM '.$this->tableName('user').' WHERE id = ? LIMIT 1';
        $param = array($uid);
        
        $userInfo = $this->getRowBySQL($sql, $param);
        
        if(!empty($userInfo)){
            $this->setUserInfoCache($uid, $userInfo);
        }
        
        return $userInfo;
    }
    
    public function getUidByName($uname){
        $uid = 0;
        
        $res = $this->getUidMap($uname);
        if(!empty($res)){
            return $res;
        }
        
        $sql = 'SELECT id FROM '.$this->tableName('user').' WHERE `user_name` = ? LIMIT 1';
        $param = array($uname);
        
        $userInfo = $this->getRowBySQL($sql, $param);
        
        if(!empty($userInfo)){
            $uid = $userInfo['id'];
        }
        
        $this->setUidMap($uname, $uid);
        
        return $uid;
    }
    
    public function checkExistName($uname){
        
        $uid = $this->getUidByName($uname);
        
        $return = empty($uid)?FALSE:TRUE;
        
        return $return;
    }
    

    private function setUidMap($value,$uid){
        
        $key = $this->_Cache_Prefix.'map_'.$value;
        $key = md5($key);        
        
        $this->_UserRedis->set($key,$uid);
    }
    
    private function getUidMap($value){
        
        $key = $this->_Cache_Prefix.'map_'.$value;
        $key = md5($key);
        
        return $this->_UserRedis->get($key);
    }
    
    private function setUserInfoCache($uid,$data){
        $key = $this->_Cache_Prefix.'info_'.$uid;
        $key = md5($key);
        
        $this->_UserRedis->hMSet($key,$data);
    }
    
    private function getUserInfoCache($uid){
        $key = $this->_Cache_Prefix.'info_'.$uid;
        $key = md5($key);

        return $this->_UserRedis->hGetAll($key);
    }
    
}
<?php
/**
 * @package 用户数据管理类
 * @author LamanLu
 * @since 2016-03-21
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Userinfo_model extends MY_Model{
    
    private $_Cache_Prefix = 'User_';
    
    private $_UserRedis = null;
    
    public function __construct() {
        parent::__construct();
        
        $this->_Current_DB = 'user';
        
        $this->_Table = 'user';
        
        $this->load->library('Redis_connecter',array('redis_user'),'UserRedis');
        $this->_UserRedis = $this->UserRedis->getInstance();
    }
    
    public function getUserInfoById($uid){
        
        $uid = intval($uid);
        
        if(empty($uid)){
            return FALSE;
        }
        
        $cache = $this->getUserInfoCache($uid);
        if(!empty($cache)){
            return $cache;
        }
        
        $sql = 'SELECT id AS uid,user_pwd,nickname,email,mobile,create_time FROM '.$this->tableName('user').' WHERE id = ? LIMIT 1';
        $param = array($uid);
        
        $userInfo = $this->getRowBySQL($sql, $param);
        
        if(!empty($userInfo)){
            $this->setUserInfoCache($uid, $userInfo);
        }
        
        return $userInfo;
    }
    
    private function setUserInfoCache($uid,$data){
        $key = $this->_Cache_Prefix.'info_'.$uid;
//        $key = md5($key);
        
        $this->_UserRedis->hMSet($key,$data);
    }
    
    private function getUserInfoCache($uid){
        $key = $this->_Cache_Prefix.'info_'.$uid;
//        $key = md5($key);

        return $this->_UserRedis->hGetAll($key);
    }
}
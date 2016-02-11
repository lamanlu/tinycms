<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends MY_Model{


    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'admin';
    }
    
    public function saveAdmin($data){        
        return $this->insertData($data);
    }
    
    public function updateAdmin($id, $data){        
        $params = array(
            'id' => $id,
        );
        return $this->updateData($params, $data);
    }
    
    public function checkExist($username,$id = 0){
        $where = '';
        $params = array($username);
        
        if(!empty($id)){
            $where = ' AND id != ? ';
            $params[] = $id;
        }
        
        $sql = 'SELECT id FROM '.$this->tableName($this->_Table).' WHERE username = ? '.$where.' LIMIT 1';
        return $this->getRowBySQL($sql, $params);        
    }
    
    /**
     * 根据条件获取管理员列表
     * @param type $conditions
     * @param type $offSet
     * @param type $pageSize 
     * @return array
     * @author LamanLu
     * @since 2016-01-24
     */
    public function getAdminList($conditions = array(),$limit = TRUE ,$offSet = 0,$pageSize = 10){

        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'role_id':
                    $where .= " AND role_id = ? ";
                    $params[] = $val;
                    break;
                case 'name':
                    $where .= " AND (username LIKE '%".$this->_DB_Main->escape_like_str($val)."%' OR nickname LIKE '%".$this->_DB_Main->escape_like_str($val)."%') ";
                    break;
            }
        }
                
        $sql = 'SELECT id,username,nickname,role_id,is_baned,last_login,create_time FROM '.$this->tableName($this->_Table).' WHERE 1 '.$where.' ORDER BY id ASC ';
		
        if($limit){
            $sql .= 'LIMIT '.$offSet.' , '.$pageSize.';';
        }
        
        $tmpArr = $this->getListBySQL($sql,$params);
        
        return create_array_by_key($tmpArr, 'id');
    }
    
    /**
     * 根据条件获取城市数量
     * @param type $conditions
     * @return array
     * @author LamanLu
     * @since 2016-01-24
     */
    public function getAdminCount($conditions = array()){
        
        $count = 0;
        
        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'role_id':
                    $where .= " AND role_id = ? ";
                    $params[] = $val;
                    break;
                case 'name':
                    $where .= " AND (username LIKE '%".$this->_DB_Main->escape_like_str($val)."%' OR nickname LIKE '%".$this->_DB_Main->escape_like_str($val)."%') ";
                    break;
            }
        }
        
        $sql = 'SELECT COUNT(*) AS count FROM '.$this->tableName($this->_Table).' WHERE 1 '.$where;
        
        $res = $this->getRowBySQL($sql,$params);
        
        if(!empty($res)){
            $count = $res['count'];
        }
        
        return $count;
    }
    
    public function getAdminByName($username){
        $params = array($username);
        
        $sql = 'SELECT * FROM '.$this->tableName($this->_Table).' WHERE username = ? LIMIT 1';
        return $this->getRowBySQL($sql, $params);    
    }
    
}
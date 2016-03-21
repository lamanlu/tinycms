<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model{
    
    protected $_DB = array();
    
    protected $_Current_DB = 'main';

    protected $_Table = NULL;

    public function __construct() {
        parent::__construct();        
    }
    
    public function db($group){
        
        if(!isset($this->_DB[$group])){            
            $db = $this->load->database($group,TRUE);
            if(!is_null($db)){
                $this->_DB[$group] = $db;
            }
        }
        
        return $this->_DB[$group];
    }


    protected function tableName($name){
        return $this->config->item('dbprefix').$name;
    }
    
    protected function checkTable(){
        if(is_null($this->_Table)){
            exit('Table属性不能为空');
        }
        
        return $this->tableName($this->_Table);
    }
    
    public function getDataByID($id,$table = NULL,$db_group = NULL){
        
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        $sql = "SELECT * FROM $table WHERE id = ? LIMIT 1;";
        
        return $this->getRowBySQL($sql, array($id),$db_group);
    }

    public function insertData($data,$table = NULL,$db_group = NULL){
        
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        if(is_null($db_group)){
            $db_group = $this->_Current_DB;
        }
        
        $this->db($db_group)->insert($table, $data);
        
        return $this->db($db_group)->insert_id();
    }
    
    public function insertBatchData($data,$table = NULL,$db_group = NULL){
        
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        if(is_null($db_group)){
            $db_group = $this->_Current_DB;
        }
        
        $this->db($db_group)->insert_batch($table, $data);
    }
    
    public function updateData($params,$data = array(),$table = NULL,$db_group = NULL){
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        if(is_null($db_group)){
            $db_group = $this->_Current_DB;
        }
        
        $this->db($db_group)->update($table,$data,$params);
        
        return $this->db($db_group)->affected_rows();
    }
    
    public function deleteData($params,$table = NULL,$db_group = NULL){
        if(empty($params)){
            return FALSE;
        }
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        if(is_null($db_group)){
            $db_group = $this->_Current_DB;
        }
        
        return $this->db($db_group)->delete($table,$params);        
    }
    
    public function getListBySQL($sql,$params = array(),$db_group = NULL){
        
        if(is_null($db_group)){
            $db_group = $this->_Current_DB;
        }
        
        $query = $this->db($db_group)->query($sql,$params);
        
        $data = $query->result_array();
        if(empty($data)){
            return array();
        }
        
        return $data;        
    }
    
    public function getRowBySQL($sql,$params = array(),$db_group = NULL){
        
        if(is_null($db_group)){
            $db_group = $this->_Current_DB;
        }
        
        $query = $this->db($db_group)->query($sql,$params);
        
        return $query->row_array();        
        
    }
}




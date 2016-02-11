<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model{
    
    protected $_DB_Main = null;
    
    protected $_Table = NULL;

    public function __construct() {
        parent::__construct();
        
        $this->_DB_Main = $this->load->database('main',TRUE);
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
    
    public function getDataByID($id,$table = NULL){
        
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        $sql = "SELECT * FROM $table WHERE id = ? LIMIT 1;";
        
        return $this->getRowBySQL($sql, array($id));
    }

    public function insertData($data,$table = NULL){
        
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        $this->_DB_Main->insert($table, $data);
        
        return $this->_DB_Main->insert_id();
    }
    
    public function insertBatchData($data,$table = NULL){
        
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        $this->_DB_Main->insert_batch($table, $data);
    }
    
    public function updateData($params,$data = array(),$table = NULL){
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        $this->_DB_Main->update($table,$data,$params);
        
        return $this->_DB_Main->affected_rows();
    }
    
    public function deleteData($params,$table = NULL){
        if(empty($params)){
            return FALSE;
        }
        if(is_null($table)){
            $table = $this->checkTable();
        }else{
            $table = $this->tableName($table);
        }
        
        return $this->_DB_Main->delete($table,$params);        
    }
    
    public function getListBySQL($sql,$params = array()){
        
        $query = $this->_DB_Main->query($sql,$params);
        
        $data = $query->result_array();
        if(empty($data)){
            return array();
        }
        
        return $data;        
    }
    
    public function getRowBySQL($sql,$params = array()){
        
        $query = $this->_DB_Main->query($sql,$params);
        
        return $query->row_array();        
        
    }
}




<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdmRoleNode_model extends MY_Model{


    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'adm_role_node';
    }
    
    
    public function getNodeList($roleId){
        
        $sql = 'SELECT A.*,B.role_id FROM '.$this->tableName('adm_node').' A '
                . 'LEFT JOIN (SELECT * FROM '.$this->tableName('adm_role_node').' WHERE role_id = ? ) B ON A.id = B.node_id '
                . 'WHERE 1 '
                . 'ORDER BY A.parent_id,A.id;';
        
        $params = array($roleId);
        
        $tmp = $this->getListBySQL($sql,$params);
        
        $data = array();
        
        foreach($tmp as $t){
            if(empty($t['parent_id'])){
                $data[$t['id']] = $t;
            }else{
                $data[$t['parent_id']]['sub'][$t['id']] = $t;
            }
        }
        
        return $data;
    }
    
    public function getRoleNode($roleId){
        $sql = 'SELECT B.* FROM '.$this->tableName('adm_role_node').' A '
                . 'LEFT JOIN '.$this->tableName('adm_node').' B ON A.node_id = B.id '
                . 'WHERE A.role_id = ? ;';
        
        $params = array($roleId);
        
        return $this->getListBySQL($sql,$params);
    }
    
    public function saveRoleNode($roleId,$nodeArr){
        
        $sql = 'SELECT node_id FROM '.$this->tableName('adm_role_node').' WHERE role_id = ? ;';
        
        $params = array($roleId);
        
        $tmp = $this->getListBySQL($sql, $params);        
        $roleNodes = create_array_by_key($tmp, 'node_id');
        
        $data = array();
        foreach($nodeArr as $idx => $node_id){
            if(isset($roleNodes[$node_id])){
                unset($roleNodes[$node_id]);
                unset($nodeArr[$idx]);
                continue;
            }
            $data[$node_id] = array(
                'role_id' => $roleId,
                'node_id' => $node_id,
            );
        }
        
        $delArr = array_keys($roleNodes);
        $delStr = implode(',', $delArr);
        
        $this->_DB_Main->trans_start();//开启事务
        
        if($delStr != ''){
            $sql = 'DELETE FROM '.$this->tableName('adm_role_node').' WHERE  role_id = '.$roleId.' AND node_id IN ( '.$delStr.' )';
            $this->_DB_Main->query($sql);
        }
        
        if(!empty($data)){
            $this->insertBatchData($data, 'adm_role_node');
        }
        
        $this->_DB_Main->trans_complete();//提交事务
        
        //判断事务提交结果
        if($this->_DB_Main->trans_status() === FALSE){
            return FALSE;
        }
        
        return TRUE;
    }
}
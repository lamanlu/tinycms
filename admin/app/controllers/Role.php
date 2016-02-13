<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends MY_Controller{
    
    
    public function __construct() {
        parent::__construct();
        
        $this->_Redirect_URL = get_url('role');
        
        $this->setBreadcrumb('角色管理', $this->_Redirect_URL);
        
        $this->load->model('AdmRoleNode_model','RoleNodeModel');
    }
    
    public function roleNode(){
        $this->setBreadcrumb('设置权限');
        
        $roleId = intval($this->getField('id'));
        
        $nodeArr = $this->RoleNodeModel->getNodeList($roleId);
        
        $this->setData('roleId', $roleId);
        $this->setData('nodeArr', $nodeArr);
        $this->display('role/role_node.html');
    }
    
    public function save(){
        $nodes = $this->postField('node');
        $roleId = intval($this->postField('id'));

        if(empty($roleId)){
            $this->showNoticePage(array('错误参数'));
        }
        
        if(empty($nodes) || !is_array($nodes)){
            $this->showNoticePage(array('错误参数'));
        }
        
        $data = array();
        foreach ($nodes as $moduleId => $subArr){
            $data[$moduleId] = $moduleId;
            foreach ($subArr as $nodeId){
                if(!isset($data[$nodeId])){
                    $data[$nodeId] = $nodeId;
                }
            }
        }
        
        $res = $this->RoleNodeModel->saveRoleNode($roleId,$data);
        
        if($res){
            $this->showNoticePage(array('设置权限成功'));
        }else{
            $this->showNoticePage(array('设置权限失败'));
        }
    }
}
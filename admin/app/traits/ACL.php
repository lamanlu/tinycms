<?php
/**
 * @package ACL
 * @author LamanLu
 * @since 2016-02-13
 */
defined('BASEPATH') OR exit('No direct script access allowed');

trait ACL {
    
    private $_ACL_Key = 'admin_acl';

    private function checkACL(){        
        $class = strtolower($this->router->class);
        $method = strtolower($this->router->method);

        if(!isset($_SESSION[$this->_ACL_Key][$class][$method])){
            $msg = array('没有操作权限');
            $this->showNoticePage($msg);
        }
    }
    
    
    private function loadRoleACL($roleId){
        $this->load->model('AdmRoleNode_model','RoleNodeModel');
        
        $nodeArr = $this->RoleNodeModel->getRoleNode($roleId);
        
        $acl = array();
        
        foreach($nodeArr as $node){
            $class = strtolower($node['node_module']);
            $method = strtolower($node['node_act']);
            $acl[$class][$method] = 1;
        }

        $_SESSION[$this->_ACL_Key] = $acl;
    }
}
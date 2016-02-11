<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller{
    
    public function __construct() {
        parent::__construct();
        
        $this->_Redirect_URL = get_url('admin');
        $this->setBreadcrumb('后台账号管理', $this->_Redirect_URL);
        
        $this->load->model('Admin_model','AdminModel');
        $this->load->model('AdmRole_model','AdmRoleModel');
    }
    
    public function index(){  
        
        $this->setBreadcrumb('账号列表');
        
        $roleList = $this->AdmRoleModel->getRoleList();
        
        $this->setData('roleList', $roleList);
        $this->display('admin/index.html');
    }
    
    public function getList(){
        $draw = intval($this->getField('draw'));
        $start = intval($this->getField('start'));
        $pageSize = intval($this->getField('length'));
        $tmp = $this->getField('search');//datatable查询条件
        
        $search = array();
        if(!empty($tmp['value'])){
            $search = json_decode($tmp['value'],TRUE);
        }
        
        $pageSize = empty($pageSize)?$this->_PageSize:$pageSize;
        
        $conditon = array();
		
        if(isset($search['role'])){
            $tmp = intval($search['role']);
            if(!empty($tmp)){
                $conditon['role_id'] = $tmp;
            }
        }
        if(isset($search['name'])){
            $tmp = trim($search['name']);
            if($tmp != ''){
                $conditon['name'] = $tmp;
            }
        }
        
        $adminList = $this->AdminModel->getAdminList($conditon,TRUE,$start,$pageSize);
        $count = $this->AdminModel->getAdminCount($conditon);
        
        $roleList = $this->AdmRoleModel->getRoleList();
        
        $data = array(
            "draw" => $draw,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => array(),
        ); 
        
        foreach($adminList as $admin){
            $data['data'][] = array(
                'uname' => $admin['username'],
                'nickname' => $admin['nickname'],
                'role_name' => $roleList[$admin['role_id']]['role_name'],
                'status' => $admin['is_baned']?'已禁用':'正常',
                'last_login' => empty($admin['last_login'])?'从未登陆':date('Y-m-d H:i',$admin['last_login']),
                'id' => $admin['id'],
            );
        }
        
        echo json_encode($data);
    }
    
    public function add(){
        
        $this->setBreadcrumb('新建账号');
        
        $roleList = $this->AdmRoleModel->getRoleList();
        
        $this->setData('roleList', $roleList);
        $this->display('admin/add.html');
    }
    
    
    /**
     * 新增管理员账号
     * @author LamanLu
     * @since 2016-01-24
     */
    public function addAdmin(){
        $nickname = trim($this->postField('nickname'));
        $uname = trim($this->postField('uname'));
        $pwd = $this->postField('pwd');
        $repwd = $this->postField('repwd');
        $roleId = intval($this->postField('role'));
        $status = intval($this->postField('status'));
        
        $msg = array();
        if($nickname == ''){
            $msg[] = '【真实姓名】不能为空！';
        }
        if($uname == ''){
            $msg[] = '【用户名】不能为空！';
        }
        if($pwd == ''){
            $msg[] = '【密码】不能为空！';
        }
        if($pwd != $repwd){
            $msg[] = '两次输入的密码不一致！';
        }
        if(empty($roleId)){
            $msg[] = '【用户组】不能为空！';
        }
        if(!empty($msg)){
            $this->showNoticePage($msg);
        }
        
        //检查重复
        $existData = $this->AdminModel->checkExist($uname);
        if(!empty($existData)){
            $this->showNoticePage(array('已存在管理员账号：'.$uname));
        }
        
        $user = array(
            'username' => $uname,
            'userpwd' => $this->password($pwd),
            'nickname' => $nickname,
            'role_id' => $roleId,
            'is_baned' => $status,
            'create_time' => time(),
            'update_time' => time(),
        );
        
        $res = $this->AdminModel->saveAdmin($user);
        
        if(!empty($res)){
            $msg[] = '新增账号成功！';
        }else{
            $msg[] = '新增账号失败！';
        }
        
        $this->showNoticePage($msg);
    }
    
    public function edit(){
        
        $this->setBreadcrumb('编辑账号');
        
        $id = intval($this->getField('id'));
        
        if(empty($id)){
            $this->showNoticePage(array('参数不能为空！'));
        }
        
        $admin = $this->AdminModel->getDataByID($id);
        
        if(empty($admin)){
            $this->showNoticePage(array('无效的参数！'));
        }
        
        $roleList = $this->AdmRoleModel->getRoleList();
        
        $this->setData('admin', $admin);
        $this->setData('roleList', $roleList);
        $this->display('admin/edit.html');
    }
    
    public function updateAdmin(){
        $id = intval($this->postField('id'));
        $nickname = trim($this->postField('nickname'));
        $roleId = intval($this->postField('role'));
        $status = intval($this->postField('status'));
        
        $msg = array();
        if(empty($id)){
            $msg[] = '参数不能为空！';
        }
        if($nickname == ''){
            $msg[] = '【真实姓名】不能为空！';
        }
        if(empty($roleId)){
            $msg[] = '【用户组】不能为空！';
        }
        if(!empty($msg)){
            $this->showNoticePage($msg);
        }
        
        if($id == 1){
            $status = 0;
        }
        
        $data = array(
            'nickname' => $nickname,
            'role_id' => $roleId,
            'is_baned' => $status,
            'update_time' => time()
        );
        $this->AdminModel->updateAdmin($id,$data);
        
        $this->showNoticePage(array('编辑账号信息成功'));
    }
    
    /**
     * 生成管理员账号的密码
     * @param type $pwd
     * @return type
     * @author LamanLu
     * @since 2016-01-24
     */
    private function password($pwd){
        $str = $this->config->item('salt_key').$pwd;
        return md5($str);
    }
}

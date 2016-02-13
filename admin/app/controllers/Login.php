<?php
/**
 * @package 后台用户登录
 * @author LamanLu
 * @since 2016-01-10
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller{
    
    use ACL;
    
    public function __construct() {
        parent::__construct();        
    }
    
    public function index(){
        $this->load->view('login.html');
    }
    
    public function doLogin(){
        $data = array(
            'code' => 0,
            'msg' => '未知错误',
        );
        
        $uname = trim($this->input->post('uname'));
        $pwd = $this->input->post('pwd');
        
        if($uname == ''){
            $data = array(
                'code' => -1,
                'msg' => '用户名不能为空',
            );
            echo json_encode($data); exit;
        }
        
        $this->load->model('Admin_model','AdminModel');        
        $admin = $this->AdminModel->getAdminByName($uname);
        
        if(empty($admin)){
            $data = array(
                'code' => -2,
                'msg' => '用户名或者密码错误',
            );
            echo json_encode($data); exit;
        }

        $checkPwd = md5($this->config->item('salt_key').$pwd);
        
        if($checkPwd != $admin['userpwd']){
            $data = array(
                'code' => -3,
                'msg' => '用户名或者密码错误',
            );
            echo json_encode($data); exit;
        }
        
        if($admin['is_baned'] == 1){
            $data = array(
                'code' => -4,
                'msg' => '用户已被禁用，请联系管理员',
            );
            echo json_encode($data); exit;            
        }
        
        $data = array(
            'last_login' => time()
        );
        $this->AdminModel->updateAdmin($admin['id'],$data);        
        
        $this->session->set_userdata('admin_uid',$admin['id']);
        $this->session->set_userdata('admin_uname',$admin['username']);
        $this->session->set_userdata('admin_nickname',$admin['nickname']);
        $this->session->set_userdata('admin_roleId',$admin['role_id']);        
        
        $this->loadRoleACL($admin['role_id']);
        
        $data = array(
            'code' => 1,
            'msg' => '登陆成功',
        );
        echo json_encode($data);
    }
    
    public function logout(){
        $this->session->sess_destroy();
        redirect(get_url('login'));
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller{
    
    public function __construct() {
        parent::__construct();
        
        $this->load->helper('ajax_helper');
        $this->load->helper('pwd');
        $this->load->model('User_model','UserModel');
    }    
    
    public function regist(){
        $uname = trim($this->postField('username'));
        $pwd = $this->postField('password');
        $repwd = $this->postField('repwd');
        
        if($uname == ''){
            echo ajax_return(-1, '用户名不能为空');exit;
        }
        
        if($pwd == '' || strlen($pwd) < 6){
            echo ajax_return(-2, '密码长度必须大于6位');exit;
        }
        
        if($pwd != $repwd){
            echo ajax_return(-3, '2次密码输入不一致');exit;
        }
        
        $checkArr = array();
        $checkArr[] = array(
            'user_name' => $uname,
        );
        $existUser = $this->UserModel->checkExist($checkArr);
        if(!empty($existUser)){
            echo ajax_return(-4, '已经存在的用户名');exit;            
        }        
        
        $password = createUserPwd($pwd);
        
        $data = array(
            'user_name' => $uname,
            'user_pwd' => $password,
        );
 
        $userId = $this->UserModel->regist($data);
        
        if(!empty($userId)){
            $res = array(
                'uid' => $userId,
                'uname' => $uname,
            );
            echo ajax_return(1, '注册成功', $res);
        }else{
            echo ajax_return(-5, '注册失败');
        }
    }
    
    public function login(){
        $uname = trim($this->postField('username'));
        $pwd = $this->postField('password');
        $type = $this->postField('type');
        
        if($uname == '' || $pwd == ''){
            echo ajax_return(-1, '用户名与密码不能为空');exit;
        }
        

        $userId = $this->UserModel->getUidByField('user_name',$uname);

        if(empty($userId)){
            echo ajax_return(-2, '无效的登录用户');exit;
        }
        
        $userInfo = $this->UserModel->getUserById($userId);
        
        $password = createUserPwd($pwd);
        if($password != $userInfo['user_pwd']){
            echo ajax_return(-3, '登录密码不正确');exit;
        }
        
        $data = array(
            'uid' => $userInfo['id'],
            'uname' => $userInfo['user_name'],
        );
        $this->session->set_userdata($data);
        echo ajax_return(1, '登录成功', $data);
    }
    
    
    private function doLogin($username,$password){
        
        
        
    }
    
    
}



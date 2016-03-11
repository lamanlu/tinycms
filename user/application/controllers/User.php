<?php
/**
 * @package 用户注册/登录接口
 * @author LamanLu
 * @since 2016-03-08
 */

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
        
        $rule = "/^[a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i";
        if(!preg_match($rule, $uname)){
            echo ajax_return(-2, '不合法的用户名');exit;
        }
        
        if($pwd == '' || strlen($pwd) < 6){
            echo ajax_return(-3, '密码长度必须大于6位');exit;
        }
        
        if($pwd != $repwd){
            echo ajax_return(-4, '2次密码输入不一致');exit;
        }
        
        $existFlag = $this->UserModel->checkExistName($uname);
        if($existFlag){
            echo ajax_return(-5, '已经存在的用户名');exit;            
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
            $this->doLogin($uname, $pwd);
            echo ajax_return(1, '注册成功', $res);
        }else{
            echo ajax_return(-5, '注册失败');
        }
    }
    
    public function login(){
        $uname = trim($this->postField('username'));
        $pwd = $this->postField('password');
        
        if($uname == '' || $pwd == ''){
            echo ajax_return(-1, '用户名与密码不能为空');exit;
        }

        $res = $this->doLogin($uname, $pwd);
        echo $res;
    }    
    
    private function doLogin($uname,$pwd){
        
        $userId = $this->UserModel->getUidByName($uname);

        if(empty($userId)){
            return ajax_return(-2, '无效的登录用户');
        }
        
        $userInfo = $this->UserModel->getUserById($userId);

        if(empty($userInfo)){
            return ajax_return(-3, '无效的UID');
        }
        
        $password = createUserPwd($pwd);
        if($password != $userInfo['user_pwd']){
            return ajax_return(-3, '登录密码不正确');
        }
        
        $data = array(
            'uid' => $userInfo['uid'],
            'uname' => $userInfo['user_name'],
        );
        $this->session->set_userdata($data);

        $timeStamp = time();
        $signStr = $userInfo['uid'].$timeStamp.$this->config->item('login_cookie_key');
        $sign = md5($signStr);
        $expire = 7*86400;
        set_cookie('uid', $userInfo['uid'], $expire);
        set_cookie('lg_ct', $timeStamp, $expire);
        set_cookie('sign', $sign, $expire);
        
        $param = array('id' => $userInfo['uid']);
        $data = array('last_login' => time());
        $this->UserModel->updateData($param,$data);
        
        return ajax_return(1, '登录成功', $data);        
    }
    
    
}



<?php
/**
 * @package 用户注册/登录接口
 * @author LamanLu
 * @since 2016-03-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller{
    
    //注册/登录类型数组
    private $_Act_Type = array(
        'email' => 1,
        'mobile' => 2,
    );
    
    //账号格式类型，对应表字段
    private $_ACC_Field = array(
        1 => 'email',
        2 => 'mobile',
    );

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
        $type = trim($this->postField('type'));
        
        if(!isset($this->_Act_Type[$type])){
            echo ajax_return(-1, '无效的注册类型');exit;
        }
        
        $typeId = $this->_Act_Type[$type];
        
        if($uname == ''){
            echo ajax_return(-2, '用户名不能为空');exit;
        }
        
        switch ($typeId){
            case 1:
                $rule = "/^[a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i";                
                break;
            case 2:
                $rule = "/^1[34578]\d{9}$/";
                break;
        }
        if(!preg_match($rule, $uname)){
            echo ajax_return(-3, '不合法的用户名');exit;
        }
        
        $userField = $this->_ACC_Field[$typeId];
        
        if($pwd == '' || strlen($pwd) < 6){
            echo ajax_return(-4, '密码长度必须大于6位');exit;
        }
        
        if($pwd != $repwd){
            echo ajax_return(-5, '2次密码输入不一致');exit;
        }
        
        $existFlag = $this->UserModel->checkExistName($userField,$uname);
        if($existFlag){
            echo ajax_return(-6, '已经存在的用户名');exit;            
        }        
        
        $password = createUserPwd($pwd);
        
        $data = array(
            $userField => $uname,
            'user_pwd' => $password,
        );
 
        $userId = $this->UserModel->regist($data);
        
        if(!empty($userId)){
            $res = array(
                'uid' => $userId,
                $userField => $uname,
            );
            $this->doLogin($typeId,$uname, $pwd);
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
        
        $ruleEmail = "/^[a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i";
        $ruleMobile = "/^1[34578]\d{9}$/";
        
        $typeId = 0;
        if(preg_match($ruleEmail, $uname)){
            $typeId = 1;
        }
        if(preg_match($ruleMobile, $uname)){
            $typeId = 2;
        }
        
        if(empty($typeId)){
            echo ajax_return(-2, '无效的账号格式');exit;
        }

        $res = $this->doLogin($typeId, $uname, $pwd);
        echo $res;
    }    
    
    private function doLogin($typeId,$uname,$pwd){
        
        $field = $this->_ACC_Field[$typeId];
        
        $userId = $this->UserModel->getUidByName($field,$uname);

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
        
        $this->setLoginSession($userInfo);
        $this->setLoginCookie($userInfo['uid']);
        $this->afterLogin($userInfo);
        
        $data = array(
            'uid' => $userInfo['uid'],
            'email' => $userInfo['email'],
            'mobile' => $userInfo['mobile'],
        );
        return ajax_return(1, '登录成功', $data);        
    }
    
    private function setLoginSession($userInfo){
        $data = array(
            'uid' => $userInfo['uid'],
            'email' => $userInfo['email'],
            'mobile' => $userInfo['mobile'],
        );
        $this->session->set_userdata($data);
    }

    private function setLoginCookie($uid){
        $timeStamp = time();
        $signStr = $uid.$timeStamp.$this->config->item('login_cookie_key');
        $sign = md5($signStr);
        $expire = 7*86400;
        set_cookie('uid', $uid, $expire);
        set_cookie('lg_ct', $timeStamp, $expire);
        set_cookie('sign', $sign, $expire);
    }
    
    private function afterLogin($userInfo){
        $param = array('id' => $userInfo['uid']);
        $data = array('last_login' => time());
        $this->UserModel->updateData($param,$data);
    }
    
    
}



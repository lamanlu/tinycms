<?php

trait UserManager {
    
    
    protected function checkLoginStatus(){
        $uid = $this->session->userdata('uid');
        
        if(empty($uid)){            
            //try auto login
            $cookie_uid = get_cookie('uid', TRUE);
            $lg_ct = get_cookie('lg_ct', TRUE);
            $sign = get_cookie('sign', TRUE);
            
            if($cookie_uid == '' || $lg_ct == '' || $sign == ''){
                return;
            }
            $signStr = $cookie_uid.$lg_ct.$this->config->item('login_cookie_key');
            $signCheck = md5($signStr);

            if($sign == $signCheck){                
                $this->loginByUid($cookie_uid);                
            }

        }
    }
    
    protected function loginByUid($uid){        
        $this->load->model('global/UserInfo_model','UserInfoModel');
        
        $userInfo = $this->UserInfoModel->getUserInfoById($uid);

        if(empty($userInfo)){
            return -1;
        }
        
        $this->setLoginSession($userInfo);        
    }
    
    protected function setLoginSession($userInfo){
        $data = array(
            'uid' => $userInfo['uid'],
            'email' => $userInfo['email'],
            'mobile' => $userInfo['mobile'],
        );
        $this->session->set_userdata($data);
    }
    
    
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller{
    
    public function __construct() {
        parent::__construct();
        $this->load->model('User_model','UserModel');
    }    
    
    public function regist(){
        $uname = trim($this->postField('username'));
        $pwd = $this->postField('password');
        
        
    }
    
    public function login(){
        
        echo 'login';
    }
    
    private function doLogin(){
        
    }
    
    
}



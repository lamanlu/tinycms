<?php
/**
 * @package 页面测试类，不做实际用途
 * @author LamanLu
 * @since 2016-01-14
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $id = intval($this->getField('id'));
        $text = $this->postField('text');
        
        if($id > 0){
            echo 'Update Text:'.$text;
        }else{
            echo 'Insert Test:'.$text;
        }
    }
    
    public function reg(){
        $this->display('test/reg.html','blank.html');
    }
    
    public function login(){
        $this->display('test/login.html','blank.html');
    }
    
    public function echoSession(){
        print_r($_SESSION);
    }
    
}




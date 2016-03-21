<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once BASEPATH.'traits/UserManager.php';

class MY_Controller extends CI_Controller {
    
    use UserManager;
    
    protected $_Redirect_URL = '/';
    
    protected $_SYS_Config = array();

    public function __construct() {
        parent::__construct();       
        date_default_timezone_set($this->config->item('time_reference'));
    }
    
    protected function getField($key,$xss = FALSE){
        return $this->input->get($key,$xss);
    }
    
    protected function postField($key,$xss = FALSE){
        return $this->input->post($key,$xss);
    }
    
    
}


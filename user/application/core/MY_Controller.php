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
        $this->loadGlobalConfig();
        $this->checkLoginStatus();
    }
    
    protected function getField($key,$xss = FALSE){
        return $this->input->get($key,$xss);
    }
    
    protected function postField($key,$xss = FALSE){
        return $this->input->post($key,$xss);
    }
    
    private function loadGlobalConfig(){
        $this->load->model('global/Config_model','ConfigModel');
        $this->_SYS_Config = $this->ConfigModel->loadConfig();
    }
    
}


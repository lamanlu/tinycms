<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'traits/ACL.php';

class MY_Controller extends CI_Controller {
    
    use ACL;
    
    protected $_PageSize = 15;

    protected $_Redirect_URL = '/';
    
    protected $_Breadcrumb = array(); //面包屑数组
    
    protected $_SYS_Config = array();

    public function __construct() {
        parent::__construct();       
        date_default_timezone_set($this->config->item('time_reference'));
        
        $this->checkLogin();
        
        $this->checkACL();
        
        //加载站点全局系统配置               
        $this->loadSysConfig();
    }
    
    protected function getField($key,$xss = FALSE){
        return $this->input->get($key,$xss);
    }
    
    protected function postField($key,$xss = FALSE){
        return $this->input->post($key,$xss);
    }
    

    protected function showNoticePage($messages = array()){       
        $this->setData('massages', $messages);
        $this->setData('redirect_url', $this->_Redirect_URL);
        echo $this->display('/errors/notice_page.html','default.html',TRUE);
        exit;
    }
    
    protected function setBreadcrumb($title,$url = ''){
        $this->_Breadcrumb[] = array(
            'title' => $title,
            'url' => $url,
        );
    }
    
    protected function loadSysConfig(){
        $this->load->model('SysConfig_model','SysConfigModel'); 
        $configs = $this->SysConfigModel->getConfigList(1);
        if(!empty($configs)){
            foreach ($configs as $config){
                $this->_SYS_Config[$config['config_key']] = $config['config_val'];
            }
        }
    }

    protected function SysConfigItem($key){        
        if(!isset($this->_SYS_Config[$key])){            
            return FALSE;
        }
        
        return $this->_SYS_Config[$key];
    }

    protected function _beforeDisplay() {
        $this->setData('breadcrumb', $this->_Breadcrumb);
        
        $nickname = $this->session->userdata('admin_nickname');
        $this->setData('admin_nickname', $nickname);
    }
    
    protected function checkLogin(){
        $uid = $this->session->userdata('admin_uid');
        if(empty($uid)){
            redirect(get_url('login'));
        }
    }
}

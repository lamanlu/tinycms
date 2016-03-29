<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once BASEPATH.'traits/UserManager.php';

class MY_Controller extends CI_Controller {
    
    use UserManager;
    
    protected $_Redirect_URL = '/';

    public function __construct() {
        parent::__construct();       
        date_default_timezone_set($this->config->item('time_reference'));
        $this->loadGlobalConfig();
        $this->checkLoginStatus();
    }
    
    private function loadGlobalConfig(){
        $this->load->model('global/Config_model','ConfigModel');
        $config = $this->ConfigModel->loadConfig();
        
        $data = array();
        
        //图片类
        $data['img_upload_int'] = $config['img_upload_int'];
        if(!empty($config['img_cdn_enable'])){
            $data['img_domain'] = $config['img_cdn_domain'];
        }else{
            $data['img_domain'] = $config['img_base_domain'];
        }
        
        //文件类
        $data['file_upload_int'] = $config['file_upload_int'];
        if(!empty($config['file_cdn_enable'])){
            $data['file_domain'] = $config['file_cdn_domain'];
        }else{
            $data['file_domain'] = $config['file_base_domain'];
        }
        
        //静态资源
        if(!empty($config['static_cdn_enable'])){
            $data['static_domain'] = $config['static_cdn_domain'];
        }else{
            $data['static_domain'] = $this->config->item('static_base_domain');
        }
        
        foreach($data as $key => $val){
            $this->config->set_item($key, $val);
        }
        
    }
    
    
}


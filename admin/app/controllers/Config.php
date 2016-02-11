<?php
/**
 * @package 系统配置参数管理
 * @author LamanLu
 * @since 2016-01-26
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends MY_Controller{
    
    public function __construct() {
        parent::__construct();
        
        $this->setBreadcrumb('公用信息管理');
        
        $this->load->model('SysConfig_model','SysConfigModel'); 
        
    }
    
    public function designConfig(){        
        $this->setBreadcrumb('定制参数配置');
        
        $configs = $this->SysConfigModel->getConfigList(2);
//        print_r($configs); exit;
        $this->setData('configs', $configs);
        $this->display('config/edit_2.html');
    }
    
    public function save(){
        $fields = $this->postField('cfg_field');
        $values = $this->postField('cfg_value');

        if(!empty($fields)){
            foreach($fields as $idx => $field){
                if(isset($values[$idx])){
                    $this->SysConfigModel->updateConfig($field,$values[$idx]);
                }
            }
        }
        $this->showNoticePage(array('保存成功'));
    }
}

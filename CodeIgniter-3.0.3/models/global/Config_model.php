<?php
/**
 * @package 站点全局配置信息
 * @author LamanLu
 * @since 2016-03-21
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends MY_Model {
    
    /**
     * 获取站点全局配置
     * 1、静态资源文件域名
     * 2、图片相关配置
     * 3、文件相关配置
     * @author LamanLu
     * @since 2016-03-21
     */
    public function loadGlobalConfig(){
        
        $config = array();
        
        $sql = 'SELECT config_key,config_val FROM '.$this->tableName('sys_config').' WHERE group_id = 1;';
        $tmp = $this->getListBySQL($sql); 
        
        foreach($tmp as $t){
            $config[$t['config_key']] = $t['config_val'];
        }
        
        return $config;
    }
}

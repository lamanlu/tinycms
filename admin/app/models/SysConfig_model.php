<?php
/**
 * @package 系统全局配置参数
 * @author LamanLu
 * @since 2016-01-14
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class SysConfig_model extends MY_Model{

    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'sys_config';
    }
    
    public function getConfigList($group_id = 0){
        $where = '';
        $params = array();
        if(!empty($group_id)){
            $where = ' AND group_id = ? ';
            $params[] = $group_id;
        }
        
        $sql = 'SELECT * FROM '.$this->tableName($this->_Table).' WHERE 1 '.$where;
        
        $tmp = $this->getListBySQL($sql,$params);
        
        return create_array_by_key($tmp, 'config_key');         
    }
    
    public function updateConfig($key,$value){
        $params = array(
            'config_key' => $key,
        );
        $data = array(
            'config_val' => $value,
            'update_time' => time(),
        );
        return $this->updateData($params, $data);
    }
    
}

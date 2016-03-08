<?php
/**
 * @package Redis Cluster Connecter
 * @author LamanLu
 * @since 2016-02-28
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class CI_Redis_cluster {
    
    protected $_Redis = null;
    
    public function __construct($configFile = array()) {
        $CI =& get_instance();
        
        $configName = array_shift($configFile);
        
        if ($CI->config->load($configName, TRUE, TRUE)){
            $config = $CI->config->item($configName);
        }
        
        if(empty($config)){
            exit('No redis MQ config file');
        }
        
        $hostArr = array();
        foreach($config as $conf){
            $hostArr[] = $conf['host'].':'.$conf['port'];
        }

        $this->_Redis= new RedisCluster(NULL,$hostArr);        
    }
    
    public function getInstance(){
        return $this->_Redis;
    }
    
    
}
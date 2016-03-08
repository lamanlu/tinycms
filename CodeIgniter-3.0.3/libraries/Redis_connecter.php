<?php
/**
 * @package Redis Connecter
 * @author LamanLu
 * @since 2016-02-28
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class CI_Redis_connecter {
    
    protected $_Redis = null;
    
    public function __construct($configFile = array()) {
        $CI =& get_instance();
        
        $configName = array_shift($configFile);
        
        if ($CI->config->load($configName, TRUE, TRUE)){
            $config = $CI->config->item($configName);
        }
        
        if(empty($config)){
            exit('No redis connecter config file');
        }
        
        $this->_Redis= new Redis();
        $success = $this->_Redis->connect($config['host'],  $config['port'],$config['timeout']);
        if ( ! $success){
            exit('RedisConnecter: Redis connection failed. Check your configuration.');
        }

        if (isset($config['password']) && !is_null($config['password'])){
            if(! $this->_Redis->auth($config['password'])){
                exit('RedisConnecter: Redis authentication failed.');
            }
        }
        
    }
    
    public function getInstance(){
        return $this->_Redis;
    }
    
    
}
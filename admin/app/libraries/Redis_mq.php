<?php
/**
 * @package Redis MQ Class
 * @author LamanLu
 * @since 2016-02-26
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Redis_mq {
    
    protected $_Config = array();
    
    protected $_Redis = null;

    public function __construct() {
        $CI =& get_instance();
        
        if ($CI->config->load('redis_mq', TRUE, TRUE)){
            $this->_Config = $CI->config->item('redis_mq');
        }
        
        if(empty($this->_Config)){
            exit('No redis MQ config file');
        }
        
        $this->_Redis= new Redis();
        $success = $this->_Redis->connect($this->_Config['host'],  $this->_Config['port'],$this->_Config['timeout']);
        if ( ! $success){
            exit('RedisMQ: Redis connection failed. Check your configuration.');
        }

        if (isset($this->_Config['password']) && !is_null($this->_Config['password'])){
            if(! $this->_Redis->auth($this->_Config['password'])){
                exit('RedisMQ: Redis authentication failed.');
            }
        }
    }

    /**
     * Set msg into topic
     * @param type $topic 主题
     * @param type $msg 内容
     */
    public function setMsg($topic,$msg){
        $this->_Redis->lPush($topic,$msg);
    }
    
    /**
     * Get a msg from topic
     * @param type $topic 主题
     */
    public function getMsg($topic){
        
        $msg = $this->_Redis->brPop($topic,1);

        if(empty($msg) || !isset($msg[1])){
            return FALSE;
        }
        
        return $msg[1];
    }
}

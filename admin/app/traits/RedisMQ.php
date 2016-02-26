<?php
/**
 * @package Redis消息队列插件
 * @author LamanLu
 * @since 2016-02-26
 */
trait RedisMQ {
    
    private function setDaemon(){
        ignore_user_abort(true);
        set_time_limit(0);
    }
    
    /**
     * 写消息进队列
     * @param string $topic
     * @param string $msg
     * @return boolean
     * @since 2016-02-26
     */
    public function producer($topic,$msg){
        
        if($topic == '' || $msg == ''){
            return FALSE;
        }
        
        $this->setDaemon();
        
        $this->load->library('Redis_mq',NULL,'RedisMQ');
        
        $this->RedisMQ->setMsg($topic,$msg);
        
        return TRUE;
    }
    
    /**
     * 处理队列消息
     * @param string $topic
     * @return boolean
     */
    public function consumer($topic){
        if($topic == ''){
            return FALSE;
        }

        $this->setDaemon();
        
        $this->load->library('Redis_mq',NULL,'RedisMQ');
        
        while(TRUE){
            $msg = $this->RedisMQ->getMsg($topic);
            
            if($msg === FALSE){
                echo 'no msg'.PHP_EOL;
                sleep(1);
                continue;
            }
            echo $msg.PHP_EOL;
            $this->worker($msg);
        }
    }
    
    /**
     * 处理消息
     * @param string $msg
     * @author LamanLu
     * @since 2016-02-26
     */
    private function worker($msg){
        //deal with msg
    }
}


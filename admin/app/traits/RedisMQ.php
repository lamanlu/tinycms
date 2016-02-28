<?php
/**
 * @package Redis消息队列插件
 * @author LamanLu
 * @since 2016-02-26
 */
trait RedisMQ {
    
    private function setDaemon(){
        if(!is_cli()){
            exit('CLI Only');
        }
        ignore_user_abort(true);
        set_time_limit(0);
    }
    
    /**
     * 写消息进队列
     * @param string $topic
     * @param string $msg
     * @return boolean
     * @author LamanLu
     * @since 2016-02-26
     */
    public function producer($topic,$msg){
        
        if($topic == '' || $msg == ''){
            return FALSE;
        }
        
        $this->load->library('Redis_connecter',array('redis_mq'),'RedisMQ');
        $redisMQ = $this->RedisMQ->getInstance();

        return $redisMQ->lPush($topic,$msg);
        
    }
    
    /**
     * 处理队列消息
     * @param string $topic
     * @return boolean
     * @author LamanLu
     * @since 2016-02-26
     */
    public function consumer($topic){
        if($topic == ''){
            return FALSE;
        }

        $this->setDaemon();
        
        while(TRUE){
            $msg = $this->getMsg($topic);
            
            if($msg === FALSE){
                sleep(1);
                continue;
            }
            
            $this->worker($msg);
        }
    }
    
    /**
     * 获取队列长度
     * @param string $topic
     * @return int
     * @author LamanLu
     * @since 2016-02-28
     */
    public function getMQLength($topic){
        $this->load->library('Redis_connecter',array('redis_mq'),'RedisMQ');
        $redisMQ = $this->RedisMQ->getInstance();
        
        return $redisMQ->lSize($topic);
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
    
    /**
     * Get a msg from topic
     * @param type $topic 主题
     * @author LamanLu
     * @since 2016-02-26
     */
    private function getMsg($topic){
        
        $this->load->library('Redis_connecter',array('redis_mq'),'RedisMQ');
        $redisMQ = $this->RedisMQ->getInstance();
        
        $msg = $redisMQ->brPop($topic,1);

        if(empty($msg) || !isset($msg[1])){
            return FALSE;
        }
        
        return $msg[1];
    }
}


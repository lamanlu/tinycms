<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Mq_worker {
    
    protected $_Brokers = array();


    public function __construct() {
        $this->getConfig();
    }
    
    private function getConfig(){
        $this->_Brokers = array(
            '192.168.29.128:9092',
            '192.168.29.128:9093',
            '192.168.29.128:9094',
        );
    }
    
    /**
     * 发送内容
     * @param type $topic 分类
     * @param type $msg 消息内容
     * @author LamanLu
     * @since 2016-02-25
     */
    public function setMsg($topic,$msg){
        
        $rk = new RdKafka\Producer();
        $rk->setLogLevel(LOG_INFO);
        
        $brokers = implode(',', $this->_Brokers);
        $rk->addBrokers($brokers);
        
        $topicObj = $rk->newTopic($topic);
        
        $topicObj->produce(RD_KAFKA_PARTITION_UA, 0, $msg);
    }
    
    public function getConsumerTopic($topic){
        $rk = new RdKafka\Consumer();
        $rk->setLogLevel(LOG_INFO);
        
        $brokers = implode(',', $this->_Brokers);
        $rk->addBrokers($brokers);
        
//        $topicConf = new RdKafka\TopicConf();
//        $topicConf->set("auto.commit.interval.ms", 1e3);
//        $topicConf->set("offset.store.sync.interval.ms", 60e3);

        $topicObj = $rk->newTopic($topic);

        $topicObj->consumeStart(0, RD_KAFKA_OFFSET_STORED);
        
        return $topicObj;
    }

    public function test(){
        return 'hello world!';
    }
}
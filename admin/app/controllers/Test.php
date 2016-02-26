<?php
/**
 * @package 页面测试类，不做实际用途
 * @author LamanLu
 * @since 2016-01-14
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller{
    
    public function __construct() {
        parent::__construct();
        ignore_user_abort(true);
        set_time_limit(0);
        date_default_timezone_set($this->config->item('time_reference'));
        $this->load->library('mq_worker',NULL,'MQ_Worker');
        
    }
    
    public function index(){
        echo $this->MQ_Worker->test();
    }
    
    public function send(){
        
        $msg = array(
            'task_id' => 5,
            'task_type' => 103,
            'task_commond' => 'refresh_cache',
            'target_time' => time(),
        );
        $msg = json_encode($msg);
        $this->MQ_Worker->setMsg('cms_test_1',$msg);
    }
    
    public function get(){
        $topicObj = $this->MQ_Worker->getConsumerTopic('cms_test_1');
        
        while (TRUE) {
            $msg = $topicObj->consume(0, 1000); 
            if(is_null($msg)){
                echo 'null msg'.PHP_EOL;
                sleep(1);
                continue;
            }
            if($msg->err == 0){
                echo $msg->payload.PHP_EOL;
                $this->saveLog($msg->payload);
            }
        }
    }
    
    public function set(){
        $msg = array(
            'task_id' => 5,
            'task_type' => 103,
            'task_commond' => 'refresh_cache',
            'target_time' => time(),
        );
        $msg = json_encode($msg);
        $this->RedisMQ->setMsg('cache_jobs',$msg);
    }
    
    private function saveLog($msg){
        $msg = json_decode($msg, TRUE);
       
        $log = '处理时间:'.date('Y-m-d H:i:s').',命令:'.$msg['task_commond'].',类别:'.$msg['task_type'].',时间:'.$msg['target_time'].PHP_EOL;
        
        $file = '/mnt/hgfs/project/test/worker.log';
        
        $fileObj = fopen($file, 'a');        
        fwrite($fileObj, $log);
        fclose($fileObj);
        
        return TRUE;
    }
}

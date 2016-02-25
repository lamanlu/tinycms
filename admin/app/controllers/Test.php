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
        date_default_timezone_set($this->config->item('time_reference'));
        $this->load->library('mq_worker',NULL,'MQ_Worker');
    }
    
    public function index(){
        echo $this->MQ_Worker->test();
    }
    
    public function send(){
        
        $msg = array(
            'task_id' => 1,
            'task_type' => 90,
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
            if ($msg->err) {
                echo $msg->errstr(), "\n";
                break;
            } else {
                echo $msg->payload.'<br>';
                $this->saveLog($msg->payload);
            }
        }
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

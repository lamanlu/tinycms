<?php
/**
 * RedisMQ demo
 * @author LamanLu
 * @since 2016-02-26
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'traits/RedisMQ.php';

class Test extends CI_Controller{
    
    use RedisMQ;
    
    public function __construct() {
        parent::__construct();
        date_default_timezone_set($this->config->item('time_reference'));
    }
    
    public function send(){
        $msg = array(
            'task_id' => 5,
            'task_type' => 103,
            'task_commond' => 'refresh_cache',
            'target_time' => time(),
        );
        $msg = json_encode($msg);
        $this->producer('cache_clear', $msg);
    }
    
    public function run(){
        $this->consumer('cache_clear');
    }
    
    public function len(){
        $len = $this->getMQLength('cache_clear');
        print_r($len);
    }
    
    private function worker($msg){
        $msg = json_decode($msg, TRUE);

        $log = '处理时间:'.date('Y-m-d H:i:s').',命令:'.$msg['task_commond'].',类别:'.$msg['task_type'].',时间:'.date('Y-m-d H:i:s',$msg['target_time']).PHP_EOL;
        
        $file = '/mnt/hgfs/Projects/test/worker.log';
        
        $fileObj = fopen($file, 'a');        
        fwrite($fileObj, $log);
        fclose($fileObj);
        
        return TRUE;
    }
}
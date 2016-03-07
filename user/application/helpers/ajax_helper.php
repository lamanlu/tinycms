<?php

if(!function_exists('ajax_return')){
    function ajax_return($code = 0,$msg = '未知错误',$data = array()){
        $res = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        );
        
        return json_encode($res);
    }
}


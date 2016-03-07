<?php

/**
 * 生成用户密码助手
 * @author LamanLu
 * @since 2016-03-07
 */
if(!function_exists('createUserPwd')){
    function createUserPwd($pwd){
        $CI =& get_instance();
        $salt = $CI->config->item('user_salt_key');
        $str = $salt.$pwd;
        return md5($str);
    }    
}
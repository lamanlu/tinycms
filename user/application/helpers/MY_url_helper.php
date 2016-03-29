<?php
defined('BASEPATH') OR exit('No direct script access allowed');


if ( ! function_exists('get_url'))
{
	/**
	 * Get Site URL
	 *
	 * Create a local URL based on your basepath. Segments can be passed via the
	 * first parameter either as a string or an array.
	 *
	 * @param	string	$uri
         * @param	string	$param
	 * @param	string	$protocol
	 * @return	string
	 */
	function get_url($uri = '', $param = NULL, $protocol = 'http://')
	{
		$url = site_url($uri, $protocol);
                if(!is_null($param)){
                    $url .= '?'.$param;
                }
                return $url;
	}
}

if(!function_exists('static_url')){
    
    /**
     * 获取静态资源链接
     * @param int $type 1：CSS、JS，2：图片，3：文件
     * @param string $url
     * @param string $version
     * @author LamanLu
     * @since 2016-03-29
     */
    function static_url($type, $url, $version = '20160329'){
        $CI =& get_instance();
        
        $configMap = array(
            1 => 'static_domain',
            2 => 'img_domain',
            3 => 'file_domain',
        );
        if(!isset($configMap[$type])){            
            exit('错误的资源类型');
        }
        $key = $configMap[$type];
        $site = $CI->config->item($key);
        $site = rtrim($site, '/');
        $url = ltrim($url, '/');
        return $site.'/'.$url.'?'.$version; 
    }
    
}
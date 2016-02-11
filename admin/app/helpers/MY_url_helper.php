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


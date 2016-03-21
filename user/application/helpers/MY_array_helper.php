<?php

if ( ! function_exists('create_array_by_key'))
{
	/**
	 * 根据指定的key 重新生成以KEY对应值为索引的多维数组
	 *
	 * @param	array
	 * @return	array
	 */
	function create_array_by_key($array,$key){
            $data = array();
            
            if(!empty($array) && is_array($array)){
                foreach ($array as $t){
                    if(array_key_exists($key, $t)){
                        $data[$t[$key]] = $t;
                    }
                }
            }
            
            return $data;
	}
}

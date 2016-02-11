<?php
/**
 * @package 下拉框选项生成助手
 * @author LamanLu
 * @since 2016-01-21
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 生成星期选项
 * @author LamanLu
 * @since 2016-01-21
 */
if(!function_exists('week_options')){
    function week_options($selected = 0){
        $selected = intval($selected);
        $weeks = array(
            0 => '选择星期',
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六',
            7 => '星期日',
        );
        
        $opt = '';
        foreach($weeks as $idx => $name){
            $select = ($idx == $selected)?' selected="selected" ':'';
            $opt .= "<option value=\"$idx\" $select>$name</option>";
        }
        
        return $opt;
    }
}

/**
 * 生成季节选项
 * @author LamanLu
 * @since 2016-01-21
 */
if(!function_exists('season_options')){
    function season_options($selected = 0){
        $selected = intval($selected);
        $seasons = array(
            0 => '选择季节',
            2 => '夏季',
            4 => '冬季',
        );
        
        $opt = '';
        foreach($seasons as $idx => $name){
            $select = ($idx == $selected)?' selected="selected" ':'';
            $opt .= "<option value=\"$idx\" $select>$name</option>";
        }
        
        return $opt;
    }
}

/**
 * 生成年龄选项
 * @author LamanLu
 * @since 2016-01-21
 */
if(!function_exists('age_options')){
    function age_options($selected = -1){
        $selected = intval($selected);
        
        $opt = '<option value="-1">—年龄—</option>';
        for($i = 0; $i <= 100; $i++){
            $select = ($i == $selected)?' selected="selected" ':'';
            $opt .= "<option value=\"$i\" $select>$i</option>";
        }
        
        return $opt;
    }
}

/**
 * 生成人数选项
 * @author guanliang
 * @since 2016-01-30
 */
if(!function_exists('people_num_options')){
    function people_num_options($selected = -1){
        $selected = intval($selected);

        $opt = '<option value="-1">—人数—</option>';
        for($i = 0; $i <= 100; $i++){
                $select = ($i == $selected)?' selected="selected" ':'';
                $opt .= "<option value=\"$i\" $select>$i</option>";
        }

        return $opt;
    }
}
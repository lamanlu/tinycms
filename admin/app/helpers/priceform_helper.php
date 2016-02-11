<?php
/**
 * @package 价格表单助手
 * @author LamanLu
 * @since 2016-01-22
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('create_sub_price_form')){
    function create_sub_price_form($data = array()){
        $emptyForm = '<div class="form-group">
            <label class="col-sm-3 control-label">价格：</label>
            <div class="col-sm-6">
                <div class="widget-box">
                    <div class="widget-header">
                        <div class="form-inline">
                            <div class="inline">
                                <input type="text" name="pr_sub_title[]" class="input-small" >
                            </div>
                            <div class="inline">
                                <label>
                                    <span class="btn btn-xs btn-primary selected">个人费用</span>
                                    <input type="radio" name="pr_sub_type[0]" value="preson" data-type="person" checked>
                                </label>
                            </div>
                            <div class="inline">
                                <label>
                                    <span class="btn btn-xs">团队费用</span>
                                    <input type="radio" name="pr_sub_type[0]" value="team" data-type="team">
                                </label>
                            </div>
                            <input type="hidden" name="pr_sub_id[]">
                            <div class="widget-toolbar"></div>
                        </div>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <div class="widget-item">
                                <input type="hidden" name="pr_sub_item_id[0][0]">
                                <div class="widget-item-list">
                                    <div class="form-inline">
                                    <input type="text" name="pr_item_title[0][0]" class="input-small" placeholder="儿童/老人">
                                    <input type="text" name="pr_price[0][0]" class="input-small" placeholder="价钱">
                                    <select name="pr_min_age[0][0]" class="input-mini age">'.age_options().'</select>
                                    </select>
                                    <label class="inline">至</label>
                                    <select name="pr_max_age[0][0]" class="input-mini age">'.age_options().'</select>
                                    </div>
                                    <div class="form-inline">
                                        <input type="text" name="pr_intro[0][0]" class="input-xxlarge" placeholder="价格说明">
                                    </div>
                                </div>
                                <div class="widget-item-btn-wrap btn-wrap">
                                    <i class="icon-large icon-plus-sign green" data-action="item-clone"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 widget-box-btn-wrap btn-wrap">
                <i class="icon-large icon-plus-sign green control-label" data-action="box-clone"></i>
            </div>
        </div>';
        
        if(empty($data) || !is_array($data)){
            return $emptyForm;
        }
        
        $i = 0;
        $formHtml = '';
        foreach($data as $storage_id => $main){
            $lable = '';
            $btn = '<i data-action="box-close" class="icon-large control-label icon-remove-sign grey"></i>';
            if($i == 0){
                $lable = '价格：';
                $btn = '<i class="icon-large icon-plus-sign green control-label" data-action="box-clone"></i>';
            }
            
            $type = $main['type'];
            
            $formHtml .= '<div class="form-group">
                            <label class="col-sm-3 control-label">'.$lable.'</label>
                            <div class="col-sm-6">
                                <div class="widget-box">
                                    <div class="widget-header">
                                        <div class="form-inline">
                                            <div class="inline">
                                                <input type="text" class="input-small" name="pr_sub_title[]" value="'.$main['name'].'">
                                            </div>
                                            <div class="inline">
                                                <label>';
                                                    if($type == 1){  
                                                        $formHtml .= '<span class="btn btn-xs btn-primary selected">个人费用</span>'
                                                                . '<input type="radio" name="pr_sub_type['.$i.']" value="preson" data-type="person" checked>';
                                                    }else{
                                                        $formHtml .= '<span class="btn btn-xs">个人费用</span>'
                                                                . '<input type="radio" name="pr_sub_type['.$i.']" value="preson" data-type="person">';
                                                    }
                                                $formHtml .='</label>
                                            </div>
                                            <div class="inline">
                                                <label>';
                                                    if($type == 2){
                                                        $formHtml .= '<span class="btn btn-xs btn-primary selected">团队费用</span>'
                                                                . '<input type="radio" name="pr_sub_type['.$i.']" value="team" data-type="team" checked>';
                                                    }else{
                                                        $formHtml .= '<span class="btn btn-xs">团队费用</span>'
                                                                . '<input type="radio" name="pr_sub_type['.$i.']" value="team" data-type="team">';                                        
                                                    }
                                                $formHtml .= '</label>
                                            </div>
                                            <input type="hidden" name="pr_sub_id[]" value="'.$storage_id.'">
                                            <div class="widget-toolbar">
                                                <!-- <a href="#" data-action="clone">
                                                    <i class="icon-plus"></i>
                                                </a> -->

                                                <a data-action="collapse" href="#">
                                                    <i class="icon-chevron-up"></i>
                                                </a>

                                                <!-- <a href="#" data-action="close">
                                                    <i class="icon-remove"></i>
                                                </a> -->

                                            </div>
                                        </div>
                                    </div>
                                    <div class="widget-body">
                                        <div class="widget-main">';
                                            if($type == 1){
                                                $j = 0;
                                                foreach($main['list'] as $sub){
                                                    $formHtml .= '<div class="widget-item">'
                                                            . '<input type="hidden" name="pr_sub_item_id['.$i.']['.$j.']" value="'.$sub['id'].'">'
                                                            . '<div class="widget-item-list"><div class="form-inline">'
                                                            . '<input type="text" placeholder="儿童/老人" class="input-small" name="pr_item_title['.$i.']['.$j.']"  value="'.$sub['title'].'">'
                                                            . '<input type="text" placeholder="价钱" class="input-small" name="pr_price['.$i.']['.$j.']" value="'.round($sub['price'],2).'">'
                                                            . '<select class="input-mini age" name="pr_min_age['.$i.']['.$j.']">'.  age_options($sub['min_age']).'</select>'
                                                            . '<label class="inline">至</label>'
                                                            . '<select class="input-mini age" name="pr_max_age['.$i.']['.$j.']">'.  age_options($sub['max_age']).'</select>'
                                                            . '</div><div class="form-inline">'
                                                            . '<input type="text" placeholder="价格说明" class="input-xxlarge" name="pr_intro['.$i.']['.$j.']" value="'.$sub['remark'].'">'
                                                            . '</div></div><div class="widget-item-btn-wrap btn-wrap">';
                                                    if($j == 0){
                                                        $formHtml .= '<i class="icon-large icon-plus-sign green" data-action="item-clone"></i>';
                                                    }else{
                                                        $formHtml .= '<i data-action="item-close" class="icon-large icon-remove-sign grey"></i>';
                                                    }
                                                    $formHtml .= '</div></div>';
                                                    $j++;
                                                }
                                            }
                                            if($type == 2){
                                                 $j = 0;
                                                foreach($main['list'] as $sub){
                                                    $formHtml .= '<div class="widget-item">'
                                                            . '<input type="hidden" name="pr_sub_item_id['.$i.']['.$j.']" value="'.$sub['id'].'">'
                                                            . '<div class="widget-item-list"><div class="form-inline">'
                                                            . '<input type="text" placeholder="价钱" class="input-small" name="pr_price['.$i.']['.$j.']" value="'.round($sub['price'],2).'">'
                                                            . '<input type="number" placeholder="最小人数限制" class="input-small" name="pr_min_headcount['.$i.']['.$j.']" max="999" min="0" value="'.$sub['min_people'].'">'
                                                            . '<input type="number" placeholder="最大人数限制" class="input-small" name="pr_max_headcount['.$i.']['.$j.']" max="999" min="0" value="'.$sub['max_people'].'">'
                                                            . '</div><div class="form-inline"><input type="text" placeholder="价格说明" class="input-xxlarge" name="pr_intro['.$i.']['.$j.']" value="'.$sub['remark'].'"></div>'
                                                            . '</div>'
                                                            . '<div class="widget-item-btn-wrap btn-wrap">';
                                                    if($j == 0){
                                                        $formHtml .= '<i class="icon-large icon-plus-sign green" data-action="item-clone"></i>';
                                                    }else{
                                                        $formHtml .= '<i data-action="item-close" class="icon-large icon-remove-sign grey"></i>';
                                                    }
                                                    $formHtml .= '</div></div>';
                                                    $j++;
                                                }
                                            }
                                        $formHtml .= '</div><!-- widget-main -->
                                    </div>
                                </div><!-- /.widget-box -->
                            </div>
                            <div class="col-sm-1 widget-box-btn-wrap btn-wrap">'.$btn.'</div>
                        </div>';
            $i++;
        }
        
        return $formHtml;
    }
}
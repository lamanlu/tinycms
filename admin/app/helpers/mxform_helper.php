<?php
/**
 * @package 后台管理页面表单生成助手
 * @author LamanLu
 * @since 2016-01-25
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 生成标签表单
 * @author LamanLu
 * @since 2016-01-25
 */
if(!function_exists('create_tag_form')){
    function create_tag_form($tags = array()){
        $formHtml = '';
        foreach($tags as $tag_id=>$tag){
            $checked = ($tag['checked'])?'checked="checked"':'';
            $formHtml .= '<label class="checkbox-inline">';
            $formHtml .= '<input type="checkbox" name="tags[]" value="'.$tag_id.'" class="ace" '.$checked.'>';
            $formHtml .= '<span class="lab label-sin">'.$tag['tag_name'].'</span>';
            $formHtml .= '</label>';
        }
        return $formHtml;
    }
}

/**
 * @package 开放时间表单助手
 * @author wenming
 * @since 2016-01-22
 */

if(!function_exists('create_opentime_form')){
	
	function create_opentime_form($data = array()){
		
		$emptyForm = '
		    <div class="form-group">
            <label class="col-sm-3 control-label">开放时间：</label>
            <div class="col-sm-1">
                <input type="hidden" name="ot_id[]" />
                <select name="ot_season[]" class="input-small">'
					.season_options().
                '</select>
            </div>
            <div class="col-sm-2">
                <input class="input-mini" name="ot_start_date[]" placeholder="开始日期" type="text" />
                <span class="control-label">至</span>
                <input class="input-mini" name="ot_end_date[]" placeholder="结束日期" type="text" />
            </div>
            <div class="col-sm-1">
                <select name="ot_start_week[]" class="input-small">'
                    .week_options().
                '</select>
            </div>
            <div class="col-sm-1">
                <select name="ot_end_week[]" class="input-small">'
                   .week_options().
                '</select>
            </div>
            <div class="col-sm-1">
                <input type="text" name="ot_start_time[]" class="input-large" placeholder="开始时间">
            </div>
            <div class="col-sm-1">
                <input type="text" name="ot_end_time[]" class="input-large" placeholder="结束时间">
            </div>
            <div class="col-sm-1 opentime-btn-wrap btn-wrap">
                <i class="icon-plus-sign icon-large green control-label" data-action="line-clone"></i>
            </div>
        </div>
		';
		
		if(empty($data) || !is_array($data)){
            return $emptyForm;
        }
		
		$count = 0;//计数器，判断是否是第一个
		$html = '';
		$label = '';//第一项开放时间的label
		$button = '';
		foreach($data as $key =>$value){
			
			if($count == 0)
			{
				$label = '开放时间:';
				$button = 'icon-plus-sign icon-large green control-label';
				$data_action = 'line-clone';
			}
			else{
				$label = '';
				$button = 'icon-large control-label icon-remove-sign grey';
				$data_action = 'line-close';
			}
			$html .= '
		    <div class="form-group">
            <label class="col-sm-3 control-label">'.$label.'</label>
            <div class="col-sm-1">
                <input type="hidden" name="ot_id[]" value="'.$value['id'].'" />
                <select name="ot_season[]" class="input-small">'
					.season_options($value['season']).
                '</select>
            </div>
            <div class="col-sm-2">
                <input class="input-mini" name="ot_start_date[]" placeholder="开始日期" type="text" value="'.$value['start_day'].'"/>
                <span class="control-label">至</span>
                <input class="input-mini" name="ot_end_date[]" placeholder="结束日期" type="text" value="'.$value['end_day'].'"/>
            </div>
            <div class="col-sm-1">
                <select name="ot_start_week[]" class="input-small">'
                    .week_options($value['start_week']).
                '</select>
            </div>
            <div class="col-sm-1">
                <select name="ot_end_week[]" class="input-small">'
                   .week_options($value['end_week']).
                '</select>
            </div>
            <div class="col-sm-1">
                <input type="text" name="ot_start_time[]" class="input-large" placeholder="开始时间" value="'.$value['start_time'].'">
            </div>
            <div class="col-sm-1">
                <input type="text" name="ot_end_time[]" class="input-large" placeholder="结束时间" value="'.$value['end_time'].'">
            </div>
            <div class="col-sm-1 opentime-btn-wrap btn-wrap">
                <i class="'.$button.'" data-action="'.$data_action.'"></i>
            </div>
        </div>			
			'; 
			$count++;
		}
		return $html;
	}
}

/**
 * 拼接附件html字符串，用于编辑页面
 * @author wenming
 * @since 2016-01-23
 */
if(!function_exists('attachmentHtml')){
    function attachmentHtml($attachment, $fileDownload){
        
        $html = '';
        
        if(!empty($attachment) && is_array($attachment)){
            $html .= '<div class="form-group">
                    <label class="col-sm-3 control-label">已上传附件：</label>
                </div>';
            foreach($attachment as $file) {
                $html .= '<div class="form-group">
                            <div class="file_list">
                                <div class="col-sm-offset-3 col-sm-3">
                                    <input type="text" placeholder="附件类型" class="form-control" name="file_type[]" value="'.$file['title'].'">
                                </div>
                                <div class="col-sm-4 file-list-inline">
                                    <div class="file-wrap">
                                        <a href="'.$fileDownload.$file['url'].'" target="_blank">
                                            <span>'.substr($file['url'],12).'</span>
                                        </a>
                                        <input type="hidden" value="'.$file['url'].'" name="other_attachment[]">
                                    </div>
                                    <div class="one-group-btn-wrap">
                                        <i class="icon-large icon-remove-sign grey" data-action="one-group-close"></i>
                                    </div>
                                </div>
                            </div>
                        </div>';
            }
        }
        
        return $html;
    }
}

if(!function_exists('timeTableList')){
	function timeTableList($list_arr){
		if(empty($list_arr)){
			$timetable = '   <div class="form-group">
	       <label class="col-sm-3 control-label">班次：</label>
	       <div class="col-sm-4">
	           <div class="inline">
	               <input type="text" name="schedule_star[]" class="input-medium">
	           </div>
	           <span>至</span>
	           <div class="inline">
	               <input type="text" name="schedule_end[]" class="input-medium">
	           </div>
	       </div>
	       <div class="col-sm-1 one-group-btn-wrap btn-wrap">
	           <i class="control-label icon-large icon-plus-sign green" data-action="one-group-clone"></i>
	       </div>
	   </div>  ';
		}else{
			$timetable = '';
			for($i=0;$i<count($list_arr);$i++){
				//判断是否含有添加图标和班次标记
				if($i == 0){
					$sign = "plus";
					$label = "班次：";
					$color = "green";
				}else{
					$sign = "remove";
					$label = "";
					$color = "grey";
				}
				$timetable.= '   <div class="form-group">
		       <label class="col-sm-3 control-label">'.$label.'</label>
		       <div class="col-sm-4">
		           <div class="inline">
		               <input type="text" name="schedule_star[]" class="input-medium" value="'.$list_arr[$i]['schedule_star'].'">
		           </div>
		           <span>至</span>
		           <div class="inline">
		               <input type="text" name="schedule_end[]" class="input-medium" value="'.$list_arr[$i]['schedule_end'].'">
		           </div>
		       </div>
		       <div class="col-sm-1 one-group-btn-wrap btn-wrap">
		           <i class="control-label icon-large icon-'.$sign.'-sign '.$color.'" data-action="one-group-clone"></i>
		       </div>
		   </div>';
			}
		}
		return $timetable;
	}
}

if(!function_exists('lourSubList')){
	function lourSubList($list_arr,$imgDomain){
		if(empty($list_arr)){
			$lourSub = '<!-- 游轮价格 -->
            <div class="form-group">
                <label class="col-sm-3 control-label">舱位：</label>
                <div class="col-sm-6">
                    <div class="widget-box">
                        <div class="widget-header">
                            <div class="form-inline">
                                <div class="inline">
                                    <label>
                                        <input type="text" class="input-small" name="cb_ship_sub_title[0]" placeholder="舱位类型" data-type="person">
                                        <input type="hidden" name="cb_ship_sub_type[0]" value="team">
                                    </label>
                                </div>
                                <div class="inline pic-btn-wrap">
                                    <input type="file" id="cb_ship_image-0" name="cb_ship_image[0]" data-name="cb_ship_image[0]">
                                </div>
                                <input type="hidden" name="cb_ship_sub_id[]">
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
                            <div class="widget-main">
                                <div class="widget-item">
                                    <input type="hidden" name="cb_ship_sub_item_id[0][0]">
                                    <div class="widget-item-list">
                                        <div class="form-inline">
                                            <input type="text" name="cb_ship_item_title[0][0]" class="input-medium" placeholder="价格类型" />
                                            <input type="text" name="cb_ship_price[0][0]" class="input-medium" placeholder="价钱" />
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
		}else{
			$x = 0;
			$z = 1;
			$lourSub = "";
			foreach($list_arr as $sub_key => $list_sub){
				$lable = empty($x)?'舱位：':'';
				$sub_btn ='<i class="icon-large icon-plus-sign green control-label" data-action="box-clone"></i>';
				if($x>0){
					$sub_btn ='<i class="icon-large green control-label icon-remove-sign grey" data-action="box-close"></i>';
				}
				$y = 0;
				foreach($list_sub['list'] as $item_key => $list_item){
				    $item_btn ='<i class="icon-large icon-plus-sign green" data-action="item-clone"></i>';
					if($y>0){
						$item_btn ='<i class="icon-large icon-remove-sign grey" data-action="item-close"></i>';
					}
					$item_str[$sub_key][] = '
   <div class="widget-item"> 
   <input type="hidden" name="cb_ship_sub_item_id['.$x.']['.$y.']" value="'.$list_item['id'].'" /> 
   <div class="widget-item-list"> 
    <div class="form-inline"> 
     <input type="text" name="cb_ship_item_title['.$x.']['.$y.']" class="input-medium" placeholder="价格类型" value="'.$list_item['title'].'" /> 
     <input type="text" name="cb_ship_price['.$x.']['.$y.']" class="input-medium" placeholder="价钱" value="'.$list_item['price'].'" /> 
    </div> 
   </div> 
   <div class="widget-item-btn-wrap btn-wrap">
     '.$item_btn.' 
   </div> 
  </div>';
					$y++;
				}
				if(!empty($list_sub['images'])){
					$pic_wall = "";
					foreach($list_sub['images'] as $img){
						$pic_wall.='<div>
	                <a target="_blank" href="'.$imgDomain.$img.'"><img src="'.$imgDomain.$img.'" style="width:160px;"><i class="icon-large icon-remove red"></i></a>
	                <input type="hidden" value="'.$img.'" name="cb_ship_image['.$x.'][]">
	            			</div>';
					}
				}else{
					$pic_wall = '';
				}
				$lourSub.='<!-- 游轮价格 -->
  <div class="ship-container"> 
   <div class="form-group"> 
    <label class="col-sm-3 control-label">舱位：</label> 
    <div class="col-sm-6"> 
     <div class="widget-box"> 
      <div class="widget-header"> 
       <div class="form-inline"> 
        <div class="inline"> 
         <label> <input type="text" class="input-small" name="cb_ship_sub_title['.$x.']" placeholder="舱位类型" data-type="person" value="'.$list_sub['name'].'" /> <input type="hidden" name="cb_ship_sub_type['.$x.']" value="team" /> </label> 
        </div> 
        <div class="inline pic-btn-wrap"> 
         <input type="file" id="cb_ship_image-'.$x.'" name="cb_ship_image['.$x.']" data-name="cb_ship_image['.$x.']" /> 
        </div> 
        <input type="hidden" name="cb_ship_sub_id['.$x.']" value="'.$list_sub['id'].'" /> 
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
       <div class="widget-main">
         '.implode('',$item_str[$sub_key]).'
       </div>
      </div>
     </div>
     <div class="pic-wall">'.$pic_wall.'<div> 
    </div> 
   </div> 
  </div>
    <div class="col-sm-1 widget-box-btn-wrap btn-wrap">
      '.$sub_btn.' 
    </div> 
  </div>
  </div>';
				
				$x++;
				$z++;
			}
		}
		return $lourSub;
	}
}
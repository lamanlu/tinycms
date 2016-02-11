<?php
/**
 * @package 标签数据管理助手
 * @author LamanLu
 * @since 2016-01-19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

trait TagManager {
    
    //允许添加标签的素材类别
    private $_Tag_SourceType = array(
        1 => '文章',
    );
    
    /**
     * 根据素材类型获取对应的标签列表
     * @param type $source_type
     * @param type $source_id
     * @return boolean
     * @author LamanLu
     * @since 2016-01-19
     */
    private function getTagList($source_type,$source_id = 0){
        if(!isset($this->_Tag_SourceType[$source_type])){
            return FALSE;
        }
        
        $data = array();
        
        $this->load->model('TagRelate_model','TagRelateModel');
        $this->load->model('Tag_model','TagModel');
        
        //查找已关联的Tag
        $relateTag = array();
        if(!empty($source_id)){
            $tmp = $this->TagRelateModel->getRelateBySource($source_type,$source_id);
            $relateTag = create_array_by_key($tmp, 'tag_id');
        }        
        
        //获取对应的tag列表
        $condition = array(
            'source_type' => $source_type,
        );
        $tagArr = $this->TagModel->getTagList($condition,FALSE);
        
        foreach($tagArr as $tag_id => $tag){
            $checked = isset($relateTag[$tag_id])?1:0;
            
            $data[$tag_id] = array(
                'tag_id' => $tag['id'],
                'tag_name' => $tag['tag_name'],
                'checked' => $checked,
            );
        }
        
        return $data;        
    }
    
    /**
     * 保存素材与标签的对应关系
     * @param type $source_type 素材类型ID
     * @return boolean
     * @author LamanLu
     * @since 2016-01-19
     */
    private function getTagData($source_type){
        $source_type = intval($source_type);
        $tags = $this->postField('tags');
        
        if(!isset($this->_Tag_SourceType[$source_type])){
            return FALSE;
        }
        
        if(empty($tags) || !is_array($tags)){
            return array();
        }
        
        return $tags;
    }
    
    
    
}


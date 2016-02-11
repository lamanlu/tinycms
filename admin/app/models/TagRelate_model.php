<?php
/**
 * @package 标签关系模型类
 * @author Lamanlu
 * @since 2016-01-19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class TagRelate_model extends MY_Model{
    
    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'tag_relate';
    }
    
    /**
     * 保存素材的标签关联信息
     * @param type $source_type 素材类型ID：1:城市,2 地接社,3:导游,4:供应商,5:景点,6:活动,7:餐厅,8 酒店，公寓
     * @param type $source_id
     * @param type $tags
     * @return boolean
     * @author LamanLu
     * @since 2016-01-20
     */
    public function saveRelateData($source_type,$source_id,$tags){
        $source_type = intval($source_type);
        $source_id = intval($source_id);
        if(empty($source_type) || empty($source_id)){
            return FALSE;
        }
        
        $tmp = $this->getRelateBySource($source_type, $source_id);
        $realteArr = create_array_by_key($tmp, 'tag_id');

        $data = array();
        foreach($tags as $tid){
            $tid = intval($tid);
            
            if(empty($tid)){
                continue;
            }
            
            if(isset($realteArr[$tid])){
                unset($realteArr[$tid]);
                continue;
            }
            
            $data[] = array(
                'source_type' => $source_type,
                'source_id' => $source_id,
                'tag_id' => $tid,
            );
        }

        if(!empty($data)){
            $this->insertBatchData($data);
        }
        
        if(!empty($realteArr)){
            $tmp = create_array_by_key($realteArr, 'id');
            $idArr = array_keys($tmp);
            $this->deleteRelate($idArr);
        }
        
        return TRUE;
        
    }
    
    /**
     * 获取指定素材ID关联的标签列表
     * @param type $source_type
     * @param type $source_id
     * @return type
     * @author LamanLu
     * @since 2016-01-19
     */
    public function getRelateBySource($source_type,$source_id){
        
        $params = array($source_type,$source_id);
        $sql = 'SELECT id,tag_id FROM '.$this->tableName($this->_Table).' WHERE source_type = ? AND source_id = ? ';
        
        return $this->getListBySQL($sql, $params);
    }
    
    /**
     * 删除指定ID的关系数据
     * @param type $idArr
     * @return boolean
     * @author LamanLu
     * @since 2016-01-19
     */
    public function deleteRelate($idArr){
        if(empty($idArr) || !is_array($idArr)){
            return FALSE;
        }
        $sql = 'DELETE FROM '.$this->tableName($this->_Table).' WHERE id IN ('.  implode(',', $idArr).')';
        $this->_DB_Main->query($sql);
        return TRUE;
    }
    
    public function getSourceTagList($source_type,$source_id = 0){
        $data = array();
        
        $this->load->model('Tag_model','TagModel');
        
        //查找已关联的Tag
        $relateTag = array();
        if(!empty($source_id)){
            $tmp = $this->getRelateBySource($source_type,$source_id);
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
    
}
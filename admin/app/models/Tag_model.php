<?php
/**
 * @package 标签信息模型类
 * @author Lamanlu
 * @since 2016-01-19
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Tag_model extends MY_Model{
    
    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'tag';
    }
    
    /**
     * 根据条件获取标签列表
     * @param type $conditions
     * @param type $limit
     * @param type $offSet
     * @param type $pageSize
     * @return array
     * @author LamanLu
     * @since 2016-01-19
     */
    public function getTagList($conditions = array(),$limit = TRUE ,$offSet = 0,$pageSize = 10){
        
        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'source_type':
                    $where .= " AND source_type = ? ";
                    $params[] = $val;
                    break;
                case 'keyword':
                    $where .= " AND tag_name LIKE '%".$this->_DB_Main->escape_like_str($val)."%' ";
                    break;
            }
        }
                
        $sql = 'SELECT id,source_type,tag_name,sort,update_time FROM '.$this->tableName($this->_Table).' WHERE 1 '.$where.'ORDER BY id ASC ';
		
        if($limit){
            $sql .= 'LIMIT '.$offSet.' , '.$pageSize.';';
        }

        $tmpArr = $this->getListBySQL($sql,$params);
        
        return create_array_by_key($tmpArr, 'id');
    }
    
    /**
     * 根据条件计算对应标签数量
     * @param type $conditions
     * @return int
     * @author LamanLu
     * @since 2016-01-19
     */
    public function getTagCount($conditions = array()){
        
        $count = 0;
        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'source_type':
                    $where .= " AND source_type = ? ";
                    $params[] = $val;
                    break;
                case 'keyword':
                    $where .= " AND tag_name LIKE '%".$this->_DB_Main->escape_like_str($val)."%' ";
                    break;
            }
        }
                
        $sql = 'SELECT COUNT(*) AS count FROM '.$this->tableName($this->_Table).' WHERE 1 '.$where;
        
        $res = $this->getRowBySQL($sql,$params);
        
        if(!empty($res)){
            $count = $res['count'];
        }
        
        return $count;
    }
    
    /**
     * 检查是否有重名标签
     * @param type $type 标签类别
     * @param type $name 标签名称
     * @param type $id 标签ID
     * @return array
     * @author LamanLu
     * @since 2016-01-19
     */
    public function checkExist($type,$name,$id = 0){
        
        $where = ' AND source_type = ? AND tag_name = ?';
        $params = array($type,$name);
        
        if(!empty($id)){
            $where .= ' AND id != ?';
            $params[] = $id;
        }
        
        $sql = "SELECT id FROM ".$this->tableName($this->_Table)." WHERE 1 $where LIMIT 1";
        
        $res = $this->getRowBySQL($sql,$params);

        return $res;
    }
    
    /**
     * 删除标签，及对应关系
     * @param type $id 标签ID
     * @author LamanLu
     * @since 2016-01-19
     */
    public function deleteTag($id){
        
        $params = array('id' => $id);
        $this->deleteData($params);
        
        $params = array('tag_id' => $id);
        $this->deleteData($params,'tag_relate');
    }    
    
}
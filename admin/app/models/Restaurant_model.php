<?php
/**
 * @package 餐厅信息模型类
 * @author Lamanlu
 * @since 2016-01-26
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurant_model extends MY_Model{

    private $_Self_SourceType = 7;

    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'restaurant';
    }
    
    /**
     * 保存餐厅信息
     * @param type $data 主题信息数组
     * @param type $tagData 标签数组
     * @param type $opentimeData 开放时间数组
     * @param type $priceData 价格数组
     * @param type $id
     * @return boolean
     * @author LamanLu
     * @date 2016-01-26
     */
    public function saveRestaurant($data,$tagData,$opentimeData,$priceData,$id){
        
        $this->_DB_Main->trans_start();//开启事务
        
        if(empty($id)){            
            $data['create_time'] = time();
            $id = $this->insertData($data);
        }else{
            $params = array('id'=>$id);
            $this->updateData($params, $data);
        }
        
        //保存标签信息
        $this->load->model('TagRelate_model','TagRelateModel');
        $res = $this->TagRelateModel->saveRelateData($this->_Self_SourceType, $id, $tagData);
        
        //保存开放时间
        $this->load->model('Opentime_model','OpentimeModel');
        $res = $this->OpentimeModel->saveOpentime($this->_Self_SourceType, $id, $opentimeData);
        
        $this->load->model('Price_model','PriceModel');        
        $res = $this->PriceModel->savePriceList($this->_Self_SourceType,$id,$priceData);
        
        $this->_DB_Main->trans_complete();//提交事务
        
        //判断事务提交结果
        if($this->_DB_Main->trans_status() === FALSE){
            return FALSE;
        }
        
        return TRUE;
    }
    
    /**
     * 根据餐厅ID 获取餐厅详情
     * @param type $id
     * @return type
     * @author LamanLu
     * @date 2016-01-26
     */
    public function getRestaurantById($id){
        $data = $this->getDataByID($id);

        if(empty($data)){
            return $data;
        }
        
        $data['purchase_way'] = explode(',', $data['purchase_way']);
        $data['images'] = unserialize($data['images']);
        $data['files'] = unserialize($data['files']);
        
        $this->load->model('TagRelate_model','TagRelateModel');
        $data['tag_list'] = $this->TagRelateModel->getSourceTagList($this->_Self_SourceType,$id);
        
        $this->load->model('Price_model','PriceModel');
        $data['price_list'] = $this->PriceModel->getPriceList($this->_Self_SourceType,$id);
        
        $this->load->model('Opentime_model','OpentimeModel');        
        $data['opentime_list'] = $this->OpentimeModel->getOpenTimeList($this->_Self_SourceType,$id);
        
        return $data;
    }
    
    /**
     * 根据条件获取餐厅列表
     * @param type $conditions
     * @param type $limit
     * @param type $offSet
     * @param type $pageSize
     * @return type
     * @author LamanLu
     * @date 2016-01-26
     */
    public function getRestaurantList($conditions = array(),$limit = TRUE ,$offSet = 0,$pageSize = 10){
        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'country_id':
                    $where .= " AND A.country_id = ? ";
                    $params[] = $val;
                    break;
                case 'city_id':
                    $where .= " AND A.city_id = ? ";
                    $params[] = $val;
                    break;
                case 'keyword':
                    $where .= " AND (A.name LIKE '%".$this->_DB_Main->escape_like_str($val)."%' OR A.name_en LIKE '%".$this->_DB_Main->escape_like_str($val)."%') ";
                    break;
            }
        }
                
        $sql = 'SELECT A.id,A.name,A.name_en,A.country_id,city_id,B.name as city_name,A.update_time FROM '.$this->tableName($this->_Table).' A '
                . 'LEFT JOIN '.$this->tableName('city').' B ON A.city_id = B.id '
                . 'WHERE 1 AND A.is_delete = 0 '.$where
                . 'ORDER BY A.id DESC ';
		
        if($limit){
            $sql .= 'LIMIT '.$offSet.' , '.$pageSize.';';
        }

        $tmpArr = $this->getListBySQL($sql,$params);
        
        return create_array_by_key($tmpArr, 'id');
    }
    
    /**
     * 获取符合条件的餐厅数量
     * @param type $conditions
     * @return type
     * @author LamanLu
     * @date 2016-01-26
     */
    public function getRestaurantCount($conditions = array()){
        
        $count = 0;
        
        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'country_id':
                    $where .= " AND country_id = ? ";
                    $params[] = $val;
                    break;
                case 'city_id':
                    $where .= " AND city_id = ? ";
                    $params[] = $val;
                    break;
                case 'keyword':
                    $where .= " AND (name LIKE '%".$this->_DB_Main->escape_like_str($val)."%' OR name_en LIKE '%".$this->_DB_Main->escape_like_str($val)."%') ";
                    break;
            }
        }
        
        $sql = 'SELECT COUNT(*) AS count FROM '.$this->tableName($this->_Table).' WHERE 1 AND is_delete = 0 '.$where;
        
        $res = $this->getRowBySQL($sql,$params);
        
        if(!empty($res)){
            $count = $res['count'];
        }
        
        return $count;        
    }
    
    /**
     * 判断是否有重复的餐厅
     * @param int $city_id
     * @param array $checkArr
     * @param int $id
     * @return array
     */
    public function checkExist($city_id,$checkArr,$id){
        
        $params = array($city_id);
        $sql = 'SELECT id,name,name_en FROM '.$this->tableName($this->_Table).' WHERE city_id = ? AND is_delete = 0 AND ( ';
        $tmp = array();
        foreach($checkArr as $field => $val){
            $params[] = $val;
            $tmp[] = ' `'.$field.'` = ? ';
        }
        $sql .= implode(' OR ', $tmp).' ) ';
        if(!empty($id)){
            $sql .= ' AND id != ?';
            $params[] = $id;
        }
        $sql .= ' LIMIT 1;';

        return $this->getRowBySQL($sql, $params);
    }
}

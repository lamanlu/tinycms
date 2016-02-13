<?php
/**
 * @package 目的地信息模型类
 * @author Lamanlu
 * @since 2016-01-10
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class City_model extends MY_Model{


    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'city';
    }
    
    public function saveCity($data,$tagData,$id = 0){
        
        $data['update_time'] = time();
        if(empty($id)){
            $data['create_time'] = time();            
            $id = $this->insertData($data);
        }else{            
            $params = array('id'=>$id);
            $this->updateData($params,$data);
        }
        
        $this->load->model('TagRelate_model','TagRelateModel');
        $res = $this->TagRelateModel->saveRelateData(1, $id, $tagData);
        
        return TRUE;
    }
    
    /**
     * 根据条件获取目的地数组
     * @param type $conditions
     * @param type $offSet
     * @param type $pageSize 
     * @return array
     * @author LamanLu
     * @since 2016-01-10
     */
    public function getCityList($conditions = array(),$limit = TRUE ,$offSet = 0,$pageSize = 10){

        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'country_id':
                    $where .= " AND country_id = ? ";
                    $params[] = $val;
                    break;
                case 'keyword':
                    $where .= " AND (A.name LIKE '%".$this->_DB_Main->escape_like_str($val)."%' OR name_en LIKE '%".$this->_DB_Main->escape_like_str($val)."%') ";
                    break;
            }
        }
                
        $sql = 'SELECT A.id,A.name,name_en,country_id,B.name as country_name,update_time FROM '.$this->tableName('city').' A '
                . 'LEFT JOIN '.$this->tableName('country').' B ON A.country_id = B.id '
                . 'WHERE 1 AND is_delete = 0 '.$where
                . 'ORDER BY A.id DESC ';
		
        if($limit){
            $sql .= 'LIMIT '.$offSet.' , '.$pageSize.';';
        }
//        echo $sql;exit;
        $tmpArr = $this->getListBySQL($sql,$params);
        
        return create_array_by_key($tmpArr, 'id');
    }
    
    /**
     * 根据条件获取城市数量
     * @param type $conditions
     * @return array
     * @author LamanLu
     * @since 2016-01-10
     */
    public function getCityCount($conditions = array()){
        
        $count = 0;
        
        $where = '';
        $params = array();
        
        foreach ($conditions as $field => $val){
            switch ($field){
                case 'country_id':
                    $where .= " AND country_id = ? ";
                    $params[] = $val;
                    break;
                case 'keyword':
                    $where .= " AND (name LIKE '%".$this->_DB_Main->escape_like_str($val)."%' OR name_en LIKE '%".$this->_DB_Main->escape_like_str($val)."%') ";
                    break;
            }
        }
        
        $sql = 'SELECT COUNT(*) AS count FROM '.$this->tableName('city').' WHERE 1 AND is_delete = 0 '.$where;
        
        $res = $this->getRowBySQL($sql,$params);
        
        if(!empty($res)){
            $count = $res['count'];
        }
        
        return $count;
    }
    
    /**
     * 查询是否已经存在相同的城市信息
     * @param type $name 中文名称
     * @param type $name_en 英文名称
     * @param type $country_id 国家ID
     * @param type $id 编辑时的城市ID
     * @return array
     */
    public function checkExist($name,$name_en,$country_id,$id = 0){
        
        $where = ' AND country_id = ? AND (name = ? OR name_en = ? )';
        $params = array($country_id,$name,$name_en);
        
        if(!empty($id)){
            $where .= ' AND id != ?';
            $params[] = $id;
        }
        
        $sql = 'SELECT id FROM '.$this->tableName($this->_Table).' WHERE 1 AND is_delete = 0'.$where;
        
        $res = $this->getRowBySQL($sql,$params);

        return $res;
    }
    
    /**
     * 获取所有城市列表 按国家分组
     * @return type
     * @author LamanLu
     * @since 2016-01-23
     */
    public function getAllCityGroup(){
        
        $sql = "SELECT id,name,name_en,country_id FROM ".$this->tableName($this->_Table)." WHERE is_delete = 0 ORDER BY sort ASC;";
        $cityArr = $this->getListBySQL($sql);
        
        $this->load->model('Country_model','CountryModel');
        $countryArr = $this->CountryModel->getCountryList();
        
        $data = array();
        foreach ($cityArr as $city){
            if(!isset($data[$city['country_id']])){
                $data[$city['country_id']] = array(
                    'country_id' => $city['country_id'],
                    'country_name' => $countryArr[$city['country_id']]['name'],
                    'city_list' => array(),
                );                
            }
            
            $data[$city['country_id']]['city_list'][$city['id']] = $city;
        }
        
        return $data;
    }
    
}

<?php
/**
 * @package 目的地管理
 * @author LamanLu
 * @since 2016-01-10
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'traits/TagManager.php';

class City extends MY_Controller{
    
    use TagManager;
    
    public function __construct() {
        parent::__construct();
        
        $this->_Redirect_URL = get_url('city');
        $this->setBreadcrumb('城市管理', get_url('city'));
        
        $this->load->model('Country_model','CountryModel');
        $this->load->model('City_model','CityModel');
    }
    
    public function index(){
        
        $this->setBreadcrumb('城市列表');
        
        $countryList = $this->CountryModel->getCountryList();        
        
        $this->setData('countryList', $countryList);
        $this->display('city/index.html');
    }
    
    /**
     * 目的地列表页面
     * @author LamanLu
     * @since 2016-01-10
     */
    public function cityList(){
        $draw = intval($this->getField('draw'));
        $start = intval($this->getField('start'));
        $pageSize = intval($this->getField('length'));
        $tmp = $this->getField('search');//datatable查询条件
        
        $search = array();
        if(!empty($tmp['value'])){
            $search = json_decode($tmp['value'],TRUE);
        }
        
        $pageSize = empty($pageSize)?$this->_PageSize:$pageSize;
        
        $conditon = array();
		
        if(isset($search['country'])){
            $tmp = intval($search['country']);
            if(!empty($tmp)){
                $conditon['country_id'] = $tmp;
            }
        }
        if(isset($search['keyword'])){
            $tmp = trim($search['keyword']);
            if($tmp != ''){
                $conditon['keyword'] = $tmp;
            }
        }
            
        $cityList = $this->CityModel->getCityList($conditon,TRUE,$start,$pageSize);
        $count = $this->CityModel->getCityCount($conditon);
        
        $data = array(
            "draw" => $draw,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => array(),
        ); 
        
        foreach($cityList as $city){
            $data['data'][] = array(
                'name' => $city['name'],
                'name_en' => $city['name_en'],
                'country' => $city['country_name'],
                'update_time' => date('Y-m-d H:i',$city['update_time']),
                'id' => $city['id'],
            );
        }
        
        echo json_encode($data);
    }
    
    /**
     * 添加目的地页面
     * @author LamanLu
     * @since 2016-01-10
     */
    public function add(){    
        $this->setBreadcrumb('添加城市');
        
        $this->load->helper('mxform');
        
        $countryList = $this->CountryModel->getCountryList();
        $tagList = $this->getTagList(1);
        
        $imgUploadDomain = $this->SysConfigItem('img_upload_int');//图片上传接口
        $imgDomain = $this->SysConfigItem('img_base_domain');//图片浏览域名

        $this->setData('countryList', $countryList);
        $this->setData('tagList', $tagList);
        $this->setData('imgUploadDomain', $imgUploadDomain);
        $this->setData('imgDomain', $imgDomain);
        $this->display('city/add.html');
    }
    
    /**
     * 编辑目的地页面
     * @author LamanLu
     * @since 2016-01-10
     */
    public function edit(){
        $this->setBreadcrumb('编辑城市');
        
        $id = intval($this->getField('id'));
        
        if(empty($id)){
            $this->showNoticePage(array('参数不能为空！'));
        }
        
        $city = $this->CityModel->getDataByID($id);
        
        if(empty($city)){
            $this->showNoticePage(array('无效的参数！'));
        }
        
        $city['images'] = unserialize($city['images']);

        $countryList = $this->CountryModel->getCountryList();
        
        $this->load->helper('mxform');
        $tagList = $this->getTagList(1,$id);
        
        $imgUploadDomain = $this->SysConfigItem('img_upload_int');//图片上传接口
        $imgDomain = $this->SysConfigItem('img_base_domain');//图片浏览域名
        
        $this->setData('city', $city);
        $this->setData('countryList', $countryList);
        $this->setData('tagList', $tagList);
        $this->setData('imgUploadDomain', $imgUploadDomain);
        $this->setData('imgDomain', $imgDomain);
        $this->display('city/edit.html');
    }
    
    /**
     * 保存目的地信息
     * @author LamanLu
     * @since 2016-01-10
     */
    public function save(){
        $id = intval($this->postField('id'));
        $name = trim($this->postField('name'));
        $name_en = trim($this->postField('name_en'));
        $country_id = intval($this->postField('country'));
        $desc = $this->postField('content');
        $photos = $this->postField('photo');
        
//        print_r($photos);exit;
        
        $images = array();//图片数组
        if(is_array($photos) && !empty($photos)){
            foreach($photos as $photo){
                $images[] = $photo;
            }
        }
        
        //判断数据完整性
        $msg = array();
        if($name == ''){
            $msg[] = '【中文名称】不能为空！';
        }
        
        if($name_en == ''){
            $msg[] = '【英文名称】不能为空！';
        }
        
        if(empty($country_id)){
            $msg[] = '【所属国家】不能为空！';
        }
        
        if(!empty($msg)){
            $this->showNoticePage($msg);
        }

        $res = $this->CityModel->checkExist($name,$name_en,$country_id,$id);
        if(!empty($res)){
            $msg[] = '已经存在此城市信息！';
            $this->showNoticePage($msg);
        }
        
        //保存数据
        $data = array();
        $data['name'] = $name;
        $data['name_en'] = $name_en;
        $data['country_id'] = $country_id;
        $data['desc'] = $desc;
        $data['images'] = serialize($images);

        $tags = $this->getTagData(1);

        $res = $this->CityModel->saveCity($data,$tags,$id);
        
        if(empty($res)){
            //保存失败，跳转至失败页面            
            $this->showNoticePage(array('保存信息失败！'));
        }
        
        $this->showNoticePage(array('保存信息成功！'));
    }
    
    /**
     * 根据ID删除目的地信息
     * @author LamanLu
     * @since 2016-01-10
     */
    public function delete(){
        $id = intval($this->postField('id'));
        
        $data = array(
            "code" => 0,
            "msg" => '无效的错误',
        );
        
        if(empty($id)){            
            echo json_encode($data);exit;
        }
        
        $params = array(
            'id' => $id,
        );
        $arr = array(
            'is_delete' => 1,
            'update_time' => time(),
        );
        $res = $this->CityModel->updateData($params,$arr);
        
        if($res){
            $data['code'] = 1;
            $data['msg'] = '删除成功';
        }else{
            $data['code'] = -1;
            $data['msg'] = '删除失败';
        }
        
        echo json_encode($data);
    }
	
    /**根据国家ID获取所有城市
    * @print json string
    * @author wenming
    * @since 2016-01-12
    */
    public function getCityByCountryId(){

        $country_id = intval($this->postField('country_id'));

        $condition['country_id'] = $country_id;

        $cityList = $this->CityModel->getCityList($condition,FALSE);

        echo json_encode($cityList);
    }
	
}


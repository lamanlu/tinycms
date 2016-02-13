<?php
/**
 * @package 餐厅管理
 * @author LamanLu
 * @since 2016-01-25
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'traits/TagManager.php';

require APPPATH.'traits/OpentimeManager.php';

require APPPATH.'traits/PriceSubManager.php';

class Restaurant extends MY_Controller{
    
    use TagManager;
    
    use OpentimeManager;

    use PriceSubManager;
    
    private $_Self_SourceType = 7;

    public function __construct() {
        parent::__construct();
        
        $this->_Redirect_URL = get_url('restaurant');
        $this->setBreadcrumb('餐厅管理', $this->_Redirect_URL);
        
        $this->load->helper('mxform');
        
        $this->load->model('City_model','CityModel');
        $this->load->model('Restaurant_model','RestaurantModel');
    }
    
    /**
     * 餐厅列表页面
     * @author LamanLu
     * @date 2016-01-26
     */
    public function index(){
        $this->setBreadcrumb('餐厅列表');
        
        $cityList = $this->CityModel->getAllCityGroup();
        
        $this->setData('cityList', $cityList);
        $this->display('restaurant/index.html');
    }
    
    /**
     * 餐厅列表接口
     * @author LamanLu
     * @date 2016-01-26
     */
    public function getList(){
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
		
        if(isset($search['keyword'])){
            $tmp = trim($search['keyword']);
            if($tmp != ''){
                $conditon['keyword'] = $tmp;
            }
        }
        if(isset($search['country_id'])){
            $tmp = intval($search['country_id']);
            if(!empty($tmp)){
                $conditon['country_id'] = $tmp;
            }
        }
        if(isset($search['city_id'])){
            $tmp = intval($search['city_id']);
            if(!empty($tmp)){
                $conditon['city_id'] = $tmp;
            }
        }        
            
        $dataList = $this->RestaurantModel->getRestaurantList($conditon,TRUE,$start,$pageSize);
        $count = $this->RestaurantModel->getRestaurantCount($conditon);
        
        $this->load->model('Country_model','CountryModel');
        $countryArr = $this->CountryModel->getCountryList();
        
        $data = array(
            "draw" => $draw,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => array(),
        ); 
        
        foreach($dataList as $tmp){
            $data['data'][] = array(
                'name' => $tmp['name'],
                'name_en' => $tmp['name_en'],
                'country' => $countryArr[$tmp['country_id']]['name'],
                'city' => $tmp['city_name'],
                'update_time' => date('Y-m-d H:i',$tmp['update_time']),
                'id' => $tmp['id'],
            );
        }
        
        echo json_encode($data);
    }    
    
    /**
     * 添加餐厅信息页面
     * @author LamanLu
     * @date 2016-01-26
     */
    public function add(){
        $this->setBreadcrumb('添加餐厅');
        
        $cityList = $this->CityModel->getAllCityGroup();
        $tagList = $this->getTagList($this->_Self_SourceType);
        
        $imgUploadDomain = $this->SysConfigItem('img_upload_int');//图片上传接口
        $imgDomain = $this->SysConfigItem('img_base_domain');//图片浏览域名
		
        $fileUploadDomain = $this->SysConfigItem('file_upload_int');//附件上传接口
        $fileDomain = $this->SysConfigItem('file_base_domain');//附件浏览域名
        
        $this->setData('cityList', $cityList);
        $this->setData('tagList', $tagList);
        $this->setData('imgUploadDomain', $imgUploadDomain);
        $this->setData('imgDomain', $imgDomain);
        $this->setData('fileDomain',$fileDomain);
        $this->setData('fileUploadDomain',$fileUploadDomain);
        $this->display('restaurant/add.html');
    }
    
    /**
     * 编辑餐厅信息页面
     * @author LamanLu
     * @date 2016-01-26
     */
    public function edit(){
        $this->setBreadcrumb('编辑餐厅');
        
        $id = intval($this->getField('id'));
        
        if(empty($id)){
            $this->showNoticePage(array('参数不能为空'));
        }
            
        $data = $this->RestaurantModel->getRestaurantById($id);
//        print_r($data);exit;
        if(empty($data)){
            $this->showNoticePage(array('无效的参数'));
        }
        
        $cityList = $this->CityModel->getAllCityGroup();
        
        $imgUploadDomain = $this->SysConfigItem('img_upload_int');//图片上传接口
        $imgDomain = $this->SysConfigItem('img_base_domain');//图片浏览域名
		
        $fileUploadDomain = $this->SysConfigItem('file_upload_int');//附件上传接口
        $fileDomain = $this->SysConfigItem('file_base_domain');//附件浏览域名
        
        $this->setData('cityList', $cityList);
        $this->setData('data', $data);
        $this->setData('imgUploadDomain', $imgUploadDomain);
        $this->setData('imgDomain', $imgDomain);
        $this->setData('fileDomain',$fileDomain);
        $this->setData('fileUploadDomain',$fileUploadDomain);
        $this->display('restaurant/edit.html');
    }
    
    /**
     * 保存餐厅信息
     * @author LamanLu
     * @date 2016-01-26
     */
    public function save(){
        $id = intval($this->postField('id'));
        $country_id = intval($this->postField('country_id'));
        $city_id = intval($this->postField('city_id'));
        $name = trim($this->postField('name'));
        $name_en = trim($this->postField('name_en'));
        $show_detail = intval($this->postField('show_detail'));
        $desc = $this->postField('intro');
        $attention = trim($this->postField('attention'));
        $tel = trim($this->postField('tel'));
        $fax = trim($this->postField('fax'));
        $website = trim($this->postField('website'));
        $address = trim($this->postField('address'));
        $currency = intval($this->postField('currency'));
        $opentime_remark = trim($this->postField('open_remark'));
        $purchase_way = $this->postField('purchases');//购买方式
        $reserveInfo = array();
        $reserveInfo['1'] = trim($this->postField('reserve_website'));
        $reserveInfo['2'] = trim($this->postField('reserve_email'));
        $reserveInfo['3'] = trim($this->postField('reserve_tel'));
        $reserveInfo['4'] = trim($this->postField('purchases_address'));
        $inside_remark = trim($this->postField('remark'));
        $photos = $this->postField('photo');//图片
        $fileNames = $this->postField('file_type');//附件标题
        $attachments = $this->postField('other_attachment');//附件URL
        
        
        $msg = array();
        if(empty($country_id)){
            $msg[] = '【国家】不能为空！';
        }
        if(empty($city_id)){
            $msg[] = '【城市】不能为空！';
        }
        if($name == ''){
            $msg[] = '【中文名称】不能为空！';
        }
        if($name_en == ''){            
            $msg[] = '【英文名称】不能为空！';
        }        
        if(!empty($msg)){
            $this->showNoticePage($msg);
        } 
        
        //判断是否已经存在相同餐厅信息
        $checkArr = array(
            'name'=>$name,
            'name_en'=>$name_en
        );
        $res = $this->RestaurantModel->checkExist($city_id,$checkArr,$id);
        if(!empty($res)){
            $msg = array();
            if($name == $res['name']){
                $msg[] = '已经存在【'.$name.'】餐厅信息！';
            }
            if($name_en == $res['name_en']){
                $msg[] = '已经存在【'.$name_en.'】餐厅信息！';
            }
            $this->showNoticePage($msg);
        }
        
        $purchase_way_str = '';
        $booking_website = '';
        $booking_email = '';
        $booking_tel = '';
        $purchase_address = '';
        if(!empty($purchase_way)){
            $purchase_config = $this->config->item('purchases');
            $tmp = array();
            foreach($purchase_way as $way){
                if(isset($purchase_config[$way])){
                    $tmp[] = $way;
                    switch ($way){
                        case 1:
                            $booking_website = $reserveInfo[$way];
                            break;
                        case 2:
                            $booking_email = $reserveInfo[$way];
                            break;
                        case 3:
                            $booking_tel = $reserveInfo[$way];
                            break;
                        case 4:
                            $purchase_address = $reserveInfo[$way];
                            break;
                    }
                }
            }
            $purchase_way_str = implode(',', $tmp);
        }
        
        $images = array();
        if(!empty($photos) && is_array($photos)){
            foreach($photos as $photo){
                $images[] = $photo;
            }
        }
        
        $files = array();
        if(!empty($attachments) && is_array($attachments)){
            foreach($attachments as $idx => $attachment){
                $files[] = array(
                    'title' => $fileNames[$idx],
                    'url' => $attachment,
                );
            }
        }
        
        $tagData = $this->getTagData($this->_Self_SourceType);
        $opentimeData = $this->getOpentimeData();
        $priceData = $this->getPriceListData('menu_');
                
        $data = array(
            'name' => $name,
            'name_en' => $name_en,
            'country_id' => $country_id,
            'city_id' => $city_id,
            'show_detail' => $show_detail,
            'address' => $address,
            'fax' => $fax,
            'tel' => $tel,
            'website' => $website,
            'attention_msg' => $attention,
            'currency' => $currency,
            'desc' => $desc,
            'images' => serialize($images),
            'files' => serialize($files),
            'opentime_remark' => $opentime_remark,
            'inside_remark' => $inside_remark,
            'purchase_way' => $purchase_way_str,
            'booking_website' => $booking_website,
            'booking_email' => $booking_email,
            'booking_tel' => $booking_tel,
            'purchase_address' => $purchase_address,
            'update_time' => time(),
        );
        
        $res = $this->RestaurantModel->saveRestaurant($data,$tagData,$opentimeData,$priceData,$id);
        
        if($res){
            $this->showNoticePage(array('保存餐厅信息成功'));
        }else{
            $this->showNoticePage(array('保存餐厅信息失败'));
        }        
    }
    
    /**
     * 删除餐厅信息
     * @author LamanLu
     * @date 2016-01-26
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
        $res = $this->RestaurantModel->updateData($params,$arr);
        
        if($res){
            $data['code'] = 1;
            $data['msg'] = '删除成功';
        }else{
            $data['code'] = -1;
            $data['msg'] = '删除失败';
        }
        
        echo json_encode($data);
    }
    
    /**
     * 餐厅详情
     * @author LamanLu
     * @since 2016-02-01
     */
    public function detail(){
        $this->setBreadcrumb('编辑餐厅');
        
        $id = intval($this->getField('id'));
        
        if(empty($id)){
            $this->showNoticePage(array('参数不能为空'));
        }
            
        $data = $this->RestaurantModel->getRestaurantById($id);

        if(empty($data)){
            $this->showNoticePage(array('无效的参数'));
        }
        
        $imgDomain = $this->SysConfigItem('img_base_domain');//图片浏览域名
        $fileDomain = $this->SysConfigItem('file_base_domain');//附件浏览域名
        

        $this->setData('imgDomain', $imgDomain);
        $this->setData('fileDomain',$fileDomain);
        $this->setData('data', $data);
        $this->display('restaurant/detail.html');
    }
    
    /**
     * 判断重复接口
     * @author LamanLu
     * @since 2016-02-01
     */
    public function checkExist(){
        $id = intval($this->postField('id'));
        $city_id = intval($this->postField('city_id'));
        $name = trim($this->postField('name'));
        $name_en = trim($this->postField('name_en'));

        $data = array(
            'code' =>  1,
            'msg' => '参数不完整',
            'exists' => array(),
        );
        
        if(empty($city_id)){
            echo json_encode($data);exit;
        }
        
        $checkArr = array();
        if($name != ''){
            $checkArr['name'] = $name;
        }
        if($name_en != ''){
            $checkArr['name_en'] = $name_en;
        }
        if(empty($checkArr)){
            echo json_encode($data);exit;
        }
        
        $res = $this->RestaurantModel->checkExist($city_id,$checkArr,$id);
        if(empty($res)){
            $data['code'] = 1;
            $data['msg'] = '无重复';
            echo json_encode($data);exit;
        }
        
        $data['code'] = 0;
        if($name == $res['name']){
            $data['exists'][] = 'name';
        }
        if($name_en == $res['name_en']){
            $data['exists'][] = 'name_en';
        }
        $data['msg'] = '已经存在相同餐厅信息！';
        
        echo json_encode($data);
    }

}
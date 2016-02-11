<?php
/**
 * @package 页面测试类，不做实际用途
 * @author LamanLu
 * @since 2016-01-14
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'traits/OpentimeManager.php';

require APPPATH.'traits/TagManager.php';

require APPPATH.'traits/PriceSubManager.php';

class Test extends MY_Controller{
    
    use OpentimeManager;
    
    use TagManager;
    
    use PriceSubManager;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->display('test/index.html');
    }
    
    public function pricesub(){
        $this->load->helper('priceform');//加载价格表单助手
        $this->display('test/pricesub.html');
    }

    public function hotel () {
        $this->display('test/hotel.html');
    }

    public function ship_train () {
        $this->display('test/ship_train.html');
    }
    
    public function roomPrice () {
        $this -> display('test/room_price.html');
    }

    public function cabinsub () {
        $this -> display('test/cabinsub.html');
    }

    public function savePrice(){
        
        $priceData = $this->getPriceSubData();//接收SUB类型价格数据
        
        $this->load->model('Test_model','TestModel');//初始化自己模块的MODEL
        
        $this->TestModel->saveExample($priceData);//保存数据
        
        
    }
    
    public function getPrice(){
        
        $this->load->helper('priceform');//加载价格表单助手
        
        $this->load->model('Price_model','PriceModel');
        $data = $this->PriceModel->getPriceSubList(5,99);
//        print_r($data);exit;
        
        $this->setData('priceData', $data);
        $this->display('test/price_edit.html');
    }
    
    //list类型价格表单
    public function pricelist(){
        $this->display('test/price_list.html');
    }
    
    //保存list类型价格数据
    public function savePriceList(){
        
        $priceData = $this->getPriceListData();
        
        $this->load->model('Test_model','TestModel');//初始化自己模块的MODEL
        
        $this->TestModel->saveExample2($priceData);//保存数据
    }
    
    //获取list类型价格数据
    public function getPriceList(){
        $this->load->model('Price_model','PriceModel');
        $data = $this->PriceModel->getPriceList(7,98);
        print_r($data);exit;
    }
    
    /**
     * 提交开放时间表单字段示例
     * @author LamanLu
     * @since 2016-01-19
     */
    public function opentime(){
        $this->display('test/opentime.html');
    }
    
    /**
     * 保存开放时间示例
     * source_type 素材类型ID：1:城市,2 地接社,3:导游,4:供应商,5:景点,6:活动,7:餐厅,8 酒店，公寓
     * @author LamanLu
     * @since 2016-01-19
     */
    public function saveOpentime(){
        $source_type = intval($this->postField('source_type'));
        $source_id = intval($this->postField('source_id'));
        
        $data = $this->getOpentimeData();//获取开放时间表单数据
        
        print_r($data);
        
        //保存开放时间，可以放到model里
        $this->load->model('Opentime_model','OpentimeModel');
        $res = $this->OpentimeModel->saveOpentime($source_type, $source_id, $data);//保存开放时间
        
        var_dump($res);
    }
    
    /**
     * 获取开放时间示例
     * source_type 素材类型ID：1:城市,2 地接社,3:导游,4:供应商,5:景点,6:活动,7:餐厅,8 酒店，公寓
     * @author LamanLu
     * @since 2016-01-19
     */
    public function getOpentime(){
        $source_type = 5;
        $source_id = 42;
        
        $data = $this->getOpentimeList($source_type, $source_id);
        
        print_r($data);
    }
    
    /**
     * 提交标签表单字段示例
     * @author LamanLu
     * @since 2016-01-19
     */
    public function tag(){        
        $this->display('test/tag.html');
    }
    
    /**
     * 保存标签示例
     * @author LamanLu
     * @since 2016-01-19
     */
    public function saveTag(){
        $source_type = intval($this->postField('source_type'));
        $source_id = intval($this->postField('source_id'));
        
        $tags = $this->getTagData($source_type);
        
        print_r($tags);
        
        $this->load->model('TagRelate_model','TagRelateModel');
        $res = $this->TagRelateModel->saveRelateData($source_type, $source_id, $tags);
		
		$this->load->model('TagRelate_model','TagRelateModel');
        $res = $this->TagRelateModel->saveRelateData($source_type, $source_id, $tags);
        
        var_dump($res);
    }
    
    /**
     * 获取标签列表示例
     * @author LamanLu
     * @since 2016-01-19
     */
    public function tagList(){
        $source_type = 1;
        $source_id = 0;
        
        $data = $this->getTagList($source_type, $source_id);
        
        print_r($data);
    }    
    
    
    public function testm(){
        $this->load->model('Test_model','TestModel');
        $this->TestModel->test();
    }
    
    //按国家分组 获取所有城市列表
    public function getALLCity(){
        $this->load->model('City_model','CityModel');
        
        $data = $this->CityModel->getAllCityGroup();
        
        print_r($data);
    }
}

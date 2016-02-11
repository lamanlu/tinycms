<?php
/**
 * @package 文件上传接口
 * @author LamanaLu
 * @since 2016-01-14
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends MY_Controller{

    public function __construct() {
        parent::__construct();
        set_time_limit(300);
    }
    
    public function index(){
        show_404();
    }
    
    /**
     * 上传图片接口
     * @author LamanLu
     * @since 2016-01-14
     */
    public function image(){
        $data =array(
            'code' => 0,
            'msg' => '未知错误',
            'url' => '',
            'width' => 0,
            'height' => 0,
            'size' => 0,
        );
        
        $file_name = 'file_name';//文件字段名
        
        $root = $this->config->item('upload_root_path');
        
        $config = $this->config->item('upload_img_config');
        
        $img_path = $config['upload_path'];
        
        $upload_path = $root.$img_path;

        if(!file_exists($upload_path)){
            $data['msg'] = 'Image Upload Path Not Exist';
            echo json_encode($data);exit;
        }
        
        $sub_path = $this->createDir($upload_path);
        
        if($sub_path === FALSE){
            $data['msg'] = 'Create Dir Faild';
            echo json_encode($data);exit;
        }
        
        $upload_path .= $sub_path;//完整上传路径

        $params = array();
        $params['upload_path'] = $upload_path;
        $params['allowed_types'] = implode('|', $config['allow_type']);
        $params['max_size'] = $config['max_size'];
        $params['file_name'] = uniqid();
        $this->load->library('upload', $params);

        if (!$this->upload->do_upload($file_name)){
            $error = $this->upload->display_errors('','');            
            $data['msg'] = $error;
            echo json_encode($data);exit;
        }
        
        $file = $this->upload->data();
        
        $url = $sub_path.DS.$file['file_name'];
        
        $data['code'] = 1;
        $data['msg'] = '上传成功';
        $data['url'] = $url;
        $data['width'] = $file['image_width'];
        $data['height'] = $file['image_height'];
        $data['size'] = $file['file_size'];
        echo json_encode($data);
    }
    
    /**
     * 上传文件接口
     * @author LamanLu
     * @since 2016-01-14
     */
    public function file(){
        $data =array(
            'code' => 0,
            'msg' => '未知错误',
            'url' => '',
            'size' => 0,
        );
        
        $file_name = 'file_name';//文件字段名
        
        $root = $this->config->item('upload_root_path');
        
        $config = $this->config->item('upload_file_config');
        
        $file_path = $config['upload_path'];
        
        $upload_path = $root.$file_path;

        if(!file_exists($upload_path)){
            $data['msg'] = 'File Upload Path Not Exist';
            echo json_encode($data);exit;
        }
        
        $sub_path = $this->createDir($upload_path);
        
        if($sub_path === FALSE){
            $data['msg'] = 'Create Dir Faild';
            echo json_encode($data);exit;
        }
        
        $upload_path .= $sub_path;//完整上传路径
        
        $params = array();
        $params['upload_path'] = $upload_path;
        $params['allowed_types'] = implode('|', $config['allow_type']);
        $params['max_size'] = $config['max_size'];
//        $params['file_name'] = uniqid();
        $this->load->library('upload', $params);

        if (!$this->upload->do_upload($file_name)){
            $error = $this->upload->display_errors('','');            
            $data['msg'] = $error;
            echo json_encode($data);exit;
        }
        
        $file = $this->upload->data();
        
        $url = $sub_path.DS.$file['file_name'];
        
        $data['code'] = 1;
        $data['msg'] = '上传成功';
        $data['url'] = $url;
        $data['size'] = $file['file_size'];
        echo json_encode($data);
    }
    
    /**
     * 根据年月日生成三级文件夹
     * @param type $upload_path 上传文件的主目录
     * @return boolean|string
     */
    private function createDir($upload_path){
        $now = time();
        $sub_path = '';
        
        $year = date('Y',$now);
        $sub_path .= DS.$year;
        $upload_path .= DS.$year;
        if(!file_exists($upload_path)){
            mkdir($upload_path, DIR_WRITE_MODE);
        }
        
        $month = date('m',$now);
        $sub_path .= DS.$month;
        $upload_path .= DS.$month;
        if(!file_exists($upload_path)){
            mkdir($upload_path, DIR_WRITE_MODE);
        }
        
        $day = date('d',$now);
        $sub_path .= DS.$day;
        $upload_path .= DS.$day;
        if(!file_exists($upload_path)){
            mkdir($upload_path, DIR_WRITE_MODE);
        }
        
        if(!file_exists($upload_path)){
            return FALSE;
        }
        
        return $sub_path;
    }
}

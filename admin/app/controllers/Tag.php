<?php
/**
 * @package 标签管理
 * @author LamanLu
 * @since 2016-01-19
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'traits/TagManager.php';

class Tag extends MY_Controller{
    
    use TagManager;
    
    public function __construct() {
        parent::__construct();
        
        $this->_Redirect_URL = get_url('tag');
        $this->setBreadcrumb('标签管理', get_url('tag'));
        
        $this->load->model('Tag_model','TagModel');
    }
    
    /**
     * 标签列表页面
     * @author LamanLu
     * @since 2016-01-19
     */
    public function index(){   
        
        $this->setBreadcrumb('标签列表');
        
        $this->setData('typeList', $this->_Tag_SourceType);
        $this->display('tag/index.html');
    }
    
    /**
     * 标签列表接口
     * @author LamanLu
     * @since 2016-01-19
     */
    public function tagList(){
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
		
        if(isset($search['type'])){
            $tmp = intval($search['type']);
            if(!empty($tmp)){
                $conditon['source_type'] = $tmp;
            }
        }
        if(isset($search['keyword'])){
            $tmp = trim($search['keyword']);
            if($tmp != ''){
                $conditon['keyword'] = $tmp;
            }
        }
            
        $tagList = $this->TagModel->getTagList($conditon,TRUE,$start,$pageSize);
        $count = $this->TagModel->getTagCount($conditon);
        
        $data = array(
            "draw" => $draw,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
            "data" => array(),
        ); 
        
        foreach($tagList as $tag){
            $data['data'][] = array(
                'name' => $tag['tag_name'],
                'type' => $this->_Tag_SourceType[$tag['source_type']],
                'update_time' => date('Y-m-d H:i',$tag['update_time']),
                'id' => $tag['id'],
            );
        }
        
        echo json_encode($data);
    }
    
    /**
     * 新增标签页面
     * @author LamanLu
     * @since 2016-01-19
     */
    public function add(){        
        $this->setBreadcrumb('添加标签');
        $this->setData('typeList', $this->_Tag_SourceType);
        $this->display('tag/add.html');
    }
    
    /**
     * 编辑标签页面
     * @author LamanLu
     * @since 2016-01-19
     */
    public function edit(){
        $id = intval($this->getField('id'));
        
        if(empty($id)){
            $this->showNoticePage(array('参数不能为空！'));
        }
        
        $tag = $this->TagModel->getDataByID($id);
        
        if(empty($tag)){
            $this->showNoticePage(array('无效的参数！'));
        }
        
        $this->setData('tag', $tag);
        $this->setData('typeList', $this->_Tag_SourceType);
        $this->display('tag/edit.html');
    }
    
    /**
     * 保存标签
     * @author LamanLu
     * @since 2016-01-19
     */
    public function save(){
        $id = intval($this->postField('id'));
        $name = trim($this->postField('name'));
        $type = intval($this->postField('type'));
        
        //判断数据完整性
        $msg = array();
        if($name == ''){
            $msg[] = '【标签名称】不能为空！';
        }
        
        if(empty($type)){
            $msg[] = '【所属类别】不能为空！';
        }
        
        if(!isset($this->_Tag_SourceType[$type])){
            $msg[] = '无效的所属类别！';
        }
        
        if(!empty($msg)){
            $this->showNoticePage($msg);
        }
        
        $res = $this->TagModel->checkExist($type,$name,$id);
        if(!empty($res)){
            $msg[] = '已经存在此标签信息！';
            $this->showNoticePage($msg);
        }
        
        //保存数据
        $data = array();
        $data['tag_name'] = $name;
        $data['source_type'] = $type;
        $data['update_time'] = time();
        
        if(empty($id)){            
            $res = $this->TagModel->insertData($data);
        }else{            
            $params = array('id'=>$id);
            $res = $this->TagModel->updateData($params,$data);
        }
        
        if(empty($res)){
            //保存失败，跳转至失败页面            
            $this->showNoticePage(array('保存信息失败！'));
        }
        
        $this->showNoticePage(array('保存信息成功！'));        
    }
    
    /**
     * 删除标签数据及对应关系
     * @author LamanLu
     * @since 2016-01-19
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
        
        $this->TagModel->deleteTag($id);
        
        $data['code'] = 1;
        $data['msg'] = '删除成功';
        
        echo json_encode($data);
    }
    
}
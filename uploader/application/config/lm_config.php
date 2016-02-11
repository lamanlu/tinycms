<?php

//上传根目录 /mnt/hgfs/project/test/uploads
$config['upload_root_path'] = '/data/www/muxi/uploader/public/uploads';

//图片上传配置
$config['upload_img_config'] = array(
    'upload_path' => '/images', //图片上传目录
    'allow_type' => array('jpg','jpeg','png'), //允许上传的图片类型
    'max_size' => 3072, //上传图片最大尺寸限制
);

//文件上传目录
$config['upload_file_config'] = array(
    'upload_path' => '/files', //上传目录
    'allow_type' => array('pdf','doc','docx','xls','xlsx'), //允许上传的类型
    'max_size' => 30720, //上传文件最大尺寸限制
);
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        sleep(45);
        echo '123';
//        show_404();
    }

    public function test(){
        $this->load->view('test.html');
    }
}
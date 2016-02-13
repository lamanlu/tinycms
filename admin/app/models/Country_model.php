<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Country_model extends MY_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCountryList(){
        
        $sql = "SELECT id,name FROM ".$this->tableName('country')." ORDER BY sort";
        $tmp = $this->getListBySQL($sql);
        
        return create_array_by_key($tmp, 'id');
    }
    
}

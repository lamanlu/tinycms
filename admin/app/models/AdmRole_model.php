<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdmRole_model extends MY_Model{


    public function __construct() {
        parent::__construct();
        
        $this->_Table = 'adm_role';
    }

    public function getRoleList(){        
        $sql = 'SELECT id,role_name FROM '.$this->tableName($this->_Table).' ORDER BY id ;';
        $tmp = $this->getListBySQL($sql);
        return create_array_by_key($tmp, 'id');
    }
    
}

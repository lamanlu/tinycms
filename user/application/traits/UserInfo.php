<?php

trait UserInfo {
    
    private function getUserStatus(){
        $uid = get_cookie('uid', TRUE);
        echo $uid;
    }
}

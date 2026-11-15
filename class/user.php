<?php
// require_once __DIR__ ."/../configs/Database.php";
require_once __DIR__ ."/crud.php";

class User extends CRUD{
    public function getUser($id){
       return $this->query("SELECT id,username,email,profile_image,isAdmin,isActive FROM USERS",[],true);
       
    }
}
?>
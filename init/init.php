<?php 
$baseurl = '/webdevolop/';
// session_set_cookie_params(60*30);
$current_photo = !empty($user->photo) ? "./".$user->photo : "./assets/images/emptyprofile.jpg";
session_start();
require_once './init/db.init.php';
require_once './init/func/auth.func.php';
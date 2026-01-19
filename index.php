<?php
require_once './init/db.init.php';
include './includes/header.inc.php';
include './includes/navbar.inc.php';

$available_pages = ['login', 'register'];

if(isset($_GET['page'])) {
    $page = $_GET['page'];
      if (in_array($page, $available_pages)) {
      include './pages/' . $page . '.php';
  } else {
      echo '<h1>Error 404</h1>';
  }
  } else {
      echo '<h1>Home pages</h1>';
  }
  include './includes/footer.inc.php';
?>
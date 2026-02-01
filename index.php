<?php
require_once './init/db.init.php';
require_once './init/func/auth.func.php';

include './includes/header.inc.php';
include './includes/navbar.inc.php';

$available_pages = ['login', 'register'];

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if (in_array($page, $available_pages)) {
        include './pages/' . $page . '.php';
    } else {
        // ... (កូដផ្សេងទៀត)
    }
} else {
    echo '<h1>Home page</h1>';
}

include './includes/footer.inc.php';
?>
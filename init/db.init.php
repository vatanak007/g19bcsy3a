<?php
    $db_host = '127.0.0.1';    //localhost
    $db_name = 'webdevolop';
    $db_user = 'root';
    $db_pass = '';
    $db_port = 3306;

    $db = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

    if($db->connect_error){
        echo $db->connect_error;
        die();
    }


    function loginUser($username, $passwd)
{
    global $db;
    
    $query = $db->prepare('SELECT * FROM tbl_users WHERE username = ? AND passwd = ?');
    $query->bind_param('ss', $username, $passwd);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        
        return true;
    }
    return false;
}
?>

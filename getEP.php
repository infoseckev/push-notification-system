<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'password';
$dbname = 'moon';


if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    //Request identified as ajax request

    if(@isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']=="http://localhost/new-notifications/main.php")
    {
        //HTTP_REFERER verification
        if($_POST['token'] == $_SESSION['token']) {
            $db = new db($dbhost, $dbuser, $dbpass, $dbname);

            $endpoints = $db->query('SELECT * FROM user_info')->fetchAll();

            $db->close();

            header('Content-type: application/json');
            echo json_encode($endpoints);
        }
        else {
            //header('Location: http://yourdomain.com');
        }
    }
    else {
        //header('Location: http://yourdomain.com');
    }
}
else {
    //header('Location: http://yourdomain.com');
}
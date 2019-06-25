<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'password';
//$dbpass = 'Kj$gX%2f2019_2020';
$dbname = 'moon';


if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    //Request identified as ajax request


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
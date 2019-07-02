<?php
require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'password';
$dbpass = 'Kj$gX%2f2019_2020';
$dbname = 'moon';

//$params = json_decode($_POST['json']);


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        /* $db = new db($dbhost, $dbuser, $dbpass, $dbname);

         $endpoints = $db->query('SELECT * FROM user_info')->fetchAll();

         $db->close();

         header('Content-type: application/json');
         echo json_encode($endpoints);*/
        break;
    case 'POST':
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

        $json = json_decode($_POST['json']);
        $ep = $json->ep;

        $res = $db->query('UPDATE sent_logs SET  `is_clicked` = 1 WHERE `endpointId` = ? AND `id` > 0', $ep);

        break;
    case 'PUT':

        break;
    case 'DELETE':

        break;
    default:

        break;
}
/*header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
}*/





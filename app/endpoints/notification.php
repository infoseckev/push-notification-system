<?php
require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../classes/db.php';
include '../classes/SendNotification.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
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

        header('Access-Control-Allow-Origin: *');

        //header('Content-type: application/json');

        $params =  json_decode(file_get_contents('php://input'), TRUE);

        $json = $params['json'];

        //return;
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);
        //var_dump($_POST);
        //$json = $params->json; //->json; //json_decode($_POST);

        $msg = $json['msg'];
        $title = $json['title'];

        $image = $json['image'];
        $icon = $json['icon'];

        $url = $json['url'];

        $domainIds = $json['domainIds'];

        foreach($domainIds as $domainId){
            $endpointarr = $db->query('SELECT user_info.endpoint, user_info.publicKey, user_info.authToken, user_info_domainId.user_info_id, user_info_domainId.domainId
                                            FROM user_info
                                            INNER JOIN user_info_domainId
                                            ON user_info.id = user_info_domainId.user_info_id 
                                            WHERE user_info_domainId.domainId = ?', $domainId)->fetchAll();

            foreach($endpointarr as $eps){

                $response = SendNotification::sendNotification($eps['endpoint'], $eps['publicKey'], $eps['authToken'], $msg, $title, $icon, $image, $url);
            }

        }


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





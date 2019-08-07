<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../keys/.env.key');

include '../classes/db.php';
include '../classes/SendNotification.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');

$dbname = getenv('DB_NAME');

//$params = json_decode($_POST['json']);
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':

        break;
    case 'POST':
        $request = $_SERVER['REQUEST_URI'];

        switch ($request) {
            case '/' :
                //require __DIR__ . '/views/index.php';
                //break;
            default:
                sendNotification($dbhost, $dbuser, $dbpass, $dbname);
                break;
        }


    case 'PUT':

        break;
    case 'DELETE':

        break;
    default:

        break;
}

function sendNotification($dbhost, $dbuser, $dbpass, $dbname) {
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

    //$sent_id = sha1(time());

    /*foreach($domainIds as $domainId){
        $endpointarr = $db->query('SELECT user_info.endpoint, user_info.publicKey, user_info.authToken, user_info_domainId.user_info_id, user_info_domainId.domainId
                                            FROM user_info
                                            INNER JOIN user_info_domainId
                                            ON user_info.id = user_info_domainId.user_info_id 
                                            WHERE user_info_domainId.domainId = ?', $domainId)->fetchAll();

        foreach($endpointarr as $eps){

            $response = SendNotification::sendNotification($eps['endpoint'], $eps['publicKey'], $eps['authToken'], $msg, $title, $icon, $image, $url, $sent_id, $db);
        }

    }*/
}
/*header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
}*/





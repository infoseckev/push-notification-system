<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../keys/.env.key');
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

include '../classes/db.php';
$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');

$dbname = getenv('DB_NAME');

//$params = json_decode($_POST['json']);


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

        $endpoints = $db->query('SELECT distinct domainId, domain_name FROM user_info_domainId WHERE domain_name != NULL or domain_name != ""')->fetchAll();

        $db->close();

        header('Access-Control-Allow-Origin: *');

        header('Content-type: application/json');
        echo json_encode($endpoints);
        break;
    case 'POST':
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

        header('Access-Control-Allow-Origin: *');

        header('Content-type: application/json');
        $json = json_decode($_POST['json']);
        $ep = $json->ep;
        $sent_id = $json->sent_id;
        echo "sentid: " . $sent_id;

        $res = $db->query('UPDATE sent_logs SET  `is_clicked` = 1, date_clicked = NOW() WHERE `endpointId` = ? AND `sent_id` = ? AND `id` > 0', $ep, $sent_id);

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





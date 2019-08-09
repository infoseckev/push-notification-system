<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../keys/.env.key');

include '../classes/db.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':

        break;
    case 'POST':
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

        header('Access-Control-Allow-Origin: *');

        header('Content-type: application/json');
        $json = json_decode($_POST['json']);
        $ep = $json->ep;
        $sent_id = $json->sent_id;

        echo $sent_id;
        $res = $db->query('UPDATE sent_logs SET  `is_received` = 1, `date_received` = NOW() WHERE `endpointId` = ? AND `sent_id` = ? AND `id` > 0', $ep, $sent_id);

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





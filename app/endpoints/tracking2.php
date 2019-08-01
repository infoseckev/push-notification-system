<?php
require __DIR__ . '/../vendor/autoload.php';
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '../.env.key');

include '../classes/db.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
//$password = 'Kj$gX%2f2019_2020';
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

        $res = $db->query('UPDATE sent_logs SET  `is_received` = 1 WHERE `endpointId` = ? AND `id` > 0', $ep);

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





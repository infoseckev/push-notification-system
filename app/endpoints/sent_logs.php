<?php
require __DIR__ . '/../vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use SecureEnvPHP\SecureEnvPHP;

(new SecureEnvPHP())->parse('../../.env.enc', '../keys/.env.key');

//include '../classes/db.php';
//include '../classes/SendNotification.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');

$dbname = getenv('DB_NAME');

//  Php frameworks add the autoloader class for you.
//  If you don't use any php framework, be sure that you have included it yourself.
//
//  require_once '..path/to../vendor/autoload.php';

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;

// using SQLite as DB for the examples. Change here to fit your db.
$config['host'] = $dbhost;
$config['port'] = 3306;
$config['username'] = $dbuser;
$config['password'] = $dbpass;
$config['database'] = $dbname;
$dt = new Datatables(new MySQL($config));

$dt->query('Select id, endpointId, date_sent, is_received, is_clicked, sent_id, is_sent, domain_id from sent_logs');

echo $dt->generate();
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('../../.env.enc', '.env.key');
include '../classes/db.php';
include '../classes/SendNotification.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

//static data needed for sending notifications
//read more here https://github.com/web-push-libs/web-push-php#user-content-authentication-vapid
$auth = array(
    'VAPID' => array(
        'subject' => 'Nooooo',
        'publicKey' => file_get_contents('public_key.php'), // don't forget that your public key also lives in app.js
        'privateKey' => file_get_contents('prisvate_keyxx11a.php'), // in the real world, this would be in a secret file
    ),
);

$webPush = new WebPush($auth);
//fetchAll() is most efficient
$results = $conn->query("SELECT notifications_queue.id, notifications_queue.endpointId, notifications_queue.dateToSend, messages.click_url, messages.title, messages.icon_url, messages.image_url, 
                                    messages.message, public_key, auth_token, sent_id, notifications_queue.domain_id, notifications_queue.message_id
                                    FROM notifications_queue 
                                    INNER JOIN messages ON messages.id = 
                                    notifications_queue.message_id WHERE dateToSend <= NOW()");

//quick hack
$domainId = 0;
$endpointList = [];

foreach ($results as $res) {

    $message_id = $res['message_id'];
    //static data
    $info = [
        'subscription' => Subscription::create([
            'endpoint' => $res['endpointId'],
            'publicKey' =>  $res['public_key'], // base 64 encoded, should be 88 chars
            'authToken' =>  $res['auth_token'], // base 64 encoded, should be 24 chars
        ]),
        'payload' => array(
            'body' => $res['message'],
            'icon' =>  $res['icon_url'],
            'image' =>  $res['image_url'],
            'url' => $res['click_url'],
            'data' => ['data' => $res['endpointId'], 'sent_id' => $res['sent_id']],
            'title' => $res['title']
        ),
        'hack-data'=>array(
            'domain_id' => $res['domain_id'],
            'sent_id' =>  $res['sent_id'],
            'id' =>  $res['id']
        ),
    ];

    $endpointList[] = $info;

}

$sent_id = '';
$resArr = [];
$counter = 0;
foreach ($endpointList as $ep){
    $tmp_sent_id = $ep['hack-data']['sent_id'];
    if($sent_id != $tmp_sent_id){
        $sent_id = $tmp_sent_id;
        $counter = $counter + 1;
    }
    $resArr[$counter][] = $ep;

}
foreach ($resArr as $ep){
    $sent_identification = $ep[0]['hack-data']['sent_id'];
    foreach ($ep as $notification) {
        //var_dump($notification);
        $webPush->sendNotification(
            $notification['subscription'],
            json_encode($notification['payload']) // optional (defaults null)
        );

        //exit;
    }
////////////////////////////////////////
///
    $successSends = [];
    foreach ($webPush->flush() as $report) {
        $ep = $report->getRequest()->getUri()->__toString();

        if ($report->isSuccess()) {

            try {
                $stmt = $conn->prepare("INSERT INTO sent_logs (sent_id, endpointId, is_sent, domain_id, message_id) values (?, ?, 1,?, ?)");
                $stmt->bind_param("ssii", $sent_identification, $ep, $domainId, $message_id);
                $stmt->execute();
                $stmt->close();
            }
            catch(Exception $e)
            {
                echo  "<br>" . $e->getMessage();
            }

        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO sent_logs (sent_id, endpointId, is_sent, domain_id, message_id) values (?, ?, 0,?, ?)");
                $stmt->bind_param("ssii", $sent_identification, $ep, $domainId, $message_id);
                $stmt->execute();
                $stmt->close();
            }
            catch(Exception $e)
            {
                echo  "<br>" . $e->getMessage();
            }
        }
    }
}

exit;
//var_dump($endpointList);
// send multiple notifications with payload

////////////////////////////////////////
/*$data = [
    'John','Doe', 22,
    'Jane','Roe', 19,
];
$stmt = $pdo->prepare("INSERT INTO users (name, surname, age) VALUES (?,?,?)");
try {
    $pdo->beginTransaction();
    foreach ($data as $row)
    {
        $stmt->execute($row);
    }
    $pdo->commit();
}catch (Exception $e){
    $pdo->rollback();
    throw $e;
}*/

/////////////////////////////////////////

/*
$stmt = $conn->prepare("DELETE FROM notifications_queue WHERE `id` = ?");
$stmt->bind_param("i", $res['id']);
$stmt->execute();
$conn->close();
//$db->close();

foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();
    if ($report->isSuccess()) {
        echo "[v] Message sent successfully for subscription {$endpoint}.";
    } else {
        echo "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
    }
}*/
/*require '/var/www/pusher.f5ads.com/public_html/app/vendor/autoload.php';

use SecureEnvPHP\SecureEnvPHP;

(new SecureEnvPHP())->parse('/var/www/pusher.f5ads.com/public_html/.env.enc', '/var/www/pusher.f5ads.com/public_html/app/keys/.env.key');
include '/var/www/pusher.f5ads.com/public_html/app/classes/db.php';
include '/var/www/pusher.f5ads.com/public_html/app/classes/SendNotification.php';

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$db = new db($dbhost, $dbuser, $dbpass, $dbname);

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
$results = $db->query('SELECT id, endpointId, dateToSend, click_url, title, icon_url, image_url, message, public_key, auth_token, sent_id, domain_id
                    FROM notifications_queue WHERE dateToSend <= NOW()')->fetchAll();

foreach ($results as $res) {

    SendNotification::sendNotification($res['endpointId'], $res['public_key'], $res['auth_token'], $res['message'], $res['title'], $res['icon_url'], $res['image_url'], $res['click_url'], $res['sent_id'], $res['domain_id'], $db, $res['id']);
    $stmt = $conn->prepare("DELETE FROM notifications_queue WHERE `id` = ?");
    $stmt->bind_param("i", $res['id']);
    $stmt->execute();
}

$db->close();

*/





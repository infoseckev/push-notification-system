<?php

require '/var/www/html/blackops.f5ads.com/app/vendor/autoload.php';

use SecureEnvPHP\SecureEnvPHP;
(new SecureEnvPHP())->parse('/var/www/html/blackops.f5ads.com/.env.enc', '/var/www/html/blackops.f5ads.com/app/keys/.env.key');
ini_set('max_execution_time', 0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//MAX EXECUTION TIME IS 4 MINUTES BECAUSE NEXT CRON JOB PICKS UP IN 1 MIN SO YOU DONT WANT 2
ini_set('memory_limit', '512M');
error_reporting(E_ALL);

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
include ('app/endpoints/macro.php');
$start = microtime(true);

$dbhost = getenv('DB_HOST');
$dbuser = getenv('DB_USER');
$dbpass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$conn = new mysqli("$dbhost", $dbuser, $dbpass, $dbname);

//:::::::::::::Library stuff here
//read more here https://github.com/web-push-libs/web-push-php#user-content-authentication-vapid

$timeout = 6; // seconds
$clientOptions = [
    \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => false,
    \GuzzleHttp\RequestOptions::VERIFY => false,
]; // see \GuzzleHttp\RequestOptions
$defaultOptions = ['batchSize' => 1000];
$webPush = new WebPush([], $defaultOptions, $timeout, $clientOptions);
$webPush->setAutomaticPadding(false);

//lets start with
$results = $conn->query("SELECT 
                                    notifications_queue.id as notif_id, 
                                    notifications_queue.endpointId,
                                    notifications_queue.dateToSend, 
                                    messages.title, 
                                    messages.icon_url, 
                                    messages.image_url, 
                                    messages.message, 
                                    notifications_queue.public_key as notification_public_key, 
                                    notifications_queue.auth_token, 
                                    notifications_queue.sent_id, 
                                    notifications_queue.domain_id, 
                                    notifications_queue.message_id,
                                    notifications_queue.click_url,
                                    user_info.user_keys_id,
                                    user_info.gps,
                                    user_keys.public_key,
                                    user_keys.private_key,
                                    user_keys.subject
                                    FROM notifications_queue 
                                    INNER JOIN messages ON messages.id = notifications_queue.message_id
									INNER JOIN user_info ON user_info.endpoint = notifications_queue.endpointId
                                    INNER JOIN user_keys ON user_info.user_keys_id = user_keys.id
                                    WHERE dateToSend <= NOW() ORDER BY user_info.datevisited DESC  LIMIT 1000");

$webPush->setReuseVAPIDHeaders(true);
//$endTime = microtime(true);
//echo "<br/>time elapsed sql query fetch endpoints page : " . ($endTime - $start);
//quick hack
//$domainId = 0;
$endpointList = [];

foreach ($results as $res) {

    $notificationId = $res['notif_id'];

    //ok so all the cronjob has the same message, click url, icon url, etc, I use this variable later on
    //its a hack because
    $message_id = $res['message_id'];

    $clickURL =
    $info = [
        'subscription' => Subscription::create([
            'endpoint' => $res['endpointId'],
            'publicKey' => $res['notification_public_key'], // base 64 encoded, should be 88 chars
            'authToken' => $res['auth_token'], // base 64 encoded, should be 24 chars
        ]),
        'payload' => array('body' => $res['message'],
            'icon' => $res['icon_url'],
            'image' => $res['image_url'],
            'url' => macro_replace_GPS($res['click_url'], $res['gps']),
            'data' => ['data' => $res['endpointId'], 'sent_id' => $res['sent_id']],
            'title' => $res['title']),
        'vapid' => array('VAPID' => array(
            'subject' => $res['subject'],
            'publicKey' => $res['public_key'], // don't forget that your public key also lives in app.js
            'privateKey' => $res['private_key'], // in the real world, this would be in a secret file
        )),
        'hack-data' => array(
            'domain_id' => $res['domain_id'],
            'sent_id' => $res['sent_id'],
            'id' => $res['notif_id']
        ),
    ];

    //point of no return, deleting from queue - change to soft delete
    //delete should come after you know if its sent but had to do it this way

    $stmt = $conn->prepare("DELETE FROM notifications_queue WHERE `id` = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->close();


    $endpointList[] = $info;
}

//$endTime = microtime(true);
//echo "<br/>time elapsed first function page : " . ($endTime - $start);

$sent_id = '';
$resArr = [];
$counter = 0;

//$start = microtime(true);

//a hack to get the sentid
foreach ($endpointList as $ep) {
    $tmp_sent_id = $ep['hack-data']['sent_id'];
    if ($sent_id != $tmp_sent_id) {
        $sent_id = $tmp_sent_id;
        $counter = $counter + 1;
    }
    $resArr[$counter][] = $ep;

}
/*$endTime = microtime(true);
echo "<br/>time elapsed array initializing last page : " . ($endTime - $start);
$start = microtime(true);*/
$counter = 0;
foreach ($resArr as $ep) {
    //sent_id is unique to this cronjob
    //hack to isolate logic for performance
    $sent_identification = $ep[0]['hack-data']['sent_id'];
    $domainId = $ep[0]['hack-data']['domain_id'];

    foreach ($ep as $notification) {

        //Subscription $subscription, ?string $payload = null, bool $flush = false, array $options = [], array $auth = []
        $webPush->sendNotification(
            $notification['subscription'],
            json_encode($notification['payload']),
            false,
            $defaultOptions,
            $notification['vapid']
             // optional (defaults null)
        );
    }

    foreach ($webPush->flush() as $report) {

        $ep = $report->getRequest()->getUri()->__toString();
        $counter = $counter + 1;
        if ($report->isSuccess()) {

            try {
                $stmt = $conn->prepare("INSERT INTO sent_logs (sent_id, endpointId, is_sent, domain_id, message_id) values (?, ?, 1,?, ?)");
                $stmt->bind_param("ssii", $sent_identification, $ep, $domainId, $message_id);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                echo "<br>" . $e->getMessage();
            }

        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO sent_logs (sent_id, endpointId, is_sent, domain_id, message_id) values (?, ?, 0,?, ?)");
                $stmt->bind_param("ssii", $sent_identification, $ep, $domainId, $message_id);
                $stmt->execute();
                $stmt->close();
            } catch (Exception $e) {
                echo "<br>" . $e->getMessage();
            }
        }


    }
}
$endTime = microtime(true);
echo "<br/>time elapsed : " . ($endTime - $start) . " for " . $counter . " endpoints";

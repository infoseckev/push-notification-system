<?php
require '../vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


class SendNotification
{
    private $endpoint;
    private $publicKey;
    private $authToken;


    function __construct(  ) {
        /*$this->$endpoint = $endpoint;
        $this->$publicKey = $publicKey;
        $this->$authToken = $authToken;*/
    }

    public static function sendNotification($endpoint, $publicKey, $authToken, $message, $title, $icon, $image, $url) {
        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "password";
        //$dbpass = 'Kj$gX%2f2019_2020';
        $dbname = "moon";

        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

        $info = [
            'subscription' => Subscription::create([
                'endpoint' => $endpoint,
                'publicKey' =>  $publicKey, // base 64 encoded, should be 88 chars
                'authToken' =>  $authToken, // base 64 encoded, should be 24 chars
            ]),
            'payload' => $message,
        ];

        $res = [];

        array_push($res, $info);

        $auth = array(
            'VAPID' => array(
                'subject' => 'Nooooo',
                'publicKey' => file_get_contents( '../keys/public_key.txt'), // don't forget that your public key also lives in app.js
                'privateKey' => file_get_contents( '../keys/prisvate_keyxx11a.php'), // in the real world, this would be in a secret file
            ),
        );

        $webPush = new WebPush($auth);

        $notifContent = array(
            'body' => $message,
            'icon' =>  $icon,
            'image' =>  $image,
            'url' => $url,
            'data' => $endpoint,
            'title' => $title
        );


        $res = $webPush->sendNotification(
            $info['subscription'],
            json_encode($notifContent)
        );

        // handle eventual errors here, and remove the subscription from your server if it is expired
        foreach ($webPush->flush() as $report) {
            //$endpointz = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {

                $res = $db->query('INSERT INTO sent_logs (endpointId, is_received) values (?, 1) ', $endpoint);

                $db->close();

                //$this->response->body(json_encode("Message sent successfully for subscription {$endpoint}", JSON_UNESCAPED_SLASHES));
                echo "Success Sent";
                //return $this->response;

            } else {
                $res = $db->query('INSERT INTO sent_logs (endpointId, is_received) values (?, 0) ', $endpoint);

                //$this->response->body(json_encode("Message failed to sent for subscription {$endpoint}: {$report->getReason()}", JSON_UNESCAPED_SLASHES));
                echo "oh NO :(";
                //return $this->response;

            }
        }
    }
}
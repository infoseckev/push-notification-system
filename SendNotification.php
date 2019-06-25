<?php
require __DIR__ . '/vendor/autoload.php';
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
                'publicKey' => file_get_contents(__DIR__ . '/keys/public_key.txt'), // don't forget that your public key also lives in app.js
                'privateKey' => file_get_contents(__DIR__ . '/keys/prisvate_keyxx11a.php'), // in the real world, this would be in a secret file
            ),
        );

        $webPush = new WebPush($auth);

        $notifContent = array(
            'body' => $message,
            'icon' =>  'uploads/icon.jpg',
            'image' =>  'uploads/image.jpg',
            'url' => $url,
            'title' => $title
        );



        $res = $webPush->sendNotification(
            $info['subscription'],
            json_encode($notifContent)
        );
        /*foreach ($res as $kk) {
            $ell = $webPush->sendNotification(
                $kk['subscription'],
                $kk['payload']
            );
        }*/


        // handle eventual errors here, and remove the subscription from your server if it is expired
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                //$this->response->body(json_encode("Message sent successfully for subscription {$endpoint}", JSON_UNESCAPED_SLASHES));
                //echo "on ho";
                //return $this->response;

            } else {
                //$this->response->body(json_encode("Message failed to sent for subscription {$endpoint}: {$report->getReason()}", JSON_UNESCAPED_SLASHES));
                //echo "on ho";
                //return $this->response;

            }
        }

    }
}
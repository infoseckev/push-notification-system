<?php
require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
include 'SendNotification.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'password';
//$dbpass = 'Kj$gX%2f2019_2020';
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

        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

         $endpoints = $db->query('SELECT * FROM user_info')->fetchAll();

         $db->close();

         header('Content-type: application/json');
         echo json_encode($endpoints);

        //$total = count($_FILES['file']['tmp_name']);
        //echo $total;
        ///////TODO:make this in Class////////////////
        $target_dir = "uploads/";
        $target_file = $target_dir . basename("image.jpg"); // . basename($_FILES["file"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        //var_dump($_FILES);
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
        } else {
            //echo "File is not an image.";
        }
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            //echo "The file " . basename($_FILES["file"]["name"]) . " has been uploaded.";
        } else {
            //echo "Sorry, there was an error uploading your file.";
        }
        $target_file = $target_dir . basename("icon.jpg"); // . basename($_FILES["file"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        //var_dump($_FILES);
        $check = getimagesize($_FILES["icon"]["tmp_name"]);
        if ($check !== false) {
            //echo "File is an image - " . $check["mime"] . ".";
        } else {
            //echo "File is not an image.";
        }
        if (move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file)) {
            //echo "The file " . basename($_FILES["file"]["name"]) . " has been uploaded.";
        } else {
            //echo "Sorry, there was an error uploading your file.";
        }
        /////////////END///////////////////////////////
        $json = json_decode($_POST['json']);
        $msg = $json->msg;
        $title = $json->title;

        $image = $json->image;
        $icon = $json->icon;

        //echo $icon;

        $url = $json->url;

        $endpointId = $json->endpointid;
        $db = new db($dbhost, $dbuser, $dbpass, $dbname);

        $endpoint = $db->query('SELECT endpoint, publicKey, authToken FROM user_info WHERE id = ?', $endpointId)->fetchArray();
        $response = SendNotification::sendNotification($endpoint['endpoint'], $endpoint['publicKey'], $endpoint['authToken'], $msg, $title, $icon, $image, $url);
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





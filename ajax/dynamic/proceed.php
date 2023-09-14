<?php
if(!isset($_POST['r'])){
    die('ERROR');
}
require_once '../../php/global.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die('AUTH');
}
$dbobject = DBconfig::getDB();
$dbobject->update("booked_courses", [
    "marked_paid" => "Yes"
], [
    "user_id" => $_SESSION['user_id'],
    "reference" => $_POST['r']
]);
echo 'DONE';
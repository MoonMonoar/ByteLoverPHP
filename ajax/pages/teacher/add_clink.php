<?php
if(!isset($_POST['c'])
|| !isset($_POST['l'])){
    die("Input unsatisfied!");
}
if(empty($_POST['c'])
|| empty($_POST['l'])){
    die("Input unsatisfied!");
}
require_once '../../../php/global.php';
require_once '../../../php/dbconfig.php';
require_once '../../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die("Unauthorized!");
}
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$teachers = new Teachers();
$courses = new Courses();
$routine = new Routine();
$calender = new Calender();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$class_id = $security->decryptText($_POST['c']);
if(null == $class_id){
    die("Unauthorized!");
}
$db->update("classes", [
        "class_link" => $_POST['l'],
        "status" => "Live now",
        "is_taken" => "Yes"
    ], [
        "id" => $class_id
    ]);
die('DONE');
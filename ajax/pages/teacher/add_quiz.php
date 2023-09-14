<?php
if(!isset($_POST['c'])
|| !isset($_POST['q'])
|| !isset($_POST['m'])
|| !isset($_POST['o1'])
|| !isset($_POST['o2'])
|| !isset($_POST['o3'])
|| !isset($_POST['o4'])
|| !isset($_POST['a'])
|| !isset($_POST['e'])){
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
if($courses->quizCount($conn, $class_id) >= 15){
    die("MAXED");
}
$db->insert("quiz_questions", [
    "class_id" => $class_id,
    "question" => $_POST['q'],
    "answer" => $_POST['a'],
    "explanation" => $_POST['e'],
    "marks" => $_POST['m'],
    "option_1" => $_POST['o1'],
    "option_2" => $_POST['o2'],
    "option_3" => $_POST['o3'],
    "option_4" => $_POST['o4'],
]);
if(null != $db->id()){
    //Update exam timer
    $db->update("classes", [
        "exam_timer_from" => date("Y-m-d h:m:s", time())
    ], [
        "id" => $class_id
    ]);
    die('DONE');
}
die('ERROR');

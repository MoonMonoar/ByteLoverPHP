<?php
if(!isset($_POST['c'])
|| !isset($_POST['q'])
|| !isset($_POST['d'])){
    die("Input unsatisfied!");
}
if(empty($_POST['c'])
|| empty($_POST['q'])
|| empty($_POST['d'])){
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
$course_id = $security->decryptText($_POST['c']);
$date = $security->decryptText($_POST['d']);
if(null == $course_id || null == $date){
    die("Unauthorized!");
}
$exam_questions = $courses->getExamquestions($conn, $course_id, $date);
if(null != $exam_questions && $exam_questions->num_rows >= 3){
    die("MAXED");
}
$db->insert("exam_questions", [
    "course_id" => $course_id,
    "exam_date" => $date,
    "question" => $_POST['q']
]);
if(null != $db->id()){
    die('DONE');
}
die('ERROR');

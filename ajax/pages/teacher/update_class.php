<?php
if(!isset($_POST['cid']) || !isset($_POST['title']) || !isset($_POST['date']) || !isset($_POST['time']) || !isset($_POST['delay'])){
    die("Unauthorized!");
}
require_once '../../../php/global.php';
require_once '../../../php/strings.php';
require_once '../../../php/langset.php';
require_once '../../../php/templates.php';
require_once '../../../php/dbconfig.php';
require_once '../../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die("Unauthorized!");
}
$db_config = new DBconfig();
$courses = new Courses();
$routine = new Routine();
$calender = new Calender();
$profile = new Profile();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
$web_client = new WebClient();
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$course = $courses->courseByid($conn, $_POST['cid']);
$students = $courses->getStudents($conn, $_POST['cid']);
$get_class = $courses->getClass($conn, $_POST['cid'], $_SESSION['user_id'], $_POST['date']);
$video_link = 'None';
$video_file_name = NULL;
if(isset($_POST['vl']) && $_POST['vl'] != 'None'){
    $video_link = $_POST['vl'];
}
if(isset($_POST['vf']) && $_POST['vf'] != 'None'){
    $video_file_name = $_POST['vf'];
}

if($get_class == false){
    //New
    $db->insert("classes", [
        "title" => $_POST['title'],
        "start_date" => $_POST['date'],
        "course_id" => $_POST['cid'],
        "video_link" => $video_link,
        "file_name" => $video_file_name,
        "teacher_id" => $_SESSION['user_id']
    ]);
}
else {
    $initital_status = $get_class[0]['status'];
    if($video_link != 'None'){
        $initital_status = 'Record available';
    }
    //Just update
    $db->update("classes", [
        "title" => $_POST['title'],
        "video_link" => $video_link,
        "file_name" => $video_file_name,
        "status" => $initital_status
    ],[
        "start_date" => $_POST['date'],
        "course_id" => $_POST['cid'],
        "teacher_id" => $_SESSION['user_id']
    ]);
}

if($video_link != 'None' && $get_class != false && $video_link != $get_class[0]['video_link']){
    //Send video avilable notification
    $body = '📚 '.$get_class[0]['title'].'.
📽️ Class record available.';
            foreach($students as $s){
                $token = $web_client->getUsertoken($s['user_id']);
                if(null != $token){
                    $client->sendNotification($token, $course[0]['name'], $body);
                }
            }
}

//Delay date update
if(strtotime($_POST['delay']) != false){
    if((str_replace(' 00:00:00', '', $get_class[0]['delayed_date']) != $_POST['delay']) ||
        ($_POST['time'] != "DEFAULT" && $get_class[0]['start_time'] != $_POST['time'])){
        //Update the time
        if($_POST['time'] != "DEFAULT"){
            $db->update("classes", [
                'start_time' => $_POST['time']
            ], [
                'id' => $get_class[0]['id']
            ]);
        }
        //Update date
        $delayed = new DateTime($_POST['delay']);
        $currentDate = date("Y-m-d");
        if($delayed >= $currentDate) {
            $db->update("classes", [
                "delayed_date" => $_POST['delay']
            ],[
                "id" => $get_class[0]['id']
        ]);
        }
        //SEND WEB-NOTIFICATION
        if($get_class != false){
            $body = '📚 '.$get_class[0]['title'].'.
🕛 Rescheduled to '.$_POST['delay'].' '.$_POST['time'].'.';
            foreach($students as $s){
                $token = $web_client->getUsertoken($s['user_id']);
                if(null != $token){
                    $client->sendNotification($token, $course[0]['name'], $body);
                }
            }
        }
    }
}
echo 'DONE';
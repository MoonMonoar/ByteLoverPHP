<?php
if(!isset($_POST['cid'])){
    die("Unauthorized!");
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
$slot = $courses->getSlot($conn, $_POST['cid']);
$dates = $routine->getDates($slot[0], $slot[1]);
?>
<div class="class_editor_pad">
    <div class="qm">
        Select a class to add/delete quiz questions<br>
        <small>If you dont see a date, that means the class routine is not made for that date. Go to routine first.</small>
    </div>
<div class="qf">
<?php
for($l = 0; $l < count($dates); $l++){
    $date = $dates[$l];
    $day = $calender->getDay($date);
    if($day != "Friday"){
    $get_class = $courses->getClass($conn, $_POST['cid'], $_SESSION['user_id'], $date);
    if(!!$get_class){
        if(NULL != $get_class[0]["delayed_date"]){
            $date = str_replace(" 00:00:00", "", $get_class[0]["delayed_date"]);
        }
        $class_title = $get_class[0]["title"];
        $class_id = $get_class[0]["id"];
        if(isset($class_id)){
            echo '<div onclick="quiz_data(this)" data-cid="'.$security->encryptText($class_id).'" class="q_date">'.$date.'</div>';
        }
        unset($class_title);
        unset($class_id);
    }
    }
}
?>
</div>
</div>
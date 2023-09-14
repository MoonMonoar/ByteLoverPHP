<?php
if(!isset($_POST['cid'])){
    die("Unauthorized!");
}
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
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
$functions = new Functions();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
$course_id = $security->decryptText($_POST['cid']);
if(null == $course_id){
    die("Unauthorized! Decryption error.");
}
?>
<section class="marks">
<div class="title b">
    Your latest marks
</div>
<?php
//Select the latest marks in this course
//Select the latest given quizes, all of them! (Moderate slow)
$user_own_profile = $profile->getProfile($conn, $_SESSION['user_id']);
$stmt = $conn->prepare("SELECT * FROM taken_quizes WHERE user_id = ? ORDER BY time DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach($rows as $row){
    $class_data = $courses->getclassByid($conn, $row['class_id']);
        ?>
        <div class="sub_marks">
            <div class="intro">
                <div>
                    <img src="/img/users/<?php echo $user_own_profile['image'];?>" height="50px" width="50px" alt="<?php echo $user_own_profile['fullname'];?>">
                </div>
                <div class="name cflex">
                    <?php echo $user_own_profile['fullname'];?>
                </div>
            </div>
            <div class="marks_got B">
                <?php echo $row['marks_obtained'];?> / <?php echo $row['marks_total'];?>
            </div>
            <div class="class_info">
                <i class="fa fa-calendar-days mr5"></i><?php echo date('Y-m-d', strtotime($class_data[0]['start_date']));?> &middot <?php echo $class_data[0]['title'];?>
                <div>
                <i class="fa fa-clock mr5"></i>Taken <?php echo $functions->time_ago(strtotime($row['time']));?></div>
            </div>
        </div>
        <?php
    }
}
else {
    ?>
    <div class="fer mt10">
        You did not take any quizes in this course!
    </div>
    <?php
}
?>
<div class="title b mt10">
    Select a class date
</div>
<?php
//Get the dates for happened classes for the course
$slot = $courses->getSlot($conn, $course_id);
$dates = $routine->getDates($slot[0], $slot[1]);
?>
<div class="qf">
<?php
$spin = 0;
for($l = count($dates) - 1; $l >= 0 ; $l--){
    $date = $dates[$l];
    if(strtotime($date) > time()){
        continue;
    }
    $current_prefix = '';
    if($spin == 0){
        $current_prefix = ' id="qd_last"';
    }
    $day = $calender->getDay($date);
    if($day != "Friday"){
        $get_class = $courses->gettakenclassBydate($conn, $course_id, $date);
        if(!!$get_class){
            if(!$courses->checkClassquiz($conn, $get_class["id"])){
                //Quiz not ready
                continue;
            }
            if(NULL != $get_class["delayed_date"]){
                $date = date("Y-m-d", strtotime($get_class["delayed_date"]));
            }
            echo '<div'.$current_prefix.' onclick="marks_data(this)" data-cid="'.$security->encryptText($get_class["id"]).'" class="q_date">'.$date.'</div>';
        $spin++;
        }
    }
}
?>
</div>
<div id="marks_data" class="mt10">
    <?php 
    if($spin == 0){
        ?>
        <div class="em">
            No classes taken yet!
        </div>
        <?php
    }
    else {
    ?>
    <div class="em">
        Select a class date to see results!
    </div>
    <?php
        }
    ?>
</div>
</section>
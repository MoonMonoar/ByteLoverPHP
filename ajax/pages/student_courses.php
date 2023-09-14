<?php
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
$courses = new Courses();
$functions = new Functions();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
$user_courses = $courses->getCourses($conn, $_SESSION['user_id']);
if(null == $user_courses || count($user_courses) == 0){
?>
<div class="empty">
<?php echo $strings["no_course"][$lang];?>
    <div id="book_class" class="cflex">
            <a href="/courses/?ref=dash_button">
                <button class="b"><?php echo $strings["book_course"][$lang];?></button>
            </a>
    </div>
<div>
<?php
die();
}
?>
<section class="courses">
<div class="stex mb15">
        Your courses
    </div>
<?php
$serial = 1;
foreach($user_courses as $row){
$course_data = $courses->coursebyId($conn, $row['course_id']);
if(null == $course_data || null == $course_data[0]){
    continue;
}
?>
<div class="course_child">
    <div class="title b">
        <a target="_blank" href="/courses/<?php echo $course_data[0]['course_code'];?>?ref=courses">
        <?php echo $serial.'. '.$course_data[0]['name'];?>
        </a>
    </div>
    <div class="course_data">
        <div>
            <span><i class="fa-solid fa-clock"></i> Booked:</span> <?php echo ucfirst($functions->time_ago(strtotime($row['time'])));?>
        </div>
        <?php
        $exp = $courses->getExpiry($conn, $_SESSION['user_id'], $row['course_id']);
        if(NULL != $exp){
         ?>
        <div>
            <span><i class="fa-solid fa-stopwatch"></i> Expiry:</span> <?php echo $exp;?>
        </div>
         <?php
        }
        ?>
            <div>
            <span><i class="fa-solid fa-graduation-cap"></i> Course type:</span> <?php echo $course_data[0]['status'];?>
        </div>
                <div class="price">
            <span><i class="fa-solid fa-piggy-bank"></i> Fee:</span> <?php echo $row['course_price'];?> ৳ (With charge & VAT - After deducted by discount if applied)
        </div>
        <div>
            <span><i class="fa-solid fa-money-bills"></i> Paid:</span> <?php echo $row['marked_paid'];?>
            <?php if($row['marked_paid'] == "No"){
                echo '- ';
                ?> <a target="_blank" class="b" href="/courses/booknow/<?php echo $course_data[0]['course_code'];?>?ref=course_child">
                <u>SEND MONEY NOW</u>
            </a>
            <?php
            }
            ?>
        </div>
        <?php if($row['marked_paid'] != "No"){?>
        <div>
            <span><i class="fa-solid fa-check-circle"></i> Payment status:</span> <?php echo $row['payment_receive_approval'];?>
        </div>
        <?php } ?>
        <div class="pdo">
        <?php
        if(NULL == $exp){
            ?>
            <span class="b">From <?php echo $functions->formatDate($course_data[0]['start_date']).' to '.$functions->formatDate($course_data[0]['end_date']);?> (Depends on batch, public holidays and other factors)</span>
            <?php
                 }
                 else {
            ?>
            <span class="b">Record package expires on <?php echo $exp;?></span>
         <?php
        }
        ?>
        </div>
    </div>
</div>
<?php
$serial++;
}
?>
<div id="book_class" class="cflex">
            <a href="/courses/?ref=dash_button" target="_blank">
                <button class="b"><?php echo $strings["book_course"][$lang];?></button>
            </a>
</div>
</section>
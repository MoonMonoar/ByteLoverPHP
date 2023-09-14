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
$conn = $db_config->getConnection();
$db = $db_config->getDB();
$courses = new Courses();
$functions = new Functions();
//Get courses
$user_courses = $courses->getActivecourses($conn, $_SESSION['user_id']);

if(null == $user_courses){
    ?>
        <div class="no_class tm">
         Book a course first! Go to Courses.
        </div>
        <?php
    die();
}
?>

<div class="stex mb15">
        Your quizzes
</div>
<div class="class_editor_pad">
    
<?php
$l = 0;
foreach($user_courses as $row){
    $course_data = $courses->coursebyId($conn, $row['course_id']);
    $classes_today = $users->getQuizclass($conn, $row['course_id']);
    if($classes_today == null){
        continue;
    }
    ?>
    <?php
    foreach(array_reverse($classes_today) as $row){
            if(NULL != $row['delayed_date']){
                $row['start_date'] = $row['delayed_date'];
            }
            //Classes
            $date_of_class = $exam_from = date('Y-m-d', strtotime($row['start_date']));
            if(NULL != $row['exam_timer_from']){
                $exam_from = date('Y-m-d', strtotime($row['exam_timer_from']));
            }
            $held_date_in_ms = strtotime($exam_from.' 19:00:00'); //Counting from 07:00PM
            $given_time_in_ms = $held_date_in_ms+(48*3600); //48hrs
            $time_left = $given_time_in_ms-time();
            if($time_left <= 0){
                $time_left = 7*24*60*60;
            }
            $class_title = $row['title'];
            ?>
            <div class="class_editor">
            <div class="class_date">
            <?php echo ($l+1).'. '.$date_of_class.' &middot '.$class_title;?>
            </div>
            <div>
                <i class="fa fa-clock"></i> Quiz duration: 15 minutes(15 questions)
            </div>
            <div>
                <i class="fa-solid fa-marker"></i> Total marks: 15(Depends on class)
            </div>
            <div>
                <i class="fa fa-book"></i> Course: <?php echo $course_data[0]['name'];?>
            </div>
            <?php
            if($courses->checkClassquiz($conn, $row['id'])){
            ?>
            <div class="cl b cflex">
                <a target="_blank" href="/quiz/?class=<?php echo urlencode($security->encryptText($row['id']));?>&name=<?php echo $row['title'];?>">
                        <button class="ub cflex tb cbc bb">
                            <i class="fa-solid fa-clipboard-question"></i> <span class="b">Take Quiz</span>
                        </button>
                </a>
            </div>
        
            <div class="tm b">
                Quiz will disappear in <?php echo $functions->millisecondsToTime($time_left*1e3);?>, do not miss it.
            </div>
            
            <?php
            }
            else {
                ?>
                
                <div class="tm b">
                    Qustions for this class are still being made. Please wait untill done.
                </div>
            
                <?php
            }
            ?>
            </div>
        <?php
        $l++;
    }
    ?>
    <?php
}
if($l == 0){
    ?>
        <div class="empty">
            <?php echo $strings["empty_quizes"][$lang];?>
        </div>
    <?php
}
?>
</div>
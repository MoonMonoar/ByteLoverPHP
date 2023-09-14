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
$calender = new Calender();
$functions = new Functions();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
//Get courses
$user_courses = $courses->getActivecourses($conn, $_SESSION['user_id']);
$l = 0;
if(null == $user_courses){
    ?>
    <div class="no_class tm">
        You have no courses booked. Go to Courses.
        </div>
        <?php
    die();
}
foreach($user_courses as $row){
    $course_data = $courses->coursebyId($conn, $row['course_id']);
    $classes_today = $users->getTodaysclass($conn, $row['course_id']);
    if(null == $classes_today){
        continue;
    }
    ?>
    <div class="class_editor_pad">
    <?php
    foreach($classes_today as $row){
        if(NULL != $row['delayed_date']){
            $delayed_date = str_replace(" 00:00:00", "", $row['delayed_date']);
            if ($delayed_date !== date("Y-m-d")) {
              continue;
            }
            $row['start_date'] = $delayed_date;
        }
        //Classes
            $class_title = $row['title'];
            $class_link = $row['class_link'];
            $video_link = $row['video_link'];
            $class_id = $row['id'];
            ?>
            <div class="class_editor">
            <div class="class_date">
            <?php echo ($l+1).'. Today at '.$row['start_time'];?>
            </div>
            <div class="b">
                <?php echo $class_title;?>
            </div>
            <div>
                <i class="fa fa-clock"></i> Class time: <?php echo $row['start_time'];?>
            </div>
            <div>
                <i class="fa fa-stopwatch"></i> Ends in: <?php echo $row['duration'];?>
            </div>
            <div>
                <i class="fa fa-book"></i> Course: <?php echo $course_data[0]['name'];?>
            </div>
            
        <div class="smet mt5">
            Study materials
        </div>
        <div class="met_files">
            <section id="mtrs_<?php echo $class_id;?>">
            <?php
            if(isset($class_id)){
            //Get class study materials
            $class_pin = $security->encryptText($class_id);
            $materials = $courses->getMaterials($conn, $class_pin);
            if(null == $materials){
                ?>
                <div class="mmes nom">No study materials provided yet!</div>
                <?php
            }
            else {
                //Loop the materials
                ?>
                    <?php
                    foreach($materials as $file){
                    $ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
                    $size = $functions->formatFilesize('../../files/assets/'.$class_id.'/'.$file['file_name']);
                    ?>
                    <a href="/files/assets/<?php echo $class_id.'/'.$file['file_name'].'?hot_reload='.md5(time());?>" target="_blank">
                    <div class="sf">
                        <div class="ic">
                            <?php echo $ext;?>
                        </div>
                        <div class="name">
                            <?php echo '('.$size.') '.$file['file_name'];?>
                        </div>
                    </div>
                    </a>
                    <?php
                    }
            }
            }
                ?>
            </section>
    </div>
            
            <div class="cl b cflex tm">
                <?php
                if($video_link == "None"){
                if($class_link == "None"){
                    ?>
                    Class link will be available 10 minutes before time, keep refreshing.
                    <?php
                }
                else {
                    ?>
                    <a target="_blank" href="<?php echo $class_link;?>">
                                <button class="ub cflex tb cbc bb">
                                    <i class="fa-solid fa-user-plus"></i> <span class="b">Join Now</span>
                                </button>
                        </a>
                    <?php
                }
                }
                else {
                    ?>
                        <a target="_blank" href="/video/?class=<?php echo urlencode($security->encryptText($class_id));?>">
                                <button class="ub cflex tb cbc bb">
                                    <i class="fa-solid fa-circle-play"></i> <span class="b">Watch Class</span>
                                </button>
                        </a>
                    <?php
                }
                ?>
            </div>
            <div class="tm">
                <?php echo $row['status'];?>
            </div>
            </div>
        <?php
        $l++;
    }
    ?>
    </div>
    <?php
}
if($l == 0){
        //No classes
        ?>
        <div class="no_class tm">
        You don't have any class to attend today. Please go to Routine to check upcoming classes.
        </div>
        <script id="xs1">
            $(".class_editor_pad, #xs1").remove();
        </script>
        <?php
    }
?>
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
<div class="class_editor_pad">
<?php
$classes = $users->getClasses($conn, $course_id);
if(null == $classes){
    ?>
    <div class="empty">
        No classes are scheduled yet!
    <div>
    <?php
    die();
}
$l = 0;
foreach($classes as $row){
    //Class day
    $date = $row['start_date'];
    if(NULL != $row['delayed_date']){
        $date = $row['delayed_date'];
    }
    $day = $calender->getDay($date);
    $class_title = $row['title'];
    $date = date('Y-m-d', strtotime($date));
    $video_link = $row['video_link'];
    $class_id = $row['id'];
    ?>
    <div class="class_editor">
        <div class="class_date">
            <?php echo ($l+1).'. '.$date.'('.$day.')';?>
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
                    <a href="/files/assets/<?php echo $class_id.'/'.$file['file_name'].'?hot_reload='.md5(time());;?>" target="_blank">
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
                    ?>
                    Class record will be available after 1-2hours of class time.
                    <?php
                }
                else {
                    ?>
                    <a target="_blank" href="/video/?class=<?php echo urlencode($security->encryptText($row['id']));?>">
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
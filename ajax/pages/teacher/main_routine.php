<?php
if(!isset($_POST['cid'])){
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
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$slot = $courses->getSlot($conn, $_POST['cid']);
$dates = $routine->getDates($slot[0], $slot[1], $slot[2]);
?>

<?php
//Check if all are done
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND status = 'Recorded'");
$stmt->bind_param("i", $_POST['cid']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0) {
$show = true;
$stmt = $conn->prepare("SELECT video_link FROM classes WHERE course_id = ?");
$stmt->bind_param("i", $_POST['cid']);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach($rows as $row){
        if("None" == $row['video_link']){
            $show = false;
            break;
        }
    }
}
else {
    $show = false;
}
if($show){
?>
<div class="class_editor_pad">
<div class="mt5">
    <div class="at">
       <i class="fa-solid fa-record-vinyl"></i> Convert to Recorded course
    </div>
    <div class="mt5">
        Videos avilable for all scheduled classes. You can convert this course to recorded package. All students enrolled will be converted to the record package of this course.
    </div>
    <div class="mt5">
        <button class="b ce nom wauto" onclick="course_cnv(<?php echo $_POST['cid']; ?>)"><i class="fa-solid fa-arrow-right-arrow-left im"></i> Convert course</button>
    </div>
</div>
</div>
<?php
}
}
else {
    ?>

<div class="class_editor_pad">
<div class="mt5">
    <div class="at">
       <i class="fa-solid fa-record-vinyl"></i> Converted to Recorded course
    </div>
    <div class="mt5">
        This course is now on recorded package.
    </div>
</div>
</div>

    <?php
}
?>

<div class="class_editor_pad">
<div class="mt5">
    <i class="fa fa-exclamation-circle"></i> Classes are generated on date slots, so the free slots can be empty if class is not necessary that day. (Do not update the title for unnecessary class)
</div>
</div>

<div class="class_editor_pad">
<?php
$spin = 1;
for($l = 0; $l < count($dates); $l++){
    $row_uniq = uniqid();
    $date = $dates[$l];
    $actual_date = $date;
    $day = $calender->getDay($date);
    $get_class = $courses->getClass($conn, $_POST['cid'], $_SESSION['user_id'], $date);
    if(!!$get_class && null != $get_class){
        $class_title = $get_class[0]["title"];
        $class_id = $get_class[0]["id"];
        $video_link = $get_class[0]["video_link"];
        $video_file_name = $get_class[0]["file_name"];
        if($get_class[0]['delayed_date'] != NULL){
            $date = $get_class[0]['delayed_date'];
            $day = $calender->getDay($date);
        }
    if($day != "Friday" || $get_class[0]['delayed_date'] != NULL){
    //Class day
    ?>
    <div class="class_editor<?php if(isset($class_id) && $get_class[0]['is_taken'] != "No" && $get_class[0]['status'] == "Record available"){echo ' rt-col taken';};?>" id="e_<?php echo $row_uniq;?>">
        <div class="class_date">
        
        <?php
            //Edit date feature -- update is disabled for new, un-inserted class
            //Once inserted, you can update the date later
            if(isset($class_id) && $get_class[0]['is_taken'] == "No"){
                //Edit box
                $class_date = $date;
                if($get_class[0]['delayed_date'] != NULL){
                    $class_date = $get_class[0]['delayed_date'];
                }
                ?>
                <div class="qs_ops no-cg">
                <div>
                 <input type="text" id="c_dv_<?php echo $l;?>" value="<?php echo str_replace(" 00:00:00", "", $class_date);?>">
                 </div>
                 <div>
                     <input type="text" id="c_tv_<?php echo $l;?>" value="<?php echo $get_class[0]['start_time'];?>">
                 </div>
                 </div>
            <?php
            }
            else {
                echo ($spin).'. '.str_replace(" 00:00:00", "", $date).'('.$day.')';
            }
            if(isset($class_id) && $get_class[0]['is_taken'] != "No" && $get_class[0]['status'] == "Record available"){
                echo '<div class="ptr cflex" onclick="rec(this, \''.$row_uniq.'\')">Expand</div>';
            }
            else {
                echo '<div class="ptr cflex" onclick="rec(this, \''.$row_uniq.'\')">Collapse</div>';
            }
            ?>
    </div>
    <div>
        <input type="text"<?php if(isset($class_title)){echo 'value="'.$class_title.'" ';}?>placeholder="Input class title" id="c_<?php echo $l;?>">
    </div>
    <div>
        <div class="smet">
            Study materials
        </div>
        <div class="met_files">
            <?php
            if(isset($class_id)){
                ?>
            <section id="mtrs_<?php echo $class_id;?>">
            <?php
            //Get class study materials
            $class_pin = $security->encryptText($get_class[0]["id"]);
            $materials = $courses->getMaterials($conn, $class_pin);
            if(null == $materials){
                ?>
                <div class="mmes">No study materials provided yet!</div>
                <?php
            }
            else {
                //Loop the materials
                ?>
                    <?php
                    foreach($materials as $file){
                    $ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
                    $size = $functions->formatFilesize('../../../files/assets/'.$class_id.'/'.$file['file_name']);
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
                    ?>
                <?php
            }
            //Uploader panel
            ?>
            </section>
            <div class="smu">
                <div>
                <input type="file" id="file_<?php echo $class_id;?>" data-tf="<?php echo $class_id;?>" data-pin="<?php echo $class_pin;?>">
                </div>
                <div>
                <button class="b ce nom" onclick="met_upload('file_<?php echo $class_id;?>', this)"><i class="fa fa-arrow-up"></i> Upload</button>
                </div>
            </div>
            <?php
            }
            else {
                ?>
                <div class="mmes">Update class title then reload tab to upload!</div>
                <?php
            }
            ?>
    </div>
    </div>
    
    <?php 
    if(isset($video_link)){
        if($video_link == "None"){
            $video_link = '';
        }
    ?>
    <div class="smet">
        Video link (Google Drive)
    </div>
     <div>
        <input class="tm" type="text"<?php if(isset($video_link)){echo 'value="'.$video_link.'" ';}?>placeholder="Class video link" id="v_<?php echo $l;?>">
     </div>
    <?php
    }
    
    if(!isset($video_file_name) || NULL == $video_file_name){
        $video_file_name = '';
    }
    ?>
    
    <div class="smet">
        Video file name (Firebase storage)
    </div>
     <div>
        <input class="tm" type="text"<?php if(isset($video_file_name)){echo 'value="'.$video_file_name.'" ';}?>placeholder="Class video filename" id="fv_<?php echo $l;?>">
     </div>
     
     <div>
        <button class="b ce" onclick="update_class(<?php echo $_POST['cid'];?>, '<?php echo $actual_date;?>', 'c_<?php echo $l;?>', 'v_<?php echo $l;?>', this)">Update</button>
    </div>
    </div>
    
    <?php
    $spin++;
    }
    unset($class_title);
    unset($class_id);
    unset($video_link);
    unset($video_file_name);
    }
}
?>
</div>
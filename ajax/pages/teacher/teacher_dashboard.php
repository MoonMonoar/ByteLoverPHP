<?php
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
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. No a teacher!");
}
//Todays class
$current_date = date("Y-m-d");
$stmt = $conn->prepare("SELECT * FROM classes WHERE teacher_id = ? AND ((delayed_date IS NULL AND start_date = ?) OR delayed_date = ?)");
$stmt->bind_param("iss", $_SESSION['user_id'], $current_date, $current_date);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$l = 0;
if($result->num_rows == 0){
    //No class
    ?>
    <div class="no_class tm">
        You don't have any class to take today. Please go to Routine to check/make/update classes.
    </div>
    <?php
}
else {
    foreach($rows as $row){
    ?>
    <div class="class_editor_pad">
    <?php
            //Classes
            $class_title = $row['title'];
            $class_link = $row['class_link'];
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
                <i class="fa fa-book"></i> Course: <?php echo $row['title'];?>
            </div>
            <div>
            <input type="text" <?php if($class_link != "None"){echo 'value="'.$class_link.'"';}?> class="tm" placeholder="Class link" id="cl_<?php echo $l;?>">
            </div>
            <div class="cflex cl">
                <button class="ub cflex tb cbc bb" data-fi="<?php echo $l;?>" data-cid="<?php echo $security->encryptText($row['id']);?>" onclick="set_clink(this)">
                     <i class="fa-solid fa-podcast"></i> <span class="b">Invite Now</span>
                </button>
            </div>
            </div>
        <?php
        if(isset($l)){
        $l++;
        }
    }
    ?>
    </div>
    <?php
}
?>
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
$class_id = $security->decryptText($_POST['cid']);
if(null == $class_id){
    die("Unauthorized! Decryption error.");
}
//Get the results
$stmt = $conn->prepare("SELECT * FROM taken_quizes WHERE class_id = ? ORDER BY CAST(marks_obtained AS INT) DESC");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $position = 0;
    $last_top = 0;
    foreach($rows as $row){
    $user_profile = $profile->getProfile($conn, $row['user_id']);
    $class_data = $courses->getclassByid($conn, $row['class_id']);
    if($row['marks_obtained'] != $last_top){
        $position++;
    }
        ?>
        <div class="sub_marks">
            <div class="intro">
                <div>
                    <img src="/img/users/<?php echo $user_profile['image'];?>" height="50px" width="50px" alt="<?php echo $user_profile['fullname'];?>">
                </div>
                <div class="name cflex">
                    <?php echo $user_profile['fullname'];?>
                </div>
            </div>
            <div class="marks_got B">
                <?php echo $row['marks_obtained'];?> / <?php echo $row['marks_total'];?> &middot <?php echo $functions->ordinal($position);?>
            </div>
            <div class="class_info">
                <i class="fa fa-calendar-days mr5"></i><?php echo date('Y-m-d', strtotime($class_data[0]['start_date']));?> &middot <?php echo $class_data[0]['title'];?>
                <div>
                <i class="fa fa-clock mr5"></i>Taken <?php
                /*The server time zome is 4 hours backward
                As its a shared server I can't change it
                So, I can just add 4 hours to bangladshi time to fix it.
                */
                echo $functions->time_ago(strtotime($row['time'])+(4*60*60));?></div>
            </div>
        </div>
        <?php
        $last_top = $row['marks_obtained'];
    }
}
else {
    ?>
    <div class="fer mt10">
        No one took this quiz yet!
    </div>
    <?php
}
?>
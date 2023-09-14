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
$functions = new Functions();
$conn = $db_config->getConnection();
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$class_id = $_POST['cid'];
$stmt = $conn->prepare("SELECT * FROM study_material WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach($rows as $file){
        $ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
        $size = $functions->formatFilesize('../../../files/assets/'.$class_id.'/'.$file['file_name']);
        ?>
        <a href="/files/assets/<?php echo $class_id.'/'.$file['file_name'];?>" target="_blank">
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
 else {
    ?>
    <div class="mmes">No study materials provided yet!</div>
    <?php
 }
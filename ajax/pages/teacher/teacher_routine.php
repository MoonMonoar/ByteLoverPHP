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
    die("Unauthorized. Not a teacher!");
}
$courses = $teachers->getCourses($conn, $_SESSION['user_id']);
if($courses->num_rows == 0){
    //No class
    ?>
    <div class="no_class">
        You don't have a course. Please call us.
    </div>
    <?php
}
else {
    $rows = $courses->fetch_all(MYSQLI_ASSOC);
    ?>
    <div class="stex">
        Course routine
    </div>
    <div>
        <select id="tech_cour" onchange="select_course(this)">
            <option value="def">Please select a course</option>
    <?php
    foreach($rows as $row){
    ?>
    <option value="<?php echo $row['id'];?>"><?php echo '['.$row['status'],'] '.$row['name'];?></option>
    <?php
    }
    ?>
    </select>
    </div>
    <div id="course_routine"></div>
    <?php
}
?>
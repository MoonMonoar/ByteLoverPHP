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
$activeCourses = $courses->getActivecourses($conn, $_SESSION['user_id']);
if(null == $activeCourses){
?>
<div class="empty">
    <?php echo $strings["empty_routine"][$lang];?>
<div>
<?php
}
else {
    ?>
<div class="stex">
        Course routine
    </div>
    <div>
        <select id="tech_cour" onchange="select_course(this)">
            <option value="def">Please select a course</option>
    <?php
    foreach($activeCourses as $row){
    $c_row = $courses->courseByid($conn, $row['course_id']);
    ?>
    <option value="<?php echo $security->encryptText($c_row[0]['id']);?>"><?php echo '['.$c_row[0]['status'],'] '.$c_row[0]['name'];?></option>
    <?php
    }
    ?>
    </select>
    </div>
    <div id="course_routine"></div>
    <?php
}
?>
<?php
//$exam_questions = $courses->getExamquestions($conn, $_POST['cid'], $date);
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
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$uid = uniqid();
?>
<div class="class_editor_pad">
    <div class="qm">
        Each assignment can contain multiple milestones<br>
        <small>Marks, allowed time can vary from one to another also allows file formats must be given</small>
    </div>
</div>

<div class="class_editor_pad">
<div class="eq_add" id="qs_adder" style="padding:0 0 15px 0">
    <div class="qz_t">
     Create assignment
     </div>
     
     <div class="qs_ops">
     
     <div>
        <div class="qs_hint">Start date</div>
        <input type="text" id="es_<?php echo $uid;?>" placeholder="yyyy/mm/dd">
    </div>
    
    <div>
        <div class="qs_hint">End date</div>
        <input type="text" id="ee_<?php echo $uid;?>" placeholder="yyyy/mm/dd">
    </div>
    
    </div>
     
     <div>
        <div class="qs_hint">Marks</div>
        <input type="text" id="em_<?php echo $uid;?>" value="10" placeholder="eg. 10">
    </div>
     
        <div>
            <div class="qs_hint">Question statement</div>
            <textarea class="eqt" id="eq_<?php echo $uid;?>"></textarea>
        </div>
    <div>
        <button class="b ce" data-cid="<?php echo $security->encryptText($_POST['cid']);?>" data-d="<?php echo $security->encryptText($date);?>" data-ui="<?php echo $uid;?>" onclick="add_eq(this)">
            Create
        </button>
    </div>
</div>
</div>
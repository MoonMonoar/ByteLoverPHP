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
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$class_id = $security->decryptText($_POST['cid']);
$stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE class_id = ?");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
?>
<div id="qs_holder">
    <?php
    if($result->num_rows == 0) {
        ?>
    <div class="em">
        No quizes found for this class.
    </div>
    <?php
    }
else {
    $sl = 1;
    foreach($rows as $row){
    $dummy_id = uniqid();
?>
<div class="quiz_q">
    <div class="b">
        <?php echo $sl.'. '.nl2br($row['question']);?>
    </div>
    <div class="qs_ops qs2">
        <div> <input type="radio" id="<?php echo $dummy_id.'_'.$sl;?>" name="dummy_<?php echo $dummy_id;?>"> <label for="<?php echo $dummy_id.'_'.$sl;?>"><?php echo $row['option_1'];?></label> </div>
        <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+1);?>" name="dummy_<?php echo $dummy_id;?>"> <label for="<?php echo $dummy_id.'_'.($sl+1);?>"><?php echo $row['option_2'];?></label> </div>
        <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+2);?>" name="dummy_<?php echo $dummy_id;?>"> <label for="<?php echo $dummy_id.'_'.($sl+2);?>"><?php echo $row['option_3'];?></label> </div>
        <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+3);?>" name="dummy_<?php echo $dummy_id;?>"> <label for="<?php echo $dummy_id.'_'.($sl+3);?>"><?php echo $row['option_4'];?></label> </div>
    </div>

    <div class="ans_part">
    <div class="fs">
        <span class="b">Answer: </span> Option <?php echo $row['answer'];?>
    </div>
    <div class="fs">
        <div class="b">Explanation</div>
        <div class="qex">
            <?php echo nl2br($row['explanation']);?>
        </div>
    </div>
    </div>
    
</div>
<?php 
    $sl++;
    }
}
?>
</div>
<div id="qs_adder">
    <div class="qz_t">
        Add a question
    </div>
    <div>
        <div class="qs_hint">Question</div>
    <textarea id="qz_q" class="eqt" placeholder="eg. Which one is an OS?"></textarea>
    </div>
    
    <div>
        <button class="q_date" onclick="ins_c()">Insert C</button>
    </div>

    <div>
        <div class="qs_hint">Marks</div>
    <input type="text" id="qz_m" value="1" placeholder="eg. 1">
    </div>

    <div class="qs_ops">
    <div>
        <div class="qs_hint">Option 1</div>
    <input type="text" id="qz_o1" placeholder="eg. Doors">
    </div>
    
    <div>
        <div class="qs_hint">Option 2</div>
    <input type="text" id="qz_o2" placeholder="eg. Windows">
    </div>
        
    <div>
        <div class="qs_hint">Option 3</div>
    <input type="text" id="qz_o3" placeholder="eg. Penguens">
    </div>
        
    <div>
        <div class="qs_hint">Option 4</div>
    <input type="text" id="qz_o4" placeholder="eg. RoboDroid">
    </div>
    </div>

    <div>
        <div class="qs_hint">Answer option id(1/2/3/4)</div>
    <input type="text" id="qz_ans" placeholder="eg. 2">
    </div>
        
    <div>
        <div class="qs_hint">Answer explanation</div>
    <textarea id="qz_exp" placeholder="eg. Windows is an operating system made by Microsoft."></textarea>
    </div>

    <div>
        <button class="b ce" data-cid="<?php echo $_POST['cid'];?>" onclick="add_qq(this)">
            Add question
        </button>
</div>
</div>
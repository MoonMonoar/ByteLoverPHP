<?php
if(!isset($_POST['class_id'])){
    die('Auth error!');
}
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die('Auth error!');
}
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$class_id = $security->decryptText($_POST['class_id']);
if(null == $class_id){
    die('Auth error!');    
}
$conn = $db_config->getConnection();
$db = $db_config->getDB();
//Register the timer
$stmt = $conn->prepare("SELECT id FROM quiz_timer WHERE user = ? AND class_id = ?");
$stmt->bind_param("ss", $_SESSION['user_id'], $class_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0) {
    //Insert
    $db->insert("quiz_timer", [
        "user" => $_SESSION['user_id'],
        "class_id" => $class_id,
        "max_seconds" => (15*60) //15 minutes
    ]);
}
//The exam
?>
<form method="POST" class="qf2" action="/ajax/pages/quiz_submit.php">
    <?php
    //Loop the questions
    $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $sl = 1;
    $id_list = '[';
    $f = 1;
    foreach($rows as $row){
    if(count($rows) > $f){
        $id_list.= $row['id'].', ';
    }
    else {
        $id_list.= $row['id'].']';
    }
    $f++;
    $dummy_id = uniqid();
    $options = 1;
    ?>
        <div class="quiz_q">
        <div class="b">
            <?php echo $sl.'. '.nl2br($row['question']);?>
        </div>
        <div class="qs_ops qs2">
            <div> <input type="radio" id="<?php echo $dummy_id.'_'.$sl;?>" name="<?php echo 'q_'.$row['id'].'_'.$options++;?>"> <label for="<?php echo $dummy_id.'_'.$sl;?>"><?php echo $row['option_1'];?></label> </div>
            <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+1);?>" name="<?php echo 'q_'.$row['id'].'_'.$options++;?>"> <label for="<?php echo $dummy_id.'_'.($sl+1);?>"><?php echo $row['option_2'];?></label> </div>
            <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+2);?>" name="<?php echo 'q_'.$row['id'].'_'.$options++;?>"> <label for="<?php echo $dummy_id.'_'.($sl+2);?>"><?php echo $row['option_3'];?></label> </div>
            <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+3);?>" name="<?php echo 'q_'.$row['id'].'_'.$options++;?>"> <label for="<?php echo $dummy_id.'_'.($sl+3);?>"><?php echo $row['option_4'];?></label> </div>
        </div>
    </div>
    <?php
    $sl++;
    }
    ?>
    <input type="hidden" name="cid" value="<?php echo $class_id;?>">
    <input type="hidden" name="list" value="<?php echo $id_list;?>">
    <div class="cflex mt30">
     <input type="submit" id="submit_final" name="submit" value="Submit" class="ub cflex tb cbc bb">
    </div>
</form>
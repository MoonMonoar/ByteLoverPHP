<?php
if(!isset($_POST['class_id'])){
    die('00:00');
}
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die('00:00');
}
$security = new Security();
$class_id = $security->decryptText($_POST['class_id']);
if(null == $class_id){
    die('00:00');    
}
$db_config = new DBconfig();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
//Just echo the timestamp of stating point
$stmt = $conn->prepare("SELECT * FROM quiz_timer WHERE user = ? AND class_id = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $class_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $row = $result->fetch_all(MYSQLI_ASSOC)[0];
    $took = $row['seconds_took'];
    $max = $row['max_seconds'];
    $took++;
    if($took <= $max){
        //Update it
        $db->update("quiz_timer", [
                "seconds_took" => $took
            ], [
                "id" => $row['id']
        ]);
        //Left time
        die(gmdate('i:s', $max-$took));
    }
    else {
        //Ends
        die('00:00');
    }
}
else {
    die('00:00');
}
?>
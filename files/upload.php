<?php
if(!isset($_POST['pin']) || !isset($_FILES['file']) || !isset($_POST['fn'])){
    die('ERROR');
}
require_once '../php/global.php';
require_once '../php/dbconfig.php';
require_once '../php/autologin.php';
$profile = new Profile();
$dbcon = new DBconfig();
$conn = $dbcon->getConnection();
$db = $dbcon->getDB();
if(!isset($_SESSION['user_id'])){
    die('AUTH');
}
if(!$profile->isTeacher($conn, $_SESSION['user_id'])){
    die("Unauthorized. Not a teacher!");
}
$file = $_FILES['file'];
if(null == $file || count($file) == 0){
    die('ERROR'); 
}
$target_folder = 'assets/'.$_POST['fn'];
if(!is_dir($target_folder)){
    mkdir($target_folder);
}
$target = $target_folder.'/'.$file['name'];
if(file_exists($target)){
    $skip_update = true;
}
move_uploaded_file($file['tmp_name'], $target);
if(!isset($skip_update)){
$db->insert("study_material", [
    "class_id" => $_POST['fn'],
    "file_name" => $file['name']
]);
if(null != $db->id()){
    echo 'DONE';
}
}
else {
    echo 'DONE';
}
?>
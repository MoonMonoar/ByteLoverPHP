<?php
if(!isset($_FILES['file'])){
    die('ERROR');
}
require_once '../../php/global.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
$dbcon = new DBconfig();
$conn = $dbcon->getConnection();
$db = $dbcon->getDB();
$profile = new Profile();
if(!isset($_SESSION['user_id'])){
    die('AUTH');
}
$file = $_FILES['file'];
if(null == $file || count($file) == 0){
    die('ERROR'); 
}
$file_name = uniqid().'-'.md5(uniqid()).'.jpg';
move_uploaded_file($file['tmp_name'], $file_name);
//Delete the old file
$old = str_replace("/img/users/", "", $profile->getImage($conn, $_SESSION['user_id']));
if($old != "user.png"){
    if(file_exists($old)){
        unlink($old);
    }
}
$db->update("users", [
    "image" => $file_name
    ], [
    "id" => $_SESSION['user_id']    
]);
echo 'DONE';
?>
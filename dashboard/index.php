<?php
require_once '../php/global.php';
require_once '../php/dbconfig.php';
require_once '../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    header("Location: /login/?error=0002&from=dashboard");
}
$profile = new Profile();
$conn = DBconfig::getConnection();
//Email check
if(!$profile->emailVerified($conn, $_SESSION['user_id'])){
    header("Location: /verify/?ref=dashboard");
    exit();
}
//Dashboard
if($profile->isStudent($conn, $_SESSION['user_id'])){
    header("Location: /dashboard/student/?ref=auto");
}
else if($profile->isTeacher($conn, $_SESSION['user_id'])){
    header("Location: /dashboard/teacher/?ref=auto"); 
}
else {
    echo 'System can not detect user type!';
}
<?php
require_once '../php/global.php';

//Objects
$security = new Security();
$links = new Links();
$users = new Users();
//CHECKUP

//Satisfaction check
if(!isset($_POST['signup'])){
    header(header("Location: /signup/?error=0000"));
    die();
}
if(!isset($_POST['fullname']) 
|| !isset($_POST['birth']) 
|| !isset($_POST['phone']) 
|| !isset($_POST['email']) 
|| !isset($_POST['password']) 
|| !isset($_POST['password_again'])){
    header($links->regiError('0001', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}

//Type check
if(strlen($_POST['password']) < 8 || strlen($_POST['password']) > 200){
    header($links->regiError('0006', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}
else if(strcmp($_POST['password'], $_POST['password_again']) != 0){
    header($links->regiError('0007', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}
else if(!$security->strongPassword($_POST['password'])){
    header($links->regiError('0008', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}
else if(!preg_match("/^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z.]*)*$/", $_POST['fullname'])){
    header($links->regiError('0002', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}
else if(strtotime($_POST['birth']) === false){
    header($links->regiError('0003', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}
else if(!preg_match("/^(\+88)?01[0-9]{9}$/", $_POST['phone'])){
    header($links->regiError('0004', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}
else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    header($links->regiError('0005', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}

//Extra info check
if(!empty($_POST['institute'])){
    if(strlen($_POST['institute']) > 200 || !preg_match("/^[a-zA-Z0-9\s\-\.,]+$/", $_POST['institute'])){
        header($links->regiError('0009', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
        die();
    }
}
if(!empty($_POST['degree'])){
    if(strlen($_POST['degree']) > 200 || !preg_match("/^[a-zA-Z0-9\s\-\.,]+$/", $_POST['degree'])){
        header($links->regiError('0010', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
        die();
    }
}
if(!empty($_POST['profession'])){
    if(strlen($_POST['profession']) > 200 || !preg_match("/^[a-zA-Z0-9\s\-\.,]+$/", $_POST['profession'])){
        header($links->regiError('0011', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
        die();
    }
}

//Database
require '../php/dbconfig.php';

$db_config = new DBconfig();

//Already occupied
if($users->phoneOccupied($db_config->getConnection(), $_POST['phone'])){
    header($links->regiError('0013', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}

if($users->emailOccupied($db_config->getConnection(), $_POST['email'])){
    header($links->regiError('0014', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}

$db = $db_config->getDb();
$password_hash = $security->passwordHash($_POST['password']);

$user_id = $db->insert("users", [
    "fullname" => $_POST["fullname"],
    "birthday" => $_POST['birth'],
    "phone" => $_POST['phone'],
    "email" => $_POST['email'],
    "password" => $password_hash,
    "profession" => $_POST['profession'],
    "degree" => $_POST['degree'],
    "institute" => $_POST['institute']
])->id();

if(null == $user_id){
    header($links->regiError('0012', $_POST['fullname'], $_POST['phone'], $_POST['email'], $_POST['institute'], $_POST['degree'], $_POST['profession'], $_POST['birth'], $_POST['password'], $_POST['password_again'], $_POST['showpass']));
    die();
}

//Login
if(!isset($_SESSION)){
    session_start();
}
$_SESSION['user_id'] = $user_id;
$_SESSION['login_time'] = time();
setcookie("ui", Security::EncryptText($user_id), time() + (86400 * 365), "/");
setcookie("uk", Security::EncryptText($_POST['password']), time() + (86400 * 365), "/");

//Go to student dashboard
header("Location: /dashboard/student/?ref=signup");
?>
<?php
if(!isset($_POST['login'])){
    header('Location: /login/?error=max');
    die();
}
else if(!isset($_POST['user']) || !isset($_POST['password'])){
    header('Location: /login/?error=0003');
    die();
}

//Ready
session_start();
require_once '../php/global.php';
require '../php/dbconfig.php';
$security = new Security();

$user = $_POST['user'];
$password = $_POST['password'];
$show_pass = "Off";
if(isset($_POST['showpass']) && !empty($_POST['showpass'])){
    $show_pass = $_POST['showpass'];
}
$conn = DBconfig::getConnection();
if($conn->connect_error) {
    header("Location: /login/?user=".urlencode($user)."&error=0001&pass=".urlencode($security->encryptText($security->encryptText($password)))."&sp=".urlencode($show_pass));
}
$phone_2 = '+88'.$user;
$phone_3 = str_replace('+88', '', $user);
$stmt = $conn->prepare("SELECT id, password FROM users WHERE phone = ? or phone = ? or phone = ? or email = ?");
$stmt->bind_param("ssss", $user, $phone_2, $phone_3, $user);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as $row){
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['login_time'] = time();
            setcookie("ui", $security->EncryptText($row['id']), time() + (86400 * 365), "/");
            setcookie("uk", $security->EncryptText($password), time() + (86400 * 365), "/");
            setcookie('PushToken', null, time() - 3600, "/"); //To triger JS resave
            header("Location: /dashboard/?ref=".uniqid());
            break;
        }
    }
    header("Location: /login/?user=".urlencode($user)."&error=0001&pass=".urlencode($security->encryptText($security->encryptText($password)))."&sp=".urlencode($show_pass));
} else {
    header("Location: /login/?user=".urlencode($user)."&error=0001&pass=".urlencode($security->encryptText($security->encryptText($password)))."&sp=".urlencode($show_pass));
}
$stmt->close();
$conn->close();
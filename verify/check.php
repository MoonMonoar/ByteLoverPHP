<?php
if(!isset($_POST['verify']) || !isset($_POST['pin'])){
    header('Location: /verify/?error=0000');
    exit();
}
if(!is_numeric($_POST['pin']) || strlen($_POST['pin']) > 6){
    header('Location: /verify/?error=0002');
    exit();
}
require_once '../php/global.php';
require_once '../php/dbconfig.php';
require_once '../php/autologin.php';
if(!isset($_SESSION['user_id'])) header("Location: /login/?error=0002&from=verifier");
if($_POST['pin'] != $_SESSION['verification_code']){
    header('Location: /verify/?error=0001');
    exit();
}
$db_config = new DBconfig();
$db = $db_config->getDB();
$db->update("users", [
        "email_verified" => "Yes"
    ], [
        "id" => $_SESSION['user_id']
]);
setcookie('vms', null, time() - 3600, "/");
setcookie('vmp', null, time() - 3600, "/");
header("Location: /dashboard/?ref=verifier");
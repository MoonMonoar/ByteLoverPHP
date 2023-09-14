<?php
session_start();
session_destroy();
setcookie('ui', null, time() - 3600, "/");
setcookie('uk', null, time() - 3600, "/");
setcookie('vms', null, time() - 3600, "/");
setcookie('vmp', null, time() - 3600, "/");
require_once '../php/global.php';
header("Location: /");
?>
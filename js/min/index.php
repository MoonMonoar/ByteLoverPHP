<?php
ob_start();
header("Content-Type: application/javascript");
require_once '../../php/global.php';
if(!isset($_GET['file'])){
    echo 'console.info("Requested a blank JavaScript file!")';
    die();
}
if(file_exists('../'.$_GET['file'])){
    require_once '../'.$_GET['file'];
    $js = ob_get_clean();
    echo Minify::js($js);
    die();
}
echo 'console.warn("Requested JavaScript file not found('.$_GET['file'].').")';
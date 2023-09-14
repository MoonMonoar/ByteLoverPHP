<?php
ob_start();
session_start();
require_once '../../php/global.php';
require_once '../../php/dbconfig.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/autologin.php';
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$db = $dbconfig->getDB();
global $lang ,$strings, $global_key_theme,
       $script_version, $alter_lang_link,
       $alter_lang_name;
require_once 'CardMaker.php';
//Offer random contents
//Select articles from solo learn
$contents = array();
$solos = $db->select("solo_learn", "", 20, "rand()")->result();
foreach ($solos as $article){
    $contents[] = CardMaker::makeSoloLearnCard($article);
}
foreach ($contents as $item){
    echo $item;
}
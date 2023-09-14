<?php
$strings = strings();
$lang = "en";
if(isset($_COOKIE['lang']) || isset($_GET['lang'])){
    if(isset($_COOKIE['lang'])){
        $new_lang = $_COOKIE['lang'];
        if($new_lang == "en" || $new_lang == "bn"){
            $lang = $new_lang;
        }
    }
    if(isset($_GET['lang'])){
        $new_lang = $_GET['lang'];
        if($new_lang == "en" || $new_lang == "bn"){
            $lang = $new_lang;
        }
    }
}
if($lang == "bn"){
    $alter_lang_name = "English";
    $alter_lang_link = str_replace("lang=".$lang, "", Links::getLink($_SERVER['REQUEST_URI'], "lang=en"));
}
else if($lang == "en"){
    $alter_lang_name = "বাংলা";
    $alter_lang_link = str_replace("lang=".$lang, "", Links::getLink($_SERVER['REQUEST_URI'], "lang=bn"));
}
?>
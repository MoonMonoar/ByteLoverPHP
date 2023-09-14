<?php
session_start();
require_once '../../php/global.php';
require_once '../../php/dbconfig.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/autologin.php';
global $lang ,$strings, $global_key_theme,
       $script_version, $alter_lang_link,
       $alter_lang_name;
$templates = new Templates();
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$db = $dbconfig->getDb();
$courses = new Courses();
?><!DOCTYPE html>
<html lang="<?php echo $lang;?>">
<head>
    <title class="notranslate">
        <?php echo $strings["c_lang"][$lang].' - '.$strings["page_title"][$lang];?>
    </title>
    <?php
    echo $templates->headMeta();
    if($global_key_theme == "dark"){
        echo '<link rel="stylesheet" href="/css/app-dark.css?v='.$script_version.'">
              <meta name="theme-color" content="#212121">
              <meta name="msapplication-TileColor" content="#212121">';
    }
    else {
        echo '<link rel="stylesheet" href="/css/app-light.css?v='.$script_version.'">
              <meta name="theme-color" content="#ffffff">
              <meta name="msapplication-TileColor" content="#ffffff">';
    }
    ?>
    <link rel="stylesheet" href="/css/app.css?v=<?php echo $script_version;?>">
    <link rel="stylesheet" href="/css/icons/fontawesome-6.3.0/css/all.min.css">
</head>
<body class="tutorial_mode">
<header class="notranslate">
    <div class="pad header_divs">
        <div>
            <a href="/" title="<?php echo $strings["prompt_home"][$lang];?>">
                <img id="desk_image" src="/img/logos/ByteLoverBanner.svg" alt="Banner">
                <img id="mobi_image" src="/img/logos/ByteLoverLogo.svg" alt="Logo">
            </a>
        </div>
        <div class="header_links lflex">
            <div class="top_links">
                <a href="<?php echo $alter_lang_link;?>"><?php echo $alter_lang_name;?></a>
                <a href="/courses/?ref=home_header"><?php echo $strings["courses"][$lang];?></a>
                <a href="/dashboard/?push=<?php echo uniqid();?>"><?php echo $strings["dashboard"][$lang];?></a>
            </div>
            <div class="top_menu">
                <i class="fa fa-list mb"></i>
            </div>
        </div>
        <div class="header_opts rflex">
            <div class="ml12">
                <a href="<?php echo $links->themeLink();?>">
                    <button class="ub cflex nob rob">
                        <?php
                        if($global_key_theme == "dark"){
                            echo '<i class="fa-solid fa-sun"></i>';
                        }
                        else {
                            echo '<i class="fa-solid fa-moon"></i>';
                        }
                        ?>
                    </button>
                </a>
            </div>
            <div class="ml12">
                <a href="tel:+8801317215403">
                    <button class="ub cflex" title="<?php echo $strings["prompt_support"][$lang];?>">
                        <span><?php echo $strings["call_now"][$lang];?></span><i class="fa fa-phone"></i>
                    </button>
                </a>
            </div>
            <div class="ml12">
                <a href="/signup/?ref=header">
                    <button class="ub cflex" title="<?php echo $strings["prompt_signup"][$lang];?>">
                        <span><?php echo $strings["joinus"][$lang];?></span><i class="fa fa-user-plus"></i>
                    </button>
                </a>
            </div>
            <div>
                <a href="/login/?ref=header">
                    <button class="ub cflex" title="<?php echo $strings["prompt_login"][$lang];?>">
                            <span><?php
                                if(!isset($_SESSION['user_id'])){
                                    echo $strings["login"][$lang];
                                    echo '</span> <i class="fa fa-sign-in"></i>';
                                }
                                else {
                                    $profile = new Profile();
                                    echo $profile->getUsername($dbconfig->getConnection(), $_SESSION['user_id']);
                                    echo '</span>';
                                    ?>
                                    <img class="h_ui" src="<?php echo $profile->getImage($dbconfig->getConnection(), $_SESSION['user_id']);?>">
                                    <?php
                                }
                                ?>
                    </button>
                </a>
            </div>
        </div>
    </div>
</header>
<section id="main_body" class="tutorial">
    <div>
        <div class="t_l_holder">
            <div class="odesk mc notranslate">
                <a href="/solo/?ref=chapter_menu">C Solo Learn</a>
            </div>
            <div class="omob mc notranslate">
                <i class="fa fa-arrow-left mr10all"></i>
                <a href="/solo/?ref=chapter_menu"> C Solo Learn</a>
            </div>
            <ul class="tutorial_links notranslate">
                <li id="c-intro"><a href="/solo/c/?ref=chapter_menu">C Intro</a></li>
                <li id="c-get-started"><a href="/solo/c/c-get-started/?ref=chapter_menu">C Get Started</a></li>
                <li id="c-syntax"><a href="/solo/c/c-syntax/?ref=chapter_menu">C Syntax</a></li>
                <li id="c-output"><a href="/solo/c/c-output/?ref=chapter_menu">C Output</a></li>
                <li id="c-comments"><a href="/solo/c/c-comments/?ref=chapter_menu">C Comments</a></li>
                <li id="c-variables"><a href="/solo/c/c-variables/?ref=chapter_menu">C Variables</a></li>
                <li id="c-data-types"><a href="/solo/c/c-data-types/?ref=chapter_menu">C Data Types</a></li>
                <li id="c-constants"><a href="/solo/c/c-constants/?ref=chapter_menu">C Constants</a></li>
                <li id="c-operators"><a href="/solo/c/c-operators/?ref=chapter_menu">C Operators</a></li>
                <li id="c-booleans"><a href="/solo/c/c-booleans/?ref=chapter_menu">C Booleans</a></li>
                <li id="c-if-else"><a href="/solo/c/c-if-else/?ref=chapter_menu">C If...Else</a></li>
                <li id="c-switch"><a href="/solo/c/c-switch/?ref=chapter_menu">C Switch</a></li>
                <li id="c-while-loop"><a href="/solo/c/c-while-loop/?ref=chapter_menu">C While Loop</a></li>
                <li id="c-for-loop"><a href="/solo/c/c-for-loop/?ref=chapter_menu">C For Loop</a></li>
                <li id="c-break-continue"><a href="/solo/c/c-break-continue/?ref=chapter_menu">C Break/Continue</a></li>
                <li id="c-arrays"><a href="/solo/c/c-arrays/?ref=chapter_menu">C Arrays</a></li>
                <li id="c-strings"><a href="/solo/c/c-strings/?ref=chapter_menu">C Strings</a></li>
                <li id="c-user-input"><a href="/solo/c/c-user-input/?ref=chapter_menu">C User Input</a></li>
                <li id="c-memory-address"><a href="/solo/c/c-memory-address/?ref=chapter_menu">C Memory Address</a></li>
                <li id="c-pointers"><a href="/solo/c/c-pointers/?ref=chapter_menu">C Pointers</a></li>
                <li id="c-functions"><a href="/solo/c/c-functions/?ref=chapter_menu">C Functions</a></li>
                <li id="c-function-parameters"><a href="/solo/c/c-function-parameters/?ref=chapter_menu">C Function Parameters</a></li>
                <li id="c-function-declaration"><a href="/solo/c/c-function-declaration/?ref=chapter_menu">C Function Declaration</a></li>
                <li id="c-recursion"><a href="/solo/c/c-recursion/?ref=chapter_menu">C Recursion</a></li>
                <li id="c-math-functions"><a href="/solo/c/c-math-functions/?ref=chapter_menu">C Math Functions</a></li>
            </ul>
        </div>
    </div>
    <div class="tutorial_body">
        <div id="last" class="last-read-notice"></div>
        <div class="omob tmm">
                <i class="fa fa-bars"></i><span class="ml10all">Chapters</span>
        </div>

        <div id="gte"></div>

        <?php
           $key = "c-intro";
           if(isset($_GET['article'])){
               $key = $_GET['article'];
               $key = str_replace("/", "", $key);
           }
           $main_row = null;
           $main_selection = $db->select("solo_learn", [
               "article_id" => $key
           ])->result_array();
           if($main_selection != null){
               $main_row = $main_selection[0];
           }
           if(null != $main_row){
               //Okay
               ?>
               <script id="t2">
                   let o = document.getElementById("<?php echo $key;?>");
                   o.setAttribute("class", "tutorial_now");
                   o.scrollIntoView({ behavior: 'smooth' });
                   window.CURRENT_ARTICLE = "<?php echo $key;?>";
                   window.CURRENT_TUT = "C";
                   document.getElementById("t2").remove();
               </script>
               <?php
               echo $main_row['body'];
           }
           else {
               //Not found!
               echo $templates->tutorialAtricleNotFound();
           }
        ?>
    </div>
</section>
<div id="audio"></div>
<?php echo $templates->footerHtml();?>
<script src="/js/jquery.min.js?v=1"></script>
<script src="/js/min/app.js?v=<?php echo $script_version;?>"></script>
<script src="/js/min/index.js?v=<?php echo $script_version;?>"></script>
<script src="/js/min/tutorial.js?v=<?php echo $script_version;?>"></script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script id="ts1">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en'}, 'gte');
    }
    $("#ts1").remove();
</script>
</body>
</html>
<?php
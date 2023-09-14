<?php
ob_start();
session_start();
require_once '../php/global.php';
require_once '../php/dbconfig.php';
require_once '../php/strings.php';
require_once '../php/langset.php';
require_once '../php/templates.php';
require_once '../php/autologin.php';
global $lang ,$strings, $global_key_theme,
       $script_version, $alter_lang_link,
       $alter_lang_name;
$templates = new Templates();
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$courses = new Courses();
?><!DOCTYPE html>
<html lang="<?php echo $lang;?>">
<head>
    <title>
        <?php echo $strings["page_title"][$lang];?>
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
<body>
<header>
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
<section id="main_body">

    <div id="hero_line">
        <?php echo $strings["solo_learn_hero_line"][$lang];?>
        <div id="side_kick">
            <?php echo $strings["solo_learn_side_kick"][$lang];?>
        </div>
    </div>

    <div class="w_card_holder solo_home">

        <a href="c/?ref=solo_menu">
            <div class="card_welcome">

                <div class="t-info-g">
                    <div>
                        <div class="title b">
                            <?php echo $strings["learn_c"][$lang];?>
                            <sup>
                    <span class="badge badge-danger">
                            <?php echo $strings["live"][$lang];?>
                    </span>
                            </sup>
                        </div>
                        <div class="title sub">
                            <?php echo $strings["c_lang"][$lang];?>
                        </div>
                    </div>
                    <div class="t-sub-g">
                        <div class="title sub">
                            <small>
                                25 <?php echo $strings["chapters"][$lang];?>
                            </small>
                        </div>
                        <div class="title sub">
                            <small>
                                131 <?php echo $strings["languages"][$lang];?>
                            </small>
                        </div>
                    </div>
                </div>

                <img class="auto_img" src="imgs/c_banner.png" alt="C banner"/>
            </div>
        </a>

        <div class="card_welcome">
            <div class="title b">
                <?php echo $strings["learn_html"][$lang];?>
                <sup>
                    <span class="badge badge-info">
                            <?php echo $strings["coming_soon"][$lang];?>
                    </span>
                </sup>
            </div>
            <div class="title sub">
                <?php echo $strings["html_lang"][$lang];?>
            </div>
            <img class="auto_img" src="imgs/html_banner.jpg" alt="HTML banner"/>
        </div>

    </div>


</section>
<?php echo $templates->footerHtml();?>
<script src="/js/jquery.min.js?v=1"></script>
<script src="/js/min/app.js?v=<?php echo $script_version;?>"></script>
<script src="/js/min/index.js?v=<?php echo $script_version;?>"></script>
</body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
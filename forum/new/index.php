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
    ?>
    <link rel="stylesheet" href="/css/icons/fontawesome-6.3.0/css/all.min.css">
    <link rel="stylesheet" href="/plugins/izi/css/iziToast.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <?php
    if($global_key_theme == "dark"){
        ?>
        <link rel="stylesheet" href="/css/app-dark.css?v=<?php echo $script_version?>">
        <meta name="theme-color" content="#212121">
        <meta name="msapplication-TileColor" content="#212121">
        <?php
    }
    else {
        ?>
        <link rel="stylesheet" href="/css/app-light.css?v=<?php echo $script_version;?>">
        <meta name="theme-color" content="#ffffff">
        <meta name="msapplication-TileColor" content="#ffffff">
        <?php
    }
    ?>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/app.css?v=<?php echo $script_version;?>">
    <link rel="stylesheet" href="/css/icons/fontawesome-6.3.0/css/all.min.css">
</head>
<body class="forum-base">
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
<section id="main_body" class="dash filler">

    <div class="menu">
        <ul>
            <li>
                <div class="card active">
                    <div class="cflex">
                        <i class="fa fa-house"></i>
                    </div>
                    <div class="t def"> Home </div>
                </div>
            </li>
            <li>
                <div class="card">
                    <div class="cflex">
                        <i class="fa fa-bug"></i>
                    </div>
                    <div class="t def"> Problems </div>
                </div>
            </li>
            <li>
                <div class="card">
                    <div class="cflex">
                        <i class="fa-solid fa-handshake-angle"></i>
                    </div>
                    <div class="t def"> Contributions </div>
                </div>
            </li>
            <li>
                <div class="card">
                    <div class="cflex">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="t def"> Profile </div>
                </div>
            </li>
            <li>
                <div class="card">
                    <div class="cflex">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="t def"> Community </div>
                </div>
            </li>
        </ul>
        <div class="lmob">
            <a href="/dashboard/teacher/?ref=auto&amp;lang=bn">বাংলা</a>
            <a class="ml10c" href="/courses/?ref=home_header">Courses</a>
        </div>
        <div class="lmob mt5">Moon Monoar</div>
    </div>
    <div class="nav-scroller bg-body shadow-sm omob">
        <ul class="nav">
            <li class="nav-link active"><i class="fa fa-house"></i> Home</li>
            <li class="nav-link" href="#">
                <i class="fa fa-user"></i> Problems
                <span class="badge text-bg-light rounded-pill align-text-bottom">27</span>
            </li>
            <li class="nav-link"><i class="fa-solid fa-handshake-angle"></i> Contributions</li>
            <li class="nav-link"><i class="fa fa-user"></i> Profile</li>
            <li class="nav-link"><i class="fa-solid fa-user-group"></i> Community</li>
        </ul>
    </div>

    <div id="forum-root">

    </div>

</section>
<?php echo $templates->footerHtml();?>
<script src="/js/jquery.min.js?v=1"></script>
<script src="//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<script src="/plugins/izi/js/iziToast.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
<script src="/js/min/index.js?v=<?php echo $script_version;?>"></script>
<script src="/js/min/forum.js?v=<?php echo $script_version;?>"></script>
<script src="/js/min/app.js?v=<?php echo $script_version;?>"></script>
</body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
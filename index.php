<?php
ob_start();
session_start();
require_once 'php/global.php';
require_once 'php/dbconfig.php';
require_once 'php/strings.php';
require_once 'php/langset.php';
require_once 'php/templates.php';
require_once 'php/autologin.php';
global $lang ,$strings, $global_key_theme,
       $script_version, $alter_lang_link,
       $alter_lang_name;
$templates = new Templates();
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$db = $dbconfig->getDB();
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
        <link rel="stylesheet" href="/plugins/izi/css/iziToast.min.css">
    </head>
    <body>
        <header>
            <div class="pad header_divs">
                <div>
                    <a href="/" title="<?php echo $strings["prompt_home"][$lang];?>">
                    <img id="desk_image" src="/img/logos/ByteLoverBanner.svg">
                    <img id="mobi_image" src="/img/logos/ByteLoverLogo.svg">
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
            <a class="omob" href="/dashboard/student/?push=<?php echo uniqid();?>">
            <div class="tod cflex b">
                If you are a student, click here to go to the Student Dashboard.
            </div>
            </a>
            <div id="hero_line">
                <?php echo $strings["hero_line"][$lang];?>
                <div id="side_kick">
                    <?php echo $strings["side_kick"][$lang];?>
                </div>
            </div>

            <div id="book_class" class="cflex">

                <a href="/courses/?ref=home_button">
                <button class="b">
                    <?php echo $strings["book_course"][$lang];?>
                </button>
                </a>

                <a href="/forum/?ref=home_button">
                    <button class="b">
                        <?php echo $strings["code_forum"][$lang];?>
                        <sup>
                            <span class="badge badge-success">
                                <?php echo $strings["free"][$lang];?>
                            </span>
                        </sup>
                    </button>
                </a>

                <a href="/solo/?ref=home_button">
                    <button class="b">
                        <?php echo $strings["learn_solo"][$lang];?>
                        <sup>
                            <span class="badge badge-success">
                                <?php echo $strings["free"][$lang];?>
                            </span>
                        </sup>
                    </button>
                </a>

            </div>

            <div class="w_card_holder">
            <div class="card_welcome">
                <div class="title b"><?php echo $strings["free_course"][$lang];?></div>
                <div class="title sub">
                    <?php echo $strings["free_course_info"][$lang];?>
                </div>
                <div class="yt-body">
                    <iframe class="yt-pl" src="https://www.youtube.com/embed/videoseries?list=PLNUoztsuEzh70G7bkOz2IhRNYXoxJPtnU" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                </div>
            </div>

            <div class="card_welcome">
                <div class="title b"><?php echo $strings["moon_title"][$lang];?></div>
                <div class="title sub">
                    <?php echo $strings["moon_sub"][$lang];?>
                </div>
                <div class="body">
                    <div class="au">
                        <div>
                            <img src="/img/Moon.jpg" alt="Moon">
                        </div>
                        <div class="des">
                            <ul>
                                <li>
                                    <?php echo $strings["moon_bsc"][$lang];?>
                                </li>
                                <li>
                                    <?php echo $strings["moon_js"][$lang];?>
                                </li>
                                <li>
                                    <?php echo $strings["moon_php"][$lang];?>
                                </li>
                                <li>
                                    <?php echo $strings["moon_java"][$lang];?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="lset">
                    <div>
                        <a href="tel:+8801317215403">
                                <button class="ub cflex tb cbc">
                                    <i class="fa fa-phone"></i> <span><?php echo $strings["call_now"][$lang];?></span>
                                </button>
                        </a>
                    </div>
                    <div>
                        <a target="_blank" href="https://www.facebook.com/immo2n/">
                                <button class="ub cflex tb cbc">
                                    <i class="fa fa-brands fa-facebook"></i> <span><?php echo $strings["facebook"][$lang];?></span>
                                </button>
                        </a>
                    </div>
                    <div>
                        <a target="_blank" href="https://moonmonoar.github.io/portfolio/">
                                <button class="ub cflex tb cbc">
                                    <i class="fa fa-globe"></i> <span><?php echo $strings["website"][$lang];?></span>
                                </button>
                        </a>
                    </div>
                </div>
                </div>
            </div>
            </div>

            <div class="w_card_holder" id="feed">
                <div class="load-icon">
                   <i class="fa fa-circle-notch fa-spin"></i> Loading...
                </div>
            </div>

            <div class="horizontalScrollbar forceBlock">
                <div class="img_cards">

                    <div class="card_image">
                        <img src="/img/illustrations/LiveClass.jpg" alt="Live classes">
                        <div class="title">
                            <?php echo $strings["live_with_record"][$lang];?>
                        </div>
                        <div class="description">
                            <?php echo $strings["live_with_record_body"][$lang];?>
                        </div>
                    </div>

                    <div class="card_image c2">
                        <img src="/img/illustrations/ProblemSolving.jpg" alt="Problem solving">
                        <div class="title t2">
                            <?php echo $strings["2hr"][$lang];?>
                        </div>
                        <div class="description d2">
                            <?php echo $strings["2hr_body"][$lang];?>
                        </div>
                    </div>

                    <div class="card_image c3">
                        <img src="/img/illustrations/ClassQuizes.jpg" alt="Quizzes">
                        <div class="title t3">
                            <?php echo $strings["quizes"][$lang];?>
                        </div>
                        <div class="description d3">
                            <?php echo $strings["quizes_body"][$lang];?>
                        </div>
                    </div>

                    <div class="card_image c2">
                        <img src="/img/illustrations/ProblemSolvingExam.jpg" alt="Exams">
                        <div class="title t4">
                            <?php echo $strings["tests"][$lang];?>
                        </div>
                        <div class="description d4">
                            <?php echo $strings["tests_body"][$lang];?>
                        </div>
                    </div>

                </div>
            </div>

        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/plugins/izi/js/iziToast.min.js"></script>
        <script src="/js/min/app.js?v=<?php echo $script_version;?>"></script>
        <script src="/js/min/index.js?v=<?php echo $script_version;?>"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
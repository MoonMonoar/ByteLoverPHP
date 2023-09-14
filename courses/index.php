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
<html>
    <head>
        <title>
            <?php echo $strings["page_courses"][$lang];?>
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
                        <a href="<?php echo Links::themeLink();?>">
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

        <?php
        if(!isset($_GET['course_code'])){
            //Mode 
            ?>
            <div class="crs-t">
                <a href="/courses/?list=live">
                    <div class="cflex<?php if(!isset($_GET['list']) || (isset($_GET['list']) && $_GET['list'] == "live")) echo ' a b';?>">
                        <?php echo $strings["live"][$lang];?>
                        <span class="numc"><?php $x = $courses->courseCount($dbconfig->getConnection()); if($x > 0){ echo $x;}?></span>
                    </div>
                </a>
                <a href="/courses/?list=recorded">
                    <div class="cflex<?php if(isset($_GET['list']) && $_GET['list'] == "recorded") echo ' a b';?>">
                        <?php echo $strings["recorded"][$lang];?>
                        <span class="numc"><?php $x = $courses->courseCount($dbconfig->getConnection(), "Recorded"); if($x > 0){ echo $x;}?></span>
                    </div>
                </a>
            </div>
            <?php
            $courses_get = array();
            $is_recored_only = false;
            if(isset($_GET['list']) && $_GET['list'] == "recorded"){
                $courses_get = $courses->allCourses($dbconfig->getConnection(), "Recorded");
                $is_recored_only = true;
            }
            else {
                 $courses_get = $courses->allCourses($dbconfig->getConnection());
            }
            if(count($courses_get) > 0){
                foreach($courses_get as $course){
            ?>
            <div id="card_welcome">
                
                <div class="geng">
                <div class="title b bt"><i class="fa-solid fa-clock-rotate-left"></i> <?php echo $strings["booking_open"][$lang];?></div>
                
                <?php 
                if($course['status'] == "Live"){
                    ?>
                    <div class="title b bt cl2"><i class="fa-solid fa-podcast"></i> Live</div>
                    <?php
                }
                else {
                    ?>
                    <div class="title b bt cr"><i class="fa-solid fa-video"></i> Recorded</div>
                    <?php
                }
                ?>
                </div>
                
                <div class="title sub b">
                <?php echo $course['name'];?>
                </div>
                <div class="body dest">
                <?php echo $course['short_description'];?>
                <div class="cdel">
                    <span class="b"><?php echo $strings["course_code"][$lang];?></span>: <?php echo $course['course_code'];?><br>
                    <span class="b"><?php echo $strings["level"][$lang];?></span>: <?php echo $course['level'];?><br>
                    <span class="b"><?php echo $strings["batch"][$lang];?></span>: <?php echo $course['batch'];?><br>
                    <span class="b"><?php echo $strings["slot"][$lang];?></span>: <?php echo $course['slot'];?><br>
                    <?php
                    if($course['status'] == "Live"){
                        ?>
                    <span class="b"><?php echo $strings["duration"][$lang];?></span>: <?php echo $course['duration'];?>
                    <?php
                    }
                    else {
                        ?>
                        <span class="b"><?php echo $strings["duration"][$lang];?></span>: ~14hours (divided into classes)
                        <?php
                    }
                    ?>
                    <br>
                    
                    <?php
                    if(!$is_recored_only){
                    ?>
                    <span class="b"><?php echo $strings["start_date"][$lang];?></span>: <?php echo $course['start_date'];?><br>
                    
                    <span class="b"><?php echo $strings["end_date"][$lang];?></span>: <?php echo $course['end_date'];?><br>
                    <?php
                    }
                    else {
                        ?>
                        <span class="b"><?php echo $strings["expiry"][$lang];?></span>: <?php echo $course['record_expire_months'];?> Months(+2 days)<br>
                        <?php
                    }
                    ?>
                    
                    <span class="b"><?php echo $strings["course_language"][$lang];?></span>: <?php echo $course['course_language'];?><br>
                    <span class="b"><?php echo $strings["course_price"][$lang];?></span>: <?php echo $course['price'];?></span><br>
                </div>
                <div class="lset">
                    <div>
                        <a href="/courses/booknow/<?php echo $course['course_code'];?>?ref=main">
                                <button class="ub cflex tb cbc bb">
                                    <i class="fa-solid fa-cart-shopping"></i> <span class="b"><?php echo $strings["book_now"][$lang];?></span>
                                </button>
                        </a>
                    </div>
                    <div>
                        <a href="/courses/<?php echo urlencode($course['course_code']);?>?ref=self">
                                <button class="ub cflex tb cbc">
                                    <i class="fa-solid fa-circle-info"></i> <span><?php echo $strings["full_details"][$lang];?></span>
                                </button>
                        </a>
                    </div>
                </div>
                </div>
            </div>
            <?php
                }
            }
            else {
                ?>
                <div class="tm cm">
                    No live courses at this moment!<br>
                    <u>Call me for more information</u>
                </div>
                <?php
            }
        }
        else {
            $is_recored_only = false;
            //Single course with description
            $course = $courses->courseBycode($dbconfig->getConnection(), $_GET['course_code']);
            if(count($course) != 1){
                echo '<div class="spe"><div class="fer">'.$strings['unknown_error'][$lang].'</div><div>';
            }
            else {
                $course = $course[0];
                if($course['status'] == "Recorded"){
                    $is_recored_only = true;
                }
            ?>
                <div id="card_welcome">
                <div class="title sub b">
                <?php echo $course['name'];?>
                </div>
                <div class="body dest">
                <?php echo $course['short_description'];?>
                <div class="fdel">
                    <?php echo $course['main_description'];?>
                </div>
                <div class="cdel">
                    <span class="b"><?php echo $strings["course_code"][$lang];?></span>: <?php echo $course['course_code'];?><br>
                    <span class="b"><?php echo $strings["level"][$lang];?></span>: <?php echo $course['level'];?><br>
                    <span class="b"><?php echo $strings["batch"][$lang];?></span>: <?php echo $course['batch'];?><br>
                    <span class="b"><?php echo $strings["slot"][$lang];?></span>: <?php echo $course['slot'];?><br>
                    <span class="b"><?php echo $strings["duration"][$lang];?></span>: <?php echo $course['duration'];?>
                    <br>
                    
                    <?php
                    if(!$is_recored_only){
                    ?>
                    <span class="b"><?php echo $strings["start_date"][$lang];?></span>: <?php echo $course['start_date'];?><br>
                    
                    <span class="b"><?php echo $strings["end_date"][$lang];?></span>: <?php echo $course['end_date'];?><br>
                    <?php
                    }
                    else {
                        ?>
                        <span class="b"><?php echo $strings["expiry"][$lang];?></span>: <?php echo $course['record_expire_months'];?> Months(+2 days)<br>
                        <?php
                    }
                    ?>
                    <span class="b"><?php echo $strings["course_language"][$lang];?></span>: <?php echo $course['course_language'];?><br>
                    <span class="b"><?php echo $strings["course_price"][$lang];?></span>: <?php echo $course['price'];?></span><br>
                </div>
                <div class="lset">
                    <div>
                        <a href="/courses/booknow/<?php echo $course['course_code'];?>?ref=main">
                                <button class="ub cflex tb cbc bb">
                                    <i class="fa-solid fa-cart-shopping"></i> <span class="b"><?php echo $strings["book_now"][$lang];?></span>
                                </button>
                        </a>
                    </div>
                </div>
                </div>
            </div>
            <?php
            }
        }
            ?>

        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/app.js?v=<?php echo $script_version;?>"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
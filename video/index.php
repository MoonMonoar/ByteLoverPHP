<?php
ob_start();
session_start();
require_once '../php/global.php';
require_once '../php/dbconfig.php';
require_once '../php/strings.php';
require_once '../php/langset.php';
require_once '../php/templates.php';
require_once '../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    header("Location: /login/?error=0002&from=dashboard");
}
$templates = new Templates();
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$courses = new Courses();
$conn = $dbconfig->getConnection();
//Get the data
?><!DOCTYPE html>
<html>
    <head>
        <title>
            <?php echo $strings["page_video"][$lang];?>
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
        if(isset($_GET['class'])){
        $class_id = $security->decryptText($_GET['class']);
        if(null == $class_id){
                ?>
                    <div class="spe">
                        <div class="fer">
                            Invalid video link!
                        </div>
                    </div>
                <?php
        }
        else {
        $course_id = $courses->classTocourse($conn, $class_id);
        if($courses->userIncourse($conn, $course_id, $_SESSION['user_id'])){
                //GET THE VIDEO LINK
                $class_data = $courses->getclassByid($conn, $class_id);
                $video_link = $class_data[0]["video_link"];
                if($video_link != "None"){
        ?>
        <div class="vpr">
        <iframe src="<?php echo $video_link;?>" width="100%" height="400px" frameborder="0" scrolling="no" autoplay allowfullscreen></iframe>
        
        <div class="vbr">&nbsp;</div>
        </div>
        
        <div class="vin">
            <div>Class: <span class="b"><?php echo $class_data[0]['title'];?></span></div>
            <?php
            $course_data = $courses->courseByid($conn, $class_data[0]['course_id']);
            if(!!$course_data){
                ?>
            <div>Course: <span class="b"><?php echo $course_data[0]['course_code'];?></span></div>
            <?php
            }
            ?>
            <div>Video load may take some time. Try refreshing the page if you have a weak internet connection.</div>
        </div>
        <?php
                }
                else {
                    //None
                ?>
                     <div class="spe">
                        <div class="fer">
                            Video is not available yet, check back in 1-2 hours.
                        </div>
                    </div>
                    <?php
                }
        }
        else {
        ?>
        <div class="spe">
            <div class="fer">
                Trying to access a course class data that is not booked!
            </div>
        </div>
        <?php 
        }
        }
        }
        else {
            ?>
            <div class="spe">
            <div class="fer">
                Invalid video link!
            </div>
        </div>
            <?php
        }
        ?>
        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/app.js?v=1.0.1"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
<?php
ob_start();
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    header("Location: /login/?error=0002&from=dahboard");
}
$templates = new Templates();
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$courses = new Courses();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
//Email check
if(!$profile->emailVerified($conn, $_SESSION['user_id'])){
    header("Location: /verify/?ref=dashboard");
    exit();
}
?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $strings["page_courses"][$lang];?></title>
        <?php
        echo Templates::headMeta();
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
                        <a href="/logout/?ref=student">
                            <button class="ub cflex" title="<?php echo $strings["logout"][$lang];?>">
                                <span><?php echo $strings["logout"][$lang];?></span><i class="fa-solid fa-power-off"></i>
                            </button>
                        </a>
                    </div>

                    <div>
                        <a href="/dashboard/?ref=payer">
                        <button class="ub cflex" title="<?php echo $strings["profile"][$lang];?>">
                            <span><?php
                                echo Profile::getUsername(DBconfig::getConnection(), $_SESSION['user_id']);
                                echo '</span>';
                                ?>
                                <img class="h_ui" src="<?php echo Profile::getImage(DBconfig::getConnection(), $_SESSION['user_id']);?>">
                        </button>
                            </a>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
        </header>
        <section id="main_body">
            <?php
            if(!$courses->isCourseactive($conn, $_SESSION['user_id'], $_GET['course_code'])){
                ?>
            <div class="b_mt">
            <?php echo $strings["payer_d_1"][$lang];?><span class="num"><b>01317215403</b></span><?php echo $strings["payer_d_2"][$lang];?>
            </div>
            <div class="payer">
                <section>
                    <div class="methods">
                        <div id="bKash" data-all='[<?php echo $courses->getPrice($conn, $_GET['course_code']);?>, "<?php echo $_GET['course_code'];?>"]'>
                            <img src="/img/logos/Bkash.svg" alt="bKash">
                        </div>
                        <div id="nagad" data-all='[<?php echo $courses->getPrice($conn, $_GET['course_code']);?>, "<?php echo $_GET['course_code'];?>"]'>
                            <img src="/img/logos/Nagad.svg" alt="Nagad">
                        </div>
                        <div id="rocket" data-all='[<?php echo $courses->getPrice($conn, $_GET['course_code']);?>, "<?php echo $_GET['course_code'];?>"]'>
                            <img src="/img/logos/Rocket.svg" alt="Rocket">
                        </div>
                    </div>
                    <section id="payer_body">
                        Please wait...
                    </section>
                    <div class="msent">
                        <?php
                        if(!$courses->invoiceIspaid($conn, $_SESSION['user_id'], $_GET['course_code'])){
                            ?>
                        <button class="ub cflex tb cbc bb" id="sent_money">
                            <i class="fas fa-check"></i> <span class="b"> I have sent the money</span>
                        </button>
                        <?php
                        }
                        else {
                        ?>
                        <div class="b pdo">
                            <?php echo $strings["sent_money"][$lang];?>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </section>
            </div>
            <?php
            }
            else {
            ?>
            <div class="b_mt mt25">
            <?php echo $strings["already_in"][$lang];?>
            </div>
            <?php
            }
            ?>
        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/app.js?v=<?php echo $script_version;?>"></script>
        <script src="/js/payer.js?v=<?php echo $script_version;?>"></script>
        <script src="/plugins/izi/js/iziToast.min.js"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
<?php
ob_start();
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])) header("Location: /login/?error=0002&from=dahboard");
$templates = new Templates();
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if($profile->isStudent($conn, $_SESSION['user_id'])){
    header("Location: /dashboard/student/?ref=auto");
    exit();
}
//Email check
if(!$profile->emailVerified($conn, $_SESSION['user_id'])){
    header("Location: /verify/?ref=dashboard");
    exit();
}
?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $strings["page_teacher_dashboard"][$lang];?></title>
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
                    </div>
                    <div class="top_menu2">
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
                        <a href="/dashboard/?ref=dashboard">
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
        <section id="main_body" class="dash">
        <div class="menu">
                <ul>
                    <li>
                        <div class="card">
                            <div class="cflex">
                                <i class="fa fa-dashboard"></i>
                            </div>
                            <div class="t def">
                                Dashboard
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="card">
                                <div class="cflex">
                                    <i class="fa fa-calendar-days"></i>
                                </div>
                                <div class="t">
                                    Routine
                                </div>
                        </div>
                    </li>
                    <li>
                        <div class="card">
                                <div class="cflex">
                                    <i class="fa-solid fa-person-circle-question"></i>
                                </div>
                                <div class="t">
                                    Quizes
                                </div>
                        </div>
                    </li>
                    <li>
                        <div class="card">
                                <div class="cflex">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="t">
                                    Assignments
                                </div>
                        </div>
                    </li>
                    <li>
                        <div class="card">
                                <div class="cflex">
                                    <i class="fa-solid fa-pen"></i>
                                </div>
                                <div class="t">
                                    Responses
                                </div>
                        </div>
                    </li>
                    <li>
                        <div class="card">
                                <div class="cflex">
                                    <i class="fa fa-cog"></i>
                                </div>
                                <div class="t">
                                    Settings
                                </div>
                        </div>
                    </li>
                </ul>
                    <div class="lmob">
                        <a href="<?php echo $alter_lang_link;?>"><?php echo $alter_lang_name;?></a>
                        <a class="ml10c" href="/courses/?ref=home_header"><?php echo $strings["courses"][$lang];?></a>
                    </div>
                    <div class="lmob mt5"><?php
                                echo Profile::getUsername(DBconfig::getConnection(), $_SESSION['user_id']);?></div>
            </div>
            <div id="dash_body"></div>
            </div>
        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/min/app.js?v=<?php echo $script_version;?>"></script>
        <script src="/js/min/teacher-dashboard.js?s=1&v=<?php echo $script_version;?>"></script>
        <script src="/plugins/izi/js/iziToast.min.js"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
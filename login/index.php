<?php
ob_start();
require_once '../php/global.php';
require_once '../php/strings.php';
require_once '../php/langset.php';
require_once '../php/templates.php';
require_once '../php/dbconfig.php';
require_once '../php/autologin.php';
if(isset($_SESSION['user_id'])) header("Location: /dashboard/?ref=login");
$templates = new Templates();
$security = new Security();
$links = new Links();
?><!DOCTYPE html>
<html>
    <head>
        <title>
            <?php echo $strings["page_login"][$lang];?>
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
                    <div>
                    <a href="/signup/?ref=login">
                        <button class="ub cflex" title="<?php echo $strings["prompt_signup"][$lang];?>">
                            <span><?php echo $strings["joinus"][$lang];?></span><i class="fa fa-user-plus"></i>
                        </button>
                    </a>
                    </div>
                </div>
            </div>
        </header>
        <section id="main_body" class="full cflex">
        <div class="formset">
        <form method="POST" action="/login/auth.php">
            <div class="ft"><?php echo $strings["login_full"][$lang];?></div>
            <?php
            if(isset($_GET['error'])){
                $code = $_GET['error'];
                if($code == "0001"){
                ?>
                <div class="fer">
                    <?php echo $strings["user_password_error"][$lang];?>
                </div>
                <?php
                }
                else if($code == "0002"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["need_login"][$lang];?>
                    </div>
        
                    <?php
                    }
                    else if($code == "0003"){
                        ?>
                        <div class="fer">
                            <?php echo $strings["need_all_login"][$lang];?>
                        </div>
            
                        <?php
                        }
                else {
                    ?>
                <div class="fer">
                    <?php echo $strings["unknown_error"][$lang];?>
                </div>
                    <?php
                }
            }
            ?>
            <div class="fcap"><?php echo $strings["email_phone"][$lang];?></div>
            <input <?php if(isset($_GET['user'])) echo 'value="'.$_GET['user'].'"';?> required type="text" class="fin" maxlength="100" name="user" placeholder="<?php echo $strings["enter_email_phone"][$lang];?>">
            
            <div class="fcap"><?php echo $strings["password"][$lang];?></div>
            <input <?php if(isset($_GET['pass'])) echo 'value="'.$security->decryptText($security->decryptText($_GET['pass'])).'"'?> id="pass" required type="<?php if(isset($_GET['sp']) && $_GET['sp'] == "on"){echo 'text';}else{echo 'password';}?>" class="fin" maxlength="200" name="password" placeholder="<?php echo $strings["enter_password"][$lang];?>">
            
            <div class="fcap mb15"><input <?php if(isset($_GET['sp']) && $_GET['sp'] == "on"){echo 'checked';}?> type="checkbox" id="showpass" name="showpass"><label for="showpass"><?php echo $strings["show_pass"][$lang];?></label></div>

            <div>
                <input type="submit" class="fgo fgob" name="login" value="<?php echo $strings["login"][$lang];?>">
            </div>
            <div class="fh">
                <hr class="lhr">
                <a href="tel:+8801317215403"><?php echo $strings["recover_password"][$lang];?></a> &middot
                <a href="/signup/?ref=login"><?php echo $strings["create_account"][$lang];?></a>
            </div>
        </form>
        </div>
        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/app.js?v=<?php echo $script_version;?>"></script>
        <script src="/js/login.js?v=<?php echo $script_version;?>"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
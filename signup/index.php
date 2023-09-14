<?php
ob_start();
require_once '../php/global.php';
require_once '../php/dbconfig.php';
require_once '../php/strings.php';
require_once '../php/langset.php';
require_once '../php/templates.php';
require_once '../php/autologin.php';
$security = new Security();
$templates = new Templates();
?><!DOCTYPE html>
<html>
    <head>
        <title>
            <?php echo $strings["page_signup"][$lang];?>
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
                    <div>
                    <a href="/login/?ref=header">
                        <button class="ub cflex" title="<?php echo $strings["prompt_login"][$lang];?>">
                            <span><?php 
                            if(!isset($_SESSION['user_id'])){
                                echo $strings["login"][$lang];
                                echo '</span> <i class="fa fa-sign-in"></i>';
                            }
                            else {
                                echo Profile::getUsername(DBconfig::getConnection(), $_SESSION['user_id']);
                                echo '</span>';
                                ?>
                                <img class="h_ui" src="<?php echo Profile::getImage(DBconfig::getConnection(), $_SESSION['user_id']);?>">
                                <?php
                            }
                            ?>
                        </button>
                            </a>
                    </div>
                </div>
            </div>
        </header>
        <section id="main_body" class="cflex">
        <div class="formset regf">
        <form method="POST" action="/signup/add.php">
            <div class="ft"><?php echo $strings["signup_full"][$lang];?></div>
            <?php
            if(isset($_GET['error'])){
                $code = $_GET['error'];
                if($code == "0001"){
                ?>
                <div class="fer">
                    <?php echo $strings["signup_details_missing"][$lang];?>
                </div>
                <?php
                }
                else if($code == "0002"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["wrong_name"][$lang];?>
                    </div>
        
                    <?php
                    }
                else if($code == "0003"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["wrong_birthdate"][$lang];?>
                        </div>
                <?php
                }
                else if($code == "0004"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["wrong_phone"][$lang];?>
                        </div>
                <?php
                }
                else if($code == "0005"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["wrong_email"][$lang];?>
                        </div>
                <?php
                }
                else if($code == "0006"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["wrong_password"][$lang];?>
                        </div>
                <?php
                }
                else if($code == "0007"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["wrong_password_again"][$lang];?>
                        </div>
                <?php
                }
                else if($code == "0008"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["weak_password"][$lang];?>
                        </div>
                <?php
                }
                else if($code == "0009"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["invalid_institute"][$lang];?>
                    </div>
                <?php
                }
                else if($code == "0010"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["invalid_degree"][$lang];?>
                    </div>
                <?php
                }
                else if($code == "0011"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["invalid_profession"][$lang];?>
                    </div>
                <?php
                }
                else if($code == "0012"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["try_again"][$lang];?>
                    </div>
                <?php
                }
                else if($code == "0013"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["phone_taken"][$lang];?>
                    </div>
                <?php
                }
                else if($code == "0014"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["email_taken"][$lang];?>
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
            <div class="fcap"><?php echo $strings["fullname"][$lang];?> *</div>
            <input <?php if(isset($_GET['name'])) echo 'value="'.$_GET['name'].'"'?> required type="text" class="fin" maxlength="100" name="fullname" placeholder="<?php echo $strings["fullname"][$lang];?>">

            <div class="fcap"><?php echo $strings["birthday"][$lang];?> *</div>
            <input <?php if(isset($_GET['db'])) echo 'value="'.$_GET['db'].'"'?> required type="date" class="fin" maxlength="100" name="birth" placeholder="mm/dd/yyyy">

            <div class="fcap"><?php echo $strings["profession"][$lang];?></div>
            <input <?php if(isset($_GET['oc'])) echo 'value="'.$_GET['oc'].'"'?> type="text" class="fin" maxlength="200" name="profession" placeholder="e.g Student">

            <div class="fcap"><?php echo $strings["institute_all"][$lang];?></div>
            <input <?php if(isset($_GET['ins'])) echo 'value="'.$_GET['ins'].'"'?> type="text" class="fin" maxlength="200" name="institute" placeholder="e.g Daffodil International University">

            <div class="fcap"><?php echo $strings["degree_all"][$lang];?></div>
            <input <?php if(isset($_GET['deg'])) echo 'value="'.$_GET['deg'].'"'?> type="text" class="fin" maxlength="200" name="degree" placeholder="e.g B.Sc in Software Engineering">

            <div class="fcap"><?php echo $strings["phone"][$lang];?> *</div>
            <input <?php if(isset($_GET['phone'])) echo 'value="'.$security->decryptText($_GET['phone']).'"'?> required type="tel" class="fin" maxlength="14" name="phone" placeholder="+8801#########">
            
            <div class="fcap"><?php echo $strings["email"][$lang];?> *</div>
            <input <?php if(isset($_GET['email'])) echo 'value="'.$security->decryptText($_GET['email']).'"'?> required type="email" class="fin" maxlength="100" name="email" placeholder="example@gmail.com">
            
            <div class="fcap"><?php echo $strings["password"][$lang];?> *</div>
            <input <?php if(isset($_GET['p1'])) echo 'value="'.$security->decryptText($security->decryptText($_GET['p1'])).'"'?> required type="<?php if(isset($_GET['sp']) && $_GET['sp'] == "on"){echo 'text';}else{echo 'password';}?>" id="pass1" class="fin" maxlength="200" name="password" placeholder="<?php echo $strings["password"][$lang];?>">
            
            <div class="fcap"><?php echo $strings["password_again"][$lang];?> *</div>
            <input <?php if(isset($_GET['p2'])) echo 'value="'.$security->decryptText($security->decryptText($_GET['p2'])).'"'?> required type="<?php if(isset($_GET['sp']) && $_GET['sp'] == "on"){echo 'text';}else{echo 'password';}?>" id="pass2" class="fin" maxlength="200" name="password_again" placeholder="<?php echo $strings["password_again"][$lang];?>">
            
            <div class="fcap mb15"><input <?php if(isset($_GET['sp']) && $_GET['sp'] == "on"){echo 'checked';}?> type="checkbox" id="showpass" name="showpass"><label for="showpass"><?php echo $strings["show_pass"][$lang];?></label></div>

            <div class="cflex regn">
                <?php echo $strings["signup_notice"][$lang];?>
            </div>

            <div>
                <input type="submit" class="fgo fgob" name="signup" value="<?php echo $strings["signup"][$lang];?>">
            </div>
            <div class="fh">
                <hr class="lhr">
                <a href="/?ref=signup"><?php echo $strings["go_home"][$lang];?></a> &middot
                <a href="/login/?ref=signup"><?php echo $strings["have_account"][$lang];?></a>
            </div>
        </form>
        </div>
        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/app.js?v=<?php echo $script_version;?>"></script>
        <script src="/js/signup.js?v=<?php echo $script_version;?>"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
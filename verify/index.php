<?php
ob_start();
require_once '../php/global.php';
require_once '../php/strings.php';
require_once '../php/langset.php';
require_once '../php/templates.php';
require_once '../php/dbconfig.php';
require_once '../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    header("Location: /login/?error=0002&from=verifier");
}
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$templates = new Templates();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
//Check if alreday verified
if($profile->emailVerified($conn, $_SESSION['user_id'])){
    header("Location: /dashboard/?ref=verifier");
    exit();
}
//Cookie check
if(isset($_COOKIE['vms'])){
    $_SESSION['verification_mail_serail'] = $_COOKIE['vms'];
}
if(isset($_COOKIE['vmp'])){
    $_SESSION['verification_code'] = $security->decryptText($_COOKIE['vmp']);
}
if(!isset($_SESSION['verification_mail_serail']) || isset($_GET['new'])){
    require_once '../php/sendmail.php';
    $pin = $security->generatePin(6);
    $serial = substr(uniqid(), 8);
    $_SESSION['verification_mail_serail'] = $serial;
    $_SESSION['verification_code'] = $pin;
    setcookie('vms', $serial, time() + (1*60*60), "/"); //1 hour
    setcookie('vmp', $security->encryptText($pin), time() + (1*60*60), "/"); //1 hour
    $mailer = new Mail();
    $receiver = $profile->getEmail($conn, $_SESSION['user_id']);
    $mail_subject = $pin.' is your 6 digit verification pin #'.$serial;
    //NON HTML
    $non_html_body = "Use ".$pin." as your 6 digit email verification pin. This pin is valid for the browser session you requested from and expires in an hour. Do not forward this message to anyone.";
    //HTML
    $body = '<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verification code - ByteLover</title>
            </head>
            <body>
            <div>
            <div style="font-size: 45px;font-family: cursive;font-weight: bold;">
            <font color="#FF7F27">Byte</font><font color="red">Lover</font><div style="font-size: medium;font-family: monospace;font-weight: normal;color: #FF7F27;padding-top: 5px;
            ">Learn to learn programming</div>
            </div>
            </div>
            <div style="display: flex;justify-content: center;align-items: center;padding-top: 25px;">
            <div style="max-width: 340px;border: 1px solid #f1f1f1;border-radius: 5px;padding: 15px;box-shadow: rgba(0, 0, 0, 0.16) 0px 2px 5px 0px, rgba(0, 0, 0, 0.12) 0px 2px 10px 0px;font-family: system-ui">
            <div style="padding: 10px;font-weight: bold">Your 6 digit email verification pin is <b>'.$pin.'</b></div>
            <div style="padding: 0 10px 10px 10px;line-height: 25px;font-size: small">This pin is valid for the browser session you requested from and expires in an hour. Do not forward this message to anyone.
            </div>
            </div>
            </div>
            <div style="font-family: system-ui;
                padding-top: 20px;
                font-size: small;
                line-height: 25px;">
            Sent to '.$receiver.' - #'.$serial.' - '.date('d/m/y h:m:s').'<br>
            ByteLover ©'.date('Y').', a Moon Monoar production. 
            </div>
            </body></html>';
    $res = $mailer->sendMail($receiver, $mail_subject, $body, $non_html_body);
    header("Location: /verify/?ref=sent");
    die();
}
else {
    if(!isset($_SESSION['verification_mail_serail'])){
        header("/verify/?new=changed");
        die();
    }
    else {
        $serial = $_SESSION['verification_mail_serail'];
    }
}

if(isset($_POST['change']) && isset($_POST['email'])){
    //Changer
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        header("Location: /verify/?change=error&error=0001&email=".urlencode($security->encryptText($_POST['email'])));
        die();
    }
    if($users->emailOccupied($conn, $_POST['email'])){
        header("Location: /verify/?change=error&error=0002&email=".urlencode($security->encryptText($_POST['email'])));
        die();
    }
    //Change it
    $db->update("users", [
        "email" => $_POST['email'],
        "email_verified" => "No"
    ], [
        "id" => $_SESSION['user_id']
    ]);
    header("Location: /verify/?new=changed");
    die();
}
?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $strings["page_verify"][$lang];?></title>
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
                    <div>
                        <a href="tel:+8801317215403">
                            <button class="ub cflex" title="<?php echo $strings["prompt_support"][$lang];?>">
                                <span><?php echo $strings["call_now"][$lang];?></span><i class="fa fa-phone"></i>
                            </button>
                        </a>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
        </header>
        <section id="main_body" class="full cflex">

        <?php
        if(!isset($_GET['change'])){
            ?>
        <div class="formset">
        <form method="POST" action="/verify/check.php">
            <div class="ft vt"><div class="vs">
                    <?php echo "#".$serial; ?>
                </div>
                <div class="mln20">
                    <?php echo $strings["email_verify"][$lang];?>
                </div>
            </div>
            <?php
            if(isset($_GET['error'])){
                $code = $_GET['error'];
                if($code == "0001"){
                ?>
                <div class="fer">
                    <?php echo $strings["wrong_pin"][$lang];?>
                </div>
                <?php
                }
                else if($code == "0002"){
                    ?>
                    <div class="fer">
                        <?php echo $strings["invalid_pin"][$lang];?>
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
            <div class="fcap b"><?php echo $strings["6_digit_pin"][$lang];?></div>

            <div class="fcap fhi"><?php echo $strings["mail_sent_part1"][$lang];?> <b><?php echo $profile->getEmail($conn, $_SESSION['user_id']);?></b><?php echo $strings["mail_sent_part2"][$lang];?></div>

            <div class="fcap">
                <b><a href="/verify/?new=changed">Send a new code</a></b> • <b> <a href="/verify/?change=email">Change email address</a></b>
            </div>

            <input <?php if(isset($_GET['code'])) echo 'value="'.$_GET['code'].'"';?> required type="tel" class="fin pin" maxlength="6" name="pin" placeholder="######">
            
            <div>
                <input type="submit" class="fgo fgob" name="verify" value="<?php echo $strings["verify"][$lang];?>">
            </div>
        </form>
        </div>
        <?php
        }
        else {
        ?>

        <div class="formset">
        <form method="POST" action="/verify/">
            <div class="ft">
                    <?php echo $strings["change_email"][$lang];?>
            </div>
            <?php
            if(isset($_GET['error'])){
                $code = $_GET['error'];
                if($code == "0001"){
                ?>
                <div class="fer">
                    <?php echo $strings["wrong_email"][$lang];?>
                </div>
                <?php
                }
                else if($code == "0002"){
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
            <div class="fcap b"><?php echo $strings["enter_new_email"][$lang];?></div>
            <input <?php if(isset($_GET['email'])) echo 'value="'.$security->decryptText($_GET['email']).'"';?> required type="email" class="fin" maxlength="200" name="email" placeholder="example@gmail.com">
            
            <div class="fcap lc b">
                <a href="/verify/?ref=changer"><?php echo $strings["go_back"][$lang];?></a></b>
            </div>

            <div>
                <input type="submit" class="fgo fgob" name="change" value="<?php echo $strings["change"][$lang];?>">
            </div>
        </form>

        <?php
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
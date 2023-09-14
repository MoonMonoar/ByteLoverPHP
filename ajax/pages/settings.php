<?php
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die('Auth error!');
}
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
?>
<div class="profile-pic">
  <label class="-label" for="file">
    <span class="glyphicon glyphicon-camera"></span>
    <span>Change Image</span>
  </label>
  <input id="file" type="file" onchange="loadDp(event)"/>
  <img src="<?php echo $profile->getImage($db_config->getConnection(), $_SESSION['user_id']);?>" id="output" width="200"/>
</div>
<div class="tm un b">
    <?php echo $profile->getUsername($db_config->getConnection(), $_SESSION['user_id']);
    ?>
</div>
<br>
<div class="tm">
    More settings will be available soon
</div>
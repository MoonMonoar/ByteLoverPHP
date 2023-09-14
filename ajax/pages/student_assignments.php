<?php
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die("Unauthorized!");
}
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
?>
<div class="empty">
<?php echo $strings["empty_exams"][$lang];?>
<div>
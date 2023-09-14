<?php
if(!isset($_POST['m'])
|| !isset($_POST['d'])){
    die('Invalid request!');
}
require_once '../../php/global.php';
require_once '../../php/dbconfig.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die('Unauthorized request!');
}
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$courses = new Courses();
$conn = $dbconfig->getConnection();
$DBobject = $dbconfig->getDB();
$data = json_decode($_POST['d']);
if(null == $data || !$courses->checkExistance($conn, $data[1])){
    die('Course not available!');
}
//IMPORTANT
$bkash_charge_rate = 1.90/100; //1.85%
$nagad_charge_rate = 1.50/100; //1.148%
$rocket_charge_rate = 1.90/100; //1.8%
//IMPORTANT
$course_price = $data[0];
$charge = 0;
switch($_POST['m']){
    case 'bKash':
        $charge = $course_price*$bkash_charge_rate;
        break;
    case 'nagad':
        $charge = $course_price*$nagad_charge_rate;
        break;
    case 'rocket':
        $charge = $course_price*$rocket_charge_rate;
        break;
    default:
        die('Invalid request!');
}
$total_price = round($course_price+$charge);
//Get invoice upon checking
$reference = $courses->getInvoice($conn, $DBobject, $_SESSION['user_id'], $data[1], $total_price);
//$invoice is the referance code that links to the order
?>
<div class="total b">
    <?php echo $total_price;?> ৳
</div>
<div class="charge">
    <?php echo $strings["s_charge_1"][$lang];?><?php echo round($charge);?><?php echo $strings["s_charge_2"][$lang];?>
</div>
<div class="refer b" id="ref" data-ref="<?php echo $reference;?>">
    <?php echo $strings["reference"][$lang];?>: <?php echo $reference;?>
</div>
<div class="pins">
    <?php echo $strings["payer_sm_1"][$lang];?><b>01317215403</b><?php echo $strings["payer_sm_2"][$lang];?>
</div>
<div class="phelp">
    For help, please call <a href="tel:+8801317215403">01317215403</a>.
</div>
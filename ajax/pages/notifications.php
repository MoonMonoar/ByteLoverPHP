<?php
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    die("ERROR!");
}
$db_config = new DBconfig();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
$u_obj = new Users();
$f_obj = new Functions();
$notifs = $u_obj->getNotifications($conn, $_SESSION['user_id']);
if(NULL != $notifs){
    ?>
    <section class="scroll">
        <?php
            foreach($notifs as $row){
                $static_id = uniqid();
                $dateTime = new DateTime($row['time']);
                $formattedDate = $dateTime->format('h:iA - d/m/Y');
                ?>
                <div class="notif" id="n_<?php echo $static_id;?>">
                    <div class="p5">
                        <div>
                            <a href="/">
                            <div class="nt"><?php echo $row['title'];?></div>
                            <div class="nb" id="b_<?php echo $static_id;?>"><?php echo $row['body'];?></div>
                            </a>
                            <div class="nc"><button nid="<?php echo $static_id;?>" class="ne lp0" onclick="del_not(this)"><i class="fa-solid fa-trash-can"></i> Delete</button> &middot <button nid="<?php echo $static_id;?>" class="ne" onclick="ne(this)"><i class="fa-solid fa-expand"></i> Expand</button> &middot <i class="fa-solid fa-clock"></i> <?php echo $formattedDate.'('.$f_obj->time_ago(strtotime($row['time']), true).')';?></div>
                        </div>
                    </div>
                </div>
                <?php
                }
            ?>
    </section>
    <?php
}
else {
?>
<div class="cflex" style="height:100%;font-size:1.2em;color:var(--black);flex-direction:column">
    <img src="/img/illustrations/NoNotifs.png" alt="No notifications" style="height:300px;width:auto">
    <div style="color: var(--black);font-size:medium">No notifications!</div>
</div>
<?php }?>
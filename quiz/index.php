<?php
ob_start();
require_once '../php/global.php';
require_once '../php/strings.php';
require_once '../php/langset.php';
require_once '../php/templates.php';
require_once '../php/dbconfig.php';
require_once '../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    header("Location: /login/?error=0002&from=dahboard");
    die();
}
if(!isset($_GET['class'])){
    header("Location: /dashboard/?from=quiz");
    die();
}
$templates = new Templates();
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$courses = new Courses();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if($profile->isTeacher($conn, $_SESSION['user_id'])){
    header("Location: /dashboard/teacher/?ref=auto"); 
    exit();
}
//Email check
if(!$profile->emailVerified($conn, $_SESSION['user_id'])){
    header("Location: /verify/?ref=dashboard");
    exit();
}
//Main class check
$class_id = $security->decryptText($_GET['class']);
if(null == $class_id){
    header("Location: /dashboard/?from=quiz");
    die();
}
//Get course data
//Get class first
$class_data = $courses->getclassByid($conn, $class_id);
$course_id = $class_data[0]['course_id'];
$course_data = $courses->courseByid($conn, $course_id);
if(null == $course_data || count($course_data) == 0){
    header("Location: /dashboard/?from=quiz");
    die();
}
$course_data = $course_data[0];
?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $strings["page_quiz"][$lang];?></title>
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
        <section id="main_body">
          <div id="dash_body">
              <?php
              $quiz_data = $users->quizGiven($conn, $_SESSION['user_id'], $class_id);
              if(!$quiz_data){
              ?>
              <div class="qh b">Class Quiz</div>
              <div class="qs">Quiz rules are given bellow</div>
              <ul>
                  <li>
                      A timer will start after you press Start Quiz. You have to finish in time.
                  </li>
                  <li>
                      If you don't finish in time the system will automatically submit it.
                  </li>
                  <li>
                      If network issue occurs, you will get the time you had left while you were online.
                  </li>
                  <li>Total Duration: 15 minutes</li>
                  <li>
                      Total marks: 15
                  </li>
                  <li>Question language: English</li>
                  <li>You can answer a question only once. Overwritten questions will be counted as wrong!</li>
                  <li>Course: <?php echo $course_data['name'];?></li>
                  <li>Class: <?php echo $_GET['name'];?></li>
              </ul>
              <div class="cflex mt30">
              <button data-cid="<?php echo $_GET['class'];?>" onclick="start_quiz(this)" class="ub cflex tb cbc bb">
                            <i class="fa-solid fa-hourglass-start"></i> <span class="b">Start Quiz</span>
                </button>
                </div>
              <?php
              }
              else {
                  if(!isset($_GET['only_marks'])){
                  ?>
                  <div class="tm b">You have already taken this exam! You got <?php echo $quiz_data['marks_obtained'];?> out of <?php echo $quiz_data['marks_total'];?></div>
                  <?php
                  }
                  else {
                    ?>
                      <div class="tm b">You got <?php echo $quiz_data['marks_obtained'];?> out of <?php echo $quiz_data['marks_total'];?>.</div>
                    <?php
                  }
                  
                 //Full exam data
                 $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE class_id = ?");
                $stmt->bind_param("i", $class_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                if($result->num_rows == 0) {
                    ?>
                    <div class="em">
                        No quizes found for this class. Maybe its an error or quiz is deleted by the conductor.
                    </div>
                        <?php
                        }
                    else {
                        //Present the data
                        $sl = 1;
                    foreach($rows as $row){
                        $o1 = '';
                        $o2 = '';
                        $o3 = '';
                        $o4 = '';
                        $skip = '';
                        $main_answer = $row['answer'];
                        //Look for answer
                        $stmt = $conn->prepare("SELECT * FROM quiz_answers WHERE user_id = ? AND question_id = ?");
                        $stmt->bind_param("ii", $_SESSION['user_id'], $row['id']);
                        $stmt->execute();
                        $result_ans = $stmt->get_result();
                        $rows_ans = $result_ans->fetch_all(MYSQLI_ASSOC);
                        if($result_ans->num_rows == 0) {
                            $skip = ' askp';
                        }
                        else {
                            $arr = $rows_ans[0];
                            $ans_arr = json_decode($arr["answer_array"]);
                            if(count($ans_arr) == 1){
                                switch($ans_arr[0]){
                                    case 1:
                                        if($main_answer == 1){
                                            $o1 = ' class="tok b"';
                                        }
                                        else {
                                            $o1 = ' class="tr b"';
                                        }
                                        break;
                                    case 2:
                                        if($main_answer == 2){
                                            $o2 = ' class="tok b"';
                                        }
                                        else {
                                            $o2 = ' class="tr b"';
                                        }
                                        break;
                                    case 3:
                                        if($main_answer == 3){
                                            $o3 = ' class="tok b"';
                                        }
                                        else {
                                            $o3 = ' class="tr b"';
                                        }
                                        break;
                                    case 4:
                                        if($main_answer == 4){
                                            $o4 = ' class="tok b"';
                                        }
                                        else {
                                            $o4 = ' class="tr b"';
                                        }
                                        break;
                                }
                            }
                            else {
                                $skip = ' awrg';
                                for($i = 0; $i < count($ans_arr); $i++){
                                switch($ans_arr[$i]){
                                    case 1:
                                        $o1 = ' class="tr b"';
                                        break;
                                    case 2:
                                        $o2 = ' class="tr b"';
                                        break;
                                    case 3:
                                        $o3 = ' class="tr b"';
                                        break;
                                    case 4:
                                        $o4 = ' class="tr b"';
                                        break;
                                }
                                }
                            }
                        }
                        $dummy_id = uniqid();
                        ?>
                        <div class="quiz_q<?php echo $skip;?>">
                            <div class="b">
                                <?php echo $sl.'. '.nl2br($row['question']);?>
                            </div>
                        <div class="qs_ops qs2">
                            <div> <input type="radio" id="<?php echo $dummy_id.'_'.$sl;?>" name="dummy_<?php echo $dummy_id;?>"> <label<?php echo $o1;?> for="<?php echo $dummy_id.'_'.$sl;?>"><?php echo $row['option_1'];?></label> </div>
                            <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+1);?>" name="dummy_<?php echo $dummy_id;?>"> <label<?php echo $o2;?> for="<?php echo $dummy_id.'_'.($sl+1);?>"><?php echo $row['option_2'];?></label> </div>
                            <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+2);?>" name="dummy_<?php echo $dummy_id;?>"> <label<?php echo $o3;?> for="<?php echo $dummy_id.'_'.($sl+2);?>"><?php echo $row['option_3'];?></label> </div>
                            <div> <input type="radio" id="<?php echo $dummy_id.'_'.($sl+3);?>" name="dummy_<?php echo $dummy_id;?>"> <label<?php echo $o4;?> for="<?php echo $dummy_id.'_'.($sl+3);?>"><?php echo $row['option_4'];?></label> </div>
                        </div>
                    
                        <div class="ans_part">
                        <div class="fs">
                            <span class="b">Answer: </span> Option <?php echo $row['answer'];?>
                        </div>
                        <div class="fs">
                            <div class="b">Explanation</div>
                            <div class="qex">
                                <?php echo nl2br($row['explanation']);?>
                            </div>
                        </div>
                        </div>
                        
                    </div>
                    <?php 
                    $sl++;
                }
                }
              }
              ?>
          </div>
        </section>
        <?php echo $templates->footerHtml();?>
        <script src="/js/jquery.min.js?v=1"></script>
        <script src="/js/app.js?v=<?php echo $script_version;?>"></script>
        <script src="/js/quiz.js?v=<?php echo $script_version;?>"></script>
        <script src="/plugins/izi/js/iziToast.min.js"></script>
    </body>
</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);
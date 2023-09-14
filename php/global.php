<?php
//ENVIRONMENT VARS
$IS_MAINTANANCE_MODE = false;
$script_version = uniqid(); //'1.1.22';

//ENDS - ENVIRONMENT VARS

//Helper Classes - Re requirable
require 'classes/HtmlMinifier.class.php';
require 'classes/JShrink/Minifier.php';
date_default_timezone_set('Asia/Dhaka');
//Script version control
if($IS_MAINTANANCE_MODE){
   $script_version = uniqid(); 
}
//Keys
const text_enc_key = "2y10VUCo1yGRx6eaXven9prfZe7dwJpGFkDC35npWubuYCuBHQ02b7PBu";
//Global keys
$global_key_theme = "light";
if(isset($_COOKIE['theme'])){
    $global_key_theme = $_COOKIE['theme'];
}
//Behavioural patters
if(isset($_GET['lang'])){
    $new_lang = $_GET['lang'];
    if($new_lang == "en" || $new_lang == "bn"){
        setcookie("lang", $new_lang, time() + (86400 * 365), "/");
    }
}
if(isset($_GET['theme'])){
    $new_theme = $_GET['theme'];
    if($new_theme == "dark" || $new_theme == "light"){
        setcookie("theme", $new_theme, time() + (86400 * 365), "/");
        $global_key_theme = $new_theme;
    }
}
//System classes
Class Minify{
    public static function html(string $html, array $options = []) : string
    {
        $minifier = new HtmlMinifier($options);
        return $minifier->minify($html);
    }
    public static function js($js) {
        return \JShrink\Minifier::minify($js);
    }
    public static function css($css) {
         $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
         $css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);
         $css = preg_replace('/\s+$/', '', $css);
         $css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $css);
         return $css;
    }
}
Class Links {
    static function getLink($url, $to_add){
        $query = parse_url($url, PHP_URL_QUERY);
        if($query) {
            return $url .= '&' .$to_add;
        } else {
            return $url .= '?' .$to_add;
        }
    }
    static function themeLink(){
        global $global_key_theme;
        $new_theme = "dark";
        if($global_key_theme == "dark"){
            $new_theme = "light";
        }
        return str_replace("theme=".$global_key_theme, "", self::getLink($_SERVER['REQUEST_URI'], "theme=".$new_theme));
    }
    static function regiError($main, $name, $phone, $email, $ins, $deg, $oc, $birth, $pass1, $pass2, $sp){
        $link = 'Location: /signup/?error='.$main;
        $security = new Security();
        if(isset($name) && !empty($name)){
            $link.= '&name='.urlencode($name);
        }
        if(isset($phone) && !empty($phone)){
            $link.= '&phone='.urlencode($security->encryptText($phone));
        }
        if(isset($email) && !empty($email)){
            $link.= '&email='.urlencode($security->encryptText($email));
        }
        if(isset($ins) && !empty($ins)){
            $link.= '&ins='.urlencode($ins);
        }
        if(isset($deg) && !empty($deg)){
            $link.= '&deg='.urlencode($deg);
        }
        if(isset($oc) && !empty($oc)){
            $link.= '&oc='.urlencode($oc);
        }
        if(isset($birth) && !empty($birth)){
            $link.= '&db='.urlencode($birth);
        }
        if(isset($pass1) && !empty($pass1)){
            $link.= '&p1='.urlencode($security->encryptText($security->encryptText($pass1)));
        }
        if(isset($pass2) && !empty($pass2)){
            $link.= '&p2='.urlencode($security->encryptText($security->encryptText($pass2)));
        }
        if(isset($sp) && !empty($sp)){
            $link.= '&sp='.urlencode($sp);
        }
        return $link;
    }
}
Class Security {
    /*
    For error case, encrypt and decrypt shall return empty character.
    Rather then throwing exeption.
    */
    static function encryptText($plaintext){
        if(empty($plaintext)){
            return '';
        }
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, text_enc_key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, text_enc_key, $as_binary=true);
        return base64_encode( $iv.$hmac.$ciphertext_raw);
    }
    static function decryptText($ciphertext){
        if(empty($ciphertext)){
            return '';
        }
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, text_enc_key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, text_enc_key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))
        {
            return $original_plaintext;
        }
        return '';
    }
    static function passwordHash($password){
        if(empty($password)) {
            return null;
        }
        return password_hash($password, PASSWORD_DEFAULT);
    }
    static function strongPassword($password){
        if(empty($password)) {
            return null;
        }
        if (strlen($password) < 8) {
            return false;
        } elseif (!preg_match("/[a-z]/", $password)) {
            return false;
        } elseif (!preg_match("/[A-Z]/", $password)) {
            return false;
        } elseif (!preg_match("/[0-9]/", $password)) {
            return false;
        } else {
            return true;
        }
    }
    static function generatePin($length = 6){
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return random_int($min, $max);
    }
}
Class Routine {
    function getDates($date, $is_even, $last_date = NULL){
        $dates = array();
        $timestamp = strtotime($date);
        for ($i = 1; $i <= 30; $i++){
            $day_timestamp = $timestamp + ($i * 86400);
            $day_number = date('j', $day_timestamp);
            $is_day_even = ($day_number % 2 == 0);
            if($is_day_even == $is_even){
                if(NULL != $last_date && strtotime($last_date) < $day_timestamp){
                    continue;
                }
                $dates[] = date('Y-m-d', $day_timestamp);
            }
        }
        return $dates;
    }
}
Class Calender {
    static function dateToday(){
        return date("Y-m-d");
    }
    static function getDay($date){
        return date('l', strtotime($date));
    }
}
Class Profile {
    static function getProfile($connection, $id){
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!
            return null;
        }
        if(!isset($id) || empty($id)) {
            //Connection must be set and established!
            return null;
        }
        $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return null;
    }
    static function getUsername($connection, $id){
        return self::getProfile($connection, $id)['fullname'];
    }
    static function getImage($connection, $id){
        $i = self::getProfile($connection, $id)['image'];
        if(null == $i) $i = "user.png";
        return '/img/users/'.$i;
    }
    static function isTeacher($connection, $id){
        $i = self::getProfile($connection, $id)['user_type'];
        if("Teacher" == $i){
            return true;
        }
        return false;
    }
    static function isStudent($connection, $id){
        $i = self::getProfile($connection, $id)['user_type'];
        if("Student" == $i){
            return true;
        }
        return false;
    }
    static function getEmail($connection, $id){
        return self::getProfile($connection, $id)['email'];
    }
    static function emailVerified($connection, $id){
        $pre = self::getProfile($connection, $id);
        if(null != $pre && strcmp($pre['email_verified'], "Yes") == 0){
            return true;
        }
        else {
            return false;
        }
    }
}
Class Users {
    static function sendNotification($db_config_obj, $mailer, $title, $body, $user, $url = NULL){
        if(!isset($title) || !isset($title) || !isset($user) || empty($title) || empty($title) || empty($user)){
            return false;
        }
        $conn = $db_config_obj->getConnection();
        $db = $db_config_obj->getDB();
        $profile_obj = new Profile();
        $user_profile = $profile_obj->getProfile($conn, $user);
        $username = $user_profile['fullname'];
        $email = $user_profile['email'];
        
        //Insert notification
        $db->insert('student_notifications', [
            'user_id' => $user,
            'title' => $title,
            'body' => $body,
            'url' => $url
        ]);
        
        //Web token
        $web_client = new WebClient();
        $web_tokens = $web_client->getUsertoken($user);
        foreach($web_tokens as $row){
            $web_token = $row['push_token'];
            if(NULL != $web_token && !empty($web_token)){
                //Send web notification
                $web_client->sendNotification($web_token, $title, $body);
            }
        }
        
        //Android token
        $android_client = new AndroidClient();
        $android_token = $android_client->getUsertoken($user);
        if(NULL != $android_token && !empty($android_token)){
            $android_client->sendNotification($android_token, $title, $body);
        }
        
        //Email notification
        if(isset($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
            $mail_subject = 'New notification';
            $body = '<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>New notification - ByteLover</title>
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
            <div style="padding: 10px;font-weight: bold">'.$title.'</div>
            <div style="padding: 0 10px 10px 10px;line-height: 25px;font-size: small">'.$body.'
            </div>
            </div>
            </div>
            <div style="font-family: system-ui;
                padding-top: 20px;
                font-size: small;
                line-height: 25px;">
            Sent to <b>'.$username.'</b> - '.$email.' - '.date('d/m/y h:m:s').'<br>
            ByteLover ©'.date('Y').', a Moon Monoar production. 
            </div>
            </body></html>';
           $mailer->sendMail($email, $mail_subject, $body);
        }
    }

    static function getNotifications($connection, $user, $offset = 0, $limit = 20){
        if(!isset($connection) || empty($connection) || $connection->connect_error){
            return NULL;
        }
        $stmt = $connection->prepare("SELECT * FROM student_notifications WHERE user_id = ? ORDER BY time DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("iii", $user, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return NULL;
    }
    static function getMarks($connection, $user){
        if(!isset($connection) || empty($connection) || $connection->connect_error){
            return 0;
        }
        $stmt = $connection->prepare("SELECT marks FROM total_marks WHERE student_id = ?");
        $stmt->bind_param("i", $user);
        $stmt->execute();
        $a = $stmt->get_result();
        if($a->num_rows == 0) {
            return -1;
        }
        $a = $a->fetch_all(MYSQLI_ASSOC);
        if(null == $a || null == $a[0]){
            return 0;
        }
        return $a[0]["marks"];
    }
    static function quizGiven($connection, $user, $class_id){
        if(!isset($connection) || empty($connection) || $connection->connect_error) return false;
        $stmt = $connection->prepare("SELECT * FROM taken_quizes WHERE user_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $user, $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }
    static function phoneOccupied($connection, $phone){
        if(!isset($connection) || empty($connection) || $connection->connect_error) return false;
        if(!isset($phone) || empty($phone)) return false;
        $phone_format_2 = '+88'.$phone;
        $phone_format_3 = str_replace('+88', '', $phone);
        $stmt = $connection->prepare("SELECT id FROM users WHERE phone = ? OR phone = ? OR phone = ?");
        $stmt->bind_param("sss", $phone, $phone_format_2, $phone_format_3);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }
    static function emailOccupied($connection, $email){
        if(!isset($connection) || empty($connection) || $connection->connect_error) return false;
        if(!isset($email) || empty($email)) return false;
        $stmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }
    static function getCourses($connection, $user_id, $limit = 10, $offset = 0){
        $stmt = $connection->prepare("SELECT * FROM booked_courses WHERE user_id = ? ORDER BY time DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return null;
    }
    static function getClasses($connection, $course_id){
        $stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? ORDER BY COALESCE(delayed_date, start_date)");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return null;
    }
    static function getTodaysclass($connection, $course_id){
        $calender = new Calender();
        $today = $calender->dateToday();
        $stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? AND ((delayed_date IS NULL AND start_date = ?) OR delayed_date = ?)");
        $stmt->bind_param("iss", $course_id, $today, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return null;        
    }
    static function getQuizclass($connection, $course_id){
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-10 day'));
        //$stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? AND is_taken = 'Yes' AND start_date = ? OR (start_date < ? AND start_date = ?) ORDER BY start_time ASC");
        //$stmt->bind_param("isss", $course_id, $today, $today, $yesterday);
        $stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? AND is_taken = 'Yes'");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return null;        
    }
}
Class Courses {
    static function getPrice($connection, $course_code){
        $stmt = $connection->prepare("SELECT actual_price FROM courses WHERE course_code = ?");
        $stmt->bind_param("s", $course_code);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if(null != $rows && null != $rows[0] && null != $rows[0]['actual_price']){
            return $rows[0]['actual_price'];
        }
        return 1000;
    }
    static function getStudents($connection, $course_id){
        $stmt = $connection->prepare("SELECT user_id FROM booked_courses WHERE course_id = ? AND payment_receive_approval = 'Approved'");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    static function checkClassquiz($connection, $class_id){
        $stmt = $connection->prepare("SELECT id FROM quiz_questions WHERE class_id = ?");
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows >= 15) { //As minimum 15 questions are needed
            return true;
        }
        return false;
    }
    static function getclassByid($connection, $class_id){
        $stmt = $connection->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if(null != $rows){
            return $rows;
        }
        return false;
    } 
    static function getclassBydate($connection, $course_date, $date){
        $stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? AND start_date = ?");
        $stmt->bind_param("is", $course_date, $date);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if(null != $row){
            return $row;
        }
        return false;
    } 
    static function gettakenclassBydate($connection, $course_date, $date){
        $stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? AND start_date = ? AND is_taken = 'Yes'");
        $stmt->bind_param("is", $course_date, $date);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if(null != $row){
            return $row;
        }
        return false;
    } 
    static function userIncourse($connection, $course_id, $user_id){
        $stmt = $connection->prepare("SELECT * FROM booked_courses WHERE user_id = ? AND course_id = ? AND payment_receive_approval = 'Approved'");
        $stmt->bind_param("ii",$user_id, $course_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if(null != $rows && null != $rows[0] && null != $rows[0]['id']){
            return true;
        }
        return false;
    }
    static function classTocourse($connection, $class_id){
        $stmt = $connection->prepare("SELECT course_id FROM classes WHERE id = ?");
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if(null != $rows && null != $rows[0] && null != $rows[0]['course_id']){
            return ''.$rows[0]['course_id'].'';
        }
        return '0';
    }
    static function begineerCourses($connection, $limit = 1){
        //Usually for the homepage, to show begineer cources, no offset just limit
        //Also check if the end date has expired
        if(!isset($connection) || empty($connection) || $connection->connect_error){
            //Connection must be set and established!
            return null;
        }
        $calender = new Calender();
        $today = $calender->dateToday();
        $stmt = $connection->prepare("SELECT * FROM courses WHERE end_date > ? AND level = \"1 - Beginner\" limit ?");
        $stmt->bind_param("si", $today , $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    static function allCourses($connection, $mode = "Live", $limit = 20, $offset = 0){
        //Usually for the homepage, to show begineer cources, no offset just limit
        //Also check if the end date has expired
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!
            return null;         
        }
        //$calender = new Calender();
        //$today = $calender->dateToday();
        $stmt = $connection->prepare("SELECT * FROM courses WHERE status = ? LIMIT ? OFFSET ?");
        $stmt->bind_param("sii", $mode, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    static function courseCount($connection, $mode = "Live"){
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!
            return null;
        }
        $stmt = $connection->prepare("SELECT COUNT(*) as count FROM courses WHERE status = ?");
        $stmt->bind_param("s", $mode);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
    static function courseBycode($connection, $code){
        //Usually for the homepage, to show begineer cources, no offset just limit
        //Also check if the end date has expired
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!
            return null;
        }
        $stmt = $connection->prepare("SELECT * FROM courses WHERE course_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    static function courseByid($connection, $id){
        //Usually for the homepage, to show begineer cources, no offset just limit
        //Also check if the end date has expired
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established! 
            return null;
        }
        $stmt = $connection->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    static function courseidBycode($connection, $code){
        //Usually for the homepage, to show begineer cources, no offset just limit
        //Also check if the end date has expired
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!            
            return null;       
        }
        $stmt = $connection->prepare("SELECT id FROM courses WHERE course_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $a = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if(null == $a || null == $a[0]){
            return 0;
        }
        return $a[0]["id"];
    }
    static function checkExistance($connection, $course_code){
        if(!isset($connection) || empty($connection) || $connection->connect_error){
            //Connection must be set and established!
            return null;   
        }
        $course_get = self::courseIdBycode($connection, $course_code);
        if($course_get != 0){
            return true;
        }
        return false;
    }
    static function checkInvoice($connection, $user_id, $course_id){
        //Get id from code
        $stmt = $connection->prepare("SELECT reference FROM booked_courses WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $user_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $row = $result->fetch_all(MYSQLI_ASSOC);
            return $row[0]["reference"];
        }
        return false;
    }
    static function getInvoice($connection, $DBobject, $user_id, $course_code, $course_price){
        if(!isset($connection) || empty($connection) || $connection->connect_error) {  
            //Connection must be set and established!
            return null;
        }
        //Check if invoice exists, if then return the referense code, if not, generate one then return
        $course_id = self::courseidBycode($connection, $course_code);
        if($course_id == 0){
            return null;
        }
        $reference = self::checkInvoice($connection, $user_id, $course_id);
        if(!!$reference){
            //Has referance, return it
            return $reference;
        }
        else {
            $reference_pin = Security::generatePin(5);
            //False, create one
            
            //Record expeiry if recorded course
            //1. Get the course
            //2. Check if recorded
            //3. Get month
            //4. Add 2 days with month
            $REC_exp = NULL;
            $_course = self::courseByid($connection, $course_id)[0];
            if($_course['status'] == "Recorded"){
                $_months = $_course['record_expire_months'];
                $presentDate = new DateTime();
                $presentDate->add(new DateInterval('P'.$_months.'M2D')); //Months & 2days
                $REC_exp = $presentDate->format('Y-m-d');
            }
            $DBobject->insert("booked_courses", [
                "user_id" => $user_id,
                "course_id" => $course_id,
                "course_price" => $course_price,
                "reference" => $reference_pin,
                "record_expiry" => $REC_exp
            ]);
            if(null != $DBobject->id()){
                return $reference_pin;
            }
            else {
                return null;
            }
        }
    }
    static function getCourses($connection, $user_id, $limit = 10, $offset = 0){
        //Get id from code
        $stmt = $connection->prepare("SELECT * FROM booked_courses WHERE user_id = ? ORDER BY time DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return null;
    }
    static function getActivecourses($connection, $user_id, $skip_expire = false, $limit = 10, $offset = 0){
        //Get id from code
        $stmt = $connection->prepare("SELECT * FROM booked_courses WHERE user_id = ? AND payment_receive_approval = 'Approved' AND (record_expiry IS NULL OR record_expiry >= CURDATE())");
        if($skip_expire){
            $stmt = $connection->prepare("SELECT * FROM booked_courses WHERE user_id = ? AND payment_receive_approval = 'Approved'");
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return array_reverse($result->fetch_all(MYSQLI_ASSOC));
        }
        return null;
    }
    static function getExpiry($connection, $user_id, $course_id){
        $stmt = $connection->prepare("SELECT record_expiry FROM booked_courses WHERE user_id = ? AND course_id = ?");
        $stmt->bind_param("ii", $user_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC)[0]['record_expiry'];
        }
        return NULL;
    }
    static function invoiceIspaid($connection, $user, $course_code){
        if(!isset($connection) || empty($connection) || $connection->connect_error) {             
            //Connection must be set and established!
            return null;
        }
        $course_id = self::courseidBycode($connection, $course_code);
        $stmt = $connection->prepare("SELECT id FROM booked_courses WHERE user_id = ? AND course_id = ? AND marked_paid != 'No'");
        $stmt->bind_param("ii", $user, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }
    static function isCourseactive($connection, $user, $course_code){
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!
            return null;
        }
        $course_id = self::courseidBycode($connection, $course_code);
        $stmt = $connection->prepare("SELECT id FROM booked_courses WHERE user_id = ? AND course_id = ? AND payment_receive_approval = 'Approved'");
        $stmt->bind_param("ii", $user, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return true;
        }
        return false;
    }
    static function getSlot($connection, $cid){
        if(!isset($connection) || !isset($cid)){
            return null;
        }
        $course = self::courseByid($connection, $cid);
        $slot = $course[0]['slot'];
        if($slot == "A(even)"){
            return [$course[0]['start_date'], true, $course[0]['end_date']];
        }
        return [$course[0]['start_date'], false, $course[0]['end_date']];
    }
    static function getClass($connection, $course_id, $user_id, $date){
        if(!isset($connection) || !isset($course_id)){
            return null;
        }
        $stmt = $connection->prepare("SELECT * FROM classes WHERE course_id = ? AND teacher_id = ? AND start_date = ?");
        $stmt->bind_param("iis", $course_id, $user_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }
    static function getMaterials($connection, $class_id){
        if(!isset($connection) || !isset($class_id)){
            return null;
        }
        //Decrypt class id
        $class_id = Security::decryptText($class_id);
        if(null != $class_id){
            $stmt = $connection->prepare("SELECT * FROM study_material WHERE class_id = ?");
            $stmt->bind_param("i", $class_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        return null;
    }
    static function quizCount($connection, $class_id){
        if(!isset($connection) || !isset($class_id)){
            return null;
        }
        $stmt = $connection->prepare("SELECT id FROM quiz_questions WHERE class_id = ?");
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        return $result->num_rows;
    }
    static function getExamquestions($connection, $course_id, $date){
        if(!isset($connection) || !isset($course_id)){
            return null;
        }
        $stmt = $connection->prepare("SELECT * FROM exam_questions WHERE course_id = ? AND exam_date = ?");
        $stmt->bind_param("is", $course_id, $date);
        $stmt->execute();
        return $result = $stmt->get_result();
    }
}
Class Functions {
    function millisecondsToTime($milliseconds) {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $days = floor($hours / 24);
        $hours %= 24;
        $minutes %= 60;
        $seconds %= 60;
        $time = '';
        
        if ($days > 0) {
          $time .= $days . 'd ';
        }
        
        if ($hours > 0) {
          $time .= $hours . 'h ';
        }
        
        if ($minutes > 0) {
          $time .= $minutes . 'm ';
        }
        
        if ($seconds > 0) {
          $time .= $seconds . 's';
        }
          return $time;
    }
    static function ordinal($number) {
    if (!is_numeric($number)) {
        return false;
    }
    $last_digit = $number % 10;
    $last_two_digits = $number % 100;
    if ($last_two_digits >= 11 && $last_two_digits <= 13) {
        $suffix = 'th';
    } else {
        switch ($last_digit) {
            case 1: $suffix = 'st'; break;
            case 2: $suffix = 'nd'; break;
            case 3: $suffix = 'rd'; break;
            default: $suffix = 'th'; break;
        }
    }
    return $number . $suffix;
    }
    static function formatFilesize($filePath) {
        $bytes = filesize($filePath);
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $index = 0;
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }
        return round($bytes, 2) . ' ' . $units[$index];
    }
    static function formatDate($dateString) {
        $date = DateTime::createFromFormat('Y-m-d', $dateString);
        return $date->format('F j, Y');
    }
    static function time_ago($timestamp, $abbreviate = false) {
        $current_time = time();
        $time_diff = $current_time - $timestamp;
        $seconds = $time_diff;
        $minutes = round($seconds / 60);
        $hours = round($seconds / 3600);
        $days = round($seconds / 86400);
        $weeks = round($seconds / 604800);
        $months = round($seconds / 2629440);
        $years = round($seconds / 31553280);
    
        if ($seconds <= 60) {
            return "Just now";
        } else if ($minutes <= 60) {
            if ($abbreviate) {
                return ($minutes == 1) ? "1m ago" : "${minutes}m ago";
            } else {
                return ($minutes == 1) ? "one minute ago" : "${minutes} minutes ago";
            }
        } else if ($hours <= 24) {
            if ($abbreviate) {
                return ($hours == 1) ? "1h ago" : "${hours}h ago";
            } else {
                return ($hours == 1) ? "an hour ago" : "${hours} hours ago";
            }
        } else if ($days <= 7) {
            if ($abbreviate) {
                return ($days == 1) ? "1d ago" : "${days}d ago";
            } else {
                return ($days == 1) ? "yesterday" : "${days} days ago";
            }
        } else if ($weeks <= 4.3) {
            if ($abbreviate) {
                return ($weeks == 1) ? "1w ago" : "${weeks}w ago";
            } else {
                return ($weeks == 1) ? "a week ago" : "${weeks} weeks ago";
            }
        } else if ($months <= 12) {
            if ($abbreviate) {
                return ($months == 1) ? "1mo ago" : "${months}mo ago";
            } else {
                return ($months == 1) ? "a month ago" : "${months} months ago";
            }
        } else {
            if ($abbreviate) {
                return ($years == 1) ? "1y ago" : "${years}y ago";
            } else {
                return ($years == 1) ? "one year ago" : "${years} years ago";
            }
        }
    }
}
Class Teachers {
    static function getCourses($connection, $teacher){
        if(!isset($connection) || empty($connection) || $connection->connect_error) {
            //Connection must be set and established!
            return null;
        }
        $stmt = $connection->prepare("SELECT * FROM courses WHERE teacher_id = ? ORDER BY start_date DESC");
        $stmt->bind_param("i", $teacher);
        $stmt->execute();
        return $result = $stmt->get_result();
    }
}
Class AndroidClient {
    const host = "localhost";
    const username = "bytelove_main";
    const password = "){Zm&F.MBZc_";
    const name = "bytelove_main";
    static function getUsertoken($user_id){
        $conn = new mysqli(self::host, self::username, self::password, self::name);
        $conn->set_charset("utf8mb4");
        $stmt = $conn->prepare("SELECT push_token FROM android_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $pushToken = null;
        if (!empty($result[0]['push_token'])) {
            return $result[0]['push_token'];
        }
        return null;
    }
    static function queryHardware($hardware_id, $connection){
        $stmt = $connection->prepare("SELECT * FROM android_sessions WHERE hardware_id	 = ?");
        $stmt->bind_param("s", $hardware_id);
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    function sendNotification($token, $title, $message, $data = null, $icon = "Default"){
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: key=AAAANUh7iq0:APA91bH6m0iL-BJ2RY_W7-t9XgvgHhkT4ETV2BsItZo77wdKWhMSQJitReyXJm5tOa-RqYcriwuCEPjF1HsRt9rPXaZP2LadY7_eL_5ysdlOKPnw5edDYWTh3GbFhktBlVcWCyREI3hv' //Server key
        );
        $notification = array(
            'title' => $title,
            'body' => $message,
            'icon' => $icon
        );
        $fields = array(
            'to' => $token,
            'notification' => $notification,
            'data' => $data
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return true;
    }
}
Class WebClient {
    //Separate DB handler
    const host = "localhost";
    const username = "bytelove_main";
    const password = "){Zm&F.MBZc_";
    const name = "bytelove_main";
    static function getClient($session_code){
        $conn = new mysqli(self::host, self::username, self::password, self::name);
        $conn->set_charset("utf8mb4");
        $stmt = $conn->prepare("SELECT * FROM web_sessions WHERE session_code = ?");
        $stmt->bind_param("s", $session_code);
        $stmt->execute();
        return $stmt->get_result();
    }
    static function checkClient($session_code){
        $client = self::getClient($session_code);
        if($client->num_rows > 0){
            return true;
        }
        return false;
    }
    static function getUsertoken($user_id){
        $conn = new mysqli(self::host, self::username, self::password, self::name);
        $conn->set_charset("utf8mb4");
        $stmt = $conn->prepare("SELECT push_token FROM web_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    static function addClient(){
        $code = Security::generatePin(10); //Random
        $conn = new mysqli(self::host, self::username, self::password, self::name);
        $conn->set_charset("utf8mb4");
        $session_code = mysqli_real_escape_string($conn, $code);
        $ip_address = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
        if(isset($_POST['token_fcm_notification'])){
            $push_token = mysqli_real_escape_string($conn, $_POST['token_fcm_notification']);
            $sql = "INSERT INTO web_sessions (session_code, push_token, ip) VALUES ('$session_code', '$push_token', '$ip_address')";
        }
        else {
            $sql = "INSERT INTO web_sessions (session_code, ip) VALUES ('$session_code', '$ip_address')";  
        }
        $conn->query($sql);
        $conn->close();
        return $code;
    }
    static function updateClient($code){
        $conn = new mysqli(self::host, self::username, self::password, self::name);
        $conn->set_charset("utf8mb4");
        $session_code = mysqli_real_escape_string($conn, $code);
        $ip_address = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
        if(!isset($_SESSION['user_id'])){
        if(isset($_POST['token_fcm_notification'])){
            $push_token = mysqli_real_escape_string($conn, $_POST['token_fcm_notification']);
            //With token
            $sql = "UPDATE web_sessions SET last_call='".date("Y-m-d h:m:s", time())."', push_token='$push_token', user_id=NULL, ip='$ip_address' WHERE session_code=$session_code";
        }
        else {
            $sql = "UPDATE web_sessions SET last_call='".date("Y-m-d h:m:s", time())."', ip='$ip_address', user_id=NULL WHERE session_code=$session_code";
        }
        }
        else {
            $user = mysqli_real_escape_string($conn, $_SESSION['user_id']);
            if(isset($_POST['token_fcm_notification'])){
                $push_token = mysqli_real_escape_string($conn, $_POST['token_fcm_notification']);
                $sql = "UPDATE web_sessions SET user_id= '$user', push_token='$push_token', last_call='".date("Y-m-d h:m:s", time())."', ip='$ip_address' WHERE session_code=$session_code";
            }
            else {
                $sql = "UPDATE web_sessions SET user_id= '$user', last_call='".date("Y-m-d h:m:s", time())."', ip='$ip_address' WHERE session_code=$session_code";
            }
        }
        $conn->query($sql);
        $conn->close();
    }
    static function sendNotification($token, $title, $body, $data = null, $icon = "https://bytelover.com/img/favicons/apple-icon-180x180.png"){
        if(empty($token) || empty($title) || empty($body)){
            return null;
        }
        if(!defined('SERVER_KEY')){
            define('SERVER_KEY', 'AAAANUh7iq0:APA91bGBhOUCnE-lr2heoucblrGygcKHj4fzDSLUykuODuD-Bfj_zFAY2xnhKXhfLA90vQIspZilJbbTANaj2ugAYQ3rWHzx_XkekKhB6TDHN3i-qlERC7zUSkUDkF-Ue28aigFXhqfb');
        }
        // Notification payload
        $notification = array(
            'title' => $title,
            'body' => $body,
            'icon' => $icon
        );
        // FCM message body
        $message = array(
            'to' => $token,
            'notification' => $notification,
            'data' => $data
        );
        // Send the FCM message
        $fields = json_encode($message);
        $headers = array(
            'Authorization: key=' . SERVER_KEY,
            'Content-Type: application/json',
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);
        
        // Return the FCM response
        return $result;
    }
}
//WEBSESSION TRACKER
//Except api domains
if(strcmp($_SERVER['HTTP_HOST'], "api.bytelover.com") != 0){
    //Web browser check
    if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') !== false) {
        if(!isset($_SESSION)) session_start();
        $client = new WebClient();
        //Cookie check
        if(!isset($_COOKIE['SessionCode'])){
            setcookie("SessionCode", $client->addClient(), time() + (86400 * 365), "/");
        }
        else {
            $client->updateClient($_COOKIE['SessionCode']);
        }
    }
}
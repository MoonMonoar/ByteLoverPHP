<?php
if(!isset($_SESSION)) session_start();
if(!isset($_SESSION['user_id']) && (isset($_COOKIE['ui']) && isset($_COOKIE['uk']))){
    $cookie_user = Security::decryptText($_COOKIE['ui']);
    $cookie_passsword = Security::decryptText($_COOKIE['uk']);
    if(null != $cookie_user && null != $cookie_passsword){
    $conn = DBconfig::getConnection();
        if(!$conn->connect_error){
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $cookie_user);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $password_hash = $row['password'];
                if(password_verify($cookie_passsword, $password_hash)){
                    $_SESSION['user_id'] = $cookie_user;
                    $_SESSION['login_time'] = time();
                }
            }
        }
    }
}
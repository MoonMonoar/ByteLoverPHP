<?php
if(!isset($_POST['submit']) || !isset($_POST['list']) || !isset($_POST['cid'])){
    header('Location: /dashboard/?ref=error');
    die();
}
require_once '../../php/global.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/dbconfig.php';
require_once '../../php/autologin.php';
if(!isset($_SESSION['user_id'])){
    header('Location: /dashboard/?ref=error');
    die();
}
$profile = new Profile();
$db_config = new DBconfig();
$security = new Security();
$users = new Users();
$teachers = new Teachers();
$courses = new Courses();
$routine = new Routine();
$calender = new Calender();
$functions = new Functions();
$conn = $db_config->getConnection();
$db = $db_config->getDB();
if($users->quizGiven($conn, $_SESSION['user_id'], $_POST['cid'])){
    header("Location: /quiz/?class=".urlencode($security->encryptText($_POST['cid'])));
    die();
}
$quesiton_list = json_decode($_POST['list']);
$marks_gained = 0;
$total_marks = 0;
$course_id = $courses->classTocourse($conn, $_POST['cid']);
foreach($quesiton_list as $id){
    //Select the question and generate the post key
    $stmt = $conn->prepare("SELECT answer, marks FROM quiz_questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_marks+= $row['marks'];
    $post_key = 'q_'.$id.'_'.$row['answer'];
    
    //Correction flag
    $overwritten = false;
    $ov_flag = false;
    $even_touched = false;
    
    //Just make the answer array
    $answer_array = '[';
    $i = 1;
    while($i < 5){
        if(isset($_POST['q_'.$id.'_'.$i])){
            $answer_array.= $i.',';
            if($i != $row['answer']){
                $ov_flag = true;
            }
            $even_touched = true;
        }
        if($ov_flag){
            $overwritten = true;
        }
        $i++;
    }
    $answer_array.= ']';
    
    //Not answered
    if(!$even_touched){
        //Skip it
        continue;
    }
    
    //Trimmer
    $answer_array = str_replace(',]', ']', $answer_array);
    
    $was_correct = '0';
    if(isset($_POST[$post_key]) && !$overwritten){
        $was_correct = '1';
        $marks_gained+= $row['marks'];
    }
    
    //Capture answer
    $db->insert("quiz_answers", [
        "question_id" => $id,
        "user_id" => $_SESSION['user_id'],
        "answer_array" => $answer_array,
        "was_correct" => $was_correct
    ]);
}

//Insert a taken quiz
$db->insert("taken_quizes", [
        "class_id" => $_POST['cid'],
        "user_id" => $_SESSION['user_id'],
        "marks_obtained" => $marks_gained,
        "marks_total" => $total_marks
]);

//Add marks - course wise
$old_marks = $users->getMarks($conn, $_SESSION['user_id']);
if($old_marks < 0){
    //Insert
    $db->insert("total_marks", [
        "student_id" => $_SESSION['user_id'],
        "marks" => $marks_gained,
        "course_id" => $course_id
    ]);
}
else {
    //Update
    $marks_gained+= $old_marks;
    $db->update("total_marks", [
        "marks" => $marks_gained
    ], [
        "student_id" => $_SESSION['user_id']
    ]);
}

//Done, just rdr to quiz page
header("Location: /quiz/?only_marks=1&class=".urlencode($security->encryptText($_POST['cid'])));
die();
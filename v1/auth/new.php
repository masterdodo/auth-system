<?php
require_once '../../db.php';
$continue = false;
$data = json_decode(file_get_contents('php://input'), true);
$email = check_data($data["email"]);
$username = check_data($data["username"]);
$password = check_data($data["password"]);
$user_type = check_data($data["user_type"]);
$service = check_data($data["service"]);

if($service=="root"){
    if(isset($data["#0U8@ZRP8tMomhXU&3y"])){
        $continue = true;
    }
    else {
        $continue = false;
        echo json_encode('{"status":"error","message":"Secret missing!"}',true);
    }
}
else{
    $continue = true;
}

if($email==null || $email=='' ||$username==null || $username=='' ||$password==null || $password=='' ||$user_type==null || $user_type=='' ||$service==null || $service==''){
    $continue = false;
    echo json_encode('{"status":"error","message":"There was a wrong input of some sort. Check the data that was sent."}',true);
}

if($continue){
    $hashed_password = password_hash($password,PASSWORD_DEFAULT);
    $sql = $pdo->prepare("INSERT INTO users(email,username,password,user_type,service) VALUES(?,?,?,?,?)");
    if($sql->execute(array($email,$username,$hashed_password,$user_type,$service)))
    {
        echo json_encode('{"status":"success","message":"User added successfully."}',true);
    }
    else{
        echo json_encode('{"status":"error","message":"Error adding user. Please try again later."}',true);
    }
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
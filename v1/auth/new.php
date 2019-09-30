<?php
header('Content-type: text/plain; charset=utf-8');
require_once '../../db.php';
$continue = false;
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data["email"]) && isset($data["username"]) && isset($data["password"]) && isset($data["user_type"]) && isset($data["service"]) && isset($data["service_salt"])){
    
    $email = check_data($data["email"]);
    $username = check_data($data["username"]);
    $password = check_data($data["password"]);
    $user_type = check_data($data["user_type"]);
    $service = check_data($data["service"]);
    $service_salt = check_data($data["service_salt"]);

    $sql_service_exists = $pdo->prepare("SELECT * FROM services WHERE name=? AND service_salt=?");
    $sql_service_exists->execute(array($service,$service_salt));
    if($sql_service_exists->fetchColumn() > 0){
        $sql_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (email=? OR username=?) AND service=?");
        $sql_check->execute(array($email,$username,$service));

        if($sql_check->fetchColumn() == 0){
            $hashed_password = password_hash($password,PASSWORD_DEFAULT);
            $sql = $pdo->prepare("INSERT INTO users(email,username,password,user_type,service) VALUES(?,?,?,?,?)");
            if($sql->execute(array($email,$username,$hashed_password,$user_type,$service))){
                echo '{"status":"success","message":"User added successfully."}';
            }
            else{
                echo '{"status":"error","message":"Error adding user. Please try again later."}';
            }
        }
        else{
            echo '{"status":"error","message":"Username or email already exists."}';
        }
    }
    else{
        echo '{"status":"error","message":"No service matches. Check the data."}';
    }
}
else{
    echo '{"status":"error","message":"There was a wrong input of some sort. Check the data that was sent."}';
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
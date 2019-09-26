<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["service"]) && isset($data["service_salt"]) && (isset($data["id"]) || isset($data["email"]))){
    $service_name = check_data($data["service"]);
    $service_salt = check_data($data["service_salt"]);
    $sql_service_exists = $pdo->prepare("SELECT COUNT(*) FROM services WHERE name=? AND service_salt=?");
    $sql_service_exists->execute(array($service_name,$service_salt));
    if($sql_service_exists->fetchColumn() > 0){
        if(isset($data["id"])){
            $user_id = check_data($data["id"]);
            $sql1 = $pdo->prepare("SELECT id,email,username,user_type,service,created_at,updated_at FROM users WHERE id=? LIMIT 1");
            $sql1->execute(array($user_id));
            $sql2 = $pdo->prepare("SELECT id,email,username,user_type,service,created_at,updated_at FROM users WHERE id=? LIMIT 1");
            $sql2->execute(array($user_id));
            if($sql1->fetchColumn() > 0){
                $result = json_encode($sql2->fetch(PDO::FETCH_ASSOC),true);
                echo json_encode('{"status":"success","message":"Users returned.","data":'.$result.'}',true);
            }
            else{
                echo json_encode('{"status":"error","message":"No user with that id found."}',true);
            }
        }
        else if(isset($data["email"])){
            $user_email = check_data($data["email"]);
            $sql1 = $pdo->prepare("SELECT id,email,username,user_type,service,created_at,updated_at FROM users WHERE email=? AND service=? LIMIT 1");
            $sql1->execute(array($user_email,$service_name));
            $sql2 = $pdo->prepare("SELECT id,email,username,user_type,service,created_at,updated_at FROM users WHERE email=? AND service=? LIMIT 1");
            $sql2->execute(array($user_email,$service_name));
            if($sql1->fetchColumn() > 0){
                $result = $sql2->fetch(PDO::FETCH_ASSOC);
                echo json_encode('{"status":"success","message":"Users returned.","data":'.$result.'}',true);
            }
            else{
                echo json_encode('{"status":"error","message":"No user with that email found."}',true);
            }
        }
        else {
            echo json_encode('{"status":"error","message":"Id or email have to be passed."}',true);
        }
    }
    else{
        echo json_encode('{"status":"error","message":"Service doesn\'t exist."}',true);
    }
}
else{
    echo json_encode('{"status":"error","message":"Can\'t operate with given parameters."}',true);
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
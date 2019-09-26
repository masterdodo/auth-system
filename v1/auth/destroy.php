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
            $sql = $pdo->prepare("DELETE FROM users WHERE id=? AND service=?");
            if($sql->execute(array($user_id,$service_name))){
                echo json_encode('{"status":"success","message":"User removed."}',true);
            }
            else{
                echo json_encode('{"status":"error","message":"User removal failed. Try again later."}',true);
            }
        }
        else if(isset($data["email"])){
            $user_email = check_data($data["email"]);
            $sql = $pdo->prepare("DELETE FROM users WHERE email=? AND service=?");
            if($sql->execute(array($user_email,$service_name))){
                echo json_encode('{"status":"success","message":"User removed."}',true);
            }
            else{
                echo json_encode('{"status":"error","message":"User removal failed. Try again later."}',true);
            }
        }
        else {
            echo json_encode('{"status":"error","message":"Can\'t remove user without id or email provided."}',true);
        }
    }
    else{
        echo json_encode('{"status":"error","message":"Provided service doesn\'t exist."}',true);
    }
}
else{
    echo json_encode('{"status":"error","message":"Can\'t remove user with given parameters."}',true);
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
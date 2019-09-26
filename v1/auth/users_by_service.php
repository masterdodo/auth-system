<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["service"]) && isset($data["service_salt"])){
    $service_name = check_data($data["service"]);
    $service_salt = check_data($data["service_salt"]);
    $sql_service_exists = $pdo->prepare("SELECT COUNT(*) FROM services WHERE name=? AND service_salt=?");
    $sql_service_exists->execute(array($service_name,$service_salt));
    if($sql_service_exists->fetchColumn() > 0){
        $sql_users = $pdo->prepare("SELECT id,email,username,service,user_type,created_at,updated_at FROM users WHERE service=?");
        $sql_users->execute(array($service_name));
        if($sql_users->fetchColumn() > 0){
            $result = $sql_users->fetch(PDO::FETCH_ASSOC);
            echo json_encode('{"status":"success","message":"Users returned successfully.","data":'.$result.'}',true);
        }
        else{
            echo json_encode('{"status":"error","message":"No users found."}',true);
        }
    }
    else{
        echo json_encode('{"status":"error","message":"Given parameters don\'t match any service. Please check the parameters."}',true);
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
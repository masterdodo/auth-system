<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["service"]) && isset($data["service_salt"])){
    $service_name = check_data($data["service"]);
    $service_salt = check_data($data["service_salt"]);
    $sql_service_exists = $pdo->prepare("SELECT COUNT(*) FROM services WHERE name=? AND service_salt=?");
    $sql_service_exists->execute(array($service_name,$service_salt));
    if($sql_service_exists->fetchColumn() > 0){
        $sql_users1 = $pdo->prepare("SELECT id,email,username,service,user_type,created_at,updated_at FROM users WHERE service=?");
        $sql_users1->execute(array($service_name));
        $sql_users2 = $pdo->prepare("SELECT id,email,username,service,user_type,created_at,updated_at FROM users WHERE service=?");
        $sql_users2->execute(array($service_name));
        $result = json_encode($sql_users1->fetchAll(PDO::FETCH_ASSOC),true);
        if($sql_users2->fetchColumn() > 0){
            echo '{"status":"success","message":"Users returned successfully.","data":'.$result.'}';
        }
        else{
            echo '{"status":"error","message":"No users found."}';
        }
    }
    else{
        echo '{"status":"error","message":"Given parameters don\'t match any service. Please check the parameters."}';
    }
}
else{
    echo '{"status":"error","message":"Can\'t operate with given parameters."}';
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
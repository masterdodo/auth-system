<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["service"]) && isset($data["service_salt"])){
    $sql = $pdo->prepare("SELECT id,name,service_salt FROM services WHERE name=? AND service_salt=?");
    if($sql->execute(array(check_data($data["service"]),check_data($data["service_salt"])))){
        $sql_users = $pdo->prepare("SELECT id,email,username,service,user_type,created_at,updated_at FROM users WHERE service=?");
        if($sql_users->execute(array($data["service"]))){
            $result = $sql_users->fetch(PDO::FETCH_ASSOC);
            echo json_encode('{"status":"success","message":"Users returned successfully.","data":'.$result.'}',true);
        }
        else{
            echo json_encode('{"status":"error","message":"Couldn\'t return the requested users. Try again later."}',true);
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
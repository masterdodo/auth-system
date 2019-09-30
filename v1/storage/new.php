<?php
header('Content-type: text/plain; charset=utf-8');
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["service_name"]) && isset($data["service_salt"]) && isset($data["storage_url"]) && isset($data["storage_type"]) && isset($data["user_id"])){
    $service_name = check_data($data["service_name"]);
    $service_salt = check_data($data["service_salt"]);
    $storage_url = check_data($data["storage_url"]);
    $storage_type = check_data($data["storage_type"]);
    $user_id = check_data($data["user_id"]);

    $sql_service_exists = $pdo->prepare("SELECT * FROM services WHERE name=? AND service_salt=?");
    $sql_service_exists->execute(array($service,$service_salt));
    if($sql_service_exists->fetchColumn() > 0){
        $sql_check = $pdo->prepare("SELECT COUNT(*) FROM active_storage WHERE storage_url=? AND storage_type=? AND user_id=? AND service=?");
        $sql_check->execute(array($storage_url,$storage_type,$user_id,$service_name));
        if($sql_check->fetchColumn() == 0){
            $sql = $pdo->prepare("INSERT INTO active_storage(url,storage_type,user_id,service) VALUES(?,?,?,?)");
            if($sql->execute(array($storage_url,$storage_type,$user_id,$service_name))){
                echo '{"status":"success","message":"Storage link added successfully."}';
            }
            else{
                echo '{"status":"error","message":"Storage link not added successfully. Try again later."}';
            }
        }
        else{
            echo '{"status":"error","message":"Identical storage link already exists. Check the data."}';
        }
    }
    else{
        echo '{"status":"error","message":"No service matches. Check the data."}';
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
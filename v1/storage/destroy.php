<?php
header('Content-type: text/plain; charset=utf-8');
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["service_name"]) && isset($data["service_salt"]) && isset($data["id"])){
    $service_name = check_data($data["service_name"]);
    $service_salt = check_data($data["service_salt"]);
    $id = check_data($data["id"]);

    $sql_service_exists = $pdo->prepare("SELECT * FROM services WHERE name=? AND service_salt=?");
    $sql_service_exists->execute(array($service,$service_salt));
    if($sql_service_exists->fetchColumn() > 0){
        $sql = prepare("DELETE FROM active_storage WHERE id=? AND service=?");
        if($sql->execute(array($id,$service_name))){
            echo '{"status":"success","message":"Storage link successfully removed."}';
        }
        else{
            echo '{"status":"error","message":"Error when removing the storage link. Try again later."}';
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
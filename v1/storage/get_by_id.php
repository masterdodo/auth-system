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
        $sql1 = $pdo->prepare("SELECT * FROM active_storage WHERE id=? AND service=?");
        $sql1->execute(array($id,$service_name));
        $sql2 = $pdo->prepare("SELECT * FROM active_storage WHERE id=? AND service=?");
        $sql2->execute(array($id,$service_name));
        if($sql1->fetchColumn() > 0){
            $result = json_encode($sql2->fetchAll(PDO::FETCH_ASSOC),true);
            echo '{"status":"success","message":"Storage returned successfully.","data":"'.$result.'"}';
        }
        else{
            echo '{"status":"success","message":"No storage found for this id."}';
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
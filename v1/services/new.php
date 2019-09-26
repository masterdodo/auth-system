<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["name"]) && isset($data["key"])){
    $service_name = check_data($data["name"]);
    $admin_key = check_data($data["key"]);
    $sql_service_exists = $pdo->prepare("SELECT COUNT(*) FROM services WHERE name=?");
    $sql_service_exists->execute(array($service_name));
    if($sql_service_exists->fetchColumn() == 0){
        if($admin_key==="1308F771CFD56C24E90C731A4896DF27B59A8EB80A59BBC919AAB717696F6CE65D193E92CAEDE4592ED7698EAC7D18AF73136D9120EA09477A963D964FB0B427"){
            $service_code = $service_name .'_'. rand(100000,999999);
            $service_salt = password_hash($service_code,PASSWORD_DEFAULT);
            $sql = $pdo->prepare("INSERT INTO services(name, service_salt) VALUES(?,?)");
            if($sql->execute(array($service_name,$service_salt))){
                echo '{"status":"success","message":"Service added.", "data":{"name":"'.$service_name.'","salt":"'.$service_salt.'"}}';
            }
            else{
                echo '{"status":"error","message":"Service not added. Try again later."}';
            }
        }
        else{
            echo '{"status":"error","message":"Special value missing."}';
        }
    }
    else{
        echo '{"status":"error","message":"Service already exists."}';
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
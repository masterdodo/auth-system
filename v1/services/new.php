<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["name"]) && isset($data["key"])){
    if($data["key"]==="1308F771CFD56C24E90C731A4896DF27B59A8EB80A59BBC919AAB717696F6CE65D193E92CAEDE4592ED7698EAC7D18AF73136D9120EA09477A963D964FB0B427"){
        $service_code = check_data($data["name"]) .'_'. rand(100000,999999);
        $service_salt = password_hash($service_code,PASSWORD_DEFAULT);
        $sql = $pdo->prepare("INSERT INTO services(name, service_salt) VALUES(?,?)");
        if($sql->execute(array(check_data($data["name"]),$service_salt))){
            echo json_encode('{"status":"success","message":"Service added.", "data":"'.$service_salt.'"}',true);
        }
        else{
            echo json_encode('{"status":"error","message":"Service not added. Try again later."}',true);
        }
    }
    else{
        echo json_encode('{"status":"error","message":"Special value missing."}',true);
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
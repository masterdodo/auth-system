<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);

if(isset($data["id"])){
    $sql = $pdo->prepare("SELECT id,email,username,user_type,service,created_at,updated_at FROM users WHERE id=? LIMIT 1");
    if($sql->execute(array(check_data($data["id"])))){
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        echo json_encode('{"status":"success","message":"Users returned.","data":'.$result.'}',true);
    }
    else{
        echo json_encode('{"status":"error","message":"Wrong parameters passed."}',true);
    }
}
else if(isset($data["email"]) && isset($data["service"])){
    $sql = $pdo->prepare("SELECT id,email,username,user_type,service,created_at,updated_at FROM users WHERE email=? AND service=? LIMIT 1");
    if($sql->execute(array(check_data($data["email"]),check_data($data["service"])))){
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        echo json_encode('{"status":"success","message":"Users returned.","data":'.$result.'}',true);
    }
    else{
        echo json_encode('{"status":"error","message":"Wrong parameters passed."}',true);
    }
}
else {
    echo json_encode('{"status":"error","message":"Wrong parameters passed."}',true);
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
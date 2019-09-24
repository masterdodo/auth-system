<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["id"]) && isset($data["service"])){
    $sql = $pdo->prepare("DELETE FROM users WHERE id=? AND service=?");
    if($sql->execute(array(check_data($data["id"]),check_data($data["service"])))){
        echo json_encode('{"status":"success","message":"User removed."}',true);
    }
    else{
        echo json_encode('{"status":"error","message":"User removal failed."}',true);
    }
}
else if(isset($data["email"]) && isset($data["service"])){
    $sql = $pdo->prepare("DELETE FROM users WHERE email=? AND service=?");
    if($sql->execute(array(check_data($data["email"]),check_data($data["service"])))){
        echo json_encode('{"status":"success","message":"User removed."}',true);
    }
    else{
        echo json_encode('{"status":"error","message":"User removal failed."}',true);
    }
}
else {
    echo json_encode('{"status":"error","message":"Can\'t remove user with provided data."}',true);
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
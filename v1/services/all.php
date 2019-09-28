<?php
header('Content-type: text/plain; charset=utf-8');
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["key"])){
    $admin_key = check_data($data["key"]);
    if($admin_key==="1308F771CFD56C24E90C731A4896DF27B59A8EB80A59BBC919AAB717696F6CE65D193E92CAEDE4592ED7698EAC7D18AF73136D9120EA09477A963D964FB0B427"){
        $sql1 = $pdo->prepare("SELECT name, service_salt, created_at FROM services");
        $sql1->execute();
        $sql2 = $pdo->prepare("SELECT name, service_salt, created_at FROM services");
        $sql2->execute();
        if($sql1->fetchColumn() > 0){
            $result = json_encode($sql2->fetch(PDO::FETCH_ASSOC),true);
            echo '{"status":"success","message":"Services returned.", "data":'.$result.'}';
        }
        else{
            echo '{"status":"error","message":"Services not returned. Try again later."}';
        }
    }
    else{
        echo '{"status":"error","message":"Special value wrong."}';
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
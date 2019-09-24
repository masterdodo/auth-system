<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$password = check_data($data["password"]);
$service = check_data($data["service"]);

$sql_service = $pdo->prepare("SELECT name FROM services WHERE name = ?");
$sql_service->execute(array($service));
$service_exists = $sql_service->fetch(PDO::FETCH_ASSOC);

if($service_exists){
    if(isset($data["type"])){
        if($data["type"]=="email"){
            $email = check_data($data["email"]);
            $sql = $pdo->prepare("SELECT id,username,email,password,user_type,service FROM users WHERE email = ?");
            $sql->execute(array($email));
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            if($result){
                $hash = $result["password"];
                if(password_verify($password,$hash)){
                    if(password_needs_rehash($hash,PASSWORD_DEFAULT)){
                        $newhash = password_hash($password,PASSWORD_DEFAULT);
                        $sql_newhash = $pdo->prepare("UPDATE users SET password=? WHERE email=?");
                        if($sql_newhash->execute(array($newhash, $email))){
                            echo json_encode('{"status":"success", "message":"Successfully authenticated. Password secured to new encryption.","data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}',true);
                        }
                        else{
                            echo json_encode('{"status":"success", "message":"Successfully authenticated. Rehashing password to new encryption failed."}',true);
                        }
                    }
                    else{
                        echo json_encode('{"status":"success", "message":"Successfully authenticated."}',true);
                    }
                }
                else{
                    echo json_encode('{"status":"error", "message":"Wrong password."}',true);
                }
            }
            else{
                echo json_encode('{"status":"error", "message":"User doesn\'t exist. Check the email."}',true);
            }
        }
        else if($data["type"]=="username"){
            $username = check_data($data["username"]);
            $sql = $pdo->prepare("SELECT username,email,password,user_type,service FROM users WHERE username = ?");
            $sql->execute(array($username));
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            if($result){
                $hash = $result["password"];
                if(password_verify($password,$hash)){
                    if(password_needs_rehash($hash,PASSWORD_DEFAULT)){
                        $newhash = password_hash($password,PASSWORD_DEFAULT);
                        $sql_newhash = $pdo->prepare("UPDATE users SET password=? WHERE username=?");
                        if($sql_newhash->execute(array($newhash, $username))){
                            echo json_encode('{"status":"success", "message":"Successfully authenticated. Password secured to new encryption."}',true);
                        }
                        else{
                            echo json_encode('{"status":"success", "message":"Successfully authenticated. Rehashing password to new encryption failed."}',true);
                        }
                    }
                    else{
                        echo json_encode('{"status":"success", "message":"Successfully authenticated."}',true);
                    }
                }
                else{
                    echo json_encode('{"status":"error", "message":"Wrong password."}',true);
                }
            }
            else{
                echo json_encode('{"status":"error", "message":"User doesn\'t exist. Check the email."}',true);
            }
        }
        else{
            echo json_encode('{"status":"error", "message":"Unknown type."}',true);
        }
    }
    else{
        echo json_encode('{"status":"error", "message":"Type not specified."}',true);
    }
}
else{
    echo json_encode('{"status":"error", "message":"Specified service not registered. Please contact auth-system operator."}',true);
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
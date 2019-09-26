<?php
require_once '../../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(isset($data["password"]) && isset($data["service"]) && isset($data["service_salt"]) && isset($data["type"]) && (isset($data["email"]) || isset($data["username"]))){
    $password = check_data($data["password"]);
    $service = check_data($data["service"]);
    $service_salt = check_data($data["service_salt"]);
    $type = check_data($data["type"]);

    $sql_service = $pdo->prepare("SELECT COUNT(*) FROM services WHERE name = ? AND service_salt=?");
    $sql_service->execute(array($service,$service_salt));
    if($sql_service->fetchColumn() > 0){
        if($type=="email"){
            if(isset($data["email"])){
                $email = check_data($data["email"]);
                $sql_email1 = $pdo->prepare("SELECT id,username,email,password,user_type,service FROM users WHERE email = ? AND service=?");
                $sql_email1->execute(array($email,$service));
                $sql_email2 = $pdo->prepare("SELECT id,username,email,password,user_type,service FROM users WHERE email = ? AND service=?");
                $sql_email2->execute(array($email,$service));
                $result = $sql_email1->fetch(PDO::FETCH_ASSOC);
                if($sql_email2->fetchColumn() > 0){
                    $hash = $result["password"];
                    if(password_verify($password,$hash)){
                        if(password_needs_rehash($hash,PASSWORD_DEFAULT)){
                            $newhash = password_hash($password,PASSWORD_DEFAULT);
                            $sql_newhash = $pdo->prepare("UPDATE users SET password=? WHERE email=?");
                            if($sql_newhash->execute(array($newhash, $email))){
                                echo '{"status":"success", "message":"Successfully authenticated. Password secured to new encryption.", "data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}';
                            }
                            else{
                                echo '{"status":"success", "message":"Successfully authenticated. Rehashing password to new encryption failed.", "data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}';
                            }
                        }
                        else{
                            echo '{"status":"success", "message":"Successfully authenticated.", "data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}';
                        }
                    }
                    else{
                        echo '{"status":"error", "message":"Wrong password."}';
                    }
                }
                else{
                    echo '{"status":"error", "message":"User doesn\'t exist. Check the email."}';
                }
            }
            else{
                echo '{"status":"error", "message":"Email not set as defined in the type."}';
            }
        }
        else if($type=="username"){
            if(isset($data["username"])){
                $username = check_data($data["username"]);
                $sql_username1 = $pdo->prepare("SELECT username,email,password,user_type,service FROM users WHERE username = ? AND service=?");
                $sql_username1->execute(array($username,$service));
                $sql_username2 = $pdo->prepare("SELECT username,email,password,user_type,service FROM users WHERE username = ? AND service=?");
                $sql_username2->execute(array($username,$service));
                $result = $sql_username1->fetch(PDO::FETCH_ASSOC);
                if($sql_username2->fetchColumn() > 0){
                    $hash = $result["password"];
                    if(password_verify($password,$hash)){
                        if(password_needs_rehash($hash,PASSWORD_DEFAULT)){
                            $newhash = password_hash($password,PASSWORD_DEFAULT);
                            $sql_newhash = $pdo->prepare("UPDATE users SET password=? WHERE username=?");
                            if($sql_newhash->execute(array($newhash, $username))){
                                echo '{"status":"success", "message":"Successfully authenticated. Password secured to new encryption.", "data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}';
                            }
                            else{
                                echo '{"status":"success", "message":"Successfully authenticated. Rehashing password to new encryption failed.", "data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}';
                            }
                        }
                        else{
                            echo '{"status":"success", "message":"Successfully authenticated.", "data":{"id":"'.$result["id"].'","email":"'.$result["email"].'","username":"'.$result["username"].'","user_type":"'.$result["user_type"].'"}}';
                        }
                    }
                    else{
                        echo '{"status":"error", "message":"Wrong password."}';
                    }
                }
                else{
                    echo '{"status":"error", "message":"User doesn\'t exist. Check the email."}';
                }
            }
            else{
                echo '{"status":"error", "message":"Username not set as defined in the type."}';
            }
        }
        else{
            echo '{"status":"error", "message":"Unknown type."}';
        }
    }
    else{
        echo '{"status":"error", "message":"Specified service not registered. Check the data."}';
    }
}
else{
    echo '{"status":"error", "message":"Can\'t operate with given parameters."}';
}

function check_data($entry){
    $entry = trim($entry);
    $entry = stripslashes($entry);
    $entry = htmlspecialchars($entry);
    return $entry;
}
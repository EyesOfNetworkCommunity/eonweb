<?php
// 
//  Change Password with new hash      
// 

include_once('/srv/eyesofnetwork/eonweb/include/config.php');
include_once('/srv/eyesofnetwork/eonweb/include/function.php');

global $database_cacti;
global $database_eonweb;


// EONWEB
$eonweb_users = sql($database_eonweb, "SELECT user_id, user_passwd, user_name FROM users");
if (count($eonweb_users) > 0){
    foreach($eonweb_users as $eonweb_user){
        $datas = array(
            password_hash($eonweb_user["user_passwd"], PASSWORD_DEFAULT),
            $eonweb_user["user_id"]
        );
        sql($database_eonweb, "UPDATE users SET user_passwd=? WHERE user_id=?", $datas);
    }
}


// CACTI
$cacti_users = sql($database_cacti, "SELECT id, password FROM user_auth");
if($cacti_users != NULL){
    foreach($cacti_users as $cacti_user){
        $datas = array(
            password_hash($cacti_user["password"], PASSWORD_DEFAULT),
            $cacti_user["id"]
        );
        sql($database_cacti, "UPDATE user_auth SET password=? WHERE id=?", $datas);
    }
}


// NAGVIS
$bdd_nagvis = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
$req = $bdd_nagvis->prepare("SELECT userId, password FROM users;");
$req->execute();
$nagvis_users = $req->fetchAll();

if($nagvis_users != NULL){
    foreach($nagvis_users as $nagvis_user){
        $new_pwd = password_hash($nagvis_user["password"], PASSWORD_DEFAULT);
        $req_nag = $bdd_nagvis->prepare("UPDATE users SET password=? WHERE userId=?;");
        $req_nag->execute(array($new_pwd, $nagvis_user["userId"]));
    }
}

?>
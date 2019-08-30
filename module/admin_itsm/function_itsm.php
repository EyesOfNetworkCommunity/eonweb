<?php
/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
# DEV NAME : Jeremy HOARAU
# VERSION : 5.2
# APPLICATION : eonweb for eyesofnetwork project
#
# LICENCE :
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
#########################################
*/
/**
 * Upload a file in the given destination. 
 * /!\ The destination must have the correct autorisation. /!\
 * 
 * @return boolean
 */
function upload_file($url, $file, $dir="uploaded_file"){
    $old_file = get_itsm_file($url);
    if($old_file != false && file_exists($old_file["file_name"])){
        unlink($old_file["file_name"]);
    }

    if(isset($file)){
        $target_file = __DIR__."/".$dir."/".basename($file["name"]);
        
        if(move_uploaded_file($file["tmp_name"], $target_file)){

            return true;
        }
        return false;
    }
    
    return false;
}

/**
 * Verify if the given file contains the variable allowing the program to 
 * send to the external ticketing tools a personalized tickets.
 * @return Boolean 
 */
function verify_format($text){
    preg_match('/%DETAIL%|%DESCRIPTION%/', $text, $matches);
    if(count($matches) >= 1 ){
        return true;
    }else return false;
}


// ================== SQL FUNCTION =================== //
function insert_config_var($name,$value){
    global $database_eonweb;
    $var = get_config_var($name);
    $rq = "";
    if($var != false){
        $rq = 'UPDATE configs SET value="'.$value.'" WHERE name="'.$name.'"'; 
    }else{
        $rq = 'INSERT INTO configs VALUES("'.$name.'","'.$value.'")';
    }

    try{
        $res = sqlrequest("$database_eonweb",$rq);
        return $res;
    }catch(Exception $e) {
        return 'Exception reçue : '.$e->getMessage().'\n';
    }
}

function get_config_var($name){
    global $database_eonweb;
    $res = sqlrequest("$database_eonweb",'SELECT value FROM configs WHERE name="'.$name.'"');
    $val = false;
    if(mysqli_num_rows($res) == 1 ){
        $val = mysqli_result($res , 0);
    }
    return $val;
}





function change_itsm_state($state){
    if($state != get_config_var("itsm")){
        global $database_eonweb;
        sqlrequest("$database_eonweb",'UPDATE configs set value="'.$state.'" WHERE name="itsm"');
    }
    return get_itsm_state();
}

function get_itsm_state(){
    $state = get_config_var("itsm");
    if(!empty($state) && $state=="on"){
        return '<button class="btn btn-success" id="btn_activate" value="off">'.getLabel("label.admin_itsm.on").'</button>';
    }elseif((!empty($state) && $state=="off")){
        return '<button class="btn btn-danger" id="btn_activate" value="on">'.getLabel("label.admin_itsm.off").'</button>';
    }else{
        global $database_eonweb;
        sqlrequest("$database_eonweb",'INSERT INTO configs VALUES("itsm","off")');
        return '<button class="btn btn-danger" value="on" id="btn_activate">'.getLabel("label.admin_itsm.off").'</button>';
    }
}


/**
 * This function create the http request to the external server itsm used in include/classes/Custom.Action.class.php
 * ie : curl -v --header "Content-Type: text/xml;charset=UTF-8" --header "SOAPAction: mc_issue_add_CD74" --data @request-add.xml https://localhost/api/soap/mantisconnect.php
 */
function report_itsm($ged_type=NULL, $queue=NULL, $id_ged=NULL, $array_vars=array()){
    
    $itsmPeer   = new ItsmPeer();
    $itsmChilds = $itsmPeer->getItsmChilds();
    $previous   = false;
    foreach($itsmChilds as $child){
        $result = $child->execute_itsm($previous, $ged_type, $queue, $id_ged);
        if($result!=false ){
            if($result == true){
                $previous=false;
            }else{
                $previous=$result;
            }
        }else{
            return false;
        }
    }
    return true;
}

function get_champ_ged($champ, $ged_type, $queue, $id_ged){
    global $database_ged;
    $sql = "SELECT ".$champ." FROM ".$ged_type."_queue_".$queue." WHERE id = $id_ged";
    $result = sqlrequest($database_ged, $sql, false);
    $event = mysqli_fetch_assoc($result);
    return $event[$champ];
}

/**
 * @var headers ==> array
 * @var url ==> string 
 * @var file ==> string
 * @var type ==> "get" by default "post"
 * @var ssl ==> boolean false by default
 * 
 * @return curl result
 */
function curl_call($headers,$url,$file,$type="get",$ssl=false){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $ssl); // TODO create a variable in database
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    if($type=="post"){
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $file);
    }

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}



/**
 * 
 * This function format the data to become able to use them the same 
 * way as monitoring_ged. 
 * 
 */
function get_all_events(){
    global $database_ged;
    $events = array();
    $sql = "SELECT id FROM nagios_queue_active";
    $result = sqlrequest($database_ged, $sql);
    while ($row = mysqli_fetch_assoc($result)){
        array_push($events,$row["id"].":nagios");
    }
    $result = null;
    $sql = "SELECT id FROM snmptrap_queue_active";
    $result = sqlrequest($database_ged, $sql);
    while ($row = mysqli_fetch_assoc($result)){
        array_push($events,$row["id"].":snmptrap");
    }

    return $events;
}



?>

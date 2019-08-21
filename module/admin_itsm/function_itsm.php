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
function upload_file($file, $dir="uploaded_file"){
    $old_file = get_itsm_var("itsm_file");
    if(file_exists($old_file)){
        unlink($old_file);
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
function insert_itsm_var($name,$value){
    global $database_eonweb;
    $var = get_itsm_var($name);
    $rq = "";
    if($var != false){
        $rq ='UPDATE configs SET value="'.$value.'" WHERE name="'.$name.'"'; 
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

function get_itsm_var($name){
    global $database_eonweb;
    $res = sqlrequest("$database_eonweb",'SELECT value FROM configs WHERE name="'.$name.'"');
    $val = false;
    if(mysqli_num_rows($res) == 1 ){
        $val = mysqli_result($res , 0);
    }
    return $val;
}

function change_itsm_state($state){
    if($state != get_itsm_var("itsm")){
        global $database_eonweb;
        sqlrequest("$database_eonweb",'UPDATE configs set value="'.$state.'" WHERE name="itsm"');
    }
    return get_itsm_state();
}

function get_itsm_state(){
    $state = get_itsm_var("itsm");
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
 * This function create the http request to the external server itsm
 * ie : curl -v --header "Content-Type: text/xml;charset=UTF-8" --header "SOAPAction: mc_issue_add_CD74" --data @request-add.xml https://localhost/api/soap/mantisconnect.php
 */
function report_itsm($detail, $descr){
    $path       = get_itsm_var("itsm_file");
    $extension  = explode(".",basename($path))[1];
    $file       = file_get_contents($path);
    $url        = get_itsm_var("itsm_url");
    $header     = get_itsm_var("itsm_header");

    if(isset($extension)){
        $file = str_replace("%DETAIL%",$detail,$file);
        $file = str_replace("%DESCRIPTION%",$descr,$file);

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false); // TODO create a variable in database
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/'.$extension.';charset=UTF-8',$header));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $file );
        $result = curl_exec($ch);
        curl_close($ch);
        return $file;
    
    }else return false;
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
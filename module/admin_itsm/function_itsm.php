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
function upload_file($file, $dir="./uploaded_file/"){
    if(isset($file)){
        $target_file = $dir.basename($file["name"]);
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
    preg_match('/\$DETAIL\$|\$DESCRIPTION\$/', $text, $matches);
    if(count($matches) >= 1 ){
        return true;
    }else return false;
}


// ================== SQL FUNCTION =================== //
function insert_itsm_var($name,$value){
    global $database_eonweb;
    $var = get_itsm_var($name);
    $rq = "";
    if(!empty($var)){
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
    $res = mysqli_result(sqlrequest("$database_eonweb",'SELECT value FROM configs WHERE name="'.$name.'"'),0);
    return $res;
}



?>
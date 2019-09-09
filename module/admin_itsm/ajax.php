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

ini_set('display_errors','on');
error_reporting(E_ALL);
include_once("../../include/config.php");
include_once("../../include/function.php");
include_once("./function_itsm.php");
include("classes/Itsm.php");
include("classes/ItsmPeer.php");

if($_POST["action"] == "add_external_itsm"){
    $message ="<div id='log'>";
    $itsmPeer= new ItsmPeer();
    if(!empty($_POST["itsm_url_id"])){
        $itsm = $itsmPeer->getItsmById($_POST["itsm_url_id"]);
    }else{
        $itsm = $itsmPeer->getItsmByUrl($_POST["itsm_url"]);
        if($itsm == false){
            $itsm = new Itsm();
        }else{
            $message .= "<div class=\"alert alert-danger\" role=\"alert\">You try to create an Itsm with an url already used.</div>";
        }
    }

    if($itsm != false){
        $itsm->setItsm_type_request($_POST["itsm_type_request"]);

        if(!empty($_POST["itsm_url"])){
            $itsm->setItsm_url($_POST["itsm_url"]);
        }

        if(!empty($_POST["itsm_return_champ"])){
            $itsm->setItsm_return_champ($_POST["itsm_return_champ"]);
        }

        if(!empty($_POST["itsm_header"])){
            $newarray_header = array();
            foreach($_POST["itsm_header"] as $header){
                array_push($newarray_header,$header);
            }
            $itsm->setItsm_headers($newarray_header);
        }
        
        if(!empty($_POST["itsm_var"])){
            $newdict_var = array();
            foreach($_POST["itsm_var"] as $var){
                $newdict_var[$var["var_name"]] = $var["champ_ged_id"];
            } 
            $itsm->setItsm_vars($newdict_var);
        }

        if(!empty($_POST["itsm_parent"])){
            $itsm->setItsm_parent($_POST["itsm_parent"]);
        }

        if($_FILES["fileName"]["size"] > 0){
            // $contenus = file_get_contents($_FILES["fileName"]['tmp_name']);
            if(upload_file($_POST['itsm_url'],$_FILES["fileName"])){
                $message .= "<div class=\"alert alert-success\" role=\"alert\">File uploaded.</div>";
                $itsm->setItsm_file(__DIR__."/"."uploaded_file/".$_FILES["fileName"]["name"]);
            }else $message .= "<div class=\"alert alert-danger\" role=\"alert\">File failed to be upload, nothing else have been executed.</div>";

        }
        $nb_itsm=$itsmPeer->count_itsm();
        if($itsm->getItsm_order()==null){
            $itsm->setItsm_order(intval($nb_itsm)+1);
        }
        

        $id = $itsm->save();
        if($id > 0 ){
            $message .= "<div class=\"alert alert-success\" role=\"alert\">".$itsm->getItsm_url()." succesfully saved. Is id is : ".$id."</div>";
        }else {
            $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$_POST["itsm_url"]." failed to saved.</div>";
        }
    }

    $itsm_create = "false";
    $itsm_acquit = "false";
    if(!empty($_POST['itsm_create'])) $itsm_create = "true";
    if(!empty($_POST['itsm_acquit'])) $itsm_acquit = "true";

    if(insert_config_var("itsm_create",$itsm_create)){
        $message .= "<div class=\"alert alert-success\" role=\"alert\">".$itsm_create." create succesfully saved.</div>";
    }else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$itsm_create." create failed to be saved.</div>";   
    if(insert_config_var("itsm_acquit",$itsm_acquit)){
        $message .= "<div class=\"alert alert-success\" role=\"alert\">".$itsm_acquit." acquit succesfully saved.</div>";
    }else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$itsm_acquit." acquit failed to be saved.</div>";


    echo $message."</div>";
}else if ($_POST["action"] == "activate_external_itsm"){
    
    $result ="";
    $result = change_itsm_state($_POST["state"]);
    echo $result;

}else if ($_POST["action"] == "delete_header_itsm" ){
    $message ="<div id='log'>";
    $message .= "<div class=\"alert alert-success\" role=\"alert\">".delete_itsm_header($_POST["header_id"])." .</div>";
    echo $message."</div>";
}else if ($_POST["action"] == "add_empty_vars"){
    
    $itsm_champ_ged = (new ItsmPeer())->getListChampGed();
    $nb = $_POST["nb"];
    echo "<div class=\"form-group\">
            <label class=\"control-label col-sm-2\" for=\"row_var_".$nb."\"></label>
            <div class=\"col-sm-3\"> 
                <input type=\"text\" class=\"form-control itsm_var\" id=\"row_var_".$nb."\" name=\"itsm_var[add_".$nb."][var_name]\" placeholder=\"%DETAILS%\" >
            </div>
            <div class=\"col-sm-3\"> 
                <select class=\"form-control select_champ\" name=\"itsm_var[add_".$nb."][champ_ged_id]\" >
                ";
                
                foreach($itsm_champ_ged as $key=>$value){
                    echo "<option value=\"".$key."\">".$value."</option>";
                }
            
        echo " </select>
        </div>
            <div class=\"col-sm-2\"><button type=\"button\" class=\"btn btn-danger delete-var\" ><i class=\"fa fa-trash\"></i></button></div> 
        </div>";
}else if ($_POST["action"] == "delete_external_itsm"){
    $itsmPeer= new ItsmPeer();
    $itsm = $itsmPeer->getItsmById($_POST["itsm_id"]);
    $itsm->delete();
}else if ($_POST["action"] == "up_external_itsm"){
    $itsmPeer= new ItsmPeer();
    $itsm = $itsmPeer->getItsmById($_POST["itsm_id"]);
    $itsm->up();
}else if ($_POST["action"] == "down_external_itsm"){
    $itsmPeer= new ItsmPeer();
    $itsm = $itsmPeer->getItsmById($_POST["itsm_id"]);
    $itsm->down();
}else if ($_POST["action"] == "generate_itsm_request"){
    $itsmPeer= new ItsmPeer();
    $newarray_header = array();
    $newdict_var = array();
    $contenus ="";
    if(!empty($_POST["itsm_header"])){
        foreach($_POST["itsm_header"] as $header){
            array_push($newarray_header,$header);
        }
    }
    
    if(!empty($_POST["itsm_parent"])){
        $itsm = $itsmPeer->getItsmById($_POST["itsm_parent"]);
        $parent_value = $itsm->execute_itsm();
        $headers=array();
        foreach($newarray_header as $header){
            if(preg_match("%PARENT_VALUE%",$header)==1 && $parent_value != false){
                $header = str_replace("%PARENT_VALUE%",$parent_value, $header);
            } 
            array_push($headers,$header);
        }
        $newarray_header = $headers;
    }

    if($_FILES["fileName"]["size"] > 0){
        $contenus = file_get_contents($_FILES["fileName"]['tmp_name']);
    }else if (!empty($_POST["input_file_name"])){
        $contenus = file_get_contents($_POST["input_file_name"]);
    }

    $opt = "";
    $result = curl_call($newarray_header,$_POST["itsm_url"],$contenus,$_POST["itsm_type_request"]);
    $json_obj = json_decode($result,true);
    if(isset($json_obj)){
        foreach($json_obj as $key=>$value){
            $opt .= "<option value=\"".$key."\">";
        }
    }else{
        echo "<option value=\"ERREUR\">".$result."</option>";
    }
    echo "<option  >".$opt;
}

?>
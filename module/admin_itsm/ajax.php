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
        $itsm = new Itsm();
    }

    if($itsm != false){
        if(!empty($_POST["itsm_url"])){
            $itsm->setItsm_url($_POST["itsm_url"]);
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

        $id_test = $itsm->save();

        var_dump($id_test);
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
                <option ></option>";
                
                foreach($itsm_champ_ged as $key=>$value){
                    echo "<option value=\"".$key."\">".$value."</option>";
                }
            
        echo " </select>
        </div>
            <div class=\"col-sm-2\"><button type=\"button\" id=\"add_empty_var\" class=\"btn\"><i class=\"fa fa-plus\"></i></button></div>
        </div>";
}else if ($_POST["action"] == "delete_external_itsm"){
    $itsmPeer= new ItsmPeer();
    $itsm = $itsmPeer->getItsmById($_POST["itsm_id"]);
    $itsm->delete();
}

?>
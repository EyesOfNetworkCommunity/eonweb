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

include_once("../../header.php");
include_once("function_itsm.php");


$message ="<div id='log'>";
if(isset($_FILES["fileName"])){
    $old_file = basename(get_itsm_var("itsm_file"));
    if(isset($old_file) && $old_file == $_FILES["fileName"]["name"]){
        $message .= "<div class=\"alert alert-warning\" role=\"alert\">".$_FILES["fileName"]["name"]." a file whith this name already exist.</div>";
    }else{
        $contenus = file_get_contents($_FILES["fileName"]['tmp_name']);
        if(verify_format($contenus)){
            if(upload_file($_FILES["fileName"])){
                $message .= "<div class=\"alert alert-success\" role=\"alert\">File uploaded.</div>";
                if(insert_itsm_var("itsm_file",__DIR__."/"."uploaded_file/".$_FILES["fileName"]["name"])){
                    $message .= "<div class=\"alert alert-success\" role=\"alert\">".$_FILES["fileName"]["name"]." succesfully saved.</div>";
                }else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$_FILES["fileName"]["name"]." failed to be saved.</div>";
                
            }else $message .= "<div class=\"alert alert-danger\" role=\"alert\">File failed to be upload, nothing else have been executed.</div>";

        }else $message .= "<div class=\"alert alert-danger\" role=\"alert\">The File verification failed, please verify the conformity of the file you want to upload.</div>"; 
    }
}


if(insert_itsm_var("itsm_header",$_POST['itsm_header'])){
    $message .= "<div class=\"alert alert-success\" role=\"alert\">".$_POST['itsm_header']." succesfully saved.</div>";
}else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$_POST['itsm_header']." failed to be saved.</div>";
if(insert_itsm_var("itsm_url",$_POST['itsm_url'])){
    $message .= "<div class=\"alert alert-success\" role=\"alert\">".$_POST['itsm_url']." succesfully saved.</div>";
}else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$_POST['itsm_url']." failed to be saved.</div>";

$itsm_create = "false";
$itsm_acquit = "false";
if(isset($_POST['itsm_create'])) $itsm_create = "true";
if(isset($_POST['itsm_acquit'])) $itsm_acquit = "true";

if(insert_itsm_var("itsm_create",$itsm_create)){
    $message .= "<div class=\"alert alert-success\" role=\"alert\">".$itsm_create." create succesfully saved.</div>";
}else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$itsm_create." create failed to be saved.</div>";   
if(insert_itsm_var("itsm_acquit",$itsm_acquit)){
    $message .= "<div class=\"alert alert-success\" role=\"alert\">".$itsm_acquit." acquit succesfully saved.</div>";
}else $message .= "<div class=\"alert alert-danger\" role=\"alert\">".$itsm_acquit." acquit failed to be saved.</div>";


echo $message."</div>";

?>

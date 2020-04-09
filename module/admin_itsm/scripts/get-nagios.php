<?php
/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
# DEV NAME : Jeremy HOARAU
# VERSION : 5.2
# APPLICATION : eonweb for eyesofnetwork project
#
# DESCRIPTION : 
# Script php called by ged-nagios-host and ged-nagios-service
# to autmatize the creation and the acquitement when external 
# itsm tool his enable with the related parameter enable. 
# 
# /!\ WARNING /!\ 
# This command :
# "php /srv/eyesofnetwork/eonweb/module/admin_itsm/script/get-nagios.php"
# need to be added to those shell file to worked.
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

include_once(__DIR__."/../../../include/config.php");
include_once(__DIR__."/../../../include/function.php");
include_once(__DIR__."/../../../include/arrays.php");

include_once(__DIR__."/../function_itsm.php");

$itsm   = get_itsm_var("itsm");
$create = get_itsm_var("itsm_create");
$acquit = get_itsm_var("itsm_acquit");
$queue  = "active";
if($checkBoxNagios == NULL) {$checkBoxNagios = "false";}
if($itsm == "on"){
    if($create == "true"){
        $selected_events = get_all_events();
        if(!empty($selected_events)){
            $CustomActions->ged_acknowledge($selected_events, $queue);
            if($acquit == "true"){
                acknowledge($selected_events, $queue, $checkBoxNagios);
            }
        }else exit(1);
    }
}



?>
<?php
/*
#########################################
#
# Copyright (C) 2021 EyesOfNetwork Team
# DEV NAME : Julien Gonzalez
# VERSION : 6.0
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

include("../../header.php");
include("classes/ReportService.php");

if($_POST["action"] == "generateReport"){
    if(!empty($_POST["id"])){
        $report = ReportService::getById($_POST["id"]);
        if($report == null) {
            return false;
        }
        echo ReportService::reportToHTML($report);
    }
    return false;
}

if($_POST["action"] == "deleteReport"){
    if(!empty($_POST["id"])){
        $report = ReportService::getById($_POST["id"]);
        if($report == null) {
            return false;
        }
        return ReportService::delete($report);
    }
    return false;
}

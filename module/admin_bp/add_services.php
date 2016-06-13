<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.0
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
include("../../side.php");

$bp_name = $_GET['bp_name'];
$display_actually_bp = $_GET['display'];

try {
    $bdd = new PDO("mysql:host=$database_host;dbname=nagiosbp", $database_username, $database_password);
    }
catch(Exception $e) {
	echo "Connection failed: " . $e->getMessage();
	exit('Impossible de se connecter à la base de données.');
}

print "<div id=\"page-wrapper\">";
	print "<div class=\"col-lg-12\">";
		print "<h1 class=\"page-header bp_name\">Business Process : $bp_name</h1>";
    print "</div>";

	print "<form class=\"col-xs-6\">";

		print "<div class=\"row col-xs-8 form-group\">";
			if($display_actually_bp == 0){
				$disabled = "";
			}
			else{
				$disabled = "disabled";
			}
            print "<button type=\"button\" id=\"button_service\" class=\"btn btn-primary\" onclick=\"HideShowService();\" $disabled>";
                print "Adding Services";
            print "</button>";
        print "</div>";

		if($display_actually_bp > 0){
            $disabled = "";
        }
        else{
        	$disabled = "disabled";
        }

        print "<div class=\"row col-xs-8 form-group\">";
            print "<button type=\"button\" id=\"button_process\" class=\"btn btn-primary\" onclick=\"HideShowProcess();\" $disabled>";
                print "Adding Process";
            print "</button>";
        print "</div>";

        print "<div style=\"clear: both;\"></div>";

        print "<div>";
		print "<div id=\"container_service\" style=\"display:none\">";
			print "<div>";
				print "<div class=\"input-group col-xs-6\">";
	  				print "<span class=\"input-group-addon\" id=\"sizing-addon1\"><img src=\"./images/server.png\" height=\"20\" width=\"25\"></span>";
	  				print "<input type=\"text\" class=\"form-control\" id=\"host\" placeholder=\"Hostname\" aria-describedby=\"sizing-addon1\">";
				print "</div>";
			print "</div>";

			print "<br>";
			print "<div>";
	           	print "<div class=\"form-group\">";
	               	print "<label style=\"font-weight:lighter;font-size:16px;\" for=\"services\" class=\"col-xs-8 control-label pad-top text-primary\">Services linked to this host</label>";
				print "</div>";
			print "</div>";

			print "<div>";
	            print "<div class=\"form-group\">";
					print "<ul id=\"draggablePanelList\" class=\"list-unstyled\">";
					print "</ul>";
	            print "</div>";
	       	print "</div>";
        print "</div>";

		print "<div id=\"container_process\" style=\"display:none\">";
            print "<div class=\"form-group row\">";
                print "<label class=\"col-xs-3\" for=\"display\"> Display : </label>";
                print "<div class=\"col-xs-8\">";
                    print "<select class=\"form-control\" name=\"display\">";
                        print "<option> </option>";
                        print "<option>0</option>";
                        print "<option>1</option>";
                        print "<option>2</option>";
                        print "<option>3</option>";
                        print "<option>4</option>";
                        print "<option>5</option>";
                    print "</select>";
                print "</div>";
            print "</div>";
        print "</div>";
        print "</div>";

        print "<div class=\"row\">";
            print "<div class=\"form-group\">";
                print "<label style=\"font-weight:lighter;font-size:16px;\" id=\"process\" class=\"col-xs-8 control-label pad-top text-primary\">Process for display</label>";
            print "</div>";
        print "</div>";

        print "<div class=\"row\">";
            print "<div class=\"form-group\">";
                print "<ul id=\"draggablePanelListProcess\" class=\"list-unstyled\">";
                print "</ul>";
            print "</div>";
        print "</div>";

    print "</form>";

	print "<form id=\"form_drop\" class=\"form-horizontal col-xs-5 pull-right\" style=\"top:0px\">";
		$text_display = ($display_actually_bp > 0 ? "Process" : "Services");
		print "<label style=\"font-size:16px;\" for=\"services\" class=\"col-xs-8 control-label\">$text_display linked to BP $bp_name</label>";
		print "<br>";
		print "<div id=\"container-drop_zone\" class=\"pad-top container-drop_zone\">";

			if($display_actually_bp > 0){
				$sql = "select bp_link from bp_links where bp_name = '" . $bp_name . "'";
				$req = $bdd->query($sql);
				$count = 0;

				while($row = $req->fetch()){
               		$bp_name_linked = $row['bp_link'];
					print "<div id=\"$bp_name::--;;$bp_name_linked\" class=\"panel-body text-info well well-sm\" style=\"font-size:16px;\">$bp_name_linked<button type=\"button\" class=\"btn btn-danger pull-right\" onclick=\"DeleteService('$bp_name::--;;$bp_name_linked');\"><span class=\"glyphicon glyphicon-trash\"></span></button></div>";
					$count += 1;
				}
				if($count == 0){
					print "<div id=\"primary_drop_zone\" class=\"ui-widget-content panel panel-info\" style=\"width:300px;height:50px\"><div class=\"panel-body text-center\">Drop Element Here</div></div>";
				}
			}

			else{
				print "<div>";
				$old_host = "";
				$sql = "select host,service from bp_services where bp_name = '" . $bp_name . "' ORDER BY id";

				$req = $bdd->query($sql);
				while($row = $req->fetch()){
					$host = $row['host'];
					$service = $row['service'];
					if($host != $old_host){
						print "</div>";
						print "<div id=\"drop_zone::$host\" class=\"ui-widget-content panel panel-info\">";
						print "\n<div id=\"panel::$host\" class=\"panel-heading panel-title\">$host</div>";
					}

					print "<div id=\"$bp_name::$host;;$service\" class=\"panel-body text-info well well-sm\" style=\"font-size:16px;\">$service";
					if($service != 'Hoststatus'){
                    	print "<button type=\"button\" class=\"btn btn-danger pull-right\" onclick=\"DeleteService('$bp_name::$host;;$service');\"><span class=\"glyphicon glyphicon-trash\"></span></button>";
					}
					else{
						print "<button type=\"button\" class=\"btn btn-danger pull-right\" disabled><span class=\"glyphicon glyphicon-trash\"></span></button>";
                    }
                	print "</div>";

					$old_host = $host;
				}

				print "</div>"; //fermeture du div du host
				if($old_host == ""){ // ca signifie que aucun service n'est ajoute
					print "<div id=\"primary_drop_zone\" class=\"ui-widget-content panel panel-info\" style=\"width:300px;height:50px\">";
						print "<div class=\"panel-body text-center\">Drop Element Here</div>";
					print "</div>";
				}
			}
		print "</div>"; //fermeture du div container-drop_zone
		print "<br>";
		print "<button type=\"button\" class=\"btn btn-success btn-block\" onclick=\""; echo (($display_actually_bp == 0)?'ApplyService();':'ApplyProcess();');
		print "\">";
    		print "<span class=\"glyphicon glyphicon-ok\" style=\"color:#4f4;\"></span>Appliquer les modifications";
    	print "</button>";
	print "</form>";

include("../../footer.php");
?>

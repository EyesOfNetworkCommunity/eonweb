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

	print "<div class=\"row\">";
		print "<div class=\"col-lg-12\">";
			print "<h1 class=\"page-header bp_name\">".getLabel("label.admin_bp.business_process")." : $bp_name</h1>";
		print "</div>";
    print "</div>";

	print "<div class=\"row\">";
		print "<div class=\"col-md-6\">";
			print "<form onsubmit=\"return false;\">";

				if($display_actually_bp == 0) {	
					print "<div id=\"container_service\">";
						print "<label>".getLabel("label.host")."</label>";
						print "<div>";
							print "<div class=\"row col-md-12\">";
								print "<div class=\"input-group\">";
									print "<span class=\"input-group-addon\" id=\"sizing-addon1\"><img src=\"./images/server.png\" height=\"20\" width=\"25\" alt=\"server\"></span>";
									print "<input type=\"text\" class=\"form-control\" id=\"host\" placeholder=\"Hostname\" aria-describedby=\"sizing-addon1\">";
								print "</div>";
							print "</div>";
						print "</div>";

						print "<div class=\"row col-md-12\">";
							print "<div class=\"form-group\">";
								print "<label style=\"font-weight:lighter;font-size:16px;\" class=\"control-label pad-top text-primary\" id=\"process\"></label>";
							print "</div>";
						print "</div>";
						
						print "<div class=\"row col-md-12\">";
							print "<div class=\"form-group\">";
								print "<ul id=\"draggablePanelList\" class=\"list-unstyled\">";
								print "</ul>";
							print "</div>";
						print "</div>";
					print "</div>";
				}
				else {
					print "<div id=\"container_process\">";
						print "<label>".getLabel("label.admin_bp.display")."</label>";
						print "<div>";
							print "<div class=\"row col-md-12\">";
								print "<select class=\"form-control\" name=\"display\">";
									print "<option data-hidden=\"true\">".getLabel("label.admin_bp.select_display")."</option>";
									print "<option>0</option>";
									print "<option>1</option>";
									print "<option>2</option>";
									print "<option>3</option>";
									print "<option>4</option>";
									print "<option>5</option>";
								print "</select>";
							print "</div>";
						print "</div>";
						
						print "<div class=\"row col-md-12\">";
							print "<div class=\"form-group\">";
								print "<label style=\"font-weight:lighter;font-size:16px;\" class=\"control-label pad-top text-primary\" id=\"process\"></label>";
							print "</div>";
						print "</div>";
						
						print "<div class=\"row col-md-12\">";
							print "<div class=\"form-group\">";
								print "<ul id=\"draggablePanelListProcess\" class=\"list-unstyled\">";
								print "</ul>";
							print "</div>";
						print "</div>";
					print "</div>";
				}
		    print "</form>";
		print "</div>";

		print "<div class=\"col-md-6\">";
			print "<form id=\"form_drop\" class=\"form-horizontal\" style=\"top:0px\">";
				$text_display = ($display_actually_bp > 0 ? "Process" : "Services");
				print "<label>$text_display ".getLabel("label.admin_bp.linked_to_bp")." $bp_name</label>";
				print "<div id=\"container-drop_zone\" class=\"container-drop_zone\">";

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
							print "<div id=\"primary_drop_zone\" class=\"ui-widget-content panel panel-info\"><div class=\"panel-body text-center\">".getLabel("label.admin_bp.drop_here")."</div></div>";
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
								print "<button type=\"button\" class=\"btn btn-danger pull-right\" onclick=\"DeleteService('$bp_name::$host;;$service');\"><span class=\"glyphicon glyphicon-trash\"></span></button>";
		                    }
		                	print "</div>";

							$old_host = $host;
						}

						print "</div>"; //fermeture du div du host
						if($old_host == ""){ // ca signifie que aucun service n'est ajoute
							print "<div id=\"primary_drop_zone\" class=\"ui-widget-content panel panel-info\" style=\"height:50px\">";
								print "<div class=\"panel-body text-center\">".getLabel("label.admin_bp.drop_here")."</div>";
							print "</div>";
						}
					}
				print "</div>"; //fermeture du div container-drop_zone
				print "<br>";
				print "<div class=\"btn-group btn-group-justified\">";
				print "<a class=\"btn btn-success\" onclick=\""; echo (($display_actually_bp == 0)?'ApplyService();':'ApplyProcess();');
				print "\">";
		    	print getLabel("action.apply");
		    	print "</a>";
				print "<a class=\"btn btn-primary\" onclick=\"window.location = '/module/admin_bp/index.php';\">".getLabel("action.cancel")."</a>";
				print "</div>";
			print "</form>";
		print "</div>";
	print "</div>";

print "</div>";

include("../../footer.php");
?>

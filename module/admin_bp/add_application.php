<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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
?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_bp.title"); ?></h1>
		</div>
	</div>

    <div id="error-message"></div>
    
<?php
if(isset($_GET['bp_name'])){
    $bp_name = $_GET['bp_name'];
}

if(! empty($bp_name)){
	try {
    	$bdd = new PDO("mysql:host=$database_host;dbname=nagiosbp", $database_username, $database_password);
    }
	catch(Exception $e) {
    	echo "Connection failed: " . $e->getMessage();
    	exit('Impossible de se connecter à la base de données.');
	}

	$sql = "select * from bp where name = ?";    
    $req = $bdd->prepare($sql);
    $info = $req->execute(array($bp_name));
	$info = $req->fetch();
	$bp_desc = $info['description'];
	$bp_url = $info['url'];
	$bp_prior = $info['priority'];
	$bp_type = $info['type'];
	$bp_command = $info['command'];
	$bp_minvalue = $info['min_value'];
}

    print "<div class=\"panel panel-default\">"; // panel start here
        print "<div class=\"panel-heading\">";
            print getLabel("action.add_new_app");
        print "</div>";
        print "<div class=\"panel-body\">";
        	print "<form class=\"form-horizontal col-md-8 col-md-offset-2\">";
                print "<div class=\"row\">";
                	print "<div class=\"form-group\">";
                    	print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">".getLabel("label.admin_bp.unique_name")."</label>";
                        print "<div class=\"col-xs-8\">";
							print "<input type=\"hidden\" id=\"uniq_name_orig\" value=\""; echo (isset($bp_name)?$bp_name:'');
							print "\">";
                        	print "<input type=\"text\" class=\"form-control\" id=\"uniq_name\" onkeyup=\"this.value=this.value.replace(/[^éèàêâç0-9a-zA-Z-_ \/\*]/g,'')\" value=\""; echo (isset($bp_name)?$bp_name:'');
        					print "\">";
                        print "</div>";
                        print "<div class=\"control-label form-group col-xs-1\">";
                        	print "<span class=\"glyphicon glyphicon-asterisk\" style=\"font-size:10px;color:#707070;\"></span>";
                        print "</div>";
        			print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">".getLabel("label.admin_bp.process_name")."</label>";
                        print "<div class=\"col-xs-8\">";
                            print "<input type=\"text\" class=\"form-control\" id=\"process_name\" onkeyup=\"this.value=this.value.replace(/[^éèàêâç0-9a-zA-Z-_ \/\*]/g,'')\" value=\""; echo (isset($bp_desc)?$bp_desc:'');
        					print "\">";
                        print "</div>";
                        print "<div class=\"control-label form-group col-xs-1\">";
                            print "<span class=\"glyphicon glyphicon-asterisk\" style=\"font-size:10px;color:#707070;\"></span>";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">".getLabel("label.admin_bp.display")."</label>";
                        print "<div class=\"col-xs-8\">";
        					$disabled = "";
        					if(isset($bp_prior)){
        						if($bp_prior == 0){
        							$disabled = "disabled";
        						}
        					}
        			        
        					print "<select class=\"form-control\" name=\"display\" $disabled>";
        						print "<option>"; echo (isset($bp_prior)?$bp_prior: getLabel("label.none"));
        						print "</option>";
        						$list_display = array('0','1','2','3','4','5');
        						foreach($list_display as $display){
									if($bp_prior != 0 and $display == 0) {
										continue;
									}
        							if(isset($bp_prior)){
        								if($display != $bp_prior){
          									print "<option>$display</option>";
        								}
        							}
        							else{
        								print "<option>$display</option>";
        							}
        						}
        					print "</select>";
                        print "</div>";
                        print "<div class=\"control-label form-group col-xs-1\">";
                            print "<span class=\"glyphicon glyphicon-asterisk\" style=\"font-size:10px;color:#707070;\"></span>";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">Url</label>";
                        print "<div class=\"col-xs-8\">";
                            print "<input type=\"text\" class=\"form-control\" id=\"url\" value=\""; echo (isset($bp_url)?$bp_url:'');
        					print "\">";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">".getLabel("label.admin_bp.command")."</label>";
                        print "<div class=\"col-xs-8\">";
                            print "<input type=\"text\" class=\"form-control\" id=\"command\" value=\""; echo (isset($bp_command)?$bp_command:'');
        					print "\">";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">Type</label>";
                        print "<div class=\"col-xs-8\">";
                            print "<select class=\"form-control\" name=\"type\">";
        						if(isset($bp_type)){
        							print "<option>$bp_type</option>";
        						}

                                $list_type = array('ET','OU','MIN');
                                foreach($list_type as $type){
                                    if(isset($bp_type)){
                                        if($type != $bp_type){
                                            print "<option>$type</option>";
                                        }
                                    }
                                    else{
                                        print "<option>$type</option>";
                                    }
                                }
                            print "</select>";
                        print "</div>";
                        print "<div class=\"control-label form-group col-xs-1\">";
                            print "<span class=\"glyphicon glyphicon-asterisk\" style=\"font-size:10px;color:#707070;\"></span>";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\" id=\"container_select_minimum\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" class=\"col-xs-3 control-label\">".getLabel("label.admin_bp.min_value")."</label>";
                        print "<div class=\"col-xs-8\">";
                            print "<select class=\"form-control\" name=\"min_value\">";
        						print "<option>"; echo (isset($bp_minvalue)?$bp_minvalue: getLabel("label.none"));
                                print "</option>";
                                $list_minvalue = array('0','1','2','3','4','5','6','7','8','9');
                                foreach($list_minvalue as $minvalue){
                                    if(isset($bp_prior)){
                                        if($minvalue != $bp_minvalue){
                                            print "<option>$minvalue</option>";
                                        }
                                    }
                                    else{
                                        print "<option>$minvalue</option>";
                                    }
                                }
                            print "</select>";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\" style=\"margin:auto;\">";
                    print "<div class=\"form-group\">";
        				print "<button class=\"btn btn-primary col-xs-offset-3\" type=\"submit\" id=\"submit\" "; echo (isset($bp_name)?'':'disabled');
        				if(empty($bp_name)) {
							print ">".getLabel("action.create");
						}
						else {
							print ">".getLabel("action.update");
						}
            			print "</button>";
                        print " ";
                        print "<a href=\"index.php\" class=\"btn btn-default\" ";
                        print ">".getLabel("action.cancel");
                        print "</a>";
        			print "</div>";
        		print "</div>";

        	print "</form>";
        print "</div>";
    print "</div>";
	print "<p class=\"text-right\">";
        print "<label style=\"font-weight:lighter;\" class=\"control-label\">".getLabel("message.required_value1")." </label>";
        print " <span class=\"glyphicon glyphicon-asterisk\" style=\"font-size:10px;color:#707070;\"></span> ";
        print "<label style=\"font-weight:lighter;\" class=\"control-label\"> ".getLabel("message.required_value2")."</label>";
	print "</p>";

print "</div>";
include("../../footer.php");

?>

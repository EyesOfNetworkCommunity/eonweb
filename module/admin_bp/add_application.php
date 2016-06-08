<?php

include("../../include/config.php");
include("../../header.php");
include("../../side.php");

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

	$sql = "select * from bp where name = '" . $bp_name . "'";
	$req = $bdd->query($sql);
	$info = $req->fetch();
	$bp_desc = $info['description'];
	$bp_url = $info['url'];
	$bp_prior = $info['priority'];
	$bp_type = $info['type'];
	$bp_command = $info['command'];
	$bp_minvalue = $info['min_value'];
}

print "<div id=\"page-wrapper\" class=\"container-background\">";
	print "<p class=\"text-right\">";
        print "<label style=\"font-weight:lighter;\" class=\"control-label\">Les champs marqués d'une </label>";
        print " <span class=\"glyphicon glyphicon-asterisk\" style=\"font-size:10px;color:#707070;\"></span> ";
        print "<label style=\"font-weight:lighter;\" class=\"control-label\"> sont obligatoires</label>";
	print "</p>";

    print "<div class=\"panel panel-default\">"; // panel start here
        print "<div class=\"panel-heading\">";
            print "Create New Application";
        print "</div>";
        print "<div class=\"panel-body\">";
        	print "<form class=\"form-horizontal col-md-8 col-md-offset-2\">";
                print "<div class=\"row\">";
                	print "<div class=\"form-group\">";
                    	print "<label style=\"font-weight:normal\" for=\"uniq_name\" class=\"col-xs-3 control-label\">Uniq Name : </label>";
                        print "<div class=\"col-xs-8\">";
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
                        print "<label style=\"font-weight:normal\" for=\"process_name\" class=\"col-xs-3 control-label\">Process Name : </label>";
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
                        print "<label style=\"font-weight:normal\" for=\"display\" class=\"col-xs-3 control-label\">Display : </label>";
                        print "<div class=\"col-xs-8\">";
        					$disabled = "";
        					if(isset($bp_prior)){
        						if($bp_prior == 0){
        							$disabled = "disabled";
        						}
        					}
        			        
        					print "<select class=\"form-control\" name=\"display\" $disabled>";
        						print "<option>"; echo (isset($bp_prior)?$bp_prior:'None');
        						print "</option>";
        						$list_display = array('0','1','2','3','4','5');
        						foreach($list_display as $display){
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
                        print "<label style=\"font-weight:normal\" for=\"url\" class=\"col-xs-3 control-label\">Url : </label>";
                        print "<div class=\"col-xs-8\">";
                            print "<input type=\"text\" class=\"form-control\" id=\"url\" value=\""; echo (isset($bp_url)?$bp_url:'');
        					print "\">";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" for=\"command\" class=\"col-xs-3 control-label\">Command : </label>";
                        print "<div class=\"col-xs-8\">";
                            print "<input type=\"text\" class=\"form-control\" id=\"command\" value=\""; echo (isset($bp_command)?$bp_command:'');
        					print "\">";
                        print "</div>";
                    print "</div>";
                print "</div>";

        		print "<div class=\"row\">";
                    print "<div class=\"form-group\">";
                        print "<label style=\"font-weight:normal\" for=\"type\" class=\"col-xs-3 control-label\">Type : </label>";
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
                        print "<label style=\"font-weight:normal\" for=\"select_mininum\" class=\"col-xs-3 control-label\">Minimum Value : </label>";
                        print "<div class=\"col-xs-8\">";
                            print "<select class=\"form-control\" name=\"min_value\">";
        						print "<option>"; echo (isset($bp_minvalue)?$bp_minvalue:'None');
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
        				print ">Créer";
            			print "</button>";
                        print " ";
                        print "<a href=\"index.php\" class=\"btn btn-default\" ";
                        print ">Annuler";
                        print "</a>";
        			print "</div>";
        		print "</div>";

        	print "</form>";
        print "</div>";
    print "</div>";

include("../../footer.php");
?>

<script src="./bootstrap-select.min.js"></script>

<script>
$(document).ready(function() {
	$("input").change(function(){
		if($("#uniq_name").val() != "" && $("#process_name").val() != "" && $('select[name="type"]').val() != "" && $('select[name="display"]').val() != "None"){
			$('#submit').prop('disabled', false);
		}
		else{
			$('#submit').prop('disabled', true);
		}
		if($('select[name="type"]').val() == "MIN" && $('select[name="min_value"]').val() == ""){
            $('#submit').prop('disabled', true);
        }
	});

	$('select').change(function(){
        if($("#uniq_name").val() != "" && $("#process_name").val() != "" && $('select[name="type"]').val() != "" && $('select[name="display"]').val() != "None"){
            $('#submit').prop('disabled', false);
        }
        else{
            $('#submit').prop('disabled', true);
        }
		if($('select[name="type"]').val() == "MIN" && $('select[name="min_value"]').val() == ""){
			$('#submit').prop('disabled', true);
		}
    });

	$('#submit').click(function(event){
		event.preventDefault();

		var uniq_name = $("#uniq_name").val();
		var process_name = $("#process_name").val();
		var display = $('select[name="display"]').val();
		var url = $("#url").val();
		var command = $("#command").val();
		var type = $('select[name="type"]').val();
		var min_value = $('select[name="min_value"]').val();

		$.get(
			'php/function_bp.php',
			{
				action: "add_application",
				uniq_name: uniq_name,
				process_name: process_name,
				display: display,
				url: url,
				command: command,
				type: type,
				min_value: min_value
			},
			function return_value(value){
				setTimeout(function(){
                	$(location).attr('href',"add_services.php?bp_name=" + uniq_name + "&display=" + display + "");
                	},
                	500
            	);
			}
		);
	});
});

</script>

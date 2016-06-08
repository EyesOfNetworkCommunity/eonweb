<?php

include("../../include/config.php");
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
		print "<h1 class=\"page-header\">Business Process : $bp_name</h1>";
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
<link href="./auto_completion.css" rel="stylesheet">
<script src="./jquery.autocomplete.min.js"></script>
<script src="./bootstrap-select.min.js"></script>

<script>
$list_new_services = [];

$(document).ready(function () {
	var element_bp_name = $('.bp_name').html();
    var bp_name = element_bp_name.split(" : ")[1];
	var all_element_match = $('div[id^="' + bp_name + '::"');

	for(i=0;i<all_element_match.length;i++){
		var id_element = all_element_match[i].id;
		var information = id_element.split("::")[1];
		var host_name = information.split(";;")[0];
		var service_name = information.split(";;")[1];

		$list_new_services.push(host_name + "::" + service_name);
		console.log($list_new_services);
	}

	$('#host').autocomplete({
		serviceUrl: './php/auto_completion.php',
		dataType: 'json',
		// Nom de la table ou taper
		params: {table_name:'nagios_host'},
		onSelect: function(suggestion){
			$.get(
				'./php/function_bp.php',
				{
					action: 'list_services',
					host_name: suggestion['value']
				},
				function ReturnValue(list_services){
					$services = list_services['service'];
					$('#draggablePanelList').children().remove();
					for(i=0;i<$services.length;i++){
						var element = $('div[id$="::' + $("#host").val() + ';;' + $services[i] + '"]');
						console.log(element.length);
						console.log(element);

						if(! element.length){
							$('#draggablePanelList').append($('<div class="col-xs-6"><li id="' + $('#host').val() + '::' + $services[i] +'" class="draggable panel panel-warning ui-front" style=\"position:relative\"><div class="panel-heading">' + $services[i] + '</div></li></div>').draggable({ snap: true, revert: "invalid" }));
						}
					}
				},
				'json'
			);
		}
	});

	$('#container-drop_zone').droppable({
		hoverClass : "ui-state-hover",
    	drop : function(event, ui){
			$('#primary_drop_zone').remove();
			var element_bp_name = $('.bp_name').html();
            var bp_name = element_bp_name.split(" : ")[1];

			if($('#button_process').prop('disabled')){
				$element = $('div[id="drop_zone::' + $('#host').val() + '"]');
            	var id_panel = "" + bp_name + '::' + $("#host").val() + ';;' + ui.draggable.text() + "";

				if($element.length){
					$('<div id="' + id_panel + '" class="panel-body text-info well well-sm" onclick="DeleteService(id);"style=\"font-size:16px;\">' + ui.draggable.text() + '<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div>').appendTo($element);
				}

				else{
					var id_panel_hoststatus = "" + bp_name + '::' + $("#host").val() + ';;Hoststatus' + "";
					$list_new_services.push($('#host').val() + "::Hoststatus");

					$('#container-drop_zone').append('<div id="drop_zone::' + $("#host").val() + '" class="ui-widget-content panel panel-info"><div class="panel-heading panel-title" id="panel::' +$("#host").val()+ '">' + $("#host").val() + '</div><div id="' +id_panel_hoststatus+ '" class="panel-body text-primary well well-sm" style="font-size:16px;">Hoststatus<button type="button" class="btn btn-danger pull-right" disabled><span class="glyphicon glyphicon-trash"></span></button></div><div id="' +id_panel+ '" class="panel-body text-info well well-sm" onclick="DeleteService(id)" style="font-size:16px;">' + ui.draggable.text() + '<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div></div>');
				}
				$list_new_services.push($('#host').val() + "::" + ui.draggable.text());
				$('li[id$="' + ui.draggable.text() + '"]').remove();
			}

			else{
				var id_panel = "" + bp_name + '::--;;' + ui.draggable.text() + "";

				$('#container-drop_zone').append('<div id="' +id_panel+ '" class="panel-body text-info well well-sm" onclick="DeleteService(id)" style="font-size:16px;">' + ui.draggable.text() + '<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div>');

				$list_new_services.push("--::" + ui.draggable.text());
				$('li[id$="' + ui.draggable.text() + '"]').remove();
			}
    	}
	});

	$('select').change(function(){
		var nb_display = $('select[name="display"]').val();
		$('#process').html('Process for display ' + nb_display + '');

		$.get(
			'./php/function_bp.php',
            {
            	action: 'list_process',
				display: nb_display
			},
            function ReturnValue(list_process){
				$('#draggablePanelListProcess').children().remove();
				for(i=0;i<list_process.length;i++){
					$process = list_process[i]['name'];
					var element = $('div[id$=";;' + $process + '"]');

					if(! element.length){
						$('#draggablePanelListProcess').append($('<div class="col-xs-6"><li id="' + $process +'" class="draggable panel panel-warning ui-front" style=\"position:relative\"><div class="panel-heading">' + $process + '</div></li></div>').draggable({ snap: true, revert: "invalid" }));
					}
                }
            },
            'json'
        );
    });

});

$(document).scroll(function(){
	if($(document).scrollTop()>$('#form_drop').height()){
		console.log('ok');
		$('#form_drop').css('top',$(document).scrollTop() -$('#form_drop').height());
	}
});

function DeleteService(line_service){
    $('div[id="' + line_service +'"]').remove();
    var information = line_service.split("::");
    var global_bp = information[0];
    var host = information[1].split(";;")[0];
    var service = information[1].split(";;")[1];

	//on supprime le service dans la liste
	$list_new_services = jQuery.grep($list_new_services, function(value) {
  		return value != host + "::" + service;
		});

	console.log($list_new_services);

	var all_element_match = $('div[id^="' + global_bp + '::' + host + '"]');

	//si il ne reste plus que hoststatus on supprime tout
	if(all_element_match.length == 1){
		var dropzone_id = "drop_zone::" + host;
		$('div[id="' + dropzone_id + '"]').remove();

		//on supprime le service dans la liste
    	$list_new_services = jQuery.grep($list_new_services, function(value) {
        	return value != host + "::Hoststatus";
        });
	}

	//On verifie si il y a encore des elements dans la dropzone
	var element_dropzone = $('div[id^="' + global_bp + '::"]');
	if(! element_dropzone.length){
		$('#container-drop_zone').append('<div id="primary_drop_zone" class="ui-widget-content panel panel-info" style="width:300px;height:50px"><div class="panel-body text-center">Drop Element Here</div></div>');
	}
}

function ApplyService(){
	var element = $('h1.page-header').html();
	var bp_name = element.split(" : ")[1];
	console.log($list_new_services);
	$.get(
		'./php/function_bp.php',
		{
			action: 'add_services',
			bp_name: bp_name,
			new_services: $list_new_services
		},
		function ReturnError(values){
			console.log(values);
			setTimeout(function(){
				$(location).attr('href',"./index.php");
				},
				1000
			);
		}
	);
}

function ApplyProcess(){
    var element = $('h1.page-header').html();
    var bp_name = element.split(" : ")[1];
    $.get(
        './php/function_bp.php',
        {
            action: 'add_process',
            bp_name: bp_name,
            new_services: $list_new_services
        },
        function ReturnError(){
            setTimeout(function(){
                $(location).attr('href',"./index.php");
                },
                1000
            );
        }
    );
}

function HideShowService(){
	$service = $('#container_service');
	if($service.is(':hidden')){
		$service.css('display', 'block');
		$('#container_process').css('display', 'none');
	}
	else{
		$service.css('display', 'none');
	}
}

function HideShowProcess(){
    $service = $('#container_process');
    if($service.is(':hidden')){
        $service.css('display', 'block');
		$('#container_service').css('display', 'none');
    }
    else{
        $service.css('display', 'none');
    }
}
</script>

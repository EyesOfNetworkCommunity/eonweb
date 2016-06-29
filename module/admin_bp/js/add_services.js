/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Michael Aubertin
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

$list_new_services = [];

$(document).ready(function () {
	var element_bp_name = $('.page-header').html();
    var bp_name = element_bp_name.split(" : ")[1];
	var all_element_match = $('div[id^="' + bp_name + '::"');

	for(i=0;i<all_element_match.length;i++){
		var id_element = all_element_match[i].id;
		var information = id_element.split("::")[1];
		var host_name = information.split(";;")[0];
		var service_name = information.split(";;")[1];

		$list_new_services.push(host_name + "::" + service_name);
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

					// add Hoststatus if necessary
					$services.unshift("Hoststatus");

					$('#draggablePanelList').children().remove();
					$('#process').html(dictionnary["label.admin_bp.serv_linked_to_host"]+' ' + $('#host').val());
					for(i=0;i<$services.length;i++){
						var element = $('div[id$="::' + $("#host").val() + ';;' + $services[i] + '"]');
						
						if(! element.length){
							$('#draggablePanelList').append($('<li id="' + $('#host').val() + '::' + $services[i] +'" class="draggable col-xs-6 panel panel-warning ui-front" style=\"position:relative\"><div class="panel-heading row">' + $services[i] + '</div></li>').draggable({ snap: true, revert: "invalid" }));
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

			if($("#container_service").length){
				$element = $('div[id="drop_zone::' + $('#host').val() + '"]');
            	var id_panel = "" + bp_name + '::' + $("#host").val() + ';;' + ui.draggable.text() + "";
            	
				if($element.length){
					$('<div id="' + id_panel + '" class="panel-body text-info well well-sm" onclick="DeleteService(id);"style=\"font-size:16px;\">' + ui.draggable.text() + '<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div>').appendTo($element);
				}

				else{
					var id_panel_hoststatus = "" + bp_name + '::' + $("#host").val() + ';;Hoststatus' + "";
					
					
					$('li[id="'+$('#host').val()+'::Hoststatus"]').remove();
					if(ui.draggable.text() != "Hoststatus"){
						$('#container-drop_zone').append('<div id="drop_zone::' + $("#host").val() + '" class="ui-widget-content panel panel-info"><div class="panel-heading panel-title" id="panel::' +$("#host").val()+ '">' + $("#host").val() + '</div><div id="' +id_panel_hoststatus+ '" class="panel-body text-primary well well-sm" onclick="DeleteService(id);" style="font-size:16px;">Hoststatus<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div><div id="' +id_panel+ '" class="panel-body text-info well well-sm" onclick="DeleteService(id)" style="font-size:16px;">' + ui.draggable.text() + '<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div></div>');
						$list_new_services.push($('#host').val() + "::Hoststatus");
					} else {
						$('#container-drop_zone').append('<div id="drop_zone::' + $("#host").val() + '" class="ui-widget-content panel panel-info"><div class="panel-heading panel-title" id="panel::' +$("#host").val()+ '">' + $("#host").val() + '</div><div id="' +id_panel+ '" class="panel-body text-info well well-sm" onclick="DeleteService(id)" style="font-size:16px;">' + ui.draggable.text() + '<button type="button" class="btn btn-danger pull-right"><span class="glyphicon glyphicon-trash"></span></button></div></div>');
					}
				}
				$list_new_services.push($('#host').val() + "::" + ui.draggable.text());
				$('li[id="' + $('#host').val() + "::" + ui.draggable.text() + '"]').remove();
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
		var element_bp_name = $('.bp_name').html();
        var bp_name = element_bp_name.split(" : ")[1];
		var nb_display = $('select[name="display"]').val();
		if(nb_display % 1 === 0) {
			$('#process').html(dictionnary["label.admin_bp.process_for_display"]+' ' + nb_display + '');
		} else {
			$('#process').html('');
		}
		
		$.get(
			'./php/function_bp.php',
            {
            	action: 'list_process',
				bp_name: bp_name,
				display: nb_display
			},
            function ReturnValue(list_process){
				$('#draggablePanelListProcess').children().remove();
				for(i=0;i<list_process.length;i++){
					$process = list_process[i]['name'];
					var element = $('div[id$=";;' + $process + '"]');

					if(! element.length){
						$('#draggablePanelListProcess').append($('<li id="' + $process +'" class="draggable col-xs-6 panel panel-warning ui-front" style=\"position:relative\"><div class="panel-heading row">' + $process + '</div></li>').draggable({ snap: true, revert: "invalid" }));
					}
                }
            },
            'json'
        );
    });

});

$(document).scroll(function(){
	if($(document).scrollTop()>$('#form_drop').height()){
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

	var all_element_match = $('div[id^="' + global_bp + '::' + host + '"]');

	//On verifie si il y a encore des elements dans la dropzone
	var element_dropzone = $('div[id^="' + global_bp + '::"]');
	if(element_dropzone.length < 1){
		$('#container-drop_zone').html('<div id="primary_drop_zone" class="ui-widget-content panel panel-info" style="height:50px"><div class="panel-body text-center">'+dictionnary["label.admin_bp.drop_here"]+'</div></div>');
	}

	// SERVICE !!!
	if($("input#host").length > 0){
		$.get(
			'./php/function_bp.php',
			{
				action: 'list_services',
				host_name: $("input#host").val()
			},
			function ReturnValue(list_services){
				$services = list_services['service'];
				// add Hoststatus if necessary
				
				$('#draggablePanelList').children().remove();
				

				if($services !== undefined){
					$('#process').html(dictionnary["label.admin_bp.serv_linked_to_host"]+' ' + $('#host').val());
					$services.unshift("Hoststatus");
					for(i=0;i<$services.length;i++){
						var element = $('div[id$="::' + $("#host").val() + ';;' + $services[i] + '"]');
						
						if(! element.length){
							$('#draggablePanelList').append($('<li id="' + $('#host').val() + '::' + $services[i] +'" class="draggable col-xs-6 panel panel-warning ui-front" style=\"position:relative\"><div class="panel-heading row">' + $services[i] + '</div></li>').draggable({ snap: true, revert: "invalid" }));
						}
					}
				}
			},
			'json'
		);
	}
	// PROCESS !!!
	else {
		var element_bp_name = $('.bp_name').html();
	    var bp_name = element_bp_name.split(" : ")[1];
		var nb_display = $('select[name="display"]').val();
		if(nb_display % 1 === 0) {
			$('#process').html(dictionnary["label.admin_bp.process_for_display"]+' ' + nb_display + '');
		} else {
			$('#process').html('');
		}
		
		$.get(
			'./php/function_bp.php',
	        {
	        	action: 'list_process',
				bp_name: bp_name,
				display: nb_display
			},
	        function ReturnValue(list_process){
				$('#draggablePanelListProcess').children().remove();
				for(i=0;i<list_process.length;i++){
					$process = list_process[i]['name'];
					var element = $('div[id$=";;' + $process + '"]');

					if(! element.length){
						$('#draggablePanelListProcess').append($('<li id="' + $process +'" class="draggable col-xs-6 panel panel-warning ui-front" style=\"position:relative\"><div class="panel-heading row">' + $process + '</div></li>').draggable({ snap: true, revert: "invalid" }));
					}
	            }
	        },
	        'json'
	    );
	}
}

function ApplyService(){
	var element = $('h1.page-header').html();
	var bp_name = element.split(" : ")[1];
	
	$.get(
		'./php/function_bp.php',
		{
			action: 'add_services',
			bp_name: bp_name,
			new_services: $list_new_services
		},
		function ReturnError(values){
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

/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Michael Aubertin
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

		var uniq_name_orig = $("#uniq_name_orig").val();
		var uniq_name = $("#uniq_name").val();
		var process_name = $("#process_name").val();
		var display = $('select[name="display"]').val();
		var url = $("#url").val();
		var command = $("#command").val();
		var type = $('select[name="type"]').val();
		var min_value = $('select[name="min_value"]').val();

		// check if a BP already exists, if this is the case we stop here !
		if(uniq_name!=uniq_name_orig) {
			var bpExists = bpAlreadyExists(uniq_name);
			if(bpExists == true){
				var msg = dictionnary["message.admin_bp.bp_already_exists"];
				message(msg, 'warning', '#error-message');
				return;
			}
		}

		$.get(
			'php/function_bp.php',
			{
				action: "add_application",
				uniq_name_orig: uniq_name_orig,
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

function message(msg, type, target)
{
    var message = ''
        +'<p class="alert alert-dismissible alert-'+type+'">'
        +   '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
        +       '<span aria-hidden="true">&times;</span>'
        +   '</button>'
        +   '<i class="fa fa-warning"> </i> '+ msg
        +'</p>';
    $(target).html(message);
}

function bpAlreadyExists(bp_name)
{
	var tmp;

	$.ajax({
		url: 'php/function_bp.php',
		data: {
			action: 'check_app_exists',
			uniq_name: bp_name
		},
		async: false,
		success: function(response){
			tmp = false;
			if(response == 'true'){
				tmp = true;
			}
		}
	});

	return tmp;
}


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

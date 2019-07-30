/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
# DEV NAME : Jeremy HOARAU
# VERSION : 5.2
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


$(document).ready(function(){
	$("#input_file").prop('disabled', true);
	$("#itsm_header").prop('disabled', true);
	$("#itsm_url").prop('disabled', true);
	$("#itsm_create").prop('disabled', true);
	$("#itsm_acquit").prop('disabled', true);
	$("#btn_form").prop('disabled',true);
	
	$("#btn_unlock").click(function(){
		$("#input_file").prop('disabled', false);
		$("#itsm_header").prop('disabled', false);
		$("#itsm_url").prop('disabled', false);
		$("#itsm_create").prop('disabled', false);
		$("#itsm_acquit").prop('disabled', false);
		$("#btn_form").prop('disabled',false);
	});
	
	$("#input_file").change(function() {
		var fileName = $(this).val().split("\\").pop();
		$("#file_label").val(fileName);
	});

	$("#options_itsm").hover(function(){
		$("#info_options").show();
	},function(){
		$("#info_options").hide();
	});

	$("#btn_form").click(function(){
		var form = $('#myForm').get(0);
		var formData = new FormData(form);// get the form data
		$.ajax({
			type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url		    : 'add_external_itsm.php', // the url where we want to POST
			data		: formData, // our data object
			dataType	: 'html', // what type of data do we expect back from the server
			processData: false,
			contentType: false,
			beforeSend:function(){
				$('#result').html('Loading......');
			},
			success : function(result){ // success est toujours en place, bien sûr !
				$("#result").html(result);
				$("#input_file").prop('disabled', true);
				$("#itsm_header").prop('disabled', true);
				$("#itsm_url").prop('disabled', true);
				$("#itsm_create").prop('disabled', true);
				$("#itsm_acquit").prop('disabled', true);
				$("#btn_form").prop('disabled',true);
			},
			error : function(resultat, statut, erreur){
				$("#result").html(resultat);
			}
		});
	   
	});

});



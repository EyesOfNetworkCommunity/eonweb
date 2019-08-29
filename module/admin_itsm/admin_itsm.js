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
	$("#btn_import").hide();
	if($("#itsm_app_token").val() == ""){
		$("#token_app").hide();
	}

	if($("#itsm_user_token").val() == ""){
		$("#token_user").hide();
	}
	
	$("#input_file").prop('disabled', true);
	$(".itsm_header").prop('disabled', true);
	$(".select_champ").prop('disabled', true);
	$(".itsm_var").prop('disabled', true);
	$("#itsm_app_token").prop('disabled', true);
	$("#itsm_user_token").prop('disabled', true);
	$("#itsm_url").prop('disabled', true);
	$("#itsm_create").prop('disabled', true);
	$("#itsm_acquit").prop('disabled', true);
	$("#btn_form").prop('disabled',true);
	
	$("#btn_unlock").click(function(){
		$("#btn_import").show();
		$("#input_file").prop('disabled', false);
		$(".itsm_header").prop('disabled', false);
		$(".select_champ").prop('disabled', false);
		$(".itsm_var").prop('disabled', false);
		$("#itsm_url").prop('disabled', false);
		$("#itsm_app_token").prop('disabled', false);
		$("#itsm_user_token").prop('disabled', false);
		$("#itsm_create").prop('disabled', false);
		$("#itsm_acquit").prop('disabled', false);
		$("#btn_form").prop('disabled',false);
	});
	
	$("#input_file").change(function() {
		var fileName = $(this).val().split("\\").pop();
		$("#file_label").val(fileName);
		var ext = fileName.split('.').pop();
	});

	$("#options_itsm").hover(function(){
		$("#info_options").show();
	},function(){
		$("#info_options").hide();
	});
	
	var row_header = 1;
	$("#dynamic_fields_header").on("click","#add_empty_header", function(){
		$(this).remove();
		$("#dynamic_fields_header").append("<div class=\"form-group\"><label class=\"control-label col-sm-2\"></label><div class=\"col-sm-6\"><input type=\"text\" class=\"form-control\" id=\"row_header"+row_header+"\" name=\"itsm_header[]\" placeholder=\"SoapAction : mc...\" ></div><div class=\"col-sm-2\"><button type=\"button\" id=\"add_empty_header\" class=\"btn\"><i class=\"fa fa-plus\"></i></button></div></div>");
		row_header = row_header+1;
	});

	var row_var = 1;
	$("#dynamic_fields_var").on("click","#add_empty_var", function(){
		$(this).remove();
		var formData = new FormData();
		formData.append('nb', row_var);
		formData.append('action',"add_empty_vars");
		$.ajax({
			type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url		    : 'ajax.php', // the url where we want to POST
			dataType	: 'html',
			data		: formData,
			processData	: false,
			contentType	: false,
			success		:function(response) {
				$("#dynamic_fields_var").append(response);
				row_var = row_var+1;
			}
		});
	});

	$("#btn_activate").click(function(){
		var formData = new FormData();
		formData.append('state', $("#btn_activate").val());
		formData.append('action',"activate_external_itsm");
		$.ajax({
			type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url		    : 'ajax.php', // the url where we want to POST
			dataType	: 'html',
			data		: formData,
			processData	: false,
			contentType	: false,
			success		:function(response) {
				$("#result_state_itsm").html(response);
				location.reload();
			}
		});
	});

	$("#btn_form").click(function(){
		var form = $('#myForm').get(0);
		var formData = new FormData(form);// get the form data
		formData.append('action',"add_external_itsm");
		$.ajax({
			type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url		    : 'ajax.php', // the url where we want to POST
			data		: form_Data, // our data object
			dataType	: 'html', // what type of data do we expect back from the server
			processData: false,
			contentType: false,
			beforeSend:function(){
				$('#result').html('Loading......');
			},
			success : function(result){ // success est toujours en place, bien sûr !
				$("#result").html(result);
				//$("#result").html($("#log"));
				$("#btn_import").hide();
				$("#input_file").prop('disabled', true);
				$(".itsm_header").prop('disabled', true);
				$(".itsm_var").prop('disabled', true);
				$(".select_champ").prop('disabled', true);
				$("#itsm_app_token").prop('disabled', true);
				$("#itsm_user_token").prop('disabled', true);
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



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
function up_itsm(id){
	var formData = new FormData();
	formData.append('itsm_id', id);
	formData.append('action',"up_external_itsm");
	$.ajax({
		type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
		url		    : 'ajax.php', // the url where we want to POST
		data		: formData, // our data object
		dataType	: 'html', // what type of data do we expect back from the server
		processData: false,
		contentType: false,
		success : function(result){ // success est toujours en place, bien sûr !
			location.reload();
		},
		error : function(result, statut, erreur){
			$("#result").html('<div class="alert alert-danger" role="alert">'+erreur+'</div>');
		}
	});
}

function down_itsm(id){
	var formData = new FormData();
	formData.append('itsm_id', id);
	formData.append('action',"down_external_itsm");
	$.ajax({
		type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
		url		    : 'ajax.php', // the url where we want to POST
		data		: formData, // our data object
		dataType	: 'html', // what type of data do we expect back from the server
		processData: false,
		contentType: false,
		success : function(result){ // success est toujours en place, bien sûr !
			location.reload();
		},
		error : function(result, statut, erreur){
			$("#result").html('<div class="alert alert-danger" role="alert">'+erreur+'</div>');
		}
	});
}

function delete_itsm(id){
	var formData = new FormData();
	formData.append('itsm_id', id);
	formData.append('action',"delete_external_itsm");
	$.ajax({
		type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
		url		    : 'ajax.php', // the url where we want to POST
		data		: formData, // our data object
		dataType	: 'html', // what type of data do we expect back from the server
		processData: false,
		contentType: false,
		beforeSend:function(){
			$('#result').html('Loading......');
		},
		success : function(result){ // success est toujours en place, bien sûr !
			location.reload();
		},
		error : function(resultat, statut, erreur){
			$("#result").html(resultat);
		}
	});
}

$(document).ready(function(){
	

	$("#generate_list").click(function(){
		var form = $('#myForm').get(0);
		var formData = new FormData(form);// get the form data
		formData.append('action',"generate_itsm_request");
		$.ajax({
			type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url		    : 'ajax.php', // the url where we want to POST
			data		: formData, // our data object
			dataType	: 'html', // what type of data do we expect back from the server
			processData: false,
			contentType: false,
			success : function(result){ // success est toujours en place, bien sûr !
				var itsmreturn = document.getElementById("itsm_request_return_value");
				var children = itsmreturn.childNodes;
				while( children.length >= 3) {
					// La liste n'est pas une copie, elle sera donc réindexée à chaque appel
					itsmreturn.removeChild( children[2]);
				}
				
				
				 		
				  $("#itsm_request_return_value").append(result);

			}
		});
	});

	$("#dynamic_fields_header").on("click",".delete-header", function(){
		$(this).closest(".form-group").remove();
	});

	$("#dynamic_fields_var").on("click",".delete-var", function(){
		$(this).closest(".form-group").remove();
	});
	
	$("#input_file").change(function(){
		var fileName = $(this).val().split("\\").pop();
		$("#file_label").val(fileName);
		var ext = fileName.split('.').pop();
	});

	$("#what_auto_create_itsm").hover(function(){
		$("#auto_create_itsm").show();
	},function(){
		$("#auto_create_itsm").hide();
	});

	$("#what_auto_ack").hover(function(){
		$("#auto_ack").show();
	},function(){
		$("#auto_ack").hide();
	});

	$("#what_ack_thruk").hover(function(){
		$("#ack_thruk").show();
	},function(){
		$("#ack_thruk").hide();
	});
	
	var row_header = 1;
	$("#dynamic_fields_header").on("click","#add_empty_header", function(){
		$("#dynamic_fields_header").append("<div class=\"form-group\"><label class=\"control-label col-sm-2\"></label><div class=\"col-sm-6\"><input type=\"text\" class=\"form-control\" id=\"row_header"+row_header+"\" name=\"itsm_header[]\" placeholder=\"SoapAction : mc...\" ></div><div class=\"col-sm-1\"><button type=\"button\"  class=\"btn btn-danger delete-header\" ><i class=\"fa fa-trash\"></i></button></div></div>");
		row_header = row_header+1;
	});

	var row_var = 1;
	$("#dynamic_fields_var").on("click","#add_empty_var", function(){
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
			data		: formData, // our data object
			dataType	: 'html', // what type of data do we expect back from the server
			processData: false,
			contentType: false,
			beforeSend:function(){
				$('#result').html('Loading......');
			},
			success : function(result){ // success est toujours en place, bien sûr !
				$("#result").html(result);
			},
			error : function(resultat, statut, erreur){
				$("#result").html(resultat);
			}
		});
	   
	});

	$("#btn_config_itsm").click(function(){
        var form = $('#myForm_config_itsm').get(0);
        var formData = new FormData(form);// get the form data
        formData.append('action',"add_external_itsm_config");
        $.ajax({
            type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url         : 'ajax.php', // the url where we want to POST
            data        : formData, // our data object
            dataType    : 'html', // what type of data do we expect back from the server
            processData: false,
            contentType: false,
            beforeSend:function(){
                $('#result').html('Loading......');
            },
            success : function(result){ // success est toujours en place, bien sûr !
                $("#result").html(result);
            },
            error : function(resultat, statut, erreur){
                $("#result").html(resultat);
            }
        });
       
    });

});



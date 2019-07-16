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

$(document).ready(function () {
	$('.tree li.son').toggle();
	$('.tree li.end').toggle();
	$('.tree-line').click(function () {
		$(this).parent().parent().parent().children('.tree li.end').toggle(200);
		$(this).parent().parent().parent().children('.tree li.son').toggle(200);
	});
	$("[data-toggle=tooltip]").tooltip();

	$(document).keypress(function press_enter(e){
    	if(e.which == 13){
			e.preventDefault();
			findIt();
    	}
	});

	// event when we confirm the modal
	$('#modal-confirmation-apply-conf').on('click', function(){
		ApplyConfiguration();
	});
	$('#modal-confirmation-del-bp').on('click', function(){
		DeleteBP();
	});
});

$('#FindIt').on('click', findIt());

function findIt(){
	ShowAll();
	$search_text = $('#SearchFor').val();
	$('.tree-toggle').jmRemoveHighlight();
    $('.tree-toggle').jmHighlight($search_text);
	
	var offset = $("ul:contains('" + $search_text +"')").offset();
	//var offset = $('ul[id^="' + $search_text +'"]').offset();
	//console.log(offset);
	if(offset) {
		offset.left -= 20;
		offset.top -= 20;

		$('html, body').animate({
			scrollTop: offset.top,
			scrollLeft: offset.left
		});
	}
}

var n = 0;

function ShowAll(){
	$('.tree li.son').show();
	$('.tree li.end').show();
}

function HideAll(){
	$('.tree li.son').hide();
	$('.tree li.end').hide();
}

function AddingApplication(){
	$(location).attr('href',"./add_application.php");
}

function ShowModalDeleteBP(bp){
	$("#popup_confirmation .modal-title").html(dictionnary["action.delete"]);
	$("#popup_confirmation .modal-body").html(dictionnary["label.admin_bp.delete_bp"]+': ' + bp);
	$("#popup_confirmation button").hide();
	$("#modal-confirmation-del-bp").show();
	$("#action-cancel").show();
	$("#popup_confirmation").modal('show');
}

function DeleteBP(){
	var message = $(".modal-body").html();
	var message_parts = message.split(': ');
	var bp = message_parts[1];

	$('div[id="' + bp + '"]').remove();

	$.get(
		'./php/function_bp.php',
		{
			action: 'delete_bp',
			bp_name: bp
		},
		function ReturnAction(){
				location.reload();
		}
	);

	// and close the modal
	$("#popup_confirmation").modal('hide');
}

function ShowModalApplyConfiguration(){
	$("#popup_confirmation .modal-title").html(dictionnary["action.apply_conf"]);
	$("#popup_confirmation .modal-body").html(dictionnary["action.apply_conf"]+' ?');
	$("#popup_confirmation button").hide();
	$("#modal-confirmation-apply-conf").show();
	$("#action-cancel").show();
	$("#popup_confirmation").modal('show');
}

function ApplyConfiguration(){
	$.get(
		'./php/function_bp.php',
		{
			action: 'build_file'
		},
		function ReturnValue(value){
		}
	);

	// and close the modal
	$("#popup_confirmation").modal('hide');
}

function editApplication(bp_name){
	var url = "./add_application.php?bp_name=" + bp_name;
	$(location).attr('href',url);
}

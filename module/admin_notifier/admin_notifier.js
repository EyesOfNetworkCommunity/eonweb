/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Bastien PUJOS
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

// Button All/None for select
function all_none(object) {
	if(object.has('option').length > 0){
		object.find('option').remove();
	}
	else {
		object.find('option').remove();
		var o = new Option('*','*',true,true);
		object.append(o);
	}	
}

$(document).ready(function() {
	
	// Sort up/down rules
	var type = null;
	$(this).find("tbody>tr").find(".up").on('click',function(e) {
		type = "up";
	});
	$(this).find("tbody>tr").find(".down").on('click',function(e) {
		type = "down";
	});

	$(".sort").tableMove({
		after: function(tr){
			var select = $(tr).find("td label .checkbox").val();
			$.ajax({
				url : 'index.php',
				type: "POST",
				data: { actions: "move_rule", rule_id: select, move: type }
			});
		},
	})
		
	// Add
	$('#rule_contact_button').on('click',function(){
		var o = new Option($("#rule_contact1").val(),$("#rule_contact1").val(),true,true);
		$("#contacts").append(o);
	});
	$('#rule_host_button').on('click',function(){
		var o = new Option($("#rule_host1").val(),$("#rule_host1").val(),true,true);
		$("#host").append(o);
	});
	$('#rule_method_button').on('click',function(){
		var o = new Option($("#rule_method").val(),$("#rule_method").val(),true,true);
		$("#methods").append(o);
	});

	$('#rule_timeperiod_button').on('click', function(){
		var i = "";
		i += $('#selectHeureDeb1 option:selected').val();
		i += $('#selectMinuteDeb1 option:selected').val();
		i +="-";
		i += $('#selectHeureFin1 option:selected').val();
		i += $('#selectMinuteFin1 option:selected').val();
			
		var o = new Option(i,i,true,true);
		
		$("#timeperiods").append(o);
		$("#timeperiods option").prop("selected",true);
	});

	// Delete
	$('#rule_contact_button_del').on('click',function(){
		$("#contacts").find('option:selected').remove();
		$("#contacts").find("option").attr('selected','selected');
	});
	$('#rule_host_button_del').on('click',function(){
		$("#host").find('option:selected').remove();
		$("#host").find("option").attr('selected','selected');
	});
	$('#rule_method_button_del').on('click',function(){
		$("#methods").find('option:selected').remove();
		$("#methods").find("option").attr('selected','selected');
	});
	
	// All/None
	$('#contact_all').on('click',function(){
		all_none($('#contacts'));
	});
	
	$('#host_all').on('click',function(){
		all_none($('#host'));
	});

	$('#service_all').on('click',function(){
		all_none($('#services'));
	});
	
	$('#rule_timeperiod_button_del').on('click', function(){
		$("#timeperiods").find('option:selected').remove();
		$("#timeperiods option").prop("selected",true);
	});
	
	// All/None checkbox days
	$('#rule_all').on('click', function(){
		var t=0;
		var check = true;
		var c = document.getElementsByTagName('input');
		
		for (var i = 0; i < c.length; i++) {
			if (c[i].type == 'checkbox' && c[i].checked) { 
				t=t+1;
			}
		}
		if(t==7) { check=false; }
		for (var i = 0; i < c.length; i++) {
			if (c[i].type == 'checkbox') {
				c[i].checked = check;
			}
		}
	});

	// All/None checkbox states
	$('#rule_all1').on('click',function(){
		var t=0;
		var check = true;
		var c = document.getElementsByTagName('input');
		
		for (var i = 0; i < c.length; i++) {
			if (c[i].type == 'checkbox' && c[i].checked) { 
				t=t+1;
			}
		}
		if(t==3) { check=false; }
		for (var i = 0; i < c.length; i++) {
			if (c[i].type == 'checkbox') {
				c[i].checked = check;
			}
		}
	});
	
	// select All/None
	$('#notifperiod_all').on('click',function(){
		if($('#timeperiods').has('option').length > 0){
			$('#timeperiods').find('option').remove();
		}
		else {
			$('#timeperiods').find('option').remove();
			var o = new Option('*','*',true,true);
			$('#timeperiods').append(o);
		}	
	});
	
	$('#ajout').on('submit', 'form', function(e) {
		var self = this;
		e.preventDefault();
		$("#timeperiods option").prop("selected",true);
		self.submit();
	});
	
	// Service rule
	if(document.getElementById('rule_service_button')!=null){
		// Add
		$('#rule_service_button').on('click',function(){
			var o = new Option($("#rule_service2").val(),$("#rule_service2").val(),true,true);
			$("#services").append(o);
		});
		
		// Delete
		$('#rule_service_button_del').on('click',function(){
			$("#services").find('option:selected').remove();
			$("#services").find("option").attr('selected','selected');
		});
	}
});

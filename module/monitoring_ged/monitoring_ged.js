/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

function loadTable()
{
	// get all filter params to ajax
	var queue 	  = $("#queue").val();
	var type 	  = $("#type").val();
	var owner 	  = $("#owner").val();
	var filter 	  = $("#filter").val();
	var search 	  = $("#search").val();
	var daterange = $("#daterange").val();

	// get all states
	var ok = "";
	if($("#ok").prop("checked") == true){ ok = "on"; }
	var warning = "";
	if($("#warning").prop("checked") == true){ warning = "on"; }
	var critical = "";
	if($("#critical").prop("checked") == true){ critical = "on"; }
	var unknown = "";
	if($("#unknown").prop("checked") == true){ unknown = "on"; }
	
	$.ajax({
		url: "ajax.php",
		data:{
			queue: queue,
			type: type,
			owner: owner,
			filter: filter,
			search: search,
			ok: ok,
			warning: warning,
			critical: critical,
			unknown: unknown,
			daterange: daterange
		},
		beforeSend: function(){
			$("#result").empty();
			$("#loader").css("visibility", "visible");
		},
		success: function(response){
			$("#loader").css("visibility", "hidden");
			$("#result").html(response);
		}
	});
}

function removeModalState()
{
	if($(".modal-content").hasClass("panel-success")){$(".modal-content").removeClass("panel-success")}
	if($(".modal-content").hasClass("panel-warning")){$(".modal-content").removeClass("panel-warning")}
	if($(".modal-content").hasClass("panel-danger")){$(".modal-content").removeClass("panel-danger")}
	if($(".modal-content").hasClass("panel-info")){$(".modal-content").removeClass("panel-info")}
}

function changeModalState(e)
{
	event_infos = e.split(":");
	event_state = event_infos[2];

	removeModalState();

	$(".modal-content").addClass("panel-"+event_state);
}

$(document).ready(function(){
	$(".focus-to-search").on('change', function(){
		console.log($("#search"));
		$('#search').focus();
	});

	$(document).on('click', "tbody tr", function(){
		if($(this).hasClass("active")){
			$(this).removeClass("active");
			$(this).find("td input[type='checkbox']").prop("checked", false);
		} else {
			$(this).addClass("active");
			$(this).find("td input[type='checkbox']").prop("checked", true);
		}
	});

	// ajax when we submit filters form
	$("#events-filter").on("submit", function(event){
		// cancel form's submission
		event.preventDefault();
		// and refresh ajax table
		loadTable();
	});

	var event_index = 0;
	var selected_events = [];
	var event_state = "";
	// ajax when we execute an action into the ged table
	$(document).on("submit", "#ged-table", function(event){
		event.preventDefault();

		event_index = 0;
		var queue  = $("#queue").val();
		var action = $("#ged-action").val();

		// empty the event selected array before filling it
		selected_events = [];
		$("input:checkbox[name=events_selected]:checked").each(function(){
			var event_type = $(this).parent().parent().attr("name");
			var parent_row = $(this).parent().parent();
			var host_name = parent_row.find("td:first a").html();
			var service_name = parent_row.find("td:nth-child(2) a").html();
			
			if(parent_row.hasClass("success")){ event_state = "success"; }
			else if(parent_row.hasClass("warning")){ event_state = "warning"; }
			else if(parent_row.hasClass("danger")){ event_state = "danger"; }
			else if(parent_row.hasClass("info")){ event_state = "info"; }
			selected_events.push($(this).val()+":"+event_type+":"+event_state+":"+host_name+":"+service_name);
		});

		// stop here if no event selected
		if(selected_events.length < 1){
			return;
		}

		var events = [];
		if( action == "details" || action == "edit" ){
			events = selected_events[event_index];
		} else {
			events = selected_events;
		}

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: action,
				selected_events: events
			},
			beforeSend: function(){
				$(".modal-footer button").hide();

				// configure modal footer according to action selected
				switch(action){
					case "details":
						$("#details-next, #details-prev").show(); break;
					case "edit":
						$("#details-next, #details-prev, #edit-event, #edit-all-event").show(); break;
					default:
						$("#event-validation").show(); break;
				}

				$("#action-cancel").show();
				$("#event-message").empty();
			},
			success: function(response){
				if(action == "details" || action == "edit"){
					changeModalState(selected_events[event_index]);
					var event_infos = selected_events[event_index].split(":");
					var host_name = event_infos[3];
					var service_name = event_infos[4];
					$(".modal-title").html(host_name+" / "+service_name);
					$(".modal-body #content").html(response);
				} else {
					removeModalState();
					$(".modal-title").html(action);
					$(".modal-body #content").html("Are you sure ?");
				}
				
				$("#ged-modal").modal();
			}
		});
	});

	// click for the next event
	$(document).on("click", "#details-next", function(){
		event_index++;
		if(event_index == selected_events.length){
			event_index = 0;
		}
		
		var queue = $("#queue").val();
		var action= $("#ged-action").val();

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: action,
				selected_events: selected_events[event_index]
			},
			beforeSend: function(){
				$("#event-message").empty();
			},
			success: function(response){
				changeModalState(selected_events[event_index]);
				$(".modal-body #content").html(response);
			}
		});
	});

	// click for the previous event
	$(document).on("click", "#details-prev", function(){
		event_index--;
		if(event_index < 0){
			event_index = selected_events.length - 1;
		}

		var queue = $("#queue").val();
		var action= $("#ged-action").val();

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: action,
				selected_events: selected_events[event_index]
			},
			beforeSend: function(){
				$("#event-message").empty();
			},
			success: function(response){
				changeModalState(selected_events[event_index]);
				$(".modal-body #content").html(response);
			}
		});
	});

	// click to edit an event
	$(document).on("click", "#edit-event", function(){
		var queue = $("#queue").val();
		var comments = $("#event-comments").val();

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: "edit_event",
				selected_events: selected_events[event_index],
				comments: comments
			},
			success: function(response){
				$(".modal-body #event-message").html(response);
			}
		});
	});

	// click to edit ALL events
	$(document).on("click", "#edit-all-event", function(){
		var queue = $("#queue").val();
		var comments = $("#event-comments").val();

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: "edit_all_event",
				selected_events: selected_events,
				comments: comments
			},
			success: function(response){
				$(".modal-body #event-message").html(response);
			}
		});
	});

	// click to valid the own/disown/ack
	$(document).on("click", "#event-validation", function(){
		var global_action = $("#ged-action").val();

		
		console.log(global_action);
		console.log(selected_events);
		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: "active",
				action: "confirm",
				global_action: global_action,
				selected_events: selected_events
			},
			success: function(response){
				//console.log(response.length);
				if(response.length > 0){
					$("#messages").html(response);
					$("#result").empty();
					$("#ged-modal").modal('hide');
				} else {
					$("#ged-modal").modal('hide');
					loadTable();
				}
			}
		});
	});
});
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

function loadTable()
{
	// get all filter params to ajax
	var queue 	  = $("#queue").val();
	var type 	  = $("#type").val();
	var owner 	  = $("#owner").val();
	var filter 	  = $("#filter").val();
	var search 	  = $("#ged-search").val();
	var daterange = $("#daterange").val();

	var time_period = "";
	if(queue == "active"){
		time_period = $("#time").val();
	}
	var ack_time = "";
	if(queue == "history"){
		ack_time = $("#duration").val();
	}

	// get all states
	var ok = "";
	var warning = "";
	var critical = "";
	var unknown = "";
	var selected_states = $("#filter-state").val();
	if( $.inArray("ok", selected_states) >= 0 ){ ok = "on" }
	if( $.inArray("warning", selected_states) >= 0 ){ warning = "on" }
	if( $.inArray("critical", selected_states) >= 0 ){ critical = "on" }
	if( $.inArray("unknown", selected_states) >= 0 ){ unknown = "on" }
	
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
			daterange: daterange,
			time_period: time_period,
			ack_time: ack_time
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

function startTimer()
{
	var queue = $("#queue").val();
	if(queue == "active"){
		timer =  setInterval(loadTable, 60000);
	}
}

function stopTimer(timer)
{
	var queue = $("#queue").val();
	if(queue == "active"){
		clearInterval(timer);
	}
}

function previousNext(direction){
	if(direction == "next"){
		event_index++;
		if(event_index == selected_events.length){
			event_index = 0;
		}
	} else {
		event_index--;
		if(event_index < 0){
			event_index = selected_events.length - 1;
		}
	}

	var queue = $("#queue").val();
	var action= $("#ged-action").val();

	var event_infos = selected_events[event_index].split(":");
	var host_name = event_infos[3];
	var service_name = event_infos[4];
	
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
			$(".modal-title").html(host_name+" / "+service_name+ " ("+ (event_index + 1) +"/"+selected_events.length+")");
			$(".modal-body #content").html(response);
		}
	});
}

function searchAutocomplete(){
	var queue = $("#queue").val();
	var category = $("#filter").val();
	
	var datas;
	$.ajax({
		url: 'ged_actions.php',
		async: false,
		data: {
			action: 'advancedFilterSearch',
			filter: category,
			queue: queue
		},
		success: function(response){
			datas = response;
		}
	});
	$("#ged-search").attr('onFocus', '$(this).autocomplete({source: ' + datas + '})');
}

var event_index = 0;
var selected_events = [];
var event_state = "";
var global_action = "";
var timer;
var initialLoad = true;

$(document).ready(function(){
	var queue = $("#queue").val();

	if(queue == "active"){
		loadTable();
		startTimer();
	}
	
	$('#ged-modal').on('hidden.bs.modal', function (e) {
		startTimer();
	});

	$("#filter").on('change', function(){
		searchAutocomplete();
		$('#ged-search').val('');
	});
	
	$(".focus-to-search").on('change', function(){
		$('#ged-search').focus();
	});
	
	$('#collapseOne').on('shown.bs.collapse', function () {
		$('#ged-search').focus();	
	})
	
	$("#filter-selection").on('change', function(){
		var filter_selection = $(this).val();
		$.ajax({
			url: 'ged_actions.php',
			data: {
				action: 'changeGedFilter',
				filter_name: filter_selection
			},
			success: function(response){
				if(filter_selection == ""){
					$("#filter-link").html("none");
					$("#filter-link").attr("href", "/module/module_filters/index.php");
				} else {
					$("#filter-link").html(filter_selection);
					$("#filter-link").attr("href", "/module/module_filters/index.php?filter="+filter_selection);
				}
			}
		});
	});

	$(document).on('click', "#events-table tbody tr", function(){
		if($(this).find("td:first").hasClass("dataTables_empty") == false){
			if($(this).hasClass("child")){
				return;
			}
			if($(this).hasClass("active")){
				if($(this).next().hasClass("child") == false){
					$(this).removeClass("active");
				}
			} else {
				$(this).addClass("active");
			}
		}
	});

	// select all events in one shot
	$(document).on('click', "#select-all, #unselect-all", function(event){
		event.preventDefault();
		if($(this).attr('id') == "select-all"){
			$("#events-table tbody tr").each(function(){
				if($(this).hasClass("active") == false && $(this).hasClass("child") == false){
					$(this).addClass("active");
				}
			});
			// display the right button
			$("#select-all").addClass("hidden");
			$("#unselect-all").removeClass("hidden");
		} else if($(this).attr('id') == "unselect-all"){
			$("#events-table tbody tr").each(function(){
				if($(this).hasClass("active") == true && $(this).hasClass("child") == false){
					$(this).removeClass("active");
				}
			});
			// display the right button
			$("#unselect-all").addClass("hidden");
			$("#select-all").removeClass("hidden");
		}
		
	});

	// ajax when we submit filters form
	$("#events-filter").on("submit", function(event){
		// cancel form's submission
		event.preventDefault();
		// and refresh ajax table
		loadTable();
	});


	// ajax when we execute an action into the ged table
	$(document).on("submit", "#ged-table", function(event){
		event.preventDefault();

		event_index = 0;
		var queue  = $("#queue").val();
		var action = $("#ged-action").val();

		// empty the event selected array before filling it
		selected_events = [];
		$("#events-table tbody tr.active").each(function(){
			var event_type = $(this).attr("name");
			var parent_row = $(this);
			var host_name = parent_row.find("td.host a").html();
			var service_name = parent_row.find("td.service a").html();
			var event_id = parent_row.find("td:first input").val();
			
			if(service_name === undefined){
				service_name = parent_row.next().find("td ul li:first span.dtr-data a").html();
			}

			if(parent_row.hasClass("success")){ event_state = "success"; }
			else if(parent_row.hasClass("warning")){ event_state = "warning"; }
			else if(parent_row.hasClass("danger")){ event_state = "danger"; }
			else if(parent_row.hasClass("info")){ event_state = "info"; }
			selected_events.push(event_id+":"+event_type+":"+event_state+":"+host_name+":"+service_name);
		});

		// stop here if no event selected
		if(selected_events.length < 1){
			return;
		}

		stopTimer(timer);

		var events = [];
		if( action == "0" || action == "1" ){
			events = selected_events[event_index];
		} else {
			events = selected_events;
		}

		var action_name = "";
		if(action == 2){ action_name = "action.own"; }
		if(action == 3){ action_name = "action.disown"; }
		if(action == 4){ action_name = "action.ack"; }
		if(action == 5){ action_name = "action.delete"; }

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: action,
				selected_events: events
			},
			beforeSend: function(){
				$("#modal-nav, #edit-btns, #ack-btns, #event-validation").hide();
				
				// configure modal footer according to action selected
				switch(action){
					case "0":
						$("#modal-nav, #ack-btns").show(); break;
					case "1":
						$("#modal-nav, #edit-btns, #ack-btns").show(); break;
					default:
						$("#event-validation").show(); break;
				}

				$("#event-message").empty();
			},
			success: function(response){
				if(action == 0 || action == 1){
					changeModalState(selected_events[event_index]);
					var event_infos = selected_events[event_index].split(":");
					var host_name = event_infos[3];
					var service_name = event_infos[4];
					$(".modal-title").html(host_name+" / "+service_name+ " ("+ (event_index + 1) +"/"+selected_events.length+")");
					$(".modal-body #content").html(response);
				} else {
					removeModalState();
					$(".modal-title").html(dictionnary[action_name]);
					$(".modal-body #content").html(dictionnary["message.confirmation"]);
				}
				
				$("#ged-modal").modal();
			}
		});
	});

	// click for the next event
	$(document).on("click", "#details-next", function(){
		previousNext("next");
	});

	// click for the previous event
	$(document).on("click", "#details-prev", function(){
		previousNext("previous");
	});

	$(document).keyup(function(e) {
    switch(e.which) {
        case 37: // left
        	previousNext("previous");
        	break;
        case 39: // right
        	previousNext("next");
        	break;
        default: return; // exit this handler for other keys
    }
    e.preventDefault(); // prevent the default action (scroll / move caret)
});

	// click to edit an event
	$(document).on("click", "#edit-event, #edit-all-event", function(){
		var queue = $("#queue").val();
		var comments = $("#event-comments").val();
		var action = "";

		var events = [];
		if(this.id == "edit-event"){
			events = selected_events[event_index];
			action = "edit_event";
		} else {
			events = selected_events;
			action = "edit_all_event";
		}

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: action,
				selected_events: events,
				comments: comments
			},
			success: function(response){
				$(".modal-body #event-message").html(response);
				$("#ged-modal").modal('hide');
				loadTable();
			}
		});
	});

	$(document).on("click", "#ack-event, #ack-all-event", function(){
		global_action = this.id;
		$(".confirmation-modal-title").html(dictionnary["action.ack"]);
		$("#confirmation-modal").modal();
	});

	$(document).on("click", "#confirmation-event-validation", function(){
		var queue = $("#queue").val();

		var events = [];
		if(global_action == "ack-event"){
			events.push(selected_events[event_index]);
		} else {
			events = selected_events;
		}

		$.ajax({
			url: 'ged_actions.php',
			data: {
				queue: queue,
				action: "confirm",
				global_action: 4,
				selected_events: events
			},
			success: function(response){
				$("#confirmation-modal").modal('hide');
				$("#ged-modal").modal('hide');
				loadTable();
			}
		});
	});

	$(document).on("click", "#confirmation-action-cancel", function(){
		$("#confirmation-modal").modal('hide');
	});

	// click to valid the own/disown/ack/delete
	$(document).on("click", "#event-validation", function(){
		var queue = $("#queue").val();

		if(global_action.length == 0){
			global_action = $("#ged-action").val();
		}

		$.ajax({
			url: "ged_actions.php",
			data: {
				queue: queue,
				action: "confirm",
				global_action: global_action,
				selected_events: selected_events
			},
			success: function(response){
				global_action = "";

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
	
	// form search auto complete if initial page
	if(initialLoad) {
		searchAutocomplete();
	}
	initialLoad = false;
});

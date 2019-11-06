/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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

/**
 * Sidebar toggle 
 */
$(document).ready(function() {
	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
		$('#wrapper.toggled').find("#sidebar-wrapper").find(".collapse").collapse('hide');
	});
});

/**
 * Create the catcomplete
 */
$.widget( "custom.catcomplete", $.ui.autocomplete, {
	_create: function() {
		this._super();
		this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
    },
	_renderMenu: function( ul, items ) {
		var that = this,
		currentCategory = "";

		var cpt = 0;
		$.each( items, function( index, item ) {
			if ( item.category != currentCategory ) {
				if(cpt == 0){
					ul.append( "<li class='ui-autocomplete-category' style='font-weight: bold; padding-left: 3px;'>" + item.category + "</li>" );
				}
				else{
					ul.append( "<li class='ui-autocomplete-category' style='font-weight: bold; border-top: 1px solid #D8D8D8; padding-left: 3px;'>" + item.category + "</li>" );
				}
				currentCategory = item.category;
			}
			that._renderItemData( ul, item );
			cpt++;
		});
	}
});

/**
 * Get all infos to fill the catcomplete
 */
function my_ajax_search()
{
	$.ajax({
		url : "/thruk/cgi-bin/status.cgi",
		data : {
			format : "search",
		},
		success : function(response){
			var str = '$(this).catcomplete({delay: 0, source: [';
			$.each(response, function(i, item){
				if(i < response.length - 1){
					$.each(response[i].data, function(j, item){
						if(response[i].data[j].name) {
							item_name=response[i].data[j].name;
						} else {
							item_name=response[i].data[j];
						}
						str += '{label : "'+item_name+'", category : "'+response[i].name+'"},';
					});
				}
			});
			str = str.substring(0, str.length-1);
			str += '], select: function(event, ui) { $("#s0_value").val(ui.item.label); $("#sideMenuSearch").submit();} })';
				
			$("#s0_value").attr('onFocus', str);
		}
	});
}

/**
 * Redirect search to frame
 */
$("#sideMenuSearch").on("submit", function(event){
	// cancel form's submition
	event.preventDefault();
	
	// create the url to fill the <iframe>
	var target_url = $("#sideMenuSearch").attr("action");
	var param = $("#s0_value").val();
	target_url += encodeURIComponent(path_nagios_status+"?s0_op=~&s0_type=search&s0_value="+param);
	
	// load the <iframe>
	window.location = target_url;
});

/**
 * Reload thruk
 */
function check_reload() {
	jQuery.ajax({
		url: path_nagios_cgi+'/remote.cgi?startup',
		type: 'POST',
		success: function(){
			my_ajax_search();
		}
	});
}

if($("#sideMenuSearch").length > 0){
	check_reload();
}


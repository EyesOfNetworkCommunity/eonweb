/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION 4.2
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
	$.ui.dialog.defaults.bgiframe = true;
	$("#details").dialog({autoOpen:false, height: "500", width: "600", modal: true, resizable: false, draggable: false, title: "Event Details"});
	$("#comments").dialog({autoOpen:false, height: "350", width: "400", modal: true, resizable: false, draggable: false, title: "Comments"});
	$("#datepicker").daterangepicker({
		earliestDate: 'today',
		dateFormat: 'dd/mm/yy',	
	 	datepickerOptions: {
			changeMonth: true,
			changeYear: true
	 	}
	}); 
	setContextMenu();
        $("table")
        .tablesorter({widthFixed: false})
        .tablesorterPager({container: $("#pager")});
	$("#loading").hide();
	$("#showtable").show();
	return false;
});

function setContextMenu(){
        $("tr[name=status]").contextMenu("myMenu",{
                menuStyle: {
                        border: '1px solid #eee'
                },
                itemStyle: {
                        fontFamily: 'verdana',
                        fontSize: '10px',
                        textTransform: 'capitalize',
                        padding: '0px'
                },
                itemHoverStyle: {
                        cursor: 'pointer'
                },
                bindings: {
                        '0': function(t) {
				clearInterval(this.timer);
				$('#details').dialog('option', 'buttons', { 
					"Acknowledge": function() { ifChecked('4',t.id); $(this).dialog("close"); }, 
                        		"Cancel": function() { $(this).dialog("close"); }
				});
				$("#details").empty();
				$("#details").dialog('open');
				$("#details").html("<table>");
				$("#details table").append("<tr><td><h2>equipment</h2></td><td>"+$("input[name='"+t.id+"_equipment']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>host_alias</h2></td><td>"+$("input[name='"+t.id+"_host_alias']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>ip_address</h2></td><td>"+$("input[name='"+t.id+"_ip_address']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>service</h2></td><td>"+$("input[name='"+t.id+"_service']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>state</h2></td><td><img src='/images/states/s_"+$("input[name='"+t.id+"_statefull']").val()+".png'></td></tr>");
				$("#details table").append("<tr><td><h2>description</h2></td><td>"+$("input[name='"+t.id+"_description']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>occurences</h2></td><td>"+$("input[name='"+t.id+"_occurences']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>original-time</h2></td><td>"+$("input[name='"+t.id+"_original-time']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>last-time</h2></td><td>"+$("input[name='"+t.id+"_last-time']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>acknowledge-time</h2></td><td>"+$("input[name='"+t.id+"_acknowledge-time']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>hostgroups</h2></td><td>"+$("input[name='"+t.id+"_hostgroups']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>servicegroups</h2></td><td>"+$("input[name='"+t.id+"_servicegroups']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>source</h2></td><td>"+$("input[name='"+t.id+"_source']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>owner</h2></td><td>"+$("input[name='"+t.id+"_owner']").val()+"</td></tr>");
				$("#details table").append("<tr><td><h2>comments</h2></td><td>"+$("input[name='"+t.id+"_comments']").val()+"</td></tr>");
				$("#details").append("</table>");
				return false;
                        },
			'1': function(t) {
				clearInterval(this.timer);
				$('#comments').dialog('option', 'buttons', {	
			                        "Add": function() { ifChecked("1",t.id); $(this).dialog("close"); },
			                        "Acknowledge": function() { ifChecked("5",t.id); $(this).dialog("close"); },
                        			"Cancel": function() { $(this).dialog("close"); }
			        });
				$("#comments").empty();
				$("#comments").dialog('open');
				var comments;
				if($("input[name='actioncheck[]'][checked]").is(':checked')){
					comments="";
				}
				else {
					comments=$("input[name='"+t.id+"_comments']").val();
				}
				$("#comments").html("<textarea>"+comments+"</textarea><br><br>Add your comments here.");
				return false;
                        },
                        '2': function(t) {
				clearInterval(this.timer);
                                ifChecked('2',t.id);
				return false;
                        },
                        '3': function(t) {
				clearInterval(this.timer);
                                ifChecked('3',t.id);
				return false;
                        },
                        '4': function(t) {
				clearInterval(this.timer);
                                ifChecked('4',t.id);
				return false;
                        }
                }
        });
}

function submitFormAjax(what,line,comment)
{
      	txt = "";
      	txt = txt+$("form").serialize();
	
	if(what)
		txt = txt+"&action="+what;
	if(line)
		txt = txt+"&line="+line; 
	if(comment)
		txt = txt+"&comment="+comment; 

	$('#gedtable').ajaxStart(function(){ 
		$('#gedtable *').empty();  
		$('#loading').show();  
		return false;
	});

       	$('#gedtable').ajaxStop(function(){
                $('#loading').hide();
                $("#showtable").show();
		return false;
        });

	$.ajax({
        	type: "POST",
	        url: "ged_actions.php",
		cache: true,
	        data: txt,
	        success: function(res){
			if(!res)
				$('#gedtable').html('<ul class="ul"><li class="msg_title">Message EON - 0</li><li class="msg">EON - Standard Error : http or network must be dead</li></ul>');
			else 
				$('#gedtable').html(res);
			return false;
            	},
		error: function(){	
			$('#gedtable').html('<ul class="ul"><li class="msg_title">Message EON - 0</li><li class="msg">EON - Standard Error : http or network must be dead</li></ul>');
			return false;
		}
	}
	);

	txt="";
	return false;
}

function checkUncheckAll() {
	if($("input[name='actioncheck[]']").is(':checked')) {
		$("input[name='actioncheck[]']").removeAttr("checked");
		return false;
	}
	else { 
		$("input[name='actioncheck[]']").attr("checked","checked");
		return false;
	}
}

function ifChecked(binding,line){
	if(binding=="1") {
		comment=$("textarea").val();
	        if(!$("input[name='actioncheck[]']").is(':checked'))
                        submitFormAjax(binding,line,comment);
                else
                        submitFormAjax(binding,false,comment);
	}
	else if(confirm("Are you sure ?")){
		if(binding=="5"){
			comment=$("textarea").val();
        	        if(!$("input[name='actioncheck[]']").is(':checked'))
	                        submitFormAjax(binding,line,comment);
                	else
                        	submitFormAjax(binding,false,comment);
		}
		else if(!$("input[name='actioncheck[]']").is(':checked'))
                        submitFormAjax(binding,line);
        	else
                	submitFormAjax(binding);
	}
	else
		return 0;
}

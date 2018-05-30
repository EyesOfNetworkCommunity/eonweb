<?php
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

?>

<!-- BootstrapSelect JavaScript -->
<script src="/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

<script>
	// on page load
    $(document).ready(function() {
        $('#loading').hide();
		var filter = "<?php echo (isset($_GET["filter"])) ? $_GET["filter"] : false ?>";
		$("#filter_choice option[name='"+filter+"']").attr("selected","yes");
		updateFields("<?php echo isset($file_url) ? $file_url : false ?>");
    });
	// add form filter definition
	function addFormField() {
		var id = document.getElementById("id").value;
		var filters = <?php echo json_encode($array_ged_filters)?>;
		$("#allvalues").append('<div id="row'+id+'" class="row form-group"><div class="col-md-6"><select class="form-control" id="field'+id+'" name="field'+id+'">');
		for(var i in filters)
			$("#field"+id).append("<option value='"+i+"' name='"+i+"'>"+filters[i]+"</option>");
		$("#row"+id).append('</select></div><div class="col-md-6"><div class="input-group"><input id="value'+id+'" name="value'+id+'" class="form-control" type="text" placeholder="*'+dictionnary["action.search"]+'*" onFocus=\'$(this).autocomplete({ source: <?php echo get_host_list_from_nagios();?> })\' /><span class="input-group-btn"><button class="btn btn-danger" onClick="delFormField(\'#row'+id+'\');">'+dictionnary["action.delete"]+'</button></span></div></div>');
		$("#allvalues").append('</div>');
		id = (id - 1) + 2;
		document.getElementById("id").value = id;
	}
	// delete form filter definition
	function delFormField(id) {
		$(id).remove();
	}
	// update form filter definition
	function updateFields(file) {
		if($("#filter_choice").val()==""){
			$("#allvalues").empty();
			$("#filter_name").attr("value","");
			$("#filter_name").removeAttr("readonly");
			$("#filter_default").removeAttr("checked");
			return 0;
		}
	   	$.ajax({
			beforeSend: function(){
        		$('#loading').show();
			},
			type: "GET",
			url: file,
         	dataType: "xml",
			cache: false,
         	success: function(xml) {
        		$("#filter_name").attr("value",$("#filter_choice").val());
         		$(xml).find('default').each(function(){
            		if($(this).text() == $("#filter_choice").val()){
						$("#filter_default").prop("checked",true);
					}
					else{
						$("#filter_default").prop("checked", false);
					}
         		});
				$("#allvalues").empty();
				$(xml).find('filters').each(function(){
					if($(this).attr("name") == $("#filter_choice").val()){
						var nbr=0;
						var filters = <?php echo json_encode($array_ged_filters)?>;
						$(this).find('filter').each(function(){
							$("#allvalues").append("<div id='row"+ nbr +"' class='row form-group'>");
							$("#row"+nbr).append("<div class='col-md-6'><select class='form-control' id='field"+ nbr +"' name='field"+ nbr +"'>");
							for(var i in filters)
								$("#field"+nbr).append("<option value='"+i+"' name='"+i+"'>"+filters[i]+"</option>");
							$("#row"+nbr).append("</select></div>");
							$("#row"+nbr).append('<div class="col-md-6"><div class="input-group"><input id="value'+ nbr +'" name="value'+ nbr +'" class="form-control" type="text" value="'+ $(this).text() +'" placeholder="*'+dictionnary["action.search"]+'*" onFocus=\'$(this).autocomplete({ source: <?php echo get_host_list_from_nagios();?> })\'/><span class="input-group-btn"><button class="btn btn-danger" onClick=\'delFormField("#row'+ nbr +'");\'>'+dictionnary["action.delete"]+'</button></span></div></div>');
							$("#allvalues").append("</div>");
							$("#field"+nbr+" option[name="+$(this).attr("name")+"]").attr("selected","yes");
							nbr++;
						});
					}
				});
        		$('#loading').hide();
         	}
     	}); //close $.ajax(
    }
</script>

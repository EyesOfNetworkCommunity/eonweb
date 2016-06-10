<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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

include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.monitoring_ged.title"); ?></h1>
		</div>
	</div>

	<?php
	// Verify if user limitation
	if(isset($file)){
		$user_exist=mysqli_result(sqlrequest("$database_eonweb","SELECT count('user_name') from users where user_name='$user_name' and user_limitation='1';"),0);
		if($user_exist==0)
			message(0," : Not allowed","critical");
	}
	else {
		$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";
		$file_url="/cache/".$_COOKIE["user_name"]."-ged.xml";
		$user_id=false;
		$user_name=false;
	}

	// Open Xml function
	function openXml($file=false){
			$dom = new DOMDocument("1.0","UTF-8");
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
		if($file)
			$dom->load($file);
		return $dom;
	}

	// Define ged filters file if not exists
	if(!file_exists($file)){
		$dom = openXml();
		$root = $dom->createElement("ged");
			$root = $dom->appendChild($root);
		$default = $dom->createElement("default");
			$default = $root->appendChild($default);
			$xml=$dom->saveXML();
			$fp=@fopen($file,"w+");
			if(fwrite($fp,$xml))
					message(6," : Events filters file is created","ok");
			fclose($fp);
	}

	// Define ged filters options
	if(isset($_POST["save"])){
		$dom = openXml($file);
		$xpath = new DOMXPath($dom);
			$filter_name = retrieve_form_data("filter_name",NULL);
			$filter_choice = retrieve_form_data("filter_choice",NULL);
			$filter_default = retrieve_form_data("filter_default",NULL);
		if($filter_name=="")
			message(0," : Events filter name must be set","warning");
		else {
			// search if filter exists
			$root = $dom->getElementsByTagName("ged")->item(0);
			$records = $xpath->query("//ged/filters[@name='$filter_name']");

			// no filter name for creation
				if($records->length!=0 && ($filter_choice=="" or $filter_choice!=$filter_name)){
				message(0," : Events filter already exists","warning");
			}
			// creation or modification
			else {
				// filter renaming
				if($filter_choice!="" && $filter_choice!=$filter_name){
					$choice = $xpath->query("//ged/filters[@name='$filter_choice']");
								$root->removeChild($choice->item(0));
				}

				// modification if filter exists
							if($records->length!=0){
									$root->removeChild($records->item(0));
									$filters = $dom->createElement("filters");
									$filters = $root->appendChild($filters);
									$filters->setAttribute("name",$filter_name);
									$dom->save($file);
							}

							// creation if filter not exists
							else {
									$filters = $dom->createElement("filters");
									$filters = $root->appendChild($filters);
									$filters->setAttribute("name",$filter_name);
									$dom->save($file);
							}

				// search if filter is the default
					$default = $xpath->query("//ged[.//default='$filter_name']");

				// if default
				if($default->length!=0){
					if(!$filter_default){
							$root->removeChild($root->getElementsByTagName('default')->item(0));
							$default = $dom->createElement("default");
							$default = $root->appendChild($default);
						$default = $root->getElementsByTagName("default")->item(0);
							$dom->save($file);
					}
				}
				// if not default
				elseif($filter_default){
						$root->removeChild($root->getElementsByTagName('default')->item(0));
									$default = $dom->createElement("default");
									$default = $root->appendChild($default);
									$default = $root->getElementsByTagName("default")->item(0);
									$default->appendChild($dom->createTextNode($filter_name));
									$dom->save($file);
				}

				// set the filter definition
				$filter_name = retrieve_form_data("filter_name",NULL);
				$values=retrieve_form_data("id",NULL);
				for($i=0;$i<$values;$i++){
					$field=retrieve_form_data("field".$i,NULL);
					$value=retrieve_form_data("value".$i,NULL);
					if($value!=""){
						$filter=$filters->appendChild($dom->createElement("filter"));
						$filter->setAttribute("name",$field);
									$filter->appendChild($dom->createTextNode($value));
					}
				}
					$dom->save($file);
					message(6," : Events filter is set","ok");
			}
		}
	}

	// Delete selected filter
	if(isset($_POST["delete"])){
		$dom = openXml($file);
			$xpath = new DOMXPath($dom);
		$filter_name = retrieve_form_data("filter_name",NULL);
			$records = $xpath->query("//ged/filters[@name='$filter_name']");
		$root = $dom->getElementsByTagName("ged")->item(0);
		if($records->length!=0){
			$root->removeChild($records->item(0));
					$default = $xpath->query("//ged[.//default='$filter_name']");
					if($default->length!=0){
				$default->item(0)->removeChild($default->item(0)->getElementsByTagName('default')->item(0));
				$default = $dom->createElement("default");
					$default = $root->appendChild($default);
					}
			$dom->save($file);
					message(6," : Events filter deleted","ok");
		}
		else
			message(0," : Please select a filter","warning");
	}

	// Get filters définitions
	$dom = openXml($file);
	$filters=$dom->getElementsByTagName('filters');
	$filter=$dom->getElementsByTagName('filter');
	$filter_nbr=$filter->length;

	?>

	<div id="loading">
		<h2>Loading, please wait ...</h2><br>
	</div>

	<?php if($user_id) { ?>
	<form action="/module/admin_user/filters.php?user_id=<?php echo $user_id?>&user_name=<?php echo $user_name?>" name="option_events" method="post">
	<?php } else { ?>
	<form action="/module/monitoring_ged/index.php" name="option_events" method="post">
	<?php } ?>

	<div id="search" align="center">

	<table width="500px" class="table">
		<tr>
			<?php if(!$user_id) { ?>
					<td width="200px"><h2>EVENTS VIEWS</h2></td>
			<?php } else { ?>
					<td width="200px"><h2>USERS VIEWS</h2></td>
			<?php } ?>
					<td>
			<?php if(!$user_id) { ?>
			[ <a href="ged.php?q=active">active events</a> ]
			[ <a href="ged.php?q=history">history events</a> ]
			<?php } else { ?>
			[ <a href="/module/admin_user/index.php">all users</a> ]
			[ <a href="/module/admin_user/add_modify_user.php?user_id=<?php echo $user_id?>">user <?php echo $user_name?></a> ]
			<?php } ?>
			</td>
		</tr>
		<tr>
					<td width="200px"><h2>filter choice</h2></td>
					<td>
			<select id="filter_choice" name="filter_choice" style="width:100%;" onchange="updateFields('<?php echo $file_url?>')">
				<option value="" selected>create your filter</option>
				<?php
				foreach($filters as $filter_name)
					echo '<option name="'.$filter_name->getAttribute("name").'" value="'.$filter_name->getAttribute("name").'">'.$filter_name->getAttribute("name").'</option>';
				?>
			</select>
			</td>
			</tr>
		 <tr>
					<td width="200px"><h2>filter name</h2></td>
					<td><input type="text" id="filter_name" name="filter_name" class="value" style="width:100%;" /></td>
			</tr>
		<tr>
					<td width="200px"><h2>filter by default</h2></td>
					<td><input type="checkbox" id="filter_default" name="filter_default" class="checkbox" /></td>
			</tr>
		<tr>
			<td width="200px"><h2>events values</h2></td>
			<td>
			<input type="hidden" id="id" name="id" value="<?php echo $filter_nbr?>">
					[ <a href="#" onClick="addFormField();">add</a> ]
			<div id="allvalues">
			</div>
			</td>
		</tr>
	</table>

	<input type="submit" class="button" value="save" name="save"> 
	<input type="submit" class="button" value="delete" name="delete"> 
	<input type="submit" class="button" value="cancel" name="cancel"> 

	</div>

</div>

<?php include("../../footer.php"); ?>


<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>

<script type="text/javascript">
	// on page load
        $(document).ready(function() {
                $('#loading').hide();
		var filter = "<?php echo (isset($_GET["filter"])) ? $_GET["filter"] : false ?>";
		$("#filter_choice option[name='"+filter+"']").attr("selected","yes");
		updateFields("<?php echo $file_url?>");
        });
	// add form filter definition
	function addFormField() {
		var id = document.getElementById("id").value;
		var filters = <?php echo json_encode($array_ged_filters)?>;
		$("#allvalues").append('<div id="row'+id+'" style="margin-top:5px;"><select id="field'+id+'" name="field'+id+'">');
		for(var i in filters)
			$("#field"+id).append("<option>"+filters[i]+"</option>");
		$("#row"+id).append('</select>&nbsp;<input id="value'+id+'" name="value'+id+'" class="value" type="text" onFocus=\'$(this).autocomplete(<?php echo get_host_list_from_nagios();?>)\' /> [ <a href="#" onClick="delFormField(\'#row'+id+'\');">remove</a> ]');
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
						$("#filter_default").attr("checked","yes");
					}
					else{
						$("#filter_default").removeAttr("checked");
					}
                     		});
				$("#allvalues").empty();
				$(xml).find('filters').each(function(){
					if($(this).attr("name") == $("#filter_choice").val()){
						var nbr=0;
						var filters = <?php echo json_encode($array_ged_filters)?>;
						$(this).find('filter').each(function(){
							$("#allvalues").append("<div id='row"+ nbr +"' style='margin-top:5px;'>");
							$("#row"+nbr).append("<select id='field"+ nbr +"' name='field"+ nbr +"'>");
							for(var i in filters)
								$("#field"+nbr).append("<option name='"+filters[i]+"'>"+filters[i]+"</option>");
							$("#row"+nbr).append("</select>");
							$("#row"+nbr).append("&nbsp;<input id='value"+ nbr +"' name='value"+ nbr +"' class='value' type='text' value='"+ $(this).text() +"' />");
							$("#row"+nbr).append("&nbsp;[ <a href='#' onClick='delFormField(\"#row"+ nbr +"\");'>remove</a> ]");
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

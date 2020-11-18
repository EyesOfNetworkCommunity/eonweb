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
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

include("../../header.php");
include("../../side.php");
include("../monitoring_ged/ged_functions.php");

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.monitoring_ged.title"); ?></h1>
		</div>
	</div>

	<?php
	if(isset($_GET["user_id"]) && isset($_GET["user_name"])){
		$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : false;
		$user_name = isset($_GET["user_name"]) ? $_GET["user_name"] : false;
		$file="../../cache/".strtolower($user_name)."-ged.xml";
		$file_url="/cache/".strtolower($user_name)."-ged.xml";
	}
	
	// Verify if user limitation
	if(isset($file)){
		$user_exist=sql($database_eonweb,"SELECT count('user_name') from users where user_name=? and user_limitation='1'", array($user_name));
		if($user_exist[0][0]==0)
			message(0," : Not allowed","critical");
	}
	else {
		$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";
		$file_url="/cache/".$_COOKIE["user_name"]."-ged.xml";
		$user_id=false;
		$user_name=false;
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
		else if (preg_match('/<script>.*<\/script>/', $filter_name) > 0){
			message(0," : You have fallen into the dark side go away! ^^");	
		}else {
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
	$default = $dom->getElementsByTagName('default');

	?>

	<div id="loading">
		<h2><?php echo getLabel("message.loading"); ?></h2><br>
	</div>

	<?php if($user_id) { ?>
	<form action="/module/module_filters/index.php?user_id=<?php echo $user_id?>&user_name=<?php echo $user_name?>" name="option_events" method="post">
	<?php } else { ?>
	<form action="/module/module_filters/index.php" name="option_events" method="post">
	<?php } ?>
		<div class="row form-group">
			<?php
				if(!$user_id) { $label = "label.events_views"; }
				else { $label = "label.users_views"; }
			?>
			
			<label class="col-md-3"><?php echo getLabel($label); ?></label>
			<div class="col-md-9">
				<?php if(!$user_id) { ?>
				<a class="btn btn-primary" href="/module/monitoring_ged/index.php?q=active"><?php echo getLabel("label.act_event") ?></a>
				<a class="btn btn-primary" href="/module/monitoring_ged/index.php?q=history"><?php echo getLabel("label.his_event") ?></a>
				<?php } else { ?>
				<a class="btn btn-primary" href="/module/admin_user/index.php"><?php echo getLabel("label.all_users") ?></a>
				<a class="btn btn-primary" href="/module/admin_user/add_modify_user.php?user_id=<?php echo $user_id?>"><?php echo getLabel("label.user")." ".$user_name?></a>
				<?php } ?>
			</div>
		</div> <!-- !events/users views -->

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.filter_choice") ?></label>
			<div class="col-md-9">
				<select class="form-control" id="filter_choice" name="filter_choice" onchange="updateFields('<?php echo $file_url?>')">
					<option value="" selected><?php echo getLabel("label.create_filter"); ?></option>
					<?php
					foreach($filters as $filter_name)
						echo '<option id="'.$filter_name->getAttribute("name").'" value="'.$filter_name->getAttribute("name").'">'.$filter_name->getAttribute("name").'</option>';
					?>
				</select>
			</div>
		</div> <!-- !filter choice -->

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.filter_name") ?></label>
			<div class="col-md-9">
				<input type="text" id="filter_name" name="filter_name" class="form-control"/>
			</div>
		</div> <!-- !filter name -->

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.filter_by_default") ?></label>
			<div class="col-md-9">
				<input type="checkbox" id="filter_default" name="filter_default" class="checkbox" />
			</div>
		</div> <!-- !filter by default -->

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.event_values"); ?></label>
			<div class="col-md-9">
				<input type="hidden" id="id" name="id" value="<?php echo $filter_nbr?>">
				<a class="btn btn-success" href="#" onClick="addFormField();"><?php echo getLabel("action.add") ?></a>
				<br><br>
				<div id="allvalues">
				</div>
			</div>
		</div> <!-- !filter by default -->
		<div class="col-md-offset-3">
			<input type="submit" class="btn btn-primary" value="<?php echo getLabel('action.save') ?>" name="save"> 
			<input type="submit" class="btn btn-danger" value="<?php echo getLabel('action.delete') ?>" name="delete"> 
			<input type="submit" class="btn btn-default" value="<?php echo getLabel('action.cancel') ?>" name="cancel"> 
		</div>
	</form>
</div>

<?php include("../../footer.php"); ?>

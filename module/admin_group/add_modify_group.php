<?php
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
?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script src="../../js/jquery.js"></script>
	<link rel="stylesheet" href="../../css/jquery.autocomplete.css" type="text/css" />
	<script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>
	<?php include("../../include/include_module.php"); ?>
</head>

<body id='main'>

<script>
	function disable(){
		if(document.form_group.group_name.disabled){
			document.form_group.group_name.disabled=false;
			document.form_group.group_location.disabled=true;
		}
		else{
			document.form_group.group_name.disabled=true;
			document.form_group.group_location.disabled=false;
		}
	}
</script>

<?php
 
	// Check or uncheck input
	function check_uncheck($form_name,$name,$checked,$value)
	{
		global $defaulttab;
		if ($form_name=="tab_".$defaulttab){
			echo "<input type='hidden' name='$form_name' value='$value'>";
			echo "<input type='checkbox' class='checkbox' checked disabled='disabled'> $name <br>";
		}
		elseif ($checked)
			echo "<input type='checkbox' class='checkbox' name='$form_name' value='$value' checked> $name <br>";
		else
			echo "<input type='checkbox' class='checkbox' name='$form_name' value='$value'> $name <br>";
	}

	// Retrieve allowed menu for a group_id and build checkbox
	function retrieve_allowed_menu($count_item,$group_id)
	{
		global $xmlmenus;
		global $database_eonweb;
	
        	$xpath = new DOMXPath($xmlmenus);
	
		$sql_req = "SELECT ";
		for ($i=1;$i < $count_item;$i++)
		{
			$sql_req = "$sql_req tab_$i,";
		}
		$sql_req = "$sql_req tab_$i";
		
		$sql_req = "$sql_req FROM groupright";
		if ($group_id !="")
			$sql_req = "$sql_req WHERE group_id='$group_id'";

		$grp_right_result=sqlrequest("$database_eonweb","$sql_req");

		$check =1;
		if ($group_id !=null)
		{
			for ($i=1;$i < $count_item +1;$i++)
			{
				$menutabs = $xpath->query("//menutab[@id='$i']");
				check_uncheck("tab_$i",$menutabs->item(0)->getAttribute("name"),mysqli_result($grp_right_result,0,"tab_$i"),1);
			}
		}
		else
		{
			for ($i=1;$i < $count_item +1;$i++)
			{
				$menutabs = $xpath->query("//menutab[@id='$i']");
				check_uncheck("tab_$i",$menutabs->item(0)->getAttribute("name"),0,1);
			}
		}
	}
	
	// Retrieve Group Information
	function retrieve_group_info($group_id)
	{
		global $database_eonweb;
		return sqlrequest("$database_eonweb","SELECT group_name, group_descr, group_type, group_dn FROM groups WHERE group_id='$group_id'");
	}
	
	// Update Group Information & Right
	function update_group($count_menu_item,$group_id,$group_name,$group_descr,$group_type,$ldap_group_name,$message,$old_group=false)
	{
		global $database_eonweb;
		global $database_lilac;

		if(!$group_name)
		{
			$group_name = $ldap_group_name;
		}
		
		// Check if group exist
		if($group_name!=$old_group)
				$group_exist=mysqli_result(sqlrequest("$database_eonweb","SELECT count('group_name') from groups where group_name='$group_name';"),0);
		else
				$group_exist=0;

		// Check group descr
		if($group_descr=="")
				$group_descr=$group_name;
		
		if (($group_id != "") && ($group_id != null) && ($group_name != "") && ($group_name != null) && ($group_exist == 0 || $old_group==false))
		{
			for ($i=1;$i<$count_menu_item +1;$i++)
			{
				if (isset ($_POST["tab_$i"]))
					sqlrequest("$database_eonweb","UPDATE groupright set tab_$i='1' where group_id='$group_id'");
				else
					sqlrequest("$database_eonweb","UPDATE groupright set tab_$i='0' where group_id='$group_id'");
			}
			
			// get the DN of the ldap group !
			$group_dn = "";
			$group_ldap=sqlrequest("$database_eonweb","SELECT * from ldap_groups_extended where group_name='$group_name';");
			if(mysqli_num_rows($group_ldap) > 0){
				$group_dn = mysqli_result($group_ldap, "dn");
			}
			if($group_dn == ""){
				$group_type = 0;
			}
			
			// Update into eonweb
			sqlrequest("$database_eonweb","UPDATE groups set group_name='$group_name', group_descr='$group_descr', group_type='$group_type', group_dn='$group_dn' where group_id='$group_id'");
			// Update into lilac
			sqlrequest("$database_lilac", "UPDATE nagios_contact_group SET name='$group_name', alias='$group_descr' WHERE name='$old_group'");
			logging("admin_group","UPDATE : $group_id $group_name $group_descr");
			if($message){ message(8," : Group updated",'ok'); }
		}
		elseif($group_exist != 0)
            message(8," : Group $group_name already exists",'warning');
		else
			message(8," : Group name can not be empty",'warning');
	}
	
	// Insert Group Information
	function insert_group($group_name,$group_descr,$group_type,$ldap_group_name)
	{
		global $database_eonweb;
		global $database_lilac;
		$group_id=null;

		// Check if group exist
		if(!$group_name)
		{
			$group_name = $ldap_group_name;
		}
		$group_exist=mysqli_result(sqlrequest("$database_eonweb","SELECT count('group_name') from groups where group_name='$group_name';"),0);
		
		// Check group descr
		if($group_descr=="")
			$group_descr=$group_name;
		
		if (($group_name != "") && ($group_name != null) && ($group_exist == 0))
		{
			// get the DN of the ldap group !
			$group_dn = "";
			$group_ldap=sqlrequest("$database_eonweb","SELECT * from ldap_groups_extended where group_name='$group_name';");
			if(mysqli_num_rows($group_ldap) > 0){
				$group_dn = mysqli_result($group_ldap, "dn");
			}
			if($group_dn == ""){
				$group_type = 0;
			}
			
			// Insert into eonweb
			sqlrequest("$database_eonweb","INSERT INTO groups (group_name,group_descr,group_type,group_dn) VALUES('$group_name', '$group_descr', '$group_type', '$group_dn')");
			$group_id=mysqli_result(sqlrequest("$database_eonweb","SELECT group_id, group_descr FROM groups WHERE group_name='$group_name'"),0,"group_id");
			sqlrequest("$database_eonweb","INSERT INTO groupright (group_id) VALUES('$group_id')");
			// Insert into lilac
			sqlrequest("$database_lilac", "INSERT INTO nagios_contact_group (id, name, alias) VALUES('', '$group_name', '$group_descr')");
			logging("admin_group","INSERT : $group_id $group_name $group_descr $group_type");
			message(8," : Group inserted",'ok');
		}
 		elseif($group_exist != 0)
            message(8," : Group $group_name already exists",'warning');
		else
			message(8," : Group name can not be empty",'warning');
		
		return $group_id;
	}

	// Get menu length
	$menutabs=$xmlmenus->getElementsByTagName("menutab");
    $count_menu_item=$menutabs->length;

	// Get parameter
	$group_id = retrieve_form_data("group_id",null);
	$group_name = mysqli_result(sqlrequest("$database_eonweb","SELECT group_name FROM groups WHERE group_id='$group_id'"),0,"group_name");
	$group_descr = mysqli_result(sqlrequest("$database_eonweb","SELECT group_descr FROM groups WHERE group_id='$group_id'"),0,"group_descr");
	$group_type = mysqli_result(sqlrequest("$database_eonweb","SELECT group_type FROM groups WHERE group_id='$group_id'"),0,"group_type");
	$group_location = mysqli_result(sqlrequest("$database_eonweb","SELECT group_name FROM groups WHERE group_id='$group_id'"),0,"group_name");

	if ($group_id == null) 
	{
		echo "<h1>".$xmlmodules->getElementsByTagName("admin_group")->item(0)->getAttribute("new")."</h1>";
		if 	(isset($_POST['add']))
		{
			$group_name = retrieve_form_data("group_name",null);
        	$group_descr = retrieve_form_data("group_descr","");
			$group_type = retrieve_form_data("group_type", "");
			$ldap_group_name = retrieve_form_data("group_location", "");
			$group_id=insert_group($group_name,$group_descr,$group_type,$ldap_group_name);
			if ($group_id != null)
				update_group($count_menu_item,$group_id,$group_name,$group_descr,$group_type,$ldap_group_name, false);
		}
	}
	else
	{
		echo "<h1>".$xmlmodules->getElementsByTagName("admin_group")->item(0)->getAttribute("mod")."</h1>";
		if 	(isset($_POST['update']))
		{	
			$old_group = $group_name;
			$group_name = retrieve_form_data("group_name",null);
			$group_descr = retrieve_form_data("group_descr","");
			$group_type = retrieve_form_data("group_type", "");
			$ldap_group_name = retrieve_form_data("group_location", "");
			update_group($count_menu_item,$group_id,$group_name,$group_descr,$group_type,$ldap_group_name,true,$old_group);
		}
	}
	
	// Retrieve Group Information from database
	$group_name_descr = retrieve_group_info($group_id);
	$group_name=mysqli_result($group_name_descr,0,"group_name");
	$group_descr=mysqli_result($group_name_descr,0,"group_descr");
	$group_type=mysqli_result($group_name_descr,0,"group_type");
	$group_location=mysqli_result($group_name_descr,0,"group_name");
	if($group_type == 0){
		$group_location = "";
	}
?>

<form action='./add_modify_group.php' method='POST' name="form_group">
<input type='hidden' name='group_id' value='<?php echo $group_id?>'>
	<center>
		<table class="table">
			<tr>
				<td><h2>Group Name</h2></td>
				<td><?php echo "<input type='textbox' name='group_name' value='$group_name' ";
					if($group_type==1){echo "disabled='disabled'";}
					echo " />"; ?>
				</td>
			</tr>
			<tr>
				<td><h2>Ldap Group</h2></td>
				<td>
					<?php
						if($group_type=="1") $checked="checked='yes'";
						else $checked="";
						echo "<input type='checkbox' class='checkbox' name='group_type' value='1' $checked onclick='disable()'>";
					?>
				</td>
			</tr>
			<tr>
				<td><h2>Ldap DN</h2></td>
				<td>
					<?php
						echo "<input id='group_location' name='group_location' type='text' style='width:300px;' value='".htmlspecialchars($group_location, ENT_QUOTES)."' ";
						if($group_type==0){echo "disabled='disabled'";}
						echo " />";
					?>
					<script type="text/javascript">
					$(function() {
						$("#group_location").autocomplete("search.php?request=search_group");
						$("#group_location").result(function(event, data, formatted) {
							if (data)
								$(this).parent().find("input").val(data[1]);
						});
					});
					</script>
				</td>
			</tr>
			<tr>
				<td><h2>Group Description</h2></td>
				<td><?php echo "<input type='textbox' name='group_descr' value='$group_descr' size=50>";?></td>
			</tr>
			<tr>
				<td><h2><?php echo $group_name;?> rights</h2></td>
				<td><?php retrieve_allowed_menu($count_menu_item,$group_id);?></td>
			</tr>
			<tr>
				<td class="blanc" align="center" colspan="2">
					<?php
					if ($group_id !=null)
						echo "<input class='button' type='submit' name='update' value='update'>";
					else
						echo "<input class='button' type='submit' name='add' value='add'>";
					echo "&nbsp;<input class='button' type='button' name='back' value='back' onclick='location.href=\"index.php\"'>";
					?>
				</td>
			</tr>
		</table>
	</center>
</form>
</body>
</html>

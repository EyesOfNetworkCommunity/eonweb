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
        <?php include("../../include/include_module.php"); ?>
        <script type="text/javascript" src="function.js"></script>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquery.autocomplete.js"></script>
</head>
<?php
	$result = sqlrequest($database_nagios,"SELECT * FROM bp WHERE name='$_GET[uname]'");
	$metier = mysqli_fetch_assoc($result);?>
<body id="main">
	<form action='./add_mod_bp.php' method='POST' name='form_bp'>
		<span id="output"></span>
		<center>
			<table><tr><td valign="top">
				<h1><?php echo $xmlmodules->getElementsByTagName("admin_bp")->item(0)->getAttribute("prop")?></h1>
				<table class="table">
					<tr rowspan="3">
						<td><h2>Equipment</h2></td>
						<td id="td">
						</td>
						<td>
							<center>Use a list? <input type='checkbox' id='check' onclick='javascript:selectValue("change",this.checked);'></center>
						</td>
					</tr>
					<tr>
						<td><h2>Service</h2></td>
						<td>
							<select size='1' name='service[]' id="change" style='width:250px;'>
								<option value='Hoststatus'>Hoststatus</option>
							</select>
						</td>
						<td>
							<center><input class='button' type='button' id='add' name='add' value='add' onclick='javascript:addValue("nobp");'></center>
						</td>
					</tr>
					<tr>
						<td><h2>Process</h2></td>
						<td>
							<select size='1' name='prio' id='prio' style='50px;' onChange="javascript:chooseDisplay(this.value);">
								<option value='all'>Display All</option>
								<option value='null'>No Display</option>
								<?php 
								global $max_display;
								global $display_zero;
								for ($i=((int)!$display_zero); $i < $max_display+1 ; $i++) { 
									echo "<option value='$i'>Display $i</option>";
								}?>
							</select>
							<select size='1' name='proc[]' id='proc' style='width:230px;'>
							</select>
						</td>
						<td>
							<center><input class='button' type='button' id='addProc' name='add' value='add' onclick='javascript:addValue("bp");'></center>
						</td>
					</tr>
					<tr>
						<td class="blanc" align="center" colspan="3">
							<input class='button' type='button' name='back' value='back' onclick='location.href="index.php"'>
						</td>
					</tr>
				</table></td>
				<td valign="top">
				<h1>Definition of : <?php echo "$metier[name] ; type : $metier[type]"; if( $metier['min_value'] != "") echo " $metier[min_value]";?></h1>
				<table class='table'>
					<thead><tr>
						<th>Name;Service<br/>Process</th><th>Select</th>
					</tr></thead>
					<tbody id="sum">
					</tbody>
					<tr>
						<td class="blanc" colspan="9" align="center">
							<input class='button' type='button' name='del' value='delete' onclick='javascript:delValue();'>
						</td>
					</tr>
				</table>
			</tr></table>
		</center>
	</form>
<?php 	$tabMetier = sqlArrayNagios("SELECT * FROM bp WHERE name NOT IN (SELECT bp_name FROM bp_links WHERE bp_link='$_GET[uname]')");

	$result = sqlrequest($database_nagios,"SELECT * FROM bp_services WHERE `bp_name`='$_GET[uname]'");
	$nbrServ = mysqli_num_rows($result);
	$result = sqlrequest($database_nagios,"SELECT * FROM bp_links WHERE `bp_name`='$_GET[uname]'");
	$nbrServ += mysqli_num_rows($result);?>
<script type="text/javascript">
	setValues("<?php echo $_GET['uname']?>", <?php echo json_encode($tabMetier) ?>,"<?php echo $nbrServ?>","<?php echo $metier['min_value'];?>");
	selectValue("change",document.getElementById("check").checked);
	chooseDisplay("all");
	<?php 
	if ( $metier['is_define']){
		$result = sqlArrayNagios("SELECT * FROM bp_services WHERE `bp_name`='$_GET[uname]'");
		echo "setServ(".json_encode($result).");";
		$result = sqlArrayNagios("SELECT * FROM bp_links WHERE `bp_name`='$_GET[uname]'");
		echo "setProc(".json_encode($result).");";
	} 
	?>
</script>
</body>
</html>
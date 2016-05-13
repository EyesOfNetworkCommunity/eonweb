<?php
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

include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_import_device.title"); ?></h1>
		</div>
	</div>

	<?php
		# --- Import host from CSV to nagios (hostname, host ip, host description, host template name)
		function import_hosts_to_nagios($host_name,$host_ip,$host_desc,$host_template){
			global $database_lilac;

			$result=sqlrequest($database_lilac,"SELECT id from nagios_host_template where name like '".$host_template."'");
			
			# --- Check if the template exist in lilac
			if (mysqli_num_rows($result) == 0){
				return "Template $host_template not found";
			}
			else{
				# --- Check if the host is already present in lilac database
				$template_id = mysqli_result($result,0,"id");
				$result2=sqlrequest($database_lilac,"SELECT id from nagios_host where name like '".$host_name."'");
				if (mysqli_num_rows($result2) == 0){ 
					$id=sqlrequest($database_lilac,"INSERT INTO nagios_host (name,address,alias) values ('".$host_name."','".$host_ip."','".$host_desc."');",true);
				}
				else{
					$id=mysqli_result($result2,0,"id");
				}
				if(!$id)
					return "Error in host $host_name definition";
				
				# --- Check if the host + template link already exists
				$nbr=mysqli_result(sqlrequest($database_lilac,"SELECT count(id) from nagios_host_template_inheritance where source_host='".$id."' and target_template='".$template_id."';"),0,0);
				if($nbr==0){
					$order=mysqli_result(sqlrequest($database_lilac,"SELECT count(id) from nagios_host_template_inheritance where source_host='".$id."';"),0,0);
						sqlrequest($database_lilac,"INSERT INTO nagios_host_template_inheritance values ('','".$id."',NULL,'".$template_id."','".$order."');");
					return "ok";
				}
				return "Host $host_name already associated with template $host_template";
			}
		}

	?>

	<form class="form-inline" method="POST" ENCTYPE="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE"  VALUE=20480>
		<div class="form-group">
			<input class="file" type="file" name="filename">
		</div>
		<div class="form-group">
			<button class="btn btn-primary" type="submit" name="upload" value="Upload"><?php echo getLabel("action.submit"); ?></button>
		</div>
	</form>
	<br>
	
	<?php 
		message("", "Format : (Hostname ; IP ; Description ; Template1 ; Template2 ; TemplateN ; ...)", ""); 
		
		# --- Check if the form is post
		if( isset($_POST['upload']) )
		{
			# --- Check if there is an error in the upload
			if ($_FILES['filename']['error']) {
				switch ($_FILES['filename']['error']){
					case 1: // UPLOAD_ERR_INI_SIZE
						message(5,"The uploaded file exceeds the upload_max_filesize directive in php.ini","critical");
						break;
					case 2: // UPLOAD_ERR_FORM_SIZE
						message(5,"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.","critical");
						break;
					case 3: // UPLOAD_ERR_PARTIAL
						message(5,"The uploaded file was only partially uploaded.","critical");
						break;
					case 4: // UPLOAD_ERR_NO_FILE
						message(5,"No file was uploaded","critical");
						break;
				}
			}
			else {
				# --- Build the result table header
				echo '<table class="table table-striped">';
				echo "<thead><tr>";
				echo "<th>Hostname</td>";
				echo "<th>IP</td>";
				echo "<th>Description</td>";
				echo "<th>Template Name</td>";
				echo "<th>Import Status</td>";
				echo "</tr></thead>";
				
				$fichier = $_FILES['filename']['tmp_name'];
				$fic = fopen($fichier, 'rb');
				
				# --- Parse the uploaded csv file and extract host information
				for ($item = fgetcsv($fic, 1024,';'); !feof($fic); $item = fgetcsv($fic, 1024,';')) {
					# --- get templates
					$templates=array();
					for($i=3;$i<count($item);$i++){
						$import=import_hosts_to_nagios($item[0],$item[1],$item[2],$item[$i]);
						if ($import=="ok"){
							# --- the import is true
							echo "<tr class='success'>";
							echo "<td>".$item[0]."</td>";
							echo "<td>".$item[1]."</td>";
							echo "<td>".$item[2]."</td>";
							echo "<td>".$item[$i]."</td>";
							echo "<td>Ok</td>";
							echo "</tr>";
						}
						else{
							# --- there was an error with the template name
							echo "<tr class='danger'>";
							echo "<td>".$item[0]."</td>";
							echo "<td>".$item[1]."</td>";
							echo "<td>".$item[2]."</td>";
							echo "<td>".$item[$i]."</td>";
							echo "<td>$import</td>";
							echo "</tr>";
						}
					}
				}
				echo '</table>';
			}
		}
	?>

</div>

<?php include("../../footer.php"); ?>

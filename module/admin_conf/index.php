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
include_once("./request.php");

?>

<div id="page-wrapper">
	
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_conf.title"); ?></h1>
		</div>
	</div>

	<?php
	function createFile($name,$request)
	{
		global $database_host;
		global $database_username;
		global $database_password;
		global $database_lilac;
		global $path_eonweb;
		global $dir_imgcache;

		$file=fopen("/tmp/".$name.".csv","w");
		
		$result = sqlrequest($database_lilac, $request);
		
		echo "<div class='dataTable_wrapper'>";
		echo "<table class='table table-striped datatable-eonweb'>
				<thead>
					<tr>";
						$line="";
						while($i = mysqli_fetch_field($result)){
							echo "<th>".$i->name."</th>";
							$line=$line.";".$i->name;
						}
						fwrite($file,str_replace("\\","",utf8_decode(substr($line,1)))."\n");
		echo "		</tr>
				</thead>
				<tbody>";
					while($i=mysqli_fetch_row($result)){
						echo "<tr>";
						$line="";
						for($j=0;$j<count($i);$j++){
							$line="$line;$i[$j]";
							echo "<td>".$i[$j]."</td>";
						}
						fputs($file,str_replace("\\","",utf8_decode(substr($line,1)))."\n");
						echo "</tr>";
					}
		echo "	</tbody>
			  </table>
			  </div>";

		fclose($file);
		if( isset($connect) ){
			mysqli_close($connect);
		}
	}
	?>

	<form action="index.php" method="post" class="form-inline">
		<div class="form-group">
			<select class="form-control" id="object" name="object">
			<?php
			foreach($request as $object => $request){
				if(isset($_POST["object"])){
					if($object==$_POST["object"])
						$selected="selected";
					else
						$selected="";
				}
				echo "<option ".$selected." value=\"".$object."\">".$object."</option>";
			}
			?>
			</select>
		</div>
		<button class="btn btn-primary" type="submit" value="Submit"><?php echo getLabel("action.submit"); ?></button>
	</form>
	
	<?php
	if(isset($_POST["object"])){
		include("./request.php");
		echo "<p class='alert alert-info'><i class='fa fa-info-circle'></i> File : <a href=\"./download.php?file=".$_POST["object"].".csv\">".$_POST["object"]."</a></p>";
		createFile( $_POST["object"],$request[$_POST["object"]] );
	}
	?>

</div>

<?php include("../../footer.php"); ?>

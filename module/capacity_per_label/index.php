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
	
include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.capacity_per_label.title"); ?></h1>
		</div>
	</div>
	
	<?php
	// errors management
	if(count($_GET)>0){
		$error = false;
		# --- Retrieve the selected graph id 
		if(isset($_GET['graph'])){
			$graphlocal_graph_id = $_GET['graph'];
		}else{
			message (0," : ".getLabel("message.no_graph_value"),"critical");
			$error = true;
		}

		if(isset($_GET['date'])){
			$graphlocal_dateid = $_GET['date'];
		}else{
			message(0," : ".getLabel("message.no_date_value"),"critical");
			$error = true;
		}

		// EON 5.4 - define graph range
		$curr_time = time();
        switch($graphlocal_dateid)
        {
			case 1: // Jour
				$start_date = strtotime(date('Y-m-d', $curr_time));
				$end_date = strtotime('+1 day', $start_date) - 1;
				break;
			case 2: // Semaine
				$first_weekdayid = 1;
				$offset = (date('w',$curr_time) - $first_weekdayid + 7) % 7;
				$start_date = strtotime( '-' . $offset . ' days' . date('Y-m-d', $curr_time));
				$end_date = strtotime('+1 week', $start_date) - 1;
				break;
			case 3: // Mois
				$start_date = strtotime(date('Y-m-01', $curr_time));
				$end_date = strtotime('+1 month', $start_date) - 1;
				break;
			case 4: // Année
				$start_date = strtotime(date('Y-01-01', $curr_time));
				$end_date = strtotime('+1 year', $start_date) - 1;
				break;
			default:
				$start_date = strtotime(date('Y-m-d', $curr_time));
				$end_date = strtotime('+1 day', $start_date) - 1;
        }
	}
	?>

	<div class="row">
		<form method='GET'>
			<div class="col-md-6 form-group">
				<label>Label :</label>
				<?php get_graph_listbox_from_cacti();?><br>
			</div>
			<div class="col-md-6 form-group">
				<label>Date :</label>
				<select class="form-control" name='date'>
					<option value="1" <?php if(isset($_GET['date']) && $_GET['date']==1){echo 'selected="selected"';} ?>><?php echo getLabel("label.capacity_per_device.day"); ?></option>
					<option value="2" <?php if(isset($_GET['date']) && $_GET['date']==2){echo 'selected="selected"';} ?>><?php echo getLabel("label.capacity_per_device.week"); ?></option>
					<option value="3" <?php if(isset($_GET['date']) && $_GET['date']==3){echo 'selected="selected"';} ?>><?php echo getLabel("label.capacity_per_device.month"); ?></option>
					<option value="4" <?php if(isset($_GET['date']) && $_GET['date']==4){echo 'selected="selected"';} ?>><?php echo getLabel("label.capacity_per_device.year"); ?></option>
				</select><br><br>
				<input class="btn btn-primary" type="submit" name="submit" value="<?php echo getLabel("action.show_graph"); ?>">
			</div>
		</form>
	</div>
	
	<?php
		if(count($_GET)>0 && $error == false){
			echo '<div class="row">';
			# --- Get the graph id from the graph_template id
			if(isset($graphlocal_graph_id)){
				$result_graph = sql($database_cacti,"SELECT id FROM graph_local WHERE graph_template_id=?", array($graphlocal_graph_id));
				foreach($result_graph as $graph){
					# --- Print the graph
					$graph_id = $graph["id"];

					// EON 5.4 - fix image call
					// echo "<img class='img-responsive center-block' src='../../../cacti/graph_image.php?local_graph_id=$graph_id&rra_id=$graphlocal_dateid' alt='cacti_graph'>";
					echo "<img class='img-responsive center-block' src='../../../cacti/graph_image.php?local_graph_id=$graph_id&graph_start=$start_date&graph_end=$end_date' alt='graph_cacti'/>";
				}
			}
			echo '</div>';
		}	
	?>	

</div> <!-- !#page-wrapper -->

<?php include("../../footer.php"); ?>

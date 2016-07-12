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

include("../../include/config.php");
include("../../include/function.php");

?>

<h2> Performance report for ALL Host</h2>

<?php
	# --- TIMESPAN from now
        if(isset($_GET['date'])) $date = $_GET['date'];
        if(isset($_GET['title'])) $title = $_GET['title'];
        else message(0,"Could not get date value","critical");
        $end_date = time();

        switch($date)
        {
                case "today":
                        $start_date = $end_date - 12*60*60;
                        break;
                case "lastday":
                        $start_date = $end_date - 24*60*60;
                        break;
                case "lastweek":
                        $start_date = $end_date - 7*24*60*60;
                        break;
                case "last2week":
                        $start_date = $end_date - 2*7*24*60*60;
                        break;
                case "lastmonth":
                        $start_date = strtotime("-1 month");
                        break;
                case "last2month":
                        $start_date = strtotime("-2 months");
                        break;
                case "lastyear":
                        $start_date = strtotime("-1 year");
                        break;
                default:
                        $start_date = $end_date - 24*60*60;
        }
	
	echo '<p class="alert alert-info text-info">';
	if($title!="")
		echo "Title : $title <br>";

        echo "Periode : ";
        echo "from <i>" . date("l d M Y - h:i A",intval($start_date)) . "</i> to <i>" .  date("l d M Y - h:i A",intval($end_date)) . "</i>";
	echo '</p>';
	
	# --- For each host
	$result_host=sqlrequest($database_cacti,"select id,hostname from host");
	$nbr_ligne_host = mysqli_num_rows($result_host);
	if($nbr_ligne_host == 0) message(0,"No host find in database","critical");
	for($j=0;$j<$nbr_ligne_host;$j++)
	{
		# -- Get the infos
		$hostname=mysqli_result($result_host,$j,"hostname");
		$hostid=mysqli_result($result_host,$j,"id");
		
		# --- Get the graph id from the host id
	        $result_graph=  sqlrequest($database_cacti,"SELECT graph_local.id FROM graph_local,graph_templates_graph WHERE host_id='$hostid' and graph_templates_graph.local_graph_id=graph_local.id and graph_templates_graph.title like '%$title%' ");
	        $nbr_ligne_graph = mysqli_num_rows($result_graph);

		# --- Display info
		if($nbr_ligne_graph != 0) {
			echo '
				<div class="panel panel-default">
					<div class="panel-heading">'.$hostname.'</div>
					<div class="panel-body">';

			# --- For each graph of the host
			for ($i=0;$i<$nbr_ligne_graph;$i++)
			{	
				# --- Print the graph
					$graph_id = mysqli_result($result_graph,$i,"id");
					echo "<img class='img-responsive center-block' alt='graph cacti' src='../../../cacti/graph_image.php?local_graph_id=$graph_id&rra_id=1&graph_height=100&graph_width=300&graph_nolegend=true&graph_start=$start_date&graph_end=$end_date'>";
			}
			echo 	'</div>';
			echo '</div>';
		}
	}
?>
</body>
</html>

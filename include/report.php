<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION 4.1
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
if( !empty($_GET) )
{
	include("config.php");
	include("function.php");
	
	// create variables with ajax parameters values
	extract($_GET);

	$myfilter=false;
	$show=true;
	$filter=false;

	// Define the path file
	$pathfile=$path_reports."/".$file;
	// Test if report file exist
	if (!file_exists($pathfile)) die("The XML file doesn't exist : $pathfile");

	// Define the object
	$xml = simplexml_load_file($pathfile);

	// Parse XML File
	foreach($xml->zone as $zone) {
		
		// Define Array
		$data=array();
		$mini_data = array();
		$data_color = array();
		
		// Get the Value	
		foreach($zone->value as $value)
		{
			switch($value->source)
			{
				case "system" :
					// COMMAND RETURN : One number
					// exec the system command
					$result=false;
					exec("$value->get_value",$result);
	
					// Put the value into arrays
					$data["$value->legend"] = $result[0];
					break;

				case "ged":
					# --- original request
					#
					// dates ranges
					if($start_date != "" && $end_date != "")
						$request_where=$request_where." and o_sec >= $start_date and o_sec < $end_date";
					else
						$request_where="";

					// XML filters global options
					$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";
					if(file_exists($file) && $filter==true){
						$xmlfilters = new DOMDocument("1.0","UTF-8");
						$xmlfilters->load($file);
						$g=$xmlfilters->getElementsByTagName("ged")->item(0);

						//Default filter detection
						$default=$g->getElementsByTagName("default")->item(0)->nodeValue;

						if($default!=""){
							$xpath = new DOMXPath($xmlfilters);
							$g_filters = $xpath->query("//ged/filters[@name='$default']/filter");
							$or=0;

							foreach($g_filters as $g_filter){
								$or++;
								if($or>1)
									$request_filter=$request_filter." or (".$g_filter->getAttribute("name")." like '%".($g_filter->nodeValue)."%')";
								else
									$request_filter=" and ((".$g_filter->getAttribute("name")." like '%".($g_filter->nodeValue)."%')";
							}
							if($or>0)	
								$request_where=$request_where.$request_filter.")";
						}
					}

					// if filter search is set
					if($filter_field != "" && $filter_value != "")
					{
						$request_where=$request_where." and ".$filter_field." like '".$filter_value."'";
					}
					
					// loop on each ged packet type
					$connect=mysqli_connect($database_host,$database_username,$database_password, $database_ged);
					$result=mysqli_query($connect, "select pkt_type_name from pkt_type where pkt_type_id!='0' AND pkt_type_id<'100';");
					$nbr=0;
					while($i=mysqli_fetch_row($result)){
						// for bar graphs
						if("$zone->display_type"=="bar")
						{
							$diffold=time();
							foreach($zone->int as $int)
							{
								$diff=strtotime("$int->time");
								$request="select count(id) from ".$i[0]."_queue_".$event_state." where state='".$value->get_value."' and queue='".substr($event_state{0},0,1)."' and o_sec >= $diff and o_sec < $diffold ".$request_where.";";
								$diffold=$diff;
								$count=mysqli_query($connect, $request);
								$count=mysqli_fetch_array($count);
								$data["$value->legend"]["$int->name"] += $count[0];
							}
							$request="select count(id) from ".$i[0]."_queue_".$event_state." where state='".$value->get_value."' and queue='".substr($event_state{0},0,1)."' and o_sec < $diff ".$request_where.";";
							$count=mysqli_query($connect, $request);
							$count=mysqli_fetch_array($count);
							$data["$value->legend"]["more"] += $count[0];
						}
						// for pie graph
						else {
							$request="select count(id) from ".$i[0]."_queue_".$event_state." where state='".$value->get_value."' and queue='".substr($event_state{0},0,1)."'".$request_where.";";
							$count=mysqli_query($connect, $request);
							$count=mysqli_fetch_array($count);
							$data["$value->legend"] += $count[0];
						}
					}
					mysqli_close($connect);
					break;

				case "sla":
					# --- original request
					#
					// do not select OK states
                    $request_where=" and state!='0'";
					
					// dates ranges
                    if($start_date != "" && $end_date != "")
						$request_where=$request_where." and o_sec >= $start_date and o_sec < $end_date";

					// if filter search is set
					if($filter_field != "" && $filter_value != "")
					{
						$request_where=$request_where." and ".$filter_field." like '".$filter_value."'";
					}

					// for bar graphs
					if(isset($durationold))
						$durationold=$duration;
					else
						$durationold=0;

					$duration="$value->get_value";

					// loop on each ged packet type
					$connect=mysqli_connect($database_host,$database_username,$database_password, $database_ged);
					$result=mysqli_query($connect, "select pkt_type_name from pkt_type where pkt_type_id!='0' AND pkt_type_id<'100';");
					$nbr=0;
					while($i=mysqli_fetch_row($result)){
						// for bar graphs
						if("$zone->display_type"=="bar")
						{
							$diffold=time();
							foreach($zone->int as $int)
							{
								$diff=strtotime("$int->time");
								$request="select count(id) from ".$i[0]."_queue_".$event_state." where queue='".substr($event_state{0},0,1)."' and o_sec >= $diff and o_sec < $diffold and a_sec-o_sec < $duration and a_sec-o_sec >= $durationold ".$request_where.";";
								$diffold=$diff;
								$count=mysqli_query($connect, $request);
								$count=mysqli_fetch_array($count);
								$data["$value->legend"]["$int->name"] += $count[0];
							}
							$request="select count(id) from ".$i[0]."_queue_".$event_state." where queue='".substr($event_state{0},0,1)."' and o_sec < $diff and a_sec-o_sec < $duration and a_sec-o_sec >= $durationold ".$request_where.";";
							$count=mysqli_query($connect, $request);
							$count=mysqli_fetch_array($count);
							
							$data["$value->legend"]["more"] += $count[0];
						}
						// for pie graph
						else {
							$request="select count(id) from ".$i[0]."_queue_".$event_state." where queue='".substr($event_state{0},0,1)."' and a_sec-o_sec < $duration and a_sec-o_sec >= $durationold ".$request_where.";";
							$count=mysqli_query($connect, $request);
							$count=mysqli_fetch_array($count);
							$data["$value->legend"] += $count[0];
							
						}
					}
					mysqli_close($connect);
					break;

				default:
					message(0," Could not source type in xml file : $pathfile","critical");
			}
			$color = $value->color;
			$url = $value->url;
			$data[$value->legend."_color"] = $color;
			$data[$value->legend."_url"] = $url;
			
			//$data_color[$value->legend."_color"] = $color;
		}
		$data = array_merge($data, $data_color);

		// Languages
		$graphs = $xmlmodules->getElementsByTagName("graphs");
		$title = $graphs->item(0)->getElementsByTagName($zone->display_title);
		
		// test if there is no data in array	
		if($data == array())
		{
			message(9,"No data to display for graph $title","warning");
		}
		else
		{
			// Languages
			$xpath = new DOMXPath($xmlmodules);
            $menutabs = $xpath->query("//graphs/".$zone->display_title);
			$title = $menutabs->item(0)->getAttribute("title");

			// Display the DATA
			switch($zone->display_type)
			{
				case "pie" :
					// Add all the data to test for the pie
					$test=0;
					foreach($data as $val)
					{
						if(!is_array($val)) { $test=$test+$val; }
					}
					if($test==0){
						$data=false;
						$data["nothing"]=1;
					}
					break;
				case "bar" :
					break;
				default :
					message(0," Could not get data area type in xml file : $pathfile","critical");
			}
		}
	}
	
	echo json_encode($data);
}
?>

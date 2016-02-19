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
<link rel="stylesheet" type="text/css" href="../../css/jquery/ui.all.css" />
<?php

$events_active=$xmlmodules->getElementsByTagName("active_events");
$events_history=$xmlmodules->getElementsByTagName("history_events");

class EventBrowser {
	
	private $gedq; 
	private $mystate; 
	private $mytype; 
	private $myfilter; 

	public function __construct() {

		$this->gedq=null;
		$this->mystate=null;
		$this->mytype=null;		
		$this->myfilter=null;		

    	}  

	private function showTableTH($type) {

		global $array_ged_packets;

		echo '
		<'.$type.'>
		<tr>
                <th align="center" class="{sorter: false}"><a href="#" onclick="checkUncheckAll();">ALL</a></th>';
		foreach($array_ged_packets as $col => $field) {
			if($field["col"]==true)
				echo '<th>'.$col.'</th>';	
		}
		echo '
		</tr>
		</'.$type.'>
		';

	}

	public function showTablePager() {

		global $maxlines;

		echo '
		<div id="pager" class="pager">
        	<form>
                	<img src="/images/actions/first.png" class="first"/>
                	<img src="/images/actions/prev.png" class="prev"/>
                	<input type="text" class="pagedisplay" disabled/>
                	<img src="/images/actions/next.png" class="next"/>
                	<img src="/images/actions/last.png" class="last"/>
                	<select class="pagesize">
                        	<option selected="selected" value="15">15</option>
                        	<option value="50">50</option>
                        	<option value="100">100</option>
                        	<option value="250">250</option>
                        	<option value="'.$maxlines.'">'.$maxlines.'</option>
                	</select>
        	</form>
		</div>
		</form>
		</div>
		</div>
		';

	}	

	private function sql2xml($gedsql_count,$gedsql_result) {

		global $array_ged_packets;

		$nbr=mysqli_num_rows($gedsql_result);

		$this->gedq='<?xml version="1.0" encoding="UTF-8"?><ged '.substr($this->mystate,0,1).'="'.$gedsql_count.'">';

		for($i=0;$i<$nbr;$i++) {
		        $this->gedq.='<r t="'.mysqli_result($gedsql_result,$i,"TYPE").'" q="'.mysqli_result($gedsql_result,$i,"queue").'" o="'.mysqli_result($gedsql_result,$i,"occ").'" i="'.mysqli_result($gedsql_result,$i,"id").'">';
		        $this->gedq.='<u><s>'.mysqli_result($gedsql_result,$i,"src").'</s></u>';
		        $this->gedq.='<t>';
		        $this->gedq.='<o s="'.mysqli_result($gedsql_result,$i,"o_sec").'"></o>';
		        $this->gedq.='<l s="'.mysqli_result($gedsql_result,$i,"l_sec").'"></l>';
		        $this->gedq.='<a s="'.mysqli_result($gedsql_result,$i,"l_sec").'"></a>';
		        $this->gedq.='</t>';
		        $this->gedq.='<c>';
		        foreach($array_ged_packets as $val => $tab){
		        	if($tab["type"]==true)
				        $this->gedq.="<$val>".htmlspecialchars(mysqli_result($gedsql_result,$i,$val),ENT_QUOTES)."</$val>";
			}
		       	$this->gedq.='</c>';
		        $this->gedq.='</r>';
	        }

	}

	public function showTable($mystate, $mytype=false, $myfilter=false) {

		global $ged_prefix;
		global $database_ged;
		global $path_ged_bin;
		global $path_nagios_cgi;
		global $path_nagios_cgi_others;
		global $array_ged_packets;
		global $array_ged_states;
		global $array_serv_system;
		global $maxlines;
		global $dateformat;

		$i=0;
		$this->mystate=$mystate;
		$this->mytype=$mytype;
		$this->myfilter=$myfilter;
		
		$tm = "";

		// GED TM PARAMETER
		# --- date
		if(isset($myfilter["datepicker"])) {
			if($myfilter["datepicker"]!="") {
				$date=explode(" - ",$myfilter["datepicker"]);
				$date_start=explode("/",$date[0]);
				$start=mktime("0","0","0",$date_start[1],$date_start[0],$date_start[2]);
				if(isset($date[1]))
					$date_end=explode("/",$date[1]);
				else
					$date_end="";
				if($date_end!="")
					$end=mktime("24","00","00",$date_end[1],$date_end[0],$date_end[2]);
				else
					$end=mktime("24","00","00",$date_start[1],$date_start[0],$date_start[2]);
				$tm="AND o_sec>='$start' AND o_sec<='$end'";	
			}
			else $tm="";
		}
		if( isset($myfilter["time"]) ){
			if($myfilter["time"] != ""){
				switch($myfilter["time"]){
					case "0-5m":
						$start = time()-300;
						$tm = "WHERE o_sec > ".$start;
						break;
					case "5-15m":
						$start = time()-300;
						$end = time()-900;
						$tm = "WHERE o_sec <= ".$start." AND o_sec > ".$end;
						break;
					case "15-30m":
						$start = time()-900;
						$end = time()-1800;
						$tm = "WHERE o_sec <= ".$start." AND o_sec > ".$end;
						break;
					case "30m-1h":
						$start = time()-1800;
						$end = time()-3600;
						$tm = "WHERE o_sec <= ".$start." AND o_sec > ".$end;
						break;
					case "more":
						$start = time()-3600;
						$tm = "WHERE o_sec <= ".$start;
						break;
				}
				if( isset($myfilter["own"]) ){
					if($myfilter["own"] == "yes"){
						$tm .= " AND owner != ''";
					}
					if($myfilter["own"] == "no"){
						$tm .= " AND owner = ''";
					}
				}
			}
			else{
				$tm = "";
			}
		}
		else
			$tm="";

                # --- offsets
                if(isset($_POST["nstart"])) {
                        if(is_numeric($_POST["nstart"])) $nstart=$_POST["nstart"];
                        else $nstart="0";
                }
                else $nstart="0";

                if(isset($_POST["nend"])) {
                        if(is_numeric($_POST["nend"])) $nend=$_POST["nend"];
                        else $nend=$maxlines;
                }
                else $nend=$maxlines;

		// GED CALL
		if($mytype) {
			$gedsql_result=sqlrequest($database_ged,"select pkt_type_name from pkt_type where pkt_type_id='$mytype';");
			$gedsql_table=mysqli_result($gedsql_result,0,"pkt_type_name");

			$gedsql_result=sqlrequest($database_ged,"select count(*) from ".$gedsql_table."_queue_".$mystate." where queue='".substr($mystate,0,1)."';");
			$gedsql_count=mysqli_result($gedsql_result,0);
			if($myfilter["field"]) {
				$gedsql_request="SELECT '$mytype' as TYPE,".$gedsql_table."_queue_".$mystate.".* FROM ".$gedsql_table."_queue_".$mystate." WHERE ".$myfilter["field"]." LIKE '".$myfilter["value"]."%' $tm ORDER BY o_sec DESC, o_usec DESC LIMIT $nstart,$nend";
			}
			else {
				$gedsql_request="SELECT '$mytype' as TYPE,".$gedsql_table."_queue_".$mystate.".* FROM ".$gedsql_table."_queue_".$mystate." $tm ORDER BY o_sec DESC, o_usec DESC LIMIT $nstart,$nend";
			}
		}
		else {
			$gedsql_result1=sqlrequest($database_ged,"select pkt_type_id,pkt_type_name from pkt_type where pkt_type_id!='0' AND pkt_type_id<'100';");
			$gedsql_request="";
			$gedsql_count="0";
			
			while ($line = mysqli_fetch_array($gedsql_result1)) {
				$gedsql_result=sqlrequest($database_ged,"select count(*) from ".$line["pkt_type_name"]."_queue_".$mystate.";");
				$gedsql_count+=mysqli_result($gedsql_result,0);

				if($myfilter["field"]) {
					$gedsql_request.="SELECT '".$line["pkt_type_id"]."' as TYPE,".$line["pkt_type_name"]."_queue_".$mystate.".* FROM ".$line["pkt_type_name"]."_queue_".$mystate." WHERE ".$myfilter["field"]." LIKE '".$myfilter["value"]."%' $tm UNION ";
				}
				else {
					$gedsql_request.="SELECT '".$line["pkt_type_id"]."' as TYPE,".$line["pkt_type_name"]."_queue_".$mystate.".* FROM ".$line["pkt_type_name"]."_queue_".$mystate." $tm UNION ";
				}
			}
			$gedsql_request.="ORDER BY o_sec DESC, o_usec DESC LIMIT $nstart,$nend";
			$gedsql_request=str_replace("UNION ORDER"," ORDER",$gedsql_request);
		}

		$gedsql_result=sqlrequest($database_ged,$gedsql_request);
  
		$this->sql2xml($gedsql_count,$gedsql_result);
		$this->gedq.="</ged>";
		$gedq=$this->gedq;

		# CHECK GED PROCESS
		if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
			message(0," : ged daemon must be dead","critical");
		}

		// XPATH QUERIES	
		$xml= new DOMDocument();
                $xml->preserveWhiteSpace=false;
                $xml->loadXML($gedq);
		
		$length=$xml->getElementsByTagName("ged")->item(0)->getAttribute($mystate{0});

		$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";
		$query="";

		// XML filters global options
		if(file_exists($file)){
		        $xmlfilters = new DOMDocument("1.0","UTF-8");
        		$xmlfilters->load($file);
			$g=$xmlfilters->getElementsByTagName("ged")->item(0);

			//Default filter detection
			$default=$g->getElementsByTagName("default")->item(0)->nodeValue;
	
			if($default!=""){
				echo "<script>$('#ged_filter').empty();$('#ged_filter').append('<i><a href=\'index.php?filter=".$default."\'>filter : ".$default."</a></i>');</script>";

			 	$xpath = new DOMXPath($xmlfilters);
		                $g_filters = $xpath->query("//ged/filters[@name='$default']/filter");

	        	        foreach($g_filters as $g_filter)
        	        		$query=$query."|//r[.//".$g_filter->getAttribute("name")."[contains(translate(.,'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'),'".strtoupper($g_filter->nodeValue)."')]]";

				$query=substr($query,1);
				if($query=="")
					$query="//r";
			}
			else{
				echo "<script>$('#ged_filter').empty();$('#ged_filter').append('<i><a href=\'index.php\'>no default filter</a></i>');</script>";
				$query="//r";
			}
		}
		else{
                	echo "<script>$('#ged_filter').empty();$('#ged_filter').append('<i><a href=\'index.php\'>no default filter</a></i>');</script>";
                        $query="//r";
                }
		// XML filters search options
		if($myfilter){
			// field filter
			$value = "";
			if($myfilter["value"]!="")
				$value='[.//'.$myfilter["field"].'[contains(translate(.,\'abcdefghijklmnopqrstuvwxyz\', \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\'),\''.strtoupper($myfilter["value"]).'\')]]';

			// owner filter
			if(isset($_POST["owner"])){
				if($_POST["owner"]=="owned")
					$value=$value.'[.//owner!=""]';
				if($_POST["owner"]=="not owned")
					$value=$value.'[.//owner=""]';
			}

			// period filter
			$duration = "";
			if($myfilter["duration"]!="")
				$duration='[.//a[@d>='.$myfilter["duration"].']]';

			// no global options
			if($query=="")
				$s='//r'.$value.$duration;
			// with global options
			else{
				$s = "";
				$s_query=explode("|",$query);
				foreach($s_query as $val)
					$s=$s."|".$val.$value.$duration;
			}
			$s=substr($s,1);			

			// states filters
			foreach($array_ged_states as $col => $val){
				$s_query=explode("|",$s);
                                foreach($s_query as $s_val){
                               		if($myfilter[$col])
                                       		$queries[] = $s_val.'[.//state=\''.$val.'\']';
				}
                        }
			$query = "";
			foreach($queries as $req)
				$query=$query.$req." | ";
			$query=substr($query,0,-3);
		}
		// XML basic query without filters
		elseif($query=="" || $query=="//r"){
			$query = $query."[.//state>='0']";
		}

		$xpath = new DOMXPath($xml);
		$records = $xpath->query($query);

		// XML DISPLAY
                echo '
                <div id="gedtable">
                <div id="showtable" style="display:none;">
		<form>
		<h2>result : '.$records->length.'/'.$length.' event(s) found.</h2>';
	
		if($length>=$maxlines)
			echo "<h2>You have more than $maxlines lines, adjust your search.</h2>";
        
	        echo '<table class="tablesorter" cellspacing="1">
                ';

                $this->showTableTH("thead");
                $this->showTableTH("tfoot");

                echo '<tbody>';

		foreach($records as $record){
			$i++;
        		$ged_occurences=$record->getAttribute("o");
        		$ged_type=$record->getAttribute("t");
        		$ged_source=$record->getElementsByTagName("s")->item(0)->nodeValue;
        		$ged_originaltime=$record->getElementsByTagName("o")->item(0)->getAttribute("s");
			$ged_originaltime=date($dateformat,$ged_originaltime);
        		$ged_lasttime=$record->getElementsByTagName("l")->item(0)->getAttribute("s");
			$ged_lasttime=date($dateformat,$ged_lasttime);
	        	$ged_content=$record->getElementsByTagName("c");

			if($mystate=="history") {
        			$ged_id=$record->getAttribute("i");
				$ged_acknowledgetime=$record->getElementsByTagName("a")->item(0)->getAttribute("s");
				$ged_acknowledgetime=date($dateformat,$ged_acknowledgetime);
			}

        		foreach($ged_content as $data){
	                        foreach($array_ged_packets as $val => $tab){
					if($tab["type"]==true)
        	                        	${"ged_".str_replace("-","",$val)}=$data->getElementsByTagName($val)->item(0)->nodeValue;
                        	}
 	       		}

			switch ("$ged_state") {
                		case "0" :
                  		  echo '<tr name="status" class="status_up" id="'.$i.'">';
                  		  $state="normal";
                  		  break;
                		case "1" :
                  		  echo '<tr name="status" class="status_sleep" id="'.$i.'">';
		                  $state="warning";
		                  break;
                		case "2" :
                  		  echo '<tr name="status" class="status_down" id="'.$i.'">';
		                  $state="critical";
		                  break;
                		default :
		                  echo '<tr name="status" class="status_unknow" id="'.$i.'">';
		                  $state="unknow";
        		}

		        echo '<td align="center">
			<input type="checkbox" class="checkbox" name="actioncheck[]" value="'.$i.'">';

			foreach($array_ged_packets as $val => $tab){
				if(isset(${"ged_".str_replace("-","",$val)}))
					echo '<input type="hidden" name="'.$i.'_'.$val.'" value="'.${"ged_".str_replace("-","",$val)}.'">';
			}
			
			echo '<input type="hidden" name="'.$i.'_statefull" value="'.$state.'">';

			echo '</td>';
			foreach($array_ged_packets as $td => $val){
				if($td!="state" && $val["col"]==true){
					$nagios_equipment=preg_replace("/".$ged_prefix."/","",$ged_equipment,1);
					$ged_source=explode(";",$ged_source);
					$ged_source=explode("/",$ged_source[0]);
					$ged_source=$ged_source[0];
                                        if($td=="equipment" && $ged_type=="1" && $mystate=="active" && ($ged_source=="127.0.0.1" OR $ged_source=="0.0.0.0"))
                                                echo "<td><a href='".$path_nagios_cgi."/extinfo.cgi?type=1&host=".$nagios_equipment."'>".$ged_equipment."</a></td>";
                                        elseif($td=="equipment" && $ged_type=="1" && $mystate=="active")
                                                echo "<td><a href='http://".$ged_source.$path_nagios_cgi_others."/extinfo.cgi?type=1&host=".$nagios_equipment."' target='_blank'>".$ged_equipment."</a></td>";
                                        elseif($td=="service" && $ged_type=="1" && $mystate=="active" && ($ged_source=="127.0.0.1" OR $ged_source=="0.0.0.0"))
                                                echo "<td><a href='".$path_nagios_cgi."/extinfo.cgi?type=2&host=".$nagios_equipment."&service=".$ged_service."'>".$ged_service."</a></td>";
                                        elseif($td=="service" && $ged_type=="1" && $mystate=="active")
                                                echo "<td><a href='http://".$ged_source.$path_nagios_cgi_others."/extinfo.cgi?type=2&host=".$nagios_equipment."&service=".$ged_service."' target='_blank'>".$ged_service."</a></td>";
					else
						echo "<td>".${"ged_".str_replace("-","",$td)}."</td>";
				}
				elseif($td=="state" && $val["col"]==true){
				        echo '<td align="center"><img src="/images/states/s_'."$state.png".'" alt="'.$state.'">';
					if($ged_owner!="")
						echo '&nbsp<img src="/images/actions/own.png" alt="own">';
					if($ged_comments!="")
						echo '&nbsp<img src="/images/actions/edit.png" alt="edit">';
					echo '</td>';
				}
			}
		        echo '</tr>';
		}

	        echo '</tbody>
		</table>';

	}

	public function updateEvent($events,$value,$ack=false) {

		global $database_ged;	
		global $path_ged_bin;
		global $array_ged_packets;

		foreach($events as $event){

			$request_keys="";

			foreach($array_ged_packets as $val => $tab){
				$ged_field_tmp=str_replace("-","",$val);
				if(isset($_POST[$event."_".$val]))
					$ged[$ged_field_tmp]=$_POST[$event."_".$val];		
				if($tab["key"]==true){
					$request_keys.=$ged_field_tmp."='".$ged[$ged_field_tmp]."' AND ";
				}
                        }

			# --- check if event still exists
			$gedsql_result=sqlrequest($database_ged,"select pkt_type_name from pkt_type where pkt_type_id='".$ged["type"]."';");
			$gedsql_table=mysqli_result($gedsql_result,0,"pkt_type_name")."_queue_active";
			$gedsql_result=sqlrequest($database_ged,"SELECT count(*) from ".$gedsql_table." WHERE ".$request_keys ."id is not NULL;");
			$packet_exists=mysqli_result($gedsql_result,0);

			if($packet_exists==0) {
				continue;	
			}

			# --- add owner
			if($value=="own")
				$ged["owner"]=$_COOKIE['user_name']."@".getenv("SERVER_NAME");
			# --- delete owner
			elseif($value=="disown")
				$ged["owner"]="";
			# --- add comment
			else{
				if($ack)
					$ged["owner"]=$_COOKIE['user_name']."@".getenv("SERVER_NAME");
				$ged["comments"]=$value;
			}

			$log="-update -type ".$ged["type"]." ";
			foreach($array_ged_packets as $val => $tab){
				if($tab["type"]==true){
					$log=$log." \"".$ged[str_replace("-","",$val)]."\"";
				}
			}

			shell_exec($path_ged_bin." ".$log);

			logging("ged_update",$log);
		}

	}

	public function deleteEvent($q,$events) {

		global $path_ged_bin;
		global $array_ged_packets;
		$ids="";

		foreach($events as $event){
	
			$type=$_POST[$event."_type"];
		
			if($q=="history")
				$ids=$ids.$_POST[$event."_id"].",";
			else{
				$log="-drop -type $type -queue $q ";

	                        foreach($array_ged_packets as $val => $tab){
        	                        if($tab["key"]==true){
                	                        $log=$log." \"".$_POST[$event."_".str_replace("-","",$val)]."\"";
                        	        }
                        	}
				shell_exec($path_ged_bin." ".$log);
				logging("ged_delete",$log);
			}

		}

		if($q=="history"){
			$log="-drop -id ".substr($ids,0,-1)." -queue $q";
			shell_exec($path_ged_bin." ".$log);
                        logging("ged_delete",$log);
		}

	}

	public function __destruct() {

    	} 

}

?>

<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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
session_start();
# Internationalization
include("classes/Translator.class.php");

# Actions
include("classes/Actions.class.php");	
if(file_exists(dirname(__FILE__)."/classes/Custom.Actions.class.php")) { 
	include("classes/Custom.Actions.class.php"); 
}
$CustomActions = class_exists("CustomActions") ? new CustomActions() : new Actions();

// Display Error Message 
function message($id, $text, $type){
	
	global $array_msg;
	
	// Get standard message if exists
	if(isset($array_msg[$id])) { $tempid=$array_msg[$id]; } 
	else { $tempid=""; }
	
	// Define the message type and icon
	switch($type)
	{
		case "critical":
			$alert_type = "danger";
			$alert_icon = "fa-exclamation-circle";
			break;
		case "warning":
			$alert_type = "warning";
			$alert_icon = "fa-warning";
			break;
   		case "ok":
   			$alert_type = "success";
			$alert_icon = "fa-check-circle";
			break;
		default:
			$alert_type = "info";
			$alert_icon = "fa-info-circle";
			break;
	}

	// Display the message
	echo "<p class='alert alert-dismissible alert-".$alert_type." fade in'>
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>
			<i class='fa ".$alert_icon."'></i> $tempid $text
		  </p>";
}

function username_available(){
	$request = sql("eonweb","SELECT * FROM users WHERE user_name = ?", array($_COOKIE["user_name"]));
	$result = $request[0]["user_name"];
	if(isset($result))
		return true;
	else return false;
}

function sql($database, $sql = null, $datas = null, $arg = null){
	global $database_host;
	global $database_username;
	global $database_password;
	$type = str_word_count($sql, 1);
	$type = strtoupper($type[0]);

	try {
		$dbh = new PDO("mysql:host=$database_host;dbname=$database", $database_username, $database_password);
		if($sql != null){
			$stmt = $dbh->prepare($sql);
			
			if(is_array($datas)){
				$result = $stmt->execute($datas);
			} else {
				$result = $stmt->execute();
			}

			if($type == "SELECT"){
				if($arg != null){
					$result = $stmt->fetchAll($arg);
				} else {
					$result = $stmt->fetchAll();

				}
			}
			
			$dbh = null;
			$stmt = null;

			return $result;
		} else {
			return $dbh;
		}
		
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
    }
}

// Display array value
function display_value($value, $key){
	echo "$value\n";
}

// Function Edit and Modify a file
function filemodify($path,$get=false){
	if(is_writable($path)) {
	
		// Test If Update or Display.
		if (isset($_POST['maj'])) {
			if (!$fconf = fopen($path, "w")) message(2,$path,"critical");
			// Write the change
			if (fwrite ($fconf, str_replace("\r\n", "\n", $_POST['maj'])) === FALSE) message(3,$path,"critical");
			else { 
				message(6," : File updated","ok");
			}
			fclose ($fconf);
			if (!$fconf = fopen($path, "r")) message(2,$path,"critical");
		}
		else if (!$fconf = fopen($path, "r")) message(2,$path,"critical");

		// Display the Text Area and button
		echo "<form method='post' action='./index.php";
		if($get)
			echo "?file=$get";
		echo "'>";
		echo '<div class="form-group">';
		echo "<textarea class='form-control textarea' cols='100' rows='20' name='maj' scrolling='no'>";
			print file_get_contents($path);
		echo "</textarea>";
		echo '</div>';
		echo '<div class="form-group">';
		echo "<input class='btn btn-primary' type='submit' value='".getLabel("action.update")."'>";
		echo '</div>';
		echo "</form>";
		fclose ($fconf);
	}
	else message(3,$path,"critical");
}

// Host List form Nagios
function get_host_list_from_nagios($field=false, $queue = false){
	global $database_lilac;
	global $database_ged;
	$hosts=array();

	if($field && $field != 'owner'){
		switch ($field) {
			case 'service': $column = 'description'; break;
			case 'description': echo json_encode($hosts); return; break;
			default: $column = 'name'; break;
		}
		$request="SELECT DISTINCT $column FROM nagios_$field ORDER BY $column";
		$db = $database_lilac;
	} elseif ($field && $field === 'owner') {
		$request="SELECT DISTINCT owner FROM nagios_queue_$queue WHERE owner != '' ORDER BY owner";
		$db = $database_ged;
	}
	else {
		$request="SELECT name FROM nagios_host
		UNION SELECT name from nagios_hostgroup
		UNION SELECT DISTINCT description from nagios_service
		UNION SELECT name from nagios_service_group
		ORDER BY name";
		$db = $database_lilac;
	}

	$result=sql($db,$request);
	foreach($result as $line){
		array_push($hosts, $line[0]);
	}
	echo json_encode($hosts);
}

// Host and Address list from nagios. //TODO send the adress
function get_host_list(){
	global $database_lilac;
	$hosts=array();

	$result=sql($database_lilac,"SELECT name,address FROM nagios_host ORDER BY name");
	
	foreach($result as $line){
		$hosts[]=$line[0];
		$hosts[]=$line[1];
	}
	echo json_encode($hosts);
}

// Host and Address listbox from nagios. //TODO send the adress
function get_host_listbox_from_nagios(){
	global $database_lilac;
	
	// create input autocomplete with all nagios host values
	echo "<label>Host</label>";
	$result=sql($database_lilac,"SELECT DISTINCT name FROM nagios_host UNION ALL SELECT DISTINCT address FROM nagios_host");
	$input = "<input id='host_list' class='form-control' type='text' name='host_list' onFocus='$(this).autocomplete({source: [";
	foreach($result as $line){
		$input .= '"'.$line[0].'",';
	}
	$input = rtrim($input, ",");
	$input .= "]})'>";
	
	echo '<div class="input-group">';
	echo 	$input;
	echo 	'<span class="input-group-btn">
				<input class="btn btn-primary" type="submit" name="run" value="'.getLabel("action.run").'" >
			</span>
			';
	echo '</div>';
}

// Host list from CACTI
function get_title_list_from_cacti(){

	global $database_cacti;

	$titles=array();
	$request="SELECT DISTINCT graph_templates_graph.title FROM graph_local,graph_templates_graph WHERE graph_templates_graph.local_graph_id=graph_local.id ORDER BY title";
	$result=sql($database_cacti,$request);
	foreach($result as $line){
	$line[0]=str_replace("|host_description| - ","",$line[0]);
		$titles[]=$line[0];
	}
	echo json_encode($titles);
}

// Host listbox from CACTI
function get_host_listbox_from_cacti(){
	
	global $database_cacti;
	
	$ref = "";
	if( isset($_GET['host']) ){
		$ref = $_GET['host'];
	}
	
	$result=sql($database_cacti,"SELECT DISTINCT host.id,hostname,description FROM host INNER JOIN graph_local ON host.id = graph_local.host_id ORDER BY hostname ASC");
	echo "<SELECT name='host' class='form-control' size=7>";
	foreach($result as $line){
		echo "<OPTION value='$line[0]' ";
		if($ref == $line[0]){echo 'selected="selected"';}
		echo ">&nbsp;$line[1] ($line[2])&nbsp;</OPTION>";
	}
	echo "</SELECT><br>";
}

// system function : CUT
function cut($string, $width, $padding = "..."){
    return (strlen($string) > $width ? substr($string, 0, $width-strlen($padding)).$padding : $string);
} 

// Get graph from CACTI
function get_graph_listbox_from_cacti(){
	
	global $database_cacti;
	
	$ref = "";
	if( isset($_GET['graph']) ){
		$ref = $_GET['graph'];
	}
	
	$result=sql($database_cacti,"SELECT DISTINCT graph_templates.id,name FROM graph_templates INNER JOIN graph_local ON graph_local.graph_template_id = graph_templates.id ORDER BY name ASC");
	echo "<SELECT name='graph' class='form-control' size=7>";
	foreach($result as $line){
		echo "<OPTION value='$line[0]' ";
		if($ref == $line[0]){echo 'selected="selected"';}
		echo ">&nbsp;$line[1]&nbsp;</OPTION>";
	}
	echo "</SELECT><br>";
}

// Display TOOL list
function get_tool_listbox(){
	// Get the global table
	global $array_tools;
	
	echo "<label>".getLabel("label.tool_all.tool")."</label>";	

	// Get the first array key
	reset($array_tools);

	// Display the list of tool
	echo "<SELECT id='tool_list' name='tool_list' class='form-control'>";
 	while (list($tool_name, $tool_url) = each($array_tools)) 
	{
		echo "<OPTION value='$tool_url'>$tool_name</OPTION>";
	}
	echo "</SELECT>";
}

// Display min and max port value for show port tool
function get_toolport_ports(){
	global $default_minport;
	global $default_maxport;
	
	echo "<label>Port min - Port max</label>";
	echo "<div class='row'><div class='col-md-4'><input id='min_port' class='form-control' type=text name='min_port' value=$default_minport size=8></div>";
	echo "<div class='col-md-4'><input id='max_port' class='form-control' type=text name='max_port' value=$default_maxport size=8></div></div>";
}

// Display User list
function get_user_listbox(){
	echo "<h2>Select user : </h2>";
        global $database_eonweb;

        $result=sql($database_eonweb,"SELECT DISTINCT user_name,user_id,group_id,user_descr FROM users ORDER BY user_name");
        print "<SELECT name='users_list' class='select' size=15>";
		foreach($result as $line){
			print "<OPTION value='$line[1]'>$line[0] : $line[3]</OPTION>";
        }
        print "</SELECT>";
}

// Retrive form data
function retrieve_form_data($field_name,$default_value){
	if (!isset ($_GET[$field_name]))
		if (!isset ($_POST[$field_name]))
			return $default_value;
		else
			return $_POST[$field_name];	
	else 
		return $_GET[$field_name];
}

// Delete accents
function stripAccents($str, $charset='utf-8'){
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); 
    $str = preg_replace('#\&[^;]+\;#', '', $str); 

    return $str;
}

// Add Logs
function logging($module,$command,$user=false){
	global $database_eonweb;
	global $dateformat;
	if($user){
		sql($database_eonweb,"insert into logs values ('',?,?,?,?,?)", array(time(), $user, $module, $command, $_SERVER["REMOTE_ADDR"]));
	}elseif(isset($_COOKIE['user_name'])){
		sql($database_eonweb,"insert into logs values ('',?,?,?,?,?)", array(time(), $user, $module, $command, $_SERVER["REMOTE_ADDR"]));
	}
}

// Time
function getmtime(){
  
    $temps = microtime();
    $temps = explode(' ', $temps);
    return $temps[1] + $temps[0];
 
}

// Get the informations of nagios' config's file.
function getBpProcess(){
	
	global $path_nagiosbpcfg ;
	global $path_nagiosbpcfg_lock ;

	wait($path_nagiosbpcfg_lock);	//Wait for the file to not be in use.
	$fp=@fopen($path_nagiosbpcfg_lock,"w");	//Lock the file
    fputs($fp,getmypid());
    fclose($fp);

    $tabProcess = array() ;
	$lines = file($path_nagiosbpcfg);
	if (!$lines) {
		unlink($path_nagiosbpcfg_lock);	//Unlock the file
		message(2,$path_nagiosbpcfg,"critical");
	}
	foreach( $lines as $line) {
		
		if ( trim($line) == "# Fin def") {	//End of definition
			$tabProcess[] = $tabProp ;
			$tabProp = null ;
		}
		elseif ( preg_match("/^# (ET|OU|MIN)$/", $line, $match)) {
			$tabProp['type'] = $match[1];	//Get the type of the process
		}
		elseif ( preg_match("/^display (\d)*/", $line, $match)) {	//Get the prirority
			$tabProp['prio'] = $match[1] ;
			$tab = explode(";",$line);
			$tabProp['pnom'] = $tab[2];
		}
		elseif ( strpos($line,"info_url") !== false) {	//Get the link
			$tab = explode(";", $line);
			$tabProp['url'] = $tab[count($tab)-1] ;
		}
		elseif ( strpos($line,"external_info") !== false) {	//Get the command
			$tab = explode(";", $line);
			$tabProp['cmd'] = $tab[count($tab)-1] ;
		}
		elseif ( strpos($line,"=") !== false) {	//Get the name, the minimun, and the services
			$tab = explode("=", $line);
			$tabProp['nom'] = trim($tab[0]);
			if ($tabProp['type'] == "MIN") {
				$tabProp['min'] = (int)trim($tab[1]);
				$tab = explode(":",$tab[1]);
				$tabProp['serv'] = $tab[1];
			}
			else $tabProp['serv'] = $tab[1];
		}
	}

	unlink($path_nagiosbpcfg_lock);	//Unlock the file
	return $tabProcess ;
}

// Wait the end of modification of a file
function wait($file){
	$retry = 0 ;

	while (file_exists($file)){
		if($retry>20) { die ("$file is already in use!"); }
        $retry++;
        sleep(1);
	}
}

// Insert a value in an array
function array_push_after($src,$in,$pos){
    if(is_int($pos)) $R=array_merge(array_slice($src,0,$pos+1), $in, array_slice($src,$pos+1));
    else{
        foreach($src as $k=>$v){
            $R[$k]=$v;
            if($k==$pos)$R=array_merge($R,$in);
        }
    }return $R;
}

//Format the nagios.conf file
function formatFile(){
	global $path_nagiosbpcfg;
	global $path_nagiosbpcfg_lock;
	global $database_nagios;

	wait($path_nagiosbpcfg_lock);	//Wait for the file to not be in use.
	$fp=@fopen($path_nagiosbpcfg_lock,"w");	//Lock the file.
	fputs($fp,getmypid());
	fclose($fp);

	$lines = file($path_nagiosbpcfg);
	$file[0] = "# Checked\n";
	if ( empty($lines) || trim($lines[0]) != "# Checked"){	//Not checked. Let's read it !

		write_file($path_nagiosbpcfg,array_merge($file,$lines),"w"," : File updated");

		sql($database_nagios,"DELETE FROM bp");
		sql($database_nagios,"DELETE FROM bp_services");
		sql($database_nagios,"DELETE FROM bp_links");
		$tabName = array();
		$tabDef = array();			

		foreach($lines as $i => $line){
			if ($line[0] == "#"){
				unset($lines[$i]); continue;	//A commented line. Delete.
			}
			if (($posComment = strpos($line,"#")) !== false){	//Found a commentary. Delete.
				$line = substr($line,0,$posComment);	
			}
			//No more commentary in the file

			if (strpos($line,"=") !== false){	//Found a name
				$tab = explode("=",$line);
				$tabName[] = trim($tab[0]);	//Keep the name
				$vals = explode("=",$line);
				$tabDef[] = $vals[1];	//Keep the whole line
				unset($lines[$i]);
			}
		}

		//There we got all the names.
		$serv = null;
		foreach($tabName as $i => $name){
			$type = $prio = $url = $cmd = $val = $desc = "" ;
			//Try to get the type. Default ET
			if ( strpos($tabDef[$i], ":")){
				$vals = explode("of:",$tabDef[$i]);
				$val = trim($vals[0]);
				$type = "MIN";
				$serv = $vals[1];
			}
			else {
				if ( strpos($tabDef[$i], "&")) $type = "ET";
				elseif ( strpos($tabDef[$i], "|")) $type = "OU";
				else $type = "ET";
				$serv = $tabDef[$i];
			}
			
			foreach($lines as $j=>$line){
				if (strpos($line,"$name;") !== false){	//We found a name
					if ( preg_match("/^display (\d)+/", $line,$match)){
						$prio = $match[1];
						$vals = explode(";",$line);
						$desc = trim($vals[2]);
					} 
					elseif ( strpos($line,"info_url") !== false) {
						$vals = explode(";",$line);
						$url = trim($vals[1]);
					}
					elseif ( strpos($line,"external_info") !== false) {
						$vals = explode(";",$line);
						$cmd= trim($vals[1]);
					}
					unset($lines[$j]);
				}
			}

			if ($prio == "" ) $prio = "null";
			sql($database_nagios,"INSERT INTO bp VALUES(?,?,?,?,?,?,?,'1')", array($name, $desc, $prio, $type, $cmd, $url, $val));

			switch ($type){
				case "ET": $vals = explode("&",$serv);
					break;
				case "OU": $vals = explode("|",$serv);
					break;
				case "MIN": $vals = explode("+",$serv);
					break;
			}
			foreach ($vals as $v) {
				if ( strpos($v,";") !== false ){
					$val = explode(";",$v); $host=trim($val[0]); $service=trim($val[1]);
					sql($database_nagios,"INSERT INTO bp_services VALUES('',?,?,?)", array($name, $host, $service));
				}
				else sql($database_nagios,"INSERT INTO bp_links VALUES('',?,?)", array($name, trim($v)));
			}
		}
		message(6," : Database updated with configuration file","ok");
	}
	unlink($path_nagiosbpcfg_lock);
}

//Write in a file, with error or succes message
function write_file($file,$contenu,$mode,$message = null){
	if(is_writable($file)){
		$error = 0 ;
		if (!$fconf = fopen($file, $mode)) message(2,$file,"critical");
		
		if ( is_array($contenu)){
			foreach ($contenu as $line) {
				if (fwrite ($fconf, $line) === FALSE) $error = 1 ;
			}
		}
		else if (fwrite ($fconf, $contenu) === FALSE) $error = 1 ;

		if ($error) message(3,$file,"critical");
		else if ( $message != null )message(6,$message,"ok");
		fclose ($fconf);
	}
	else 
		message(3,$file,"critical");
}

// MySQL request in php array 
function sqlArrayNagios($request){
	global $database_nagios;
	$result = sql($database_nagios,$request);
	$values = array();
	foreach($result as $row){
		$values[] = $result;
	}
	return $values ;
}

// NagiosBP file backup
function backup_file($start){
	global $path_nagiosbpcfg;
	global $path_nagiosbpcfg_bu;

	for ($i = $start; $i > 0; $i--){
		if ( file_exists($path_nagiosbpcfg_bu.$i)){
			if ( $i == $start) unlink($path_nagiosbpcfg_bu.$i);
			else {
				rename($path_nagiosbpcfg_bu.$i,$path_nagiosbpcfg_bu.($i+1));
			}
		}
	}
	copy($path_nagiosbpcfg,$path_nagiosbpcfg_bu.'1');
}

// NagiosBP file creation
function buildFile(){

	global $path_nagiosbpcfg_lock;
	wait($path_nagiosbpcfg_lock);	//Wait for the file to not be in use.
	$fp=@fopen($path_nagiosbpcfg_lock,"w");	//Lock the file.
	fputs($fp,getmypid());
	fclose($fp);

	global $max_bu_file;
	backup_file($max_bu_file);
	global $path_nagiosbpcfg;
	$request = "SELECT * FROM bp WHERE `name` NOT IN (SELECT bp_name FROM bp_links) AND `is_define`='1'";
	$values = sqlArrayNagios($request);
	$prevRequest = str_replace("*","name",$request); 

	$file[] = "# Checked";
	foreach( $values as $metier){
		$writenBP[] = $metier['name'];
	  	switch( $metier['type']) {
	   		case "ET" : $sep = "&";break;
	   		case "OU" : $sep = "|";break;
	   		case "MIN" : $sep = "+";break;
	   	}
	   	$result = sqlArrayNagios("SELECT host,service FROM bp_services WHERE bp_name='$metier[name]'");
	   	$strServ = $string = null;
	   	foreach($result as $serv){
	   		if ( is_null($strServ) ) {
	   			$string = "\n#\n# Name : $metier[name]\n# Type : $metier[type]\n$metier[name] = ";
	   			if ( $metier['type'] == "MIN") $string .= "$metier[min_value] of: ";
	   			$strServ = "$serv[host];$serv[service]";
	   		}
	   		else $strServ .= " $sep $serv[host];$serv[service]";
	   	}
	   	$string .= $strServ."\n";
	   	if ( $metier['priority'] != "null") $string .= "display $metier[priority];$metier[name];$metier[description]\n";
	   	if ( $metier['command'] != "") $string .= "external_info $metier[name];$metier[command]\n";
	   	if ( $metier['url'] != "") $string .= "info_url $metier[name];$metier[url]\n";
	   	$file[] = $string;
    }

	if ( $values ) build($prevRequest,$file,$writenBP);
    write_file($path_nagiosbpcfg,$file,"w"," : File updated");
    unlink($path_nagiosbpcfg_lock);
}

// Nagiosbp build
function build($pRequest,&$file,$pWritenBP){

	$values = sqlArrayNagios($pRequest);
	unset($r);
	foreach( $values as $v){
		if ( !isset($r) ) $r = "SELECT bp_name FROM bp_links WHERE (bp_link='$v[name]' ";
		else $r .= " OR bp_link='$v[name]'";
	}
	$values = sqlArrayNagios($r.")");

	if ($values){
		unset($r);
		foreach ($values as $v) {
			if ( !isset($r) ) $r = "SELECT * FROM bp WHERE (name='$v[bp_name]' ";
			else $r .= " OR name='$v[bp_name]'";
		}
		$values = sqlArrayNagios($r.") AND `is_define`='1'");
		/*$request = "SELECT * FROM bp WHERE `name` IN (SELECT bp_name FROM bp_links WHERE bp_link IN ($pRequest)) AND `is_define`='1'";
		$values = sqlArrayNagios($request);
		sql takes to much time with this type of request. We split it in multiple request instead.*/
		$prevRequest = str_replace("*","name",$r.") AND `is_define`='1'");
		$writenBP = $pWritenBP;


		foreach ($pWritenBP as $r) {
			if ( !isset($reqC)) $reqC = "SELECT COUNT(bp_name) AS nbr FROM bp_links WHERE (bp_link='$r' ";
			else $reqC .= " OR bp_link='$r'";
		}

		foreach( $values as $metier){
			if (in_array($metier, $pWritenBP)) continue;
			
			$requestC = $reqC.") AND bp_name='$metier[name]'";
			$count = sqlArrayNagios($requestC);
			$cnt = sqlArrayNagios("SELECT COUNT(bp_name) AS nbr FROM bp_links WHERE bp_name='$metier[name]'");
			
			if ( $count[0]['nbr'] == $cnt[0]['nbr']){
				$writenBP[] = $metier['name'];
			  	switch( $metier['type']) {
			   		case "ET" : $sep = "&";break;
			   		case "OU" : $sep = "|";break;
			   		case "MIN" : $sep = "+";break;
			   	}
			   	$result = sqlArrayNagios("SELECT host,service FROM bp_services WHERE bp_name='$metier[name]'");
			   	$strServ = $string = null;

			   	foreach($result as $serv){
			   		if ( is_null($strServ) ) {
			   			$string = "\n#\n# Name : $metier[name]\n# Type : $metier[type]\n$metier[name] = ";
			   			if ( $metier['type'] == "MIN") $string .= "$metier[min_value] of: ";
			   			$strServ = "$serv[host];$serv[service]";
			   		}
			   		else $strServ .= " $sep $serv[host];$serv[service]";
			   	}
			   	$result = sqlArrayNagios("SELECT bp_link FROM bp_links WHERE bp_name='$metier[name]'");
			   	foreach($result as $serv){
			   		if ( is_null($strServ) ) {
			   			$string = "\n#\n# Name : $metier[name]\n# Type : $metier[type]\n$metier[name] = ";
			   			if ( $metier['type'] == "MIN") $string .= "$metier[min_value] of: ";
			   			$strServ = "$serv[bp_link]";
			   		}
			   		else $strServ .= " $sep $serv[bp_link]";
			   	}
			   	$string .= $strServ."\n";
			   	if ( $metier['priority'] != "null") $string .= "display $metier[priority];$metier[name];$metier[description]\n";
			   	if ( $metier['command'] != "") $string .= "external_info $metier[name];$metier[command]\n";
			   	if ( $metier['url'] != "") $string .= "info_url $metier[name];$metier[url]\n";
			   	$file[] = $string;
			}
	    }

	    build($prevRequest,$file,$writenBP);
	}
}

// Ldap escape special caracters
function ldap_escape($str, $login=false, $escape=false){

	$str = trim($str);
	if ( $login ) {
		$search = array("\\\\",'"','+','>','<');
		$replace = array("\\",'\"','\\2B','\>','\<');
	} else {
		$search = array("\\","'",'"');
		$replace = array("\\\\","\'",'\"');
	}
	
	$str = str_replace($search, $replace, $str);
	if ( $escape ) { $str = str_replace("\\", "\\\\", $str); }
	
	return $str;

}

// User creation
function insert_user($user_name, $user_descr, $user_group, $user_password1, $user_password2, $user_type, $user_location, $user_mail, $user_limitation, $message, $in_nagvis = false, $in_cacti = false, $nagvis_group = false, $user_language = false, $theme = false){
	global $database_host;
	global $database_cacti;
	global $database_username;
	global $database_password;

	global $database_eonweb;
	global $database_lilac;
	$user_id=null;

	// Check if user exist
	$user_exist = sql($database_eonweb,"SELECT count('user_name') from users where user_name=?", array($user_name));
	$user_exist = $user_exist[0][0];
	// Check user descr
	if($user_descr=="")
		$user_descr=$user_name;

	if($user_location != "" && $user_location != null){
		if( strpos($user_location, " -- ") !== false && strpos($user_location, "|") !== false ){
			$user_location_parts = explode(" -- ", $user_location);
			$user_loc = explode("|", $user_location_parts[1]);
			$user_dn_name = $user_loc[0];
			$user_name = $user_dn_name;
			$user_location = $user_loc[1];
		}
		else{
			$user_location = $user_location;
		}
	}
	
	if (($user_name != "") && ($user_name != null) && ($user_exist == 0)) {
		if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {
			$user_password = md5($user_password1);
			
			// Insert into eonweb
			sql($database_eonweb,"INSERT INTO users (user_name,user_descr,group_id,user_passwd,user_type,user_location,user_limitation,user_language,theme) VALUES(?,?,?,?,?,?,?,?,?)", array($user_name, $user_descr, $user_group, $user_password, $user_type, $user_location, $user_limitation, $user_language, $theme));
			$user_id = sql($database_eonweb,"SELECT user_id FROM users WHERE user_name=?", array($user_name));
			$user_id = $user_id[0]["user_id"];
			$group_name = sql($database_eonweb,"SELECT group_name FROM groups WHERE group_id=?", array($user_group));
			$group_name = $group_name[0]["group_name"];
			// Insert into lilac
			$lilac_period = sql($database_lilac,"SELECT id FROM nagios_timeperiod limit 1");
			$lilac_period = $lilac_period[0]["id"];
			
			require_once('/srv/eyesofnetwork/lilac/includes/config.inc');
			$contact_array = array(
				"contact_name"=>$user_name,
				"alias"=>$user_descr,
				"email"=>$user_mail,
				"host_notifications_enabled"=>1,
				"service_notifications_enabled"=>1,
				"host_notification_period"=>$lilac_period,
				"service_notification_period"=>$lilac_period,
				"host_notification_on_down"=>1,
				"host_notification_on_unreachable"=>1,
				"host_notification_on_recovery"=>1,
				"host_notification_on_flapping"=>1,
				"service_notification_on_warning"=>1,
				"service_notification_on_unknown"=>1,
				"service_notification_on_critical"=>1,
				"service_notification_on_recovery"=>1,
				"service_notification_on_flapping"=>1,
				"can_submit_commands"=>1,
				"retain_status_information"=>1,
				"retain_nonstatus_information"=>1,
				"host_notification_on_scheduled_downtime"=>1
			);
			$lilac->add_contact($contact_array);

			// Lilac contact_group_member
			$lilac_contactgroupid = sql($database_lilac,"SELECT id FROM nagios_contact_group WHERE name=?", array($group_name));
			$lilac_contactgroupid = $lilac_contactgroupid[0]["id"];
			$lilac_contactid = sql($database_lilac,"SELECT id FROM nagios_contact where name=?", array($user_name));
			$lilac_contactid = $lilac_contactid[0]["id"];
			if($lilac_contactgroupid!="" and $lilac_contactid!="" and $user_limitation!="1")
				sql($database_lilac,"INSERT INTO nagios_contact_group_member (contactgroup, contact) VALUES (?, ?)", array($lilac_contactgroupid, $lilac_contactid));

			// Insert into nagvis
			if($in_nagvis == "yes"){
				$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');

				$req = $bdd->query("SELECT count(*) FROM users WHERE name = '$user_name'");
				$nagvis_user_exist = $req->fetch();

				if ($nagvis_user_exist["count(*)"] == 0){
					// this is nagvis default salt for password encryption security
					$nagvis_salt = '29d58ead6a65f5c00342ae03cdc6d26565e20954';
					
					// insert user in nagvis SQLite DB
					$sql = "INSERT INTO users (name, password) VALUES ('$user_name', '".sha1($nagvis_salt.$user_password1)."')";
					$bdd->exec($sql);

					// insert user's right as "Guest" by default
					$sql = "SELECT userId FROM users WHERE name = '$user_name'";
					$req = $bdd->query($sql);
					$result = $req->fetch();
					$nagvis_id = $result['userId'];

					$sql = "INSERT INTO users2roles (userId, roleId) VALUES ($nagvis_id, $nagvis_group)";
					$bdd->exec($sql);
				}
			}

			// Insert into cacti
			if($in_cacti == "yes"){
				$bdd = new PDO('mysql:host='.$database_host.';dbname='.$database_cacti, $database_username, $database_password);
				$req = $bdd->query("SELECT count(*) FROM user_auth WHERE username='$user_name'");
				$cacti_user_exist = $req->fetch();
				if ($cacti_user_exist["count(*)"] == 0){
					$bdd->exec("INSERT INTO user_auth (username,password,realm,full_name,show_tree,show_list,show_preview,graph_settings,login_opts,policy_graphs,policy_trees,policy_hosts,policy_graph_templates,enabled) VALUES ('$user_name','',2,'$user_descr','on','on','on','on',3,2,2,2,2,'on')");
				}
			}

			// Messages
			logging("admin_user","INSERT : $user_name $user_descr $user_limitation $user_group $user_type $user_location");
			if($message){ message(8," : User Inserted",'ok'); }

			// Lilac contact_commands
			$lilac_contact_hcommand = sql($database_lilac,"select id from nagios_command where name like 'notify-by-email-host'");
			$lilac_contact_hcommand = $lilac_contact_hcommand[0]["id"];
			$lilac_contact_scommand = sql($database_lilac,"select id from nagios_command where name like 'notify-by-email-service'");
			$lilac_contact_scommand = $lilac_contact_scommand[0]["id"];
			if($lilac_contactid!="" and $lilac_contact_hcommand!="")
				sql($database_lilac,"INSERT INTO nagios_contact_notification_command (contact_id,command,type) values (?, ?,'host')", array($lilac_contactid, $lilac_contact_hcommand));
			elseif($lilac_contact_hcommand=="")
				message(8," : Verify contact 'notify-by-email-host' command in nagios configurator",'warning');
			if($lilac_contactid!="" and $lilac_contact_scommand!="")
				sql($database_lilac, "INSERT INTO nagios_contact_notification_command (contact_id,command,type) values (?, ?,'service')", array($lilac_contactid, $lilac_contact_scommand));
			elseif($lilac_contact_scommand=="")
				message(8," : Verify contact 'notify-by-email-service' command in nagios configurator",'warning');
		}
		else
			message(8," : Passwords do not match or are empty",'warning');
	}
	elseif($user_exist != 0 )
		message(8," : User $user_name already exists",'warning');
	else
		message(8," : User name can not be empty",'warning');
	return $user_id;
}


// get traduction words
function getLabel($reference){

        global $dictionnary;
        global $path_messages;
        global $path_messages_custom;
        global $t;

        // Load dictionnary if not isset
        if(!isset($t)) {
                $t = new Translator();
                $t->initFile($path_messages,$path_messages_custom);
                $dictionnary = $t->createPHPDictionnary();
        }

        // Display dictionnary reference if isset or reference
        if(isset($dictionnary[$reference])) {
                $label = $dictionnary[$reference];
        }
        else {
                $label = $reference;
        }

        return $label;

}

// get default page
function getDefaultPage($usrlimit=0){

	global $t;
	global $defaultpage;
	global $path_frame;
	global $path_menu_limited;
	global $path_menu_limited_custom;
	global $path_menus;
	global $path_menus_custom;

	// load dictionnary if not isset
	if(!isset($t)) {
		$t = new Translator();
	}
	
	// get json file
	if(isset($_COOKIE["user_limitation"])) { $usrlimit = $_COOKIE["user_limitation"]; }
	if($usrlimit == 1){
		$file=$t->getFile($path_menu_limited, $path_menu_limited_custom);
		$json_content = file_get_contents($file);
		$links = json_decode($json_content, true);
		foreach ($links["link"] as $link) {
			if(isset($link["default"])) {
				if( $link["default"] != null ){
					if($link["target"]=="frame") { $link["url"]=$path_frame.urlencode($link['url']); }
					$defaultpage = $link["url"];
				}
			}
		}
	} 
	
	return $defaultpage;

}

// get frame url
function getFrameURL($url){
	global $path_frame;
	
	$frame_url = $path_frame.urlencode($url);
	return $frame_url;
}

function pieChart($queue, $field, $search, $period)
{
	// all external variables we need
	global $database_ged;
	global $array_ged_states;
	if($queue == "active"){ global $ged_active_intervals; extract($ged_active_intervals); }
	else{ global $ged_history_intervals; extract($ged_history_intervals); }
	
	$array_result = array();
	$sql = "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100'";
	$pkt_result = sql($database_ged, $sql);
	
	// set the search clause (according to field and value)
	$search_clause = "";
	if( isset($search) && $search != "" )
	{
		switch ($field) {
			case 'host': $field = 'equipment'; break;
			case 'hostgroup': $field = 'hostgroups'; break;
			case 'service_group': $field = 'servicegroups'; break;
		}
		$like = "'";
		if( substr($search, 0, 1) === '*' ){
			$like .= "%";
		}
		$like .= trim($search, '*');
		if ( substr($search, -1) === '*' ) {
			$like .= "%";
		}
		$like .= "'";
		$search_clause = " AND $field LIKE $like";
	}
	
	// set the period clause (according to checkboxes checked)
	$period_clause = "";
	if( isset($period) && $period != "" )
	{
		switch($period)
		{
			case "day": $period_clause = " AND o_sec >= $day"; break;
			case "week": $period_clause = " AND o_sec >= $week AND o_sec < $day"; break;
			case "month": $period_clause = " AND o_sec >= $month AND o_sec < $week"; break;
			case "year": $period_clause = " AND o_sec >= $year AND o_sec < $month"; break;
		}
	}
	
	foreach($pkt_result as $pkt){
		foreach($array_ged_states as $key => $state)
		{
			if($key == "ok")
			{
				continue;
			}
			
			if( !isset($array_result[$key]) ){
				$array_result[$key] = 0;
			}
			$sql = "SELECT count(id) FROM ".$pkt[0]."_queue_".$queue." WHERE state=? AND queue=?";
			$sql .= $search_clause;
			$sql .= $period_clause;
			
			$result = sql($database_ged, $sql, array($state, substr($queue{0},0,1)));
			$array_result[$key] += $result[0][0];
		}
	}
	return json_encode($array_result);
}

function barChart($queue, $field, $search)
{
	global $database_ged;
	global $array_ged_states;
	if($queue == "active"){ global $ged_active_intervals; extract($ged_active_intervals); }
	else{ global $ged_history_intervals; extract($ged_history_intervals); }
	
	
	$sql = "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100'";
	$pkt_result = sql($database_ged, $sql);
	
	$array_result = array();
	$array_now_day = array();
	$array_day_week = array();
	$array_week_month = array();
	$array_month_year = array();
	$array_year_more = array();
	
	// set the search clause (according to field and value)
	$search_clause = "";
	if( isset($search) && $search != "" )
	{
		switch ($field) {
			case 'host': $field = 'equipment'; break;
			case 'hostgroup': $field = 'hostgroups'; break;
			case 'service_group': $field = 'servicegroups'; break;
		}
		$like = "'";
		if( substr($search, 0, 1) === '*' ){
			$like .= "%";
		}
		$like .= trim($search, '*');
		if ( substr($search, -1) === '*' ) {
			$like .= "%";
		}
		$like .= "'";
		$search_clause = " AND $field LIKE $like";
	}
	
	foreach($pkt_result as $pkt){
		foreach($array_ged_states as $key => $state)
		{
			if($key == "ok")
			{
				continue;
			}
			
			if( !isset($array_now_day[$key]) ){$array_now_day[$key] = 0;}
			if( !isset($array_day_week[$key]) ){$array_day_week[$key] = 0;}
			if( !isset($array_week_month[$key]) ){$array_week_month[$key] = 0;}
			if( !isset($array_month_year[$key]) ){$array_month_year[$key] = 0;}
			if( !isset($array_year_more[$key]) ){$array_year_more[$key] = 0;}
			$sql = "
				SELECT count(id) FROM ".$pkt[0]."_queue_".$queue." WHERE state='".$state."' AND queue='".substr($queue{0},0,1)."' AND o_sec >= $day".$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_".$queue." WHERE state='".$state."' AND queue='".substr($queue{0},0,1)."' AND o_sec >= $week AND o_sec < $day".$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_".$queue." WHERE state='".$state."' AND queue='".substr($queue{0},0,1)."' AND o_sec >= $month AND o_sec < $week".$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_".$queue." WHERE state='".$state."' AND queue='".substr($queue{0},0,1)."' AND o_sec >= $year AND o_sec < $month".$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_".$queue." WHERE state='".$state."' AND queue='".substr($queue{0},0,1)."' AND o_sec < $year".$search_clause;
			$result = sql($database_ged, $sql);
			
			$cpt = 0;
			foreach($result as $row){
				switch($cpt)
				{
					case 0: $array_now_day[$key] += $row[0]; break;
					case 1: $array_day_week[$key] += $row[0]; break;
					case 2: $array_week_month[$key] += $row[0]; break;
					case 3: $array_month_year[$key] += $row[0]; break;
					case 4: $array_year_more[$key] += $row[0]; break;
				}
				$cpt++;
			}
		}
	}
	array_push($array_result, $array_now_day);
	array_push($array_result, $array_day_week);
	array_push($array_result, $array_week_month);
	array_push($array_result, $array_month_year);
	array_push($array_result, $array_year_more);
	
	return json_encode($array_result);
}

function slaPieChart($field, $search, $period)
{
	// all external variables we need
	global $database_ged;
	global $ged_sla_intervals;
	global $ged_history_intervals;
	extract($ged_sla_intervals);
	extract($ged_history_intervals);
	
	$array_result = array();
	$sql = "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100'";
	$pkt_result = sql($database_ged, $sql);
	
	// set the search clause (according to field and value)
	$search_clause = "";
	if( isset($search) && $search != "" )
	{
		switch ($field) {
			case 'host': $field = 'equipment'; break;
			case 'hostgroup': $field = 'hostgroups'; break;
			case 'service_group': $field = 'servicegroups'; break;
		}
		$like = "'";
		if( substr($search, 0, 1) === '*' ){
			$like .= "%";
		}
		$like .= trim($search, '*');
		if ( substr($search, -1) === '*' ) {
			$like .= "%";
		}
		$like .= "'";
		$search_clause = " AND $field LIKE $like";
	}
	
	// set the period clause (according to checkboxes checked)
	$period_clause = "";
	if( isset($period) && $period != "" )
	{
		switch($period)
		{
			case "day": $period_clause = " AND o_sec >= $day"; break;
			case "week": $period_clause = " AND o_sec >= $week AND o_sec < $day"; break;
			case "month": $period_clause = " AND o_sec >= $month AND o_sec < $week"; break;
			case "year": $period_clause = " AND o_sec >= $year AND o_sec < $month"; break;
		}
	}
	
	foreach($pkt_result as $pkt){
		foreach($ged_sla_intervals as $key => $value)
		{
			if( !isset($array_result[$key]) ){
				$array_result[$key] = 0;
			}
			
			$sla_clause = "";
			switch($key)
			{
				case "first" : $sla_clause = " AND a_sec-o_sec < $first"; break;
				case "second": $sla_clause = " AND a_sec-o_sec >= $first AND a_sec-o_sec < $second"; break;
				case "third" : $sla_clause = " AND a_sec-o_sec >= $second AND a_sec-o_sec < $third"; break;
				case "fourth": $sla_clause = " AND a_sec-o_sec >= $third"; break;
			}
			$sql = "SELECT count(id) FROM ".$pkt[0]."_queue_history WHERE queue='h' AND state!='0'".$sla_clause;
			$sql .= $search_clause;
			$sql .= $period_clause;
			
			$result = sql($database_ged, $sql);
			$array_result[$key] += $result[0];
		}
	}
	return json_encode($array_result);
}

function slaBarChart($field, $search)
{
	// all external variables we need
	global $database_ged;
	global $array_ged_states;
	global $ged_sla_intervals;
	global $ged_history_intervals;
	extract($ged_sla_intervals);
	extract($ged_history_intervals);
	
	$array_result = array();
	$array_now_day = array();
	$array_day_week = array();
	$array_week_month = array();
	$array_month_year = array();
	$array_year_more = array();
	
	$array_result = array();
	$sql = "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100'";
	$pkt_result = sql($database_ged, $sql);
	
	// set the search clause (according to field and value)
	$search_clause = "";
	if( isset($search) && $search != "" )
	{
		switch ($field) {
			case 'host': $field = 'equipment'; break;
			case 'hostgroup': $field = 'hostgroups'; break;
			case 'service_group': $field = 'servicegroups'; break;
		}
		$like = "'";
		if( substr($search, 0, 1) === '*' ){
			$like .= "%";
		}
		$like .= trim($search, '*');
		if ( substr($search, -1) === '*' ) {
			$like .= "%";
		}
		$like .= "'";
		$search_clause = " AND $field LIKE $like";
	}
	
	foreach($pkt_result as $pkt){
		foreach($ged_sla_intervals as $key => $value)
		{
			if( !isset($array_now_day[$key]) ){$array_now_day[$key] = 0;}
			if( !isset($array_day_week[$key]) ){$array_day_week[$key] = 0;}
			if( !isset($array_week_month[$key]) ){$array_week_month[$key] = 0;}
			if( !isset($array_month_year[$key]) ){$array_month_year[$key] = 0;}
			if( !isset($array_year_more[$key]) ){$array_year_more[$key] = 0;}
			
			switch($key)
			{
				case "first" : $sla_clause = " AND a_sec-o_sec < $first"; break;
				case "second": $sla_clause = " AND a_sec-o_sec >= $first AND a_sec-o_sec < $second"; break;
				case "third" : $sla_clause = " AND a_sec-o_sec >= $second AND a_sec-o_sec < $third"; break;
				case "fourth": $sla_clause = " AND a_sec-o_sec >= $third"; break;
			}
			$sql = "SELECT count(id) FROM ".$pkt[0]."_queue_history WHERE queue='h' AND state !='0' AND o_sec >= $day".$sla_clause.$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_history WHERE queue='h' AND state !='0' AND o_sec >= $week AND o_sec < $day".$sla_clause.$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_history WHERE queue='h' AND state !='0' AND o_sec >= $month AND o_sec < $week".$sla_clause.$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_history WHERE queue='h' AND state !='0' AND o_sec >= $year AND o_sec < $month".$sla_clause.$search_clause.
				" UNION ALL
				SELECT count(id) FROM ".$pkt[0]."_queue_history WHERE queue='h' AND state !='0' AND o_sec < $year".$sla_clause.$search_clause;
			$result = sql($database_ged, $sql);
			
			$cpt = 0;
			foreach($result as $row){
				switch($cpt)
				{
					case 0: $array_now_day[$key] += $row[0]; break;
					case 1: $array_day_week[$key] += $row[0]; break;
					case 2: $array_week_month[$key] += $row[0]; break;
					case 3: $array_month_year[$key] += $row[0]; break;
					case 4: $array_year_more[$key] += $row[0]; break;
				}
				$cpt++;
			}
		}
	}
	array_push($array_result, $array_now_day);
	array_push($array_result, $array_day_week);
	array_push($array_result, $array_week_month);
	array_push($array_result, $array_month_year);
	array_push($array_result, $array_year_more);
	
	return json_encode($array_result);
}

# Convert seconds to human readable
function strTime($s) {

	$d = intval($s/86400);
	$s -= $d*86400;
	$h = intval($s/3600);
	$s -= $h*3600;
	$m = intval($s/60);
	$s -= $m*60;

	if($d<10) $d="0".$d;
	if($h<10) $h="0".$h;
	if($m<10) $m="0".$m;
	if($s<10) $s="0".$s;

	if ($d) $str = $d . 'd ';
	else $str = '00d ';
	if ($h) $str .= $h . 'h ';
	else $str .= '00h ';
	if ($m) $str .= $m . 'm ';
	else $str .= '00m ';
	if ($s) $str .= $s . 's';
	else $str .= '00s';

	return $str;

}

# Get eon config values
function getEonConfig($name,$type=false)
{

	global $database_eonweb;
	global ${$name};
	
	// mysql request	
	$sql = "SELECT value FROM configs WHERE name=?";
	$value = sql($database_eonweb, $sql, array($name));
	$result = $value;
	
	// return value if exists
	if(count($result)==0) {
		return ${$name};
	} elseif($type=="array") {
		return explode(",",$result[0]);
	} else {
		return $result[0];
	}
	
}

function startSessionTheme(){
	global $database_eonweb;

	if(isset($_COOKIE["user_name"])){

			$theme_value = sql($database_eonweb, "SELECT `theme` FROM users WHERE user_name = ?", array($_COOKIE["user_name"]));
			$theme_value = $theme_value[0];
			if($theme_value[0] == "Default"){
				$theme_value = sql($database_eonweb, "SELECT value FROM configs WHERE name = 'theme'");
				$theme_value = $theme_value[0];
			} 

			$_SESSION["theme"] = $theme_value[0];
	} else {
		$theme_value = sql($database_eonweb, "SELECT value FROM configs WHERE name = 'theme'");
		$theme_value = $theme_value[0]["value"];
		$_SESSION["theme"] = $theme_value;
	}
}

function checkUpdateDB(){
	global $version;
	global $database_eonweb;
	global $database_password;
	global $path_eon;

	$database_username="root";
	$dir=$path_eon."conf/eonweb/updates/";

	$version_sql = sql($database_eonweb,"SELECT count(value) as value FROM configs WHERE name='version'");
	if($version_sql[0]["value"] == 0){
		$version_sql = sql($database_eonweb,"INSERT INTO configs (name, value) VALUES('version', ?)", array($version));

		// execution de tous les .sql jusqu'à la version donné dans config
		$SQL_Files = array_slice(scandir($dir), 2);
		usort($SQL_Files, 'version_compare');

		foreach($SQL_Files as $file){
		if(version_compare(rtrim($file,'.sql'), $version) >= 0) {
			exec("mysql -f -u $database_username --password=$database_password < $dir$file");
		}
	}
	}else{
		$versionBD = sql($database_eonweb,"SELECT value FROM configs WHERE name='version'");
		$versionBD = $versionBD[0]["value"];

		// execution des .sql entre version en BD et celle config
		$SQL_Files = array_slice(scandir($dir), 2);
		usort($SQL_Files, 'version_compare');
		$version=rtrim(end($SQL_Files),'.sql');

		if($versionBD!=$version) {
			foreach($SQL_Files as $file){
				if(version_compare(rtrim($file,'.sql'), $versionBD) >= 0) {
					exec("mysql -f -u $database_username --password=$database_password < $dir$file");
				}
			}
			sql($database_eonweb,"UPDATE configs SET value= ? WHERE name='version'", array($version));
		}
	}
}

?>

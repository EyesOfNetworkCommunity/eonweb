<?php
	include("../../include/config.php");
	include("../../include/arrays.php");
	include("../../include/function.php");

	function getServices($name) {
	
		global $path_nagios_ser ;

		$tabServices = array() ;
		$lignes = file($path_nagios_ser);
		foreach( $lignes as $ligne) {
			
			if ( preg_match("/^host_name	$name$/", trim($ligne), $match)) {	//Get Host name
				$hasMatch = 1 ;
			}
			elseif ( preg_match("/^service_description	([0-9a-zA-Z.\-_ ]*)$/", trim($ligne), $match)) {	//Get service name
				if ($hasMatch)
					$tabServices[] = $match[1] ;
				$hasMatch = 0;
			}
		}

		return $tabServices ;
	}

	function mysqli_evaluate_array() {
		global $database_lilac;
		$result = sqlrequest($database_lilac,"select name,address from nagios_host");
		$values = array();
		for ($i=0; $i<mysqli_num_rows($result); ++$i)
			$values[] = mysqli_fetch_assoc($result);
		return $values;
	}

	function make_assoc($table){
		$result = array();
		foreach($table as $values){
			$result[$values['name']] = $values['address'];
		}
		return $result;
	}

switch($_POST['option']){
	case "select" :
		$results=make_assoc(mysqli_evaluate_array());
		$exist = false;

		if ( isset($results[$_POST['valeur']]))	$exist = true ;
		else{
			reset($results);
			while (list($key,$val) = each($results)){
				if ($val == $_POST['valeur']){
					$_POST['valeur'] = $key;
					$exist = true;
					break;
				}
			}	
		}

		if ($exist){
			$tabs = getServices($_POST['valeur']);

			echo "<option value='Hoststatus'>Hoststatus</option>;";
			foreach($tabs as $tab){
				echo "<option value='$tab'>$tab</option>;";
			}
			echo $_POST['valeur'];
		}
		else echo "error";
		break ;
	case "change" :
		if ($_POST['valeur'] == "true"){
			echo "<select size='1' name='equip[]' id='select' style='width:360px;' onChange='javascript:selectValue(\"select\",this.value);'>";
					$results=mysqli_evaluate_array() ;
					foreach ($results as $result){
						echo "<option value='$result[name]'>$result[name]  -  $result[address]</option>";
					}
			echo "</select>";
		}
		else {
			echo "<input type='text' name='equip' style='width:310px;' value='Rechercher ...' onclick='if(this.value==\"Rechercher ...\") this.value=\"\"' onFocus='$(\"input[name]=equip\").autocomplete(";
			echo	get_host_list();	//Dotted notation doesn't concatenate the json return values.
			echo ")' autocomplete='off' onBlur='javascript:selectValue(\"select\",this.value)';/>";
		}
		break;
	case "delete" :
		switch( $_POST['val']){
			case "serv":
				$vals = explode(";",$_POST['name']);
				$result = sqlrequest($database_nagios,"DELETE FROM bp_services WHERE `bp_name`='$_POST[bp]' AND `host`='$vals[0]' AND `service`='$vals[1]' ");
				break;
			case "proc":
				$result = sqlrequest($database_nagios,"DELETE FROM bp_links WHERE `bp_name`='$_POST[bp]' AND `bp_link`='$_POST[name]'");
				break;
		}
		if ( $result ) {
			$type = mysqli_fetch_assoc(sqlrequest($database_nagios,"SELECT type,min_value FROM bp WHERE name='$_POST[bp]'"));
			if ( $type['type'] == "MIN") {
				$return = sqlArrayNagios("SELECT COUNT(bp_name) as nbr FROM bp_services WHERE bp_name='$_POST[bp]' UNION select COUNT(bp_name) FROM bp_links WHERE bp_name='$_POST[bp]'");
				if ( ($return[0]['nbr']+$return[1]['nbr']) < $type['min_value']) $result = sqlrequest($database_nagios,"UPDATE bp SET `is_define`='0' WHERE `name`='$_POST[bp]'");
			}
			else {
				$return = sqlrequest($database_nagios,"SELECT bp_name as name FROM bp_services WHERE bp_name='$_POST[bp]' UNION select bp_name FROM bp_links WHERE bp_name='$_POST[bp]'");
				if ( !mysqli_num_rows($return)) $result = sqlrequest($database_nagios,"UPDATE bp SET `is_define`='0' WHERE `name`='$_POST[bp]'");
			}
		}
		else message(0,": Could not update Database","critical");
		if ( !$result )  message(0,": Could not update Database","critical");
		else message(6," : Value deleted","ok");
		break ;
	case "update" :
		switch( $_POST['val']){
			case "serv":
				$vals = explode(";",$_POST['name']);
				$result = sqlrequest($database_nagios,"INSERT INTO bp_services VALUES('','$_POST[bp]','$vals[0]','$vals[1]')");
				break;
			case "proc":
				$result = sqlrequest($database_nagios,"INSERT INTO bp_links VALUES('','$_POST[bp]','$_POST[name]')");
				break;
		}
		if ( $result ) {
			$type = mysqli_fetch_assoc(sqlrequest($database_nagios,"SELECT type,min_value FROM bp WHERE name='$_POST[bp]'"));
			if ( $type['type'] == "MIN") {
				$return = sqlArrayNagios("SELECT COUNT(bp_name) as nbr FROM bp_services WHERE bp_name='$_POST[bp]' UNION select COUNT(bp_name) FROM bp_links WHERE bp_name='$_POST[bp]'");
				if ( ($return[0]['nbr']+$return[1]['nbr']) >= $type['min_value']) $result = sqlrequest($database_nagios,"UPDATE bp SET `is_define`='1' WHERE `name`='$_POST[bp]'");

			}
			else $result = sqlrequest($database_nagios,"UPDATE bp SET `is_define`='1' WHERE `name`='$_POST[bp]'");
		}
		else message(0,": Could not update Database","critical");
		if ( !$result ) message(0,": Could not update Database","critical");
		else message(6," : Value added","ok");
		break ;
	case "survey" :
		echo file_get_contents($path_nagiosbpcfg_bu.$_POST['valeur']);
		break;
}
?>

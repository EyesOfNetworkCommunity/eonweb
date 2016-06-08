<?php

$action = $_GET['action'];
$bp_name = $_GET['bp_name'];
$host_name = $_GET['host_name'];
$service = $_GET['service'];
$new_services = $_GET['new_services'];

$uniq_name = $_GET['uniq_name'];
$process_name = $_GET['process_name'];
$display = $_GET['display'];
$url = $_GET['url'];
$command = $_GET['command'];
$type = $_GET['type'];
$min_value = $_GET['min_value'];

try {
        $bdd = new PDO('mysql:host=localhost;dbname=nagiosbp', 'root', 'root66');
    } catch(Exception $e) {
		 echo "Connection failed: " . $e->getMessage();
        exit('Impossible de se connecter à la base de données.');
    }

if($action == 'verify_services'){
        verify_services($bp_name,$host_name,$bdd);
}

elseif($action == 'delete_bp'){
    delete_bp($bp_name,$bdd);
}

elseif($action == 'list_services'){
    list_services($host_name);
}

elseif($action == 'list_process'){
	list_process($display,$bdd);
}

elseif ($action == 'add_services'){
	add_services($bp_name,$new_services,$bdd);
}

elseif ($action == 'add_process'){
    add_process($bp_name,$new_services,$bdd);
}

elseif ($action == 'add_application'){
	add_application($uniq_name,$process_name,$display,$url,$command,$type,$min_value,$bdd);
}

elseif ($action == 'build_file'){
	build_file($bdd);
}

elseif ($action == 'info_application'){
	info_application($bp_name,$bdd);
}

function verify_services($bp,$host,$bdd){
	$sql = "select COUNT(*),service from bp_services where bp_name = '" . $bp . "' and host = '". $host . "'";
	$req = $bdd->query($sql);
	$informations = $req->fetch();
	$number_services = intval($informations['COUNT(*)']);
	$service = $informations['service'];

	echo $bp . "::" . $host . "::" . $number_services . "::" . $service;
}

function delete_bp($bp,$bdd){
    $sql = "delete from bp where name = '" . $bp . "'";
    $bdd->exec($sql);

	$sql = "delete from bp_services where bp_name = '" . $bp . "'";
    $bdd->exec($sql);

	$sql = "delete from bp_links where bp_name = '" . $bp . "'";
	$bdd->exec($sql);
}

function list_services($host_name){
	$path_nagios_ser = "/srv/eyesofnetwork/nagios/etc/objects/services.cfg";

	$tabServices = array() ;
    $lignes = file($path_nagios_ser);
    foreach( $lignes as $ligne) {

        if ( preg_match("/$host_name$/", trim($ligne), $match)) {  //Get Host name
            $hasMatch = 1 ;
        }
        elseif ( preg_match("#^service_description#", trim($ligne))) {
			$service = preg_split("/[\s]+/", trim($ligne));
            if ($hasMatch)
                $tabServices['service'][] = $service[1];
            $hasMatch = 0;
        }
    }
	echo json_encode($tabServices);
}

function list_process($display,$bdd){
	$sql = "select name from bp where is_define = 1 and priority = '" . $display . "'";
	$req = $bdd->query($sql);
	$process = $req->fetchall();

    echo json_encode($process);
}

function add_services($bp,$services,$bdd){
	$list_services = array();
	$old_list_services = array();

	foreach($services as $values){
        $value = explode("::", $values);
        $service = $value[1];
		$list_services[] = $service;
	}
	$sql = "select service,host from bp_services where bp_name = '" . $bp . "'";
	$req = $bdd->query($sql);

	while($old_service = $req->fetch()){
		if(! in_array($old_service['service'], $list_services)) {
			$sql = "delete from bp_services where bp_name = '" . $bp . "' and host = '" . $old_service['host'] . "' and service = '" . $old_service['service'] . "'";
    		$bdd->exec($sql);
		}
		$old_list_services[] = $old_service['service'];
	}
		
	//$sql = "delete from bp_services where bp_name = '" . $bp . "'";
	//$bdd->exec($sql);

	if(count($services) > 0){
		$sql = "update bp set is_define = 1 where name = '" . $bp . "'";
		$bdd->exec($sql);
	}

	else{
		$sql = "update bp set is_define = 0 where name = '" . $bp . "'";
        $bdd->exec($sql);
    }

	foreach($services as $values){
		$value = explode("::", $values);
		$host = $value[0];
		$service = $value[1];

		if(! in_array($service, $old_list_services)){
			echo $service;

			$sql = "insert into bp_services (bp_name,host,service) values('" . trim($bp) . "','" . $host . "','" . $service . "')";

			$bdd->exec($sql);
		}
	}
}

function add_process($bp,$process,$bdd){
    $sql = "delete from bp_links where bp_name = '" . $bp . "'";
    $bdd->exec($sql);

    if(count($process) > 0){
        $sql = "update bp set is_define = 1 where name = '" . $bp . "'";
        $bdd->exec($sql);
    }

    foreach($process as $values){
        $value = explode("::", $values);
        $bp_link = $value[1];

        $sql = "insert into bp_links (bp_name,bp_link) values('" . $bp . "','" . $bp_link . "')";

        $bdd->exec($sql);
    }
}

function add_application($uniq_name,$process_name,$display,$url,$command,$type,$min_value,$bdd){
	if($type != 'MIN'){
		$min_value = "";
	}
	$sql = "select count(*) from bp where name = '" . $uniq_name . "'";
	$req = $bdd->query($sql);
	$bp_exist = $req->fetch();
	if($bp_exist["count"] == 0){
		$sql = "insert into bp (name,description,priority,type,command,url,min_value) values('" . $uniq_name ."','" . $process_name ."','" . $display . "','" . $type . "','" . $command . "','" . $url . "','" . $min_value . "')";
	}
	else{
		$sql = "update bp set name = '" . $uniq_name . "',description = '" . $process_name . "',priority = '" . $display . "',type = '" . $type . "',command = '" . $command . "',url = '" . $url . "',min_value = '" . $min_value . "' where name = '" . $uniq_name . "'";
	}

	$bdd->exec($sql);
}

function build_file($bdd){
	$sql = "SELECT * FROM bp where is_define ='1'";
	$req = $bdd->query($sql);
	$bps_informations = $req->fetchall();
	$file = "../../../../nagiosbp/etc/nagios-bp.conf";
	$backup_file = "../../../../nagiosbp/etc/nagios-bp.conf_old";
	copy($file,$backup_file);
	$bp_file = fopen($file, "w");
	fputs($bp_file, "#\n");
	fputs($bp_file, "# EyesOfNetwork\n");
	fputs($bp_file, "#\n");
	foreach($bps_informations as $bp_informations){
		fputs($bp_file, $bp_informations['name'] . " = ");
		if($bp_informations['type'] == 'ET'){
			$type = "&";
		}
		elseif($bp_informations['type'] == 'OU'){
			$type = "|";
		}
		else{
			$type = "+";
			fputs($bp_file, $bp_informations['min_value'] . " of: ");
		}
		$sql = "select host,service from bp_services where bp_name = '" . $bp_informations['name'] . "'";
		$req = $bdd->query($sql);
		$host_services = $req->fetchall();

		$counter1 = count($host_services);
		$counter2 = 0;

		foreach($host_services as $services){
			fputs($bp_file,$services['host'] . ";" . $services['service']);
			$counter2 += 1;

			if($counter2 < $counter1){
				fputs($bp_file, " " . $type . " ");
			}
		}

		$sql = "select bp_link from bp_links where bp_name = '" .$bp_informations['name'] . "'";
        $req = $bdd->query($sql);
        $link_informations = $req->fetchall();

        $counter1 = count($link_informations);
        $counter2 = 0;

        foreach($link_informations as $link_infos){
            fputs($bp_file,$link_infos['bp_link']);
            $counter2 += 1;

            if($counter2 < $counter1){
                fputs($bp_file, " " . $type . " ");
			}
        }

		fputs($bp_file, "\n");

		fputs($bp_file, "display " . $bp_informations['priority'] . ";" . $bp_informations['name'] . ";" . $bp_informations['description'] . "\n");

		if(! empty($bp_informations['url'])){
			fputs($bp_file, "info_url " . $bp_informations['name'] . ";" . $bp_informations['url']);
		}

		if(! empty($bp_informations['command'])){
            fputs($bp_file, "external_info " . $bp_informations['name'] . ";" . $bp_informations['command']);
        }
	}
	fclose($bp_file);
}

function info_application($bp_name, $bdd){
	$sql = "select * from bp where name = '" . $bp_name . "'";
	$req = $bdd->query($sql);
	$info = $req->fetch();
	echo json_encode($info);
}

?>

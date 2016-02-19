<?php
include("../../include/function.php");
include("../../include/config.php");
global $database_eonweb;
// Search function for Jquery an exit
if(isset($_GET['q']) && isset($_GET['request']) && $_GET['request'] == "search_group") {
	$result=sqlrequest($database_eonweb,"select * from ldap_groups_extended where group_name LIKE '%".$_GET['q']."%' order by group_name");
	//var_dump($result);
	while ($line = mysqli_fetch_array($result)){
		print($line[1]."\n");
	}
	exit();
}
?>

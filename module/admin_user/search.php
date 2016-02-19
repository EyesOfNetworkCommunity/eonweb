<?php
include("../../include/function.php");
include("../../include/config.php");
global $database_eonweb;
// Search function for Jquery an exit
if(isset($_GET['q']) && isset($_GET['request']) && $_GET['request'] == "search_user") {
	$result=sqlrequest($database_eonweb,"select * from ldap_users_extended where (user LIKE '%".$_GET['q']."%') OR (login LIKE '%".$_GET['q']."%') order by user");
	while ($line = mysqli_fetch_array($result)){
		print($line[2]." -- ".$line[1]."|".$line[0]. "\n");
	}
	exit();
}
?>


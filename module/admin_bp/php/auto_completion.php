<?php

include("../../../include/config.php");
include("../../../include/function.php");

// Mot tapé par l'utilisateur
$q = $_GET['query'];
$table_name = $_GET['table_name'];

// Requête SQL
$datas = array($q."%");
$requete = sql($database_lilac, "SELECT name FROM " . $table_name .  " WHERE name LIKE ? LIMIT 0, 10", $datas);
$requete = $requete[0];
for($i = 0; $i<count($requete)/2; $i++){
	$suggestions['suggestions'][] = $requete[$i];

}
echo json_encode($suggestions);

?>

<?php

include("../../../include/config.php");
include("../../../include/function.php");

// Mot tapé par l'utilisateur
$q = $_GET['query'];
$table_name = $_GET['table_name'];

// Requête SQL
$datas = array(
	$table_name,
	$q
);
$requete = sql($database_lilac, "SELECT name FROM ? WHERE name LIKE '?%' LIMIT 0, 10", $datas);
$requete = $requete[0];
foreach($requete as $row) {
	$suggestions['suggestions'][] = $row['name'];
}
echo json_encode($suggestions);

?>

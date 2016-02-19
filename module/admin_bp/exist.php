<?php
	include("../../include/config.php");
	include("../../include/arrays.php");
	include("../../include/function.php");
	//Read the file to get the informations.
	$result = sqlrequest($database_nagios,"SELECT * FROM bp WHERE name='$_POST[valeur]'");
	$metier = mysqli_fetch_assoc($result);
	
	if ( $metier ){
		foreach ($metier as $attribut) echo trim($attribut)."\n";
		echo "mod";
		exit ;
	}

	//Check if the name is correct. -- \\\ To match a single \ !
	$regEx = "/[\\\àáâãäåçèéêëìíîïðòóôõöùúûüýÿ!\"#$%&'()*+,.\/:;<=>^`{|}~?@[\]²§€\s]/";

	if ( preg_match($regEx,$_POST['valeur'])) {
		message(10," - Wrong caracteres (No Accents nor Whitespaces nor Ponctuation except - and _ )","warning");
		exit;
	}

	echo "ok";
	exit;
?>
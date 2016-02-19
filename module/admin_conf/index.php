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
<html>

<head>

<?php
include("../../include/include_module.php");
include_once("./request.php");
?>

</head>

<body id="main">

<h1><?php echo $xmlmodules->getElementsByTagName("admin_conf")->item(0)->getAttribute("title")?></h1>

<?php
function createFile($name,$request) {

        global $database_host;
        global $database_username;
        global $database_password;
        global $database_lilac;
	global $path_eonweb;
        global $dir_imgcache;

        $file=fopen("/tmp/".$name.".csv","w");
		
		$result = sqlrequest($database_lilac, $request);

        echo "<table class='table'><tr>";
        $line="";
        while($i = mysqli_fetch_field($result)){
                echo "<th>".$i->name."</th>";
                $line=$line.";".$i->name;
        }
        fwrite($file,str_replace("\\","",utf8_decode(substr($line,1)))."\n");
        echo "</tr>";
        while($i=mysqli_fetch_row($result)){
                echo "<tr>";
                $line="";
                for($j=0;$j<count($i);$j++){
                        $line="$line;$i[$j]";
                        echo "<td>".$i[$j]."</td>";
                }
                fputs($file,str_replace("\\","",utf8_decode(substr($line,1)))."\n");
                echo "</tr>";
        }
        echo "</table>";

        fclose($file);
        mysqli_close($connect);
}
?>

<form action="index.php" method="post">

<select id="object" name="object">
<?php
foreach($request as $object => $request){
        if(isset($_POST["object"])){
                if($object==$_POST["object"])
                        $selected="selected";
                else
                        $selected="";
        }
        echo "<option ".$selected." value=\"".$object."\">".$object."</option>";
}
?>
</select>

<input type="submit" class="button" value="Submit">

<?php
if(isset($_POST["object"])){
        include("./request.php");
        echo "<br>File : <a href=\"./download.php?file=".$_POST["object"].".csv\">".$_POST["object"]."</a><br><br>";
        createFile($_POST["object"],$request[$_POST["object"]]);
}
?>

<br>

</form>

</body>

</html>

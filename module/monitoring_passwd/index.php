<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe Levy
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

$login=$_COOKIE['user_name'];
$usrid=$_COOKIE['user_id'];
$user_password1= "abcdefghijklmnopqrstuvwxyz";
$user_password2= "abcdefghijklmnopqrstuvwxyz";
?>

<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <?php include("../../include/include_module.php"); ?>
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
</head>
<body id="main">

<h1><?php echo $xmlmodules->getElementsByTagName("admin_user")->item(0)->getAttribute("title")?></h1>

<?php
if(isset($_POST["update"])) {
	$user_password1 = retrieve_form_data("user_password1","");
	$user_password2 = retrieve_form_data("user_password2","");
	if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {
		if($user_password1!="abcdefghijklmnopqrstuvwxyz") {
			$user_password = md5($user_password1);

			// Insert into eonweb
			sqlrequest("$database_eonweb","UPDATE users set user_passwd='$user_password' WHERE user_id='$usrid';");

			// logging action
			logging("admin_user","UPDATE PASSWORD : $usrid $login");
		}
		message(8," : User updated",'ok');
		$user_password1= "abcdefghijklmnopqrstuvwxyz";
		$user_password2= "abcdefghijklmnopqrstuvwxyz";
	}
	else {
		message(8," : Passwords do not match or are empty",'warning');
	}
}	
?>

<form action='./index.php' method='POST' name='form_user'>
  <center>
  <table class="table">
    <tr>
      <td><h2>User Password</h2></td>
     <td>
       <input type='password' name='user_password1' value='<?php echo $user_password1?>' style="width:300px;">
     </td>
    </tr>
    <tr>
      <td><h2>User Password Confirmation</h2></td>
      <td>
        <input type='password' name='user_password2' value='<?php echo $user_password2?>' style="width:300px;">
      </td>
    </tr>
    <tr>
      <td class="blanc" align="center" colspan="2">
        <input class='button' type='submit' name='update' value='update'>
        &nbsp;<input class='button' type='button' name='back' value='back' onclick='location.href="javascript:history.go(-1)"'>
      </td>
    </tr>
  </table>
  </center>
</form>

</body>
</html>

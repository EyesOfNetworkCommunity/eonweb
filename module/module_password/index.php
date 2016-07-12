<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.0
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

include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.monitoring_passwd.title"); ?></h1>
		</div>
	</div>

	<?php
		$login=$_COOKIE['user_name'];
		$usrid=$_COOKIE['user_id'];
		$user_password1= "abcdefghijklmnopqrstuvwxyz";
		$user_password2= "abcdefghijklmnopqrstuvwxyz";

		if(isset($_POST["update"])) {
			$user_password1 = retrieve_form_data("user_password1","");
			$user_password2 = retrieve_form_data("user_password2","");
			if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {
				if($user_password1!="abcdefghijklmnopqrstuvwxyz") {
					$user_password = md5($user_password1);

					// Insert into eonweb
					sqlrequest("$database_eonweb","UPDATE users set user_passwd='$user_password' WHERE user_id='$usrid';");

					// update password into nagvis if user is in
					$bdd = new PDO('sqlite:/srv/eyesofnetwork/nagvis/etc/auth.db');
					$req = $bdd->query("SELECT userId, name FROM users WHERE name='".$login."'");
                    $nagvis_user_exist = $req->fetch();

                    // this is nagvis default salt for password encryption security
					$nagvis_salt = '29d58ead6a65f5c00342ae03cdc6d26565e20954';

					if($nagvis_user_exist["userId"] > 0){
						$nagvis_id = $nagvis_user_exist["userId"];
						$hashed_password = sha1($nagvis_salt.$user_password1);
						$bdd->exec("UPDATE users SET password = '$hashed_password' WHERE userId = $nagvis_id");
					}

					// logging action
					logging("admin_user","UPDATE PASSWORD : $usrid $login");
				}
				message(8," : ".getLabel("message.monitoring_passwd.ok"),'ok');
				$user_password1= "abcdefghijklmnopqrstuvwxyz";
				$user_password2= "abcdefghijklmnopqrstuvwxyz";
			}
			else {
				message(8," : ".getLabel("message.monitoring_passwd.error"),'warning');
			}
		}	
	?>
	
	<form method='POST' name='form_user'>
		<div class="form-group">
			<div class="row">
				<label class="col-md-3"><?php echo getLabel("label.monitoring_passwd.pwd"); ?></label>
				<div class="col-md-9">
					<input class="form-control" type='password' name='user_password1' value='<?php echo $user_password1?>'>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<label class="col-md-3"><?php echo getLabel("label.monitoring_passwd.pwd2"); ?></label>
				<div class="col-md-9">
					<input class="form-control" type='password' name='user_password2' value='<?php echo $user_password2?>'>
				</div>
			</div>
		</div>
		<button class='btn btn-primary' type='submit' name='update' value='update'><?php echo getLabel("action.update"); ?></button>
		<button id="back_btn" class='btn btn-default' type='button' onclick='history.go(-1);'><?php echo getLabel("action.cancel"); ?></button>
	</form>

</div> <!-- !#page-wrapper -->

<?php include("../../footer.php"); ?>

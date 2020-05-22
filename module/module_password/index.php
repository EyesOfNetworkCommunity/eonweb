<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.2
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
			<h1 class="page-header"><?php echo getLabel("label.monitoring_profil.title"); ?></h1>
		</div>
	</div>

	<?php
		$login=$_COOKIE['user_name'];
		$usrid=$_COOKIE['user_id'];
		$user_password1= "abcdefghijklmnopqrstuvwxyz";
		$user_password2= "abcdefghijklmnopqrstuvwxyz";
		$theme_session= $_SESSION["theme"];

		if(isset($_POST["update"])) {
			$user_password1 = retrieve_form_data("user_password1","");
			$user_password2 = retrieve_form_data("user_password2","");
			$theme = retrieve_form_data("theme","");

			if (($user_password1 != "") && ($user_password1 != null) && ($user_password1 == $user_password2)) {
				if($user_password1!="abcdefghijklmnopqrstuvwxyz") {
					$user_password = md5($user_password1);

					// Insert into eonweb
					sqlrequest("$database_eonweb","UPDATE users set user_passwd='$user_password', theme='$theme' WHERE user_id='$usrid';");

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
				} else if(($theme != "") && ($theme != null)){
					$conn = connexionDB($database_eonweb);
					$sql = $conn->prepare("UPDATE users set theme = :setTheme WHERE user_id = :userId");
					$sql->bindParam(":setTheme", $theme);
					$sql->bindParam(":userId",$usrid);
					$sql->execute();
				}
				message(8," : ".getLabel("message.monitoring_passwd.ok"),'ok');
				$user_password1= "abcdefghijklmnopqrstuvwxyz";
				$user_password2= "abcdefghijklmnopqrstuvwxyz";
			}
			else {
				message(8," : ".getLabel("message.monitoring_passwd.error"),'warning');
			}
		}	

		function GetThemeList() {

			global $database_eonweb;

			// creation of a select and catch values
			$conn = connexionDB($database_eonweb);
			$sql = $conn->prepare("SELECT `theme` FROM users WHERE user_id = :userId");
			$sql->bindParam("userId", $_COOKIE["user_id"]);
			$sql->execute();
			$result = $sql->fetch();
			$conn = null;
			$sql = null;

			$dir = "/srv/eyesofnetwork/eonweb/themes/";
			$listTheme = scandir($dir);
			$res = '<select class="form-control" name="theme">';
			foreach($listTheme as $value) {
				if(is_dir($dir . $value)) { 
					if($value != "." && $value != "..") {
						if($value == $result["theme"]){
							$res.="<option value='".$value."' selected=selected>".$value."</option>";
						}
						else if($value == "Default" && $result["theme"] == NULL){
							$res.="<option value='".$value."' selected=selected>".$value."</option>";
						}
						else{
							$res.="<option value='".$value."'>".$value."</option>";
						}
					}
				}
			}
			$res .= '</select>';

			return $res;
		}

		// No password modification link if ldap user
		$ldapsql=sqlrequest($database_eonweb,"SELECT user_type FROM users WHERE user_name='".$_COOKIE["user_name"]."';");
		$user_type=mysqli_result($ldapsql,0,"user_type");
		if($user_type != 1) { 
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

		<?php
		} 
		?>
		<div class="form-group">
			<div class="row">
				<label class="col-md-3"><?php echo getLabel("label.admin_user.user_theme"); ?></label>
				<div class="col-md-9">
					<?php echo GetThemeList(); ?>
				</div>
			</div>
		</div>
		<button class='btn btn-primary' type='submit' name='update' value='update'><?php echo getLabel("action.update"); ?></button>
		<button id="back_btn" class='btn btn-default' type='button' onclick='history.go(-1);'><?php echo getLabel("action.cancel"); ?></button>
	</form>

</div> <!-- !#page-wrapper -->

<?php include("../../footer.php"); ?>

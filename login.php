<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include("header.php");

// Display login Form
function display_login(){
	
	global $path_logo;
	
	echo '
	<div class="container" id="login">
		<div class="row">
			<div class="img col-md-4 col-md-offset-4">
				<div class="login-panel panel panel-default">
					<div class="panel-heading">
						<img class="img-responsive center-block login-logo" src="'.$path_logo.'" alt="logo eyesofnetwork">
					</div>
					<div class="panel-body">
						<form action="login.php" method="POST">
							<fieldset>
								<div class="form-group input-group">
									<span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-user"></i></span>
									<input class="form-control" type="text" autofocus="" name="login" placeholder="'. getLabel("label.placeholder.username") .'" aria-describedby="basic-addon1">
								</div>
								<div class="form-group input-group">
									<span class="input-group-addon" id="basic-addon2"><i class="glyphicon glyphicon-lock"></i></span>
									<input class="form-control" type="password" value="" name="mdp" placeholder="'. getLabel("label.placeholder.password") .'" aria-describedby="basic-addon2">
								</div>
								<button type="submit" class="btn btn-lg btn-primary btn-block">'. getLabel("action.connect") .'</button>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>';
		
	include("footer.php");
	
}

if(!empty($_COOKIE['user_name'])){
	
	if(username_available()){
	?>
	<script src="/bower_components/jquery/dist/jquery.min.js"></script>
	<script>
		document.write("<body id='main'>");
		if (window!=top){
			top.location="/module/index.php";
		}
		else{
			$("body").append('<div class="container"><div class="row">'
							+	'<div class="img col-md-4 col-md-offset-4">'
							+		'<div class="login-panel panel panel-default">'
							+			'<div class="panel-heading">'
							+				'<img class="img-responsive center-block imb-logo login-logo" src="<?php echo $path_logo; ?>" alt="logo eyesofnetwork">'
							+			'</div>'
							+			'<div class="panel-body">'
							+				'<div class="alert alert-info">Vous etes deja connecté en tant que : <?php echo $_COOKIE["user_name"]; ?></div>'
							+				'<div class="btn-group btn-group-justified">'
							+				'<a class="btn btn-primary" href="<?php echo getDefaultPage($_COOKIE["user_limitation"]); ?>">Accueil</a>'
							+				'<a class="btn btn-default" href="logout.php">Se déconnecter</a>'
							+				'</div>'
							+			'</div>'
							+		'</div>'
							+	'</div>'
							+'</div></div>');
		}
	</script>
	<?php include("footer.php");
	}else{
		?>
	<script src="/bower_components/jquery/dist/jquery.min.js"></script>
	<script>
		document.write("<body id='main'>");
		if (window!=top){
			top.location="/module/index.php";
		}
		else{
			$("body").append('<div class="container"><div class="row">'
							+	'<div class="img col-md-4 col-md-offset-4">'
							+		'<div class="login-panel panel panel-default">'
							+			'<div class="panel-heading">'
							+				'<img class="img-responsive center-block imb-logo login-logo" src="<?php echo $path_logo; ?>" alt="logo eyesofnetwork">'
							+			'</div>'
							+			'<div class="panel-body">'
							+				'<div class="alert alert-info">Sorry, It seems that the cookie have been altered</div>'
							+				'<div class="btn-group btn-group-justified">'
							+				'<a class="btn btn-default" href="logout.php">Se déconnecter</a>'
							+				'</div>'
							+			'</div>'
							+		'</div>'
							+	'</div>'
							+'</div></div>');
		}
	</script>
	<?php include("footer.php");
	}
}
else {
	if( isset($_POST['login']) && isset($_POST['mdp']) ){
		// Get login information
		$login=strtolower($_POST['login']);
		$mdp=$_POST['mdp'];
		$_POST[]=array();
		
		// set the login to false at the beginning
		$LOGIN=false;

		if(strstr($login,"'")){
			display_login();
			exit;
		}
		
		if($mdp == ""){
			display_login();
			exit;
		}

		$usersql=sql($database_eonweb,"select U.user_id as user_id, U.group_id as group_id ,U.user_name as user_name, U.user_passwd as user_passwd, U.user_descr as user_descr, U.user_type as user_type, L.dn as user_location, U.user_limitation as user_limitation  from users as U left join ldap_users_extended as L on U.user_name = L.login  where U.user_name = ?", array($login));
		$username = $usersql[0]["user_name"];
		
		// if not in eonweb DB
		if ($login != strtolower($username)) {
			// check if there is a LDAP conf
			$ldapsql=sql($database_eonweb,"SELECT * FROM auth_settings WHERE auth_type=1");
			
			//if there is a ldap conf in database
			if($ldapsql != null){
				//get ldap conf informations
				$ldap_ip= $ldapsql[0]["ldap_ip"];
				$ldap_port= $ldapsql[0]["ldap_port"];
				$ldap_rdn= $ldapsql[0]["ldap_rdn"];
				$ldap_user = $ldapsql[0]["ldap_user"];
				$ldap_password = base64_decode($ldapsql[0]["ldap_password"]);
				$ldap_search= $ldapsql[0]["ldap_search"];
				//$ldap_filter = mysqli_result($ldapsql,0,"ldap_filter");
				$user_location=str_replace("\\\\","\\",$usersql[0]["user_location"]);

				// connection to ldap
				$ldapconn=ldap_connect($ldap_ip,$ldap_port);
				ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

				$ldapbind = ldap_bind($ldapconn, $ldap_user, $ldap_password);

				// if connection successful
				if($ldapbind){
					// search the user in LDAP
					$search = ldap_search($ldapconn, $ldap_search, "(&(objectClass=person)(".$ldap_rdn."=".$login."))");
					$results = ldap_get_entries($ldapconn, $search);

					$user_finded = false;
					$user_dn = "";
					
					// if the user exists in LDAP
					if($results["count"] > 0){
						$in_clause = "";
						
						foreach($results as $result){
							// skip the first entry (this is the number of result... we don't need it !)
							if(is_int($result)){ continue; }
							
							$user_dn = $result["distinguishedname"][0];
							$user_dn = str_replace("\\","",$user_dn);
							
							$in_clause = "(";
							foreach($result["memberof"] as $group_dn){
								$group_dn=str_replace("\\","",$group_dn);
								// idem, skip the first entry is it's an int
								if(is_int($group_dn)){ continue; }
								
								$in_clause .= "'".ldap_escape($group_dn)."',";
							}
							$in_clause = rtrim($in_clause, ",");
							$in_clause .= ")";
						}
						
						$sql = "SELECT * FROM groups WHERE group_type=1 AND group_dn IN ".$in_clause;
						$sql_results = sql($database_eonweb, $sql);
						
						// we've found the user's group in eonweb DB!
						if( $sql_results != null){
							$group_id = $sql_results[0]["group_id"];
							
							// check user's connection to ldap
							$ldapbind = ldap_bind($ldapconn, ldap_escape($user_dn,true), $mdp);
							
							if($ldapbind){
								// insert the user in DB.
								insert_user($login, $user_descr, $group_id, $mdp, $mdp, 1, ldap_escape($user_dn), "", 0, false);
								
								// we can login now. And don't forget to take the new user's id (for session)
								$usersql=sql($database_eonweb,"select * from users where user_name = ?", array($login));
								$LOGIN = true;
							}
						}
					}
				}
			}
		}
		else {
			// IF LDAP USER
			if($usersql[0]["user_type"]=="1"){
				$ldapsql= sql($database_eonweb,"select * from auth_settings");
				$ldap_ip= $ldapsql[0]["ldap_ip"];
				$ldap_port= $ldapsql[0]["ldap_port"];
				$ldap_rdn= $ldapsql[0]["ldap_rdn"];
				$ldap_search= $ldapsql[0]["ldap_search"];
				$user_location=ldap_escape($usersql[0]["user_location"],true);

				$ldapconn=ldap_connect($ldap_ip,$ldap_port);
				ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

				$ldapbind=ldap_bind($ldapconn, $user_location, $mdp);

				if($ldapbind && !empty($mdp) && !empty($user_location))		
					$LOGIN=true;
			}
			// IF NOT A LDAP USER
			else{
				$userpasswd = $usersql[0]["user_passwd"];
				$mdp=md5($mdp);

				if($userpasswd == $mdp)
					$LOGIN=true;
			}
		}
		
		// trying to log
		if($LOGIN){
			// Get user & group ids + filter
			$grpid = $usersql[0]["group_id"];
			$usrid = $usersql[0]["user_id"];
			$usrlimit = $usersql[0]["user_limitation"];

			// Create session ID
			$sessid = session_id();
			sql($database_eonweb,"INSERT INTO sessions (session_id,user_id) VALUES (?,?)", array($sessid, $usrid));
			
			// Send cookie
			$cookie_time = ($cookie_time=="0") ? 0 : time() + $cookie_time;
			setcookie("session_id",$sessid,$cookie_time);
			setrawcookie("user_name",rawurlencode($login),$cookie_time,"/",$cookie_domain);
			setcookie("user_id",$usrid,$cookie_time);
			setcookie("user_limitation",$usrlimit,$cookie_time);
			setcookie("group_id",$grpid,$cookie_time);
		
			// Rename filters
			$filter="$path_eonweb/$dir_imgcache/".strtolower($_POST['login'])."-ged.xml";
			$filter_old="$path_eonweb/$dir_imgcache/".$_POST['login']."-ged.xml";
			if(file_exists($filter_old) and !file_exists($filter)) {
				rename($filter_old,$filter);
			}

			// Go to the main page
			logging("login","User logged in",$login);
			echo "<meta http-equiv='Refresh' content='0;URL=".getDefaultPage($usrlimit)."' />";
		}
		else { display_login(); 
			logging("login","User failed to login",$login);
		}
	}
	else { display_login(); }
}
?>

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
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<?php include("../../include/include_module.php"); ?>

</head>

<body id='main'>
        <h1><?php echo $xmlmodules->getElementsByTagName("admin_user")->item(0)->getAttribute("title")?></h1>

	<?php
	global $database_eonweb;
	global $database_lilac;
	$action=retrieve_form_data("action",null);
	$user_mgt_list=retrieve_form_data("user_mgt_list",null);
	$user_selected=retrieve_form_data("user_selected",null); 
	
	if($action == 'submit') 
	{
		switch($user_mgt_list)
		{
			case "add_user":
				echo "<META HTTP-EQUIV=refresh CONTENT='0;URL=add_modify_user.php'>";
				break;
			case "delete_user":
				if (isset($user_selected[0]))
				{
					for ($i = 0; $i < count($user_selected); $i++)
 					{
		                                // Get user name
                                                $user_res=sqlrequest("$database_eonweb","select user_name from users where user_id='$user_selected[$i]'");
                                                $user_name=mysqli_result($user_res,0,"user_name");

						// Delete user in eonweb
						sqlrequest("$database_eonweb","delete from users where user_id='$user_selected[$i]'");

						// Delete user in lilac
                                                $lilac_contactid=mysqli_result(sqlrequest("$database_lilac","select id from nagios_contact where name='$user_name'"),0,"id");
 						sqlrequest("$database_lilac","delete from nagios_contact where name='$user_name'");
                                                sqlrequest("$database_lilac","delete from nagios_contact_address where contact='$lilac_contactid'");
                                                sqlrequest("$database_lilac","delete from nagios_contact_group_member where contact='$lilac_contactid'");
                                		sqlrequest("$database_lilac","delete from nagios_contact_notification_command where contact_id='$lilac_contactid'");
                                                sqlrequest("$database_lilac","delete from nagios_escalation_contact where contact='$lilac_contactid'");
                                                sqlrequest("$database_lilac","delete from nagios_host_contact_member where contact='$lilac_contactid'");
                                                sqlrequest("$database_lilac","delete from nagios_service_contact_member where contact='$lilac_contactid'");

						// Delete user files
                                                $user_files_path="$path_eonweb/$dir_imgcache/$user_name";
                                                @unlink("$user_files_path-ged.xml");
                                                @unlink("$user_files_path-report.doc");
                                                @unlink("$user_files_path-report.xml");
                                                @unlink("$path_eonweb/$dir_imgcache/$user_name-report_xml.xml");

                                                foreach (glob("$user_files_path-*.png") as $filename){
                                                        @unlink($filename);
                                                }
					
						// Logging action
						logging("admin_user","DELETE : $user_selected[$i]");

						message(8," : User $user_name removed",'ok');
					}
				}
				break;
		}
	}
        
	// Get the name user and description group
        $user_name_descr=sqlrequest("$database_eonweb"," SELECT user_name,user_descr,user_id,group_name,user_type,user_limitation FROM users LEFT OUTER JOIN groups ON groups.group_id = users.group_id ORDER BY user_name");
	?>

	<form action='./index.php' method='GET'>
	<center>
		<table class="table">
			<tr>
				<th> User Name </th>
				<th> User Limited </th>
				<th> User Type </th>
				<th> User Mail </th>
				<th> User Description </th>
				<th> Group </th>
				<th> Select  </th>
			</tr>
			<?php
			while ($line = mysqli_fetch_array($user_name_descr))
			{
			$user_mail=mysqli_result(sqlrequest("$database_lilac","SELECT email FROM nagios_contact WHERE name='$line[0]'"),0,"email");

			?>
			<tr>
				<td>
					<?php echo"<a href='./add_modify_user.php?user_id=$line[2]'> $line[0] </a>";?>
				</td>
				<td>
                                        <?php
                                                if($line[5]=="0")
                                                        $type="NO";
                                                else
                                                        $type="<a href='filters.php?user_id=$line[2]&user_name=$line[0]'>YES</a>";
                                                echo "$type";
                                        ?>
                                </td>
				<td>
                                        <?php
						if($line[4]=="0")
							$type="MYSQL";
						else
							$type="LDAP";
						echo "$type";
					?>
                                </td>
				<td>
                                        <?php echo "$user_mail";?>
                                </td>
				<td>
					<?php echo "$line[1]";?>
				</td>
				<td>
					<?php echo "$line[3]";?>
				</td>
				<td>
					<center>
						<?php
						if($line[2]=="1")
							echo "<input type='checkbox' class='checkbox' name='user_selected[]' value='$line[2]' disabled>";
						else
							echo "<input type='checkbox' class='checkbox' name='user_selected[]' value='$line[2]'>";
						?>
					</center>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td class="blanc" colspan="7" align="center">
					<?php	
				        // Get the global table
				        global $array_user_mgt;

				        // Get the first array key
				        reset($array_user_mgt);

				        // Display the list of management choices
				        echo "<select name='user_mgt_list' size=1>";
				        while (list($mgt_name, $mgt_url) = each($array_user_mgt)) {

				                echo "<option value='$mgt_url'>$mgt_name</option>";
				        }
				        echo "</select>";
					?>
				<input class='button' type='submit' name='action' value='submit'>
				</td>
			</tr>
		</table>
	</center>
	</form>
</body>
</html>

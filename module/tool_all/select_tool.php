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
r
# GNU General Public License for more details.
#
#########################################
*/


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<?php include("../../include/include_module.php"); ?>


<?php
	
	//--- Redirection page ---//.
	

	//url where the tool is searched.
	if(isset($_POST['tool_list']))
	{
		$url_tool=$_POST['tool_list'];

                //Community name.
                $snmp_com=retrieve_form_data("snmp_com","");
                //snmp version selected.
                $snmp_version=retrieve_form_data("snmp_version","");
                $username = retrieve_form_data("username","");
                $password = retrieve_form_data("password","");
                $snmp_auth_protocol = retrieve_form_data("snmp_auth_protocol","");
                $snmp_priv_passphrase = retrieve_form_data("snmp_priv_passphrase","");
                $snmp_priv_protocol = retrieve_form_data("snmp_priv_protocol","");
                $snmp_context = retrieve_form_data("context","");
                $min_port=retrieve_form_data("min_port","");
                $max_port=retrieve_form_data("max_port","");
                if($snmp_version=="3")
                	$snmp_com="nothing";
                if($snmp_com=="" AND $url_tool=="../tool_interface/index.php"){
	                message(4,"<body id='main'>Could not determine snmp community</body>","critical");
                }else if($snmp_version=="3" AND $url_tool=="../tool_interface/index.php" AND ($username=="" OR $password=="")){
	                message(4,"<body id='main'>Could not determine username and password</body>","critical");
                }else if($snmp_version==""){
	                message(4,"<body id='main'>Could not determine snmp version</body>","critical");
                }

		if(isset($_GET['page']))
		{	
			if(!isset($_POST['host_list'])){
				 message(4,"<body id='main'>Please select a host</body>","critical");
			}
			else{
				//hostname selected.
				$tab_host=split(",",$_POST['host_list']);
				$host=$tab_host[1];
				if($host=="") message(4,"Please select a host","critical");
				
				$min_port=$_POST['min_port'];
				$max_port=$_POST['max_port'];

				//Redirection.
				echo "<meta http-equiv='Refresh' content='0;URL=$url_tool?host=$tab_host[1]&host_name=$tab_host[0]&max_port=$max_port&min_port=$min_port&snmp_version=$snmp_version&snmp_com=$snmp_com&username=".$username."&password=".$password."&snmp_auth_protocol=".$snmp_auth_protocol."&snmp_priv_passphrase=".$snmp_priv_passphrase."&snmp_priv_protocol=".$snmp_priv_protocol."&snmp_context=".$snmp_context."'>";				
				echo "</head>";
				echo "<body id='main'>";
				echo "<h2>Loading, please wait ...</h2><br>";
				echo "<img src='/images/actions/ajax-loader.gif' alt='ajax-loader'>";				
			}
		}
		else
		{	
			//hostname.
			$host=retrieve_form_data("hostname","");
	
                	if($host==""){
                        	message(4,"<body id='main'>Could not determine host</body>","critical");
                	}else {	
				//Redirection.
				echo "<meta http-equiv='Refresh' content='0;URL=$url_tool?host=$host&host_name=$host&snmp_version=$snmp_version&snmp_com=$snmp_com& max_port=$max_port&min_port=$min_port&username=".$username."&password=".$password."&snmp_auth_protocol=".$snmp_auth_protocol."&snmp_priv_passphrase=".$snmp_priv_passphrase."&snmp_priv_protocol=".$snmp_priv_protocol."&snmp_context=".$snmp_context."'>";
				echo "</head>";
				echo "<body id='main'>";				 
	                        echo "<h2>Loading, please wait ...</h2><br>";
	                        echo "<img src='/images/actions/ajax-loader.gif' alt='ajax-loader'>";  
			}
		}	
	}
	else
        {
                message(4,"<body id='main'>Please select a tool</body>","critical");
        }

?>

</body>
</html>

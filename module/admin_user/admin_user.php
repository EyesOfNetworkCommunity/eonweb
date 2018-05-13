<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

?>

<!-- DataTables JavaScript -->
<script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
<script src="/bower_components/datatables-responsive/js/dataTables.responsive.js"></script>
<script src="/js/datatable.js"></script>

<?php

if( isset($user_limitation) || isset($user_id) || isset($user_type) ){
	if($user_limitation=="1" && $user_id!="1"){
		echo "<script>disable_group();</script>";
	}
	elseif($user_id!="1"){
		echo "<script>disable_group();</script>";
		echo "<script>disable_group();</script>";
	}
	if($user_type=="1" && $user_id!="1"){
		echo "<script>disable();</script>";
	}
	elseif($user_id!="1"){
		echo "<script>disable();</script>";
		echo "<script>disable();</script>";
	}

}

?>

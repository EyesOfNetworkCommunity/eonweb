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
?>

</div> <!-- !#wrapper -->

<footer class="footer navbar navbar-default navbar-fixed-bottom">
	<div class="container">
		<div class="text-center">
			<a href="http://www.eyesofnetwork.com" target="_blank">EyesOfNetwork</a>
			<?php echo getLabel("label.footer.message"); ?>
		</div>
	</div>
</footer> <!-- !#footer -->
		
<?php if(preg_match("/\/module\//",$_SERVER["PHP_SELF"]) > 0 ) { ?>

<!-- jQuery -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- jQuery-ui -->
<script src="/bower_components/jquery-ui/jquery-ui.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="/bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="/bower_components/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js"></script>

<!-- EONWEB variables -->
<script src="/js/eonweb.js"></script>

<!-- EONWEB menu -->
<script src="/js/side.js"></script>

<!-- EONWEB traduction -->
<?php 
	# Include javascript dictionnary
	$t->createJSDictionnary();
	
	# Include module js and php
	$module_path=basename(dirname($_SERVER["PHP_SELF"]));
	$module_js=$module_path.".js";
	if(file_exists($module_js)) { 
?>

<!-- EONWEB module javascripts -->
<script src="<?php echo $module_js; ?>"></script>

<?php
	}
	
	$module_php=$module_path.".php";
	if(file_exists($module_php)) { 
		include($module_php);
	} 
}
?>

</body>

</html>

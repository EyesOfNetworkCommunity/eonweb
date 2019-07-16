<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

<script src="/bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="./js/jquery.autocomplete.min.js"></script>
<script src="./js/jquery.jmHighlight.min.js"></script>

<?php
$filejs="./js/".basename($_SERVER['PHP_SELF'],".php").".js";
if(file_exists($filejs)) {?>
<script src="<?php echo $filejs;?>"></script>
<?php } ?>

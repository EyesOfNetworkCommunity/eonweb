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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>menus</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php include("./include/include.php"); ?> 

<script src="./js/jquery.min.js"></script>
<script src="./js/jquery.ui.min.js"></script>
<script src="./js/jquery.cookie.js"></script>

<link rel="stylesheet" href="./css/jquery/jquery.ui.css">

<style>
	.ui-autocomplete {
		max-height: 200px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
	}
	/* IE 6 doesn't support max-height
	* we use height instead, but this forces the menu to always be this tall
	*/
	* html .ui-autocomplete {
		height: 100px;
	}
</style>

</head>

<body>

<script type="text/javascript">

$(document).ready(function(){
        $("#leftmenu").sortable({
		axis: 'y',
		handle: 'dt.handleitem',
		items: 'dl.sortableitem',
		opacity: '0.5'
	});
	return false;
});

function setLink(headernav,side_url){
	$('#zone_header',top.document).find('#headernav').html(headernav);
	$.cookie('active_page',side_url);
	return false;
}

function animateMenu(image,menu){
        var men = document.getElementById(menu);
        $(men).slideToggle();
        var img = document.getElementById(image);
        var url = location.protocol+"//"+location.host+"/images/actions/minus.gif";

        if(img.src == url)
                img.src = "./images/actions/plus.gif";
        else 
                img.src = "./images/actions/minus.gif";     

	return false;
}

</script>

<script>
	$.widget( "custom.catcomplete", $.ui.autocomplete, {
		_renderMenu: function( ul, items ) {
			var that = this,
			currentCategory = "";
			$.each( items, function( index, item ) {
				if ( item.category != currentCategory ) {
					ul.append( "<li class='ui-autocomplete-category' style='font-weight: bold'>" + item.category + "</li>" );
					currentCategory = item.category;
				}
				that._renderItemData( ul, item );
			});
		}
	});
</script>

<script>
	function my_ajax_search()
	{
		$.ajax({
			url : "thruk/cgi-bin/status.cgi",
			cache: false,
			data : {
				format : "search",
			},
			success : function(response){
				var str = '$(this).catcomplete({delay: 0, source: [';
				$.each(response, function(i, item){
					if(i < response.length - 1){
						$.each(response[i].data, function(j, item){
							str += '{label : "'+response[i].data[j]+'", category : "'+response[i].name+'"},';
						});
					}
				});
				str = str.substring(0, str.length-1);
				str += '], select: function(event, ui) { $("form").submit();} })';
					
				$("#s0_value").attr('onFocus', str);
			}
		});
	}
	
	my_ajax_search();
</script>

<?php

// Check POST : active menutab 
if(!isset($_GET['tabid'])) $tmpid=$defaulttab;
else $tmpid=$_GET['tabid'];

$cookie_time = ($cookie_time=="0") ? 0 : time() + $cookie_time;
setcookie("active_tab",$tmpid,$cookie_time);

// Get information for menus in xml file
$xpath = new DOMXPath($xmlmenus);
$menutabs = $xpath->query("//menutab[@id='$tmpid']");
$tab_id = $menutabs->item(0)->getAttribute("id");
$tab_name = $menutabs->item(0)->getAttribute("name");

// Create the Menu
echo "<div id='leftmenu'>";
	
?>

<div id="leftmenutitle">
	<?php echo $tab_name?>
</div>

<form method="get" action="<?php echo $path_nagios_cgi?>/status.cgi" target="main">
  <center>
  <input name="s0_op" value="~" id="s0_to" type="hidden">
  <input name="s0_type" value="search" type="hidden">
  <input name="s0_value" id="s0_value" type="text" placeholder="<?php echo $xmlmenus->getElementsByTagName("search")->item(0)->nodeValue;?>" autocomplete="off" onFocus="my_ajax_search();" />
  </center>
</form>

<?php	
// Display Left Menu
$menusubtabs = $menutabs->item(0)->getElementsByTagName("menusubtab");
foreach($menusubtabs as $menusubtab){
	(!isset($i)) ? $i=0 : $i++;
	$subtab_name = $menusubtab->getAttribute("name");
?>

<dl id="item_<?php echo $i?>" class="sortableitem">
  <dt class="handleitem">
    <img src="/images/actions/minus.gif" id="image_<?php echo $i?>" alt="" onclick="animateMenu('image_<?php echo $i?>','handle_<?php echo $i?>')" /><?php echo $subtab_name?>
  </dt>
  <dd>
    <ul id="handle_<?php echo $i?>" class="ul">
<?php 
// Get information for side menu in database
foreach($menusubtab->getElementsByTagName("link") as $link){
        $side_name = $link->getAttribute("name");
        $side_url = $link->getAttribute("url");
        $side_target = $link->getAttribute("target");
	$headernav="&nbsp;<b>".ucfirst($tab_name)." -> <i>".ucfirst($subtab_name)."</b> --> ".str_replace("'","\'",$side_name)."</i>&nbsp;";
	?>
	<li>
	  <a href="<?php echo $side_url?>" target="<?php echo $side_target?>" <?php if($side_target!="_blank") { ?>onclick="setLink('<?php echo $headernav?>','<?php echo $side_url?>');" <?php } ?>><?php echo $side_name?></a>
	</li>
	<?php
}
?>
    </ul>
  </dd>
</dl>

<?php } ?>

</div>

</body>

</html>

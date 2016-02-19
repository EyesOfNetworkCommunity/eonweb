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

<?php 
if(isset($_GET["q"]))
        $q=$_GET["q"];
else
        $q=$_POST["q"];

include("../../include/include_module.php"); 
include("EventBrowser.php"); 
?>

<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.ui.js"></script>
<script type="text/javascript" src="/js/ui/i18n/ui.datepicker-<?php echo $langformat?>.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="/js/jquery.date.js"></script>
<script type="text/javascript" src="/js/jquery.metadata.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="/js/jquery.contextmenu.js"></script>
<script type="text/javascript" src="ged.js"></script>

<?php if($q!="history") { ?>
<script type="text/javascript">
	this.timer = setInterval('submitFormAjax()','<?php echo $refresh_time?>000');
	function changeRefresh() {
		clearInterval(this.timer);
		this.timer = setInterval('submitFormAjax()',$("#refresh").val()+'000');
	}
</script>
<?php } ?>

</head>

<body id="main">

<h1><?php echo ${"events_".$q}->item(0)->getAttribute("title");?></h1>
<div id="ged_messages" align="right">
        <?php if($q!="history") { ?>
	<i>screen refresh every </i><input id="refresh" name="refresh" type="text" value="<?php echo $refresh_time?>" size="2" onblur="changeRefresh();" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" style="font-size: 10px;margin:0;"><i> seconds</i>
	<?php } else { ?>
	<i>no screen refresh</i>
	<?php } ?>
	<div id="ged_filter" <?php if($_COOKIE["user_limitation"]==1) echo "style=\"display:none;\""; ?>>
	</div>
</div>

<form method="post" onsubmit="return submitFormAjax();">

<input type="hidden" value="<?php echo $q?>" name="q" />

<!-- SEARCH MODULE -->

<div id="search" style="margin-bottom:10px;">

	<h2>search</h2>

	<select id="type" name="type" onchange="$('#value').focus();">
	<?php
	for($i=0;$i<count($array_ged_types);$i++)
        	echo "<option value='".$i."'>".$array_ged_types[$i]."</option>"
	?>
	</select>

	<select id="field" name="field" onchange="$('#value').focus();">
	<?php
	for($i=0;$i<count($array_ged_filters);$i++)
		echo "<option>$array_ged_filters[$i]</option>";
	?>
	</select>
	
	<input id="value" name="value" class="value" type="text" autocomplete="off" onFocus='$(this).autocomplete(<?php echo get_host_list_from_nagios();?>)' />
	|

	<?php 
        foreach($array_ged_states as $col => $val){
		echo $col.' <input type="checkbox" class="checkbox" id="'.$col.'" name="'.$col.'" onclick="$(\'#value\').focus();" checked /> | ';
	}
	?>

        owner :
        <select id="owner" name="owner">
                <option>All</option>
                <option>owned</option>
                <option>not owned</option>
        </select>

        |

	<input type="submit" class="button" value="search" />

        <h2 style="margin-bottom:5px;">parameters</h2>

        date range : <input id="datepicker" name="datepicker" class="datepicker" type="text" autocomplete="off" readonly="readonly" />
	<img src="/images/actions/delete.png" alt="delete" style="cursor:pointer;" onClick="$('#datepicker').attr('value','');" />
        |
        <?php if($q=="history") { ?>
        <select id="duration" name="duration" onchange="$('#value').focus();">
                <option value="" selected>Ack time</option>
                <option value="300">>=5min</option>
                <option value="600">>=10min</option>
                <option value="1200">>=20min</option>
                <option value="3600">>=1h</option>
        </select>
        |
        <?php } ?>
        firt event : <input type="text" id="nstart" name="nstart" class="offset" value="0" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');">
        |
        events to show : <input type="text" id="nend" name="nend" class="offset" value="<?php echo $maxlines?>" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');">

</div>

<!-- END SEARCH MODULE -->

<!-- EVENTBROWSER MODULE -->

<div id="loading">
        <h2>Loading, please wait ...</h2><br>	
	<img src="/images/actions/ajax-loader.gif" alt="ajax-loader">      
</div>

<?php
	$filter = false;
	
	if( isset($_GET["status"]) || isset($_GET["time"]) || isset($_GET["own"]) ){
		$filter = array(
			"field"         => "",
			"value"         => "",
			"datepicker"    => "",
			"duration"		=> false,
			"ok"            => false,
			"warning"       => false,
			"critical"      => false,
			"unknown"       => false,
			"time"			=> false,
			"own"			=> false,
		);
	}

	$list_status = array(0, 1, 2, 3, "incident", "2-3");
	if( isset($_GET["status"]) && in_array($_GET["status"], $list_status) ){
		switch($_GET["status"]){
			case 0:
				$filter["ok"] = "on";
				echo "<script>
						$('#warning').attr('checked', false);
						$('#critical').attr('checked', false);
						$('#unknown').attr('checked', false);
					  </script>";
				break;
			case 1:
				$filter["warning"] = "on";
				echo "<script>
						$('#ok').attr('checked', false);
						$('#critical').attr('checked', false);
						$('#unknown').attr('checked', false);
					  </script>";
				break;
			case 2:
				$filter["critical"] = "on";
				echo "<script>
						$('#ok').attr('checked', false);
						$('#warning').attr('checked', false);
						$('#unknown').attr('checked', false);
					  </script>";
				break;
			case 3:
				$filter["unknown"] = "on";
				echo "<script>
						$('#ok').attr('checked', false);
						$('#warning').attr('checked', false);
						$('#critical').attr('checked', false);
					  </script>";
				break;
		}
		if($_GET["status"] == "incident"){
			$filter["ok"] = false;
			$filter["warning"] = "on";
			$filter["critical"] = "on";
			$filter["unknown"] = "on";
			echo "<script>
					$('#ok').attr('checked', false);
					$('#warning').attr('checked', true);
					$('#critical').attr('checked', true);
					$('#unknown').attr('checked', true);
				  </script>";
		}
		if($_GET["status"] == "2-3"){
			$filter["critical"] = "on";
			$filter["unknown"] = "on";
			echo "<script>
					$('#ok').attr('checked', false);
					$('#warning').attr('checked', false);
					$('#critical').attr('checked', true);
					$('#unknown').attr('checked', true);
				  </script>";
		}
	}
	else{ $status = ""; }

	$list_time = array("0-5m", "5-15m", "15-30m", "30m-1h", "more");
	if( isset($_GET["time"]) && in_array($_GET["time"], $list_time) ){
		$filter["time"] = $_GET["time"];
	}
	else{ $time = ""; }

	$list_own = array("yes", "no");
	if( isset($_GET["own"]) && in_array($_GET["own"], $list_own) ){
		$filter["own"] = $_GET["own"];
		if($filter["own"] == "yes"){
			echo "<script>$('#owner').val('owned')</script>";
		}
		elseif($filter["own"] == "no"){
			echo "<script>$('#owner').val('not owned')</script>";
		}
	}
	else{ $own = ""; }
	
	
	
	$EventBrowser=new EventBrowser();
	$EventBrowser->showTable($q, "0", $filter);
	$EventBrowser->showTablePager();
?>

<!-- END EVENTBROWSER MODULE -->

<!-- ACTIONS MODULE -->

<div class="contextMenu" id="myMenu" style="display:none;">
    <ul>
        <?php
        if($q=="active")
                $actions=$array_action_option;
        else
                $actions=$array_resolve_action_option;

        foreach($actions as $col => $val)
                echo '<li id="'.$col.'"><img src="/images/actions/'.$val.'.png">'.$val.'</li>';
        ?>
    </ul>
</div>

<div id="details"></div>
<div id="comments"></div>

<!-- END ACTIONS MODULE -->

</form>

</body>

</html>

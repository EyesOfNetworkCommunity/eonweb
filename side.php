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

$m = new Translator();
$m::initFile($path_menus,$path_menus_custom);
$menus = $m::createPHPDictionnary();
?>

<!-- Nav menu -->
<nav class="navbar navbar-default navbar-static-top" style="margin-bottom: 0">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="/index.php">
			<img id="logo_eon" class="navbar-logo" src="/images/logo.png" alt="logo eyesofnetwork">
		</a>
	</div>
	<!-- /.navbar-header -->
	
	<ul class="nav navbar-top-links navbar-right">
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-user">
				<li><a href="/password.php"><i class="fa fa-user fa-fw"></i> <?php echo getLabel("menu.user.profile"); ?></a>
				</li>
				<li class="divider"></li>
				<li><a href="/logout.php"><i class="fa fa-sign-out fa-fw"></i> <?php echo getLabel("menu.user.disconnect"); ?></a>
				</li>
			</ul>
			<!-- /.dropdown-user -->
		</li>
		<!-- /.dropdown -->
	</ul>
	<!-- /.navbar-top-links -->
	
	<div class="navbar-default sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">
			<ul id="side-menu" class="nav in">
				<li class="sidebar-search">
					<form id="sideMenuSearch" method="get" action="<?php echo $path_frame; ?>" style="margin-bottom: 0;">
						<div class="input-group custom-search-form">
							<input name="s0_value" id="s0_value" class="form-control" type="text" placeholder="<?php echo getLabel("label.input.placeholder.search"); ?>" autocomplete="off" onFocus="my_ajax_search();">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">
									<i class="fa fa-search" style="padding: 3px 0;"></i>
								</button>
							</span>
						</div>
					</form>
				</li>
				<?php 
				foreach($menus["menutab"] as $menutab) { 	
					// Verify group rights
					$tab_request = "SELECT tab_".$menutab["id"]." FROM groupright WHERE group_id=".$_COOKIE['group_id'].";";
					$tab_right = mysqli_result(sqlrequest($database_eonweb, $tab_request),0);				
					if($tab_right == 0){ continue; }
				?>
				<li>
					<a href="#">
						<i class="<?php echo $menutab["icon"]; ?>"></i>
						<?php echo getLabel($menutab["name"]); ?>
						<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level collapse">
					<?php
					foreach($menutab as $key => $value){
						if($key == "link"){
							foreach($value as $index => $item){	
								$url=$item["url"];
								if($item["target"]=="_blank") { $url=$url.'" target="_blank'; }
								elseif($item["target"]=="frame") { $url=$path_frame.urlencode($item["url"]); }
					?>
					<li><a href="<?php echo $url; ?>"><?php echo getLabel($item["name"]); ?></a></li>
					<?php 
							}
						} elseif($key == "menusubtab") {
					?>
					
						<?php foreach($menutab["menusubtab"] as $menusubtab) { ?>
						<li>
							<a href="#"><?php echo getLabel($menusubtab["name"]); ?> <span class="fa arrow"></span> </a>
							<ul class="nav nav-third-level collapse">
								<?php foreach($menusubtab["link"] as $menulink) { ?>
								<li>
									<?php
										$url=$menulink["url"];
										if($menulink["target"]=="_blank") { $url=$url.'" target="_blank'; }
										elseif($menulink["target"]=="frame") { $url=$path_frame.urlencode($menulink["url"]); }
									?>
									<a href="<?php echo $url; ?>"><?php echo getLabel($menulink["name"]); ?></a>
								</li>
								<?php } ?>
							</ul>
						</li>
						<?php } ?>
					
					<?php 
						}
					}
					?>
					</ul>
				</li>
				<?php } ?>
			</ul>
		</div>
		<!-- /.sidebar-collapse -->
	</div>
	<!-- /.navbar-static-side -->
</nav>

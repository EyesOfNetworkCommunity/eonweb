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

$m = new Translator();

// load right menu file according to user limitation (LEFT menu)
if( $_COOKIE['user_limitation'] != 0 ){
	$m->initFile($path_menu_limited, $path_menu_limited_custom);
} else {
	$m->initFile($path_menus,$path_menus_custom);
}
$menus = $m->createPHPDictionnary();

// load right menu file according to user limitation (TOP menu)
$navbar_menus = false;
if( strpos($_SERVER["PHP_SELF"], "/module/module_frame") !== false ){
	if(isset($_GET["url"])){
		// define module name
		$_GET["url"] = htmlentities($_GET["url"]);
		$ref_url = urldecode($_GET["url"]);
		$ref_url = trim($ref_url, "/");
		$ref_url_parts = explode("/", $ref_url);
		$test_url = $ref_url_parts[0];
		
		// we test the module name in lower case (that is easier)
		if(file_exists($path_menus."-".$test_url.".json") or file_exists($path_menus_custom."-".$test_url.".json")){
			$prefix_url = "/module/module_frame/index.php?url=";
			if($m->initFile($path_menus."-".$test_url,$path_menus_custom."-".$test_url)){
				$navbar_menus = $m->createPHPDictionnary();
			}
		}
		
	}
} else {
	// custom navbar
	$module_path=basename(dirname($_SERVER["PHP_SELF"]));
	if(file_exists($path_menus."-".$module_path.".json") or file_exists($path_menus_custom."-".$module_path.".json")){
		$prefix_url = "";
		if($m->initFile($path_menus."-".$module_path,$path_menus_custom."-".$module_path)){
			$navbar_menus = $m->createPHPDictionnary();
		}
	}
}
		
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
			<img id="logo_eon" class="navbar-logo" src="<?php echo $path_logo_navbar; ?>" alt="logo eyesofnetwork">
		</a>
	</div>
	<!-- /.navbar-header -->
	
	<ul class="nav navbar-top-links navbar-right">
		<!--menu toggle button -->
		<li>
			<button id="menu-toggle" type="button" data-toggle="button" class="btn btn-default btn-xs">
				<i class="fa fa-exchange fa-fw"></i>
			</button>
		</li>
		<?php
		// create the top navbar menu
		if(isset($navbar_menus["navbarlink"])){
			foreach ($navbar_menus["navbarlink"] as $navbarlink) {
				if(!empty($prefix_url)) {
					$navbarlink["url"]=urlencode($navbarlink["url"]);
				}
		?>
				<li><a href="<?php echo $prefix_url.$navbarlink["url"]; ?>"><?php echo getLabel($navbarlink["name"]); ?></a></li>
		<?php
			}
		}
		if(isset($navbar_menus["navbarsubtab"])){
			foreach ($navbar_menus["navbarsubtab"] as $navbarsubtab) {
		?>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"> <?php echo getLabel($navbarsubtab["name"]); ?> <i class="fa fa-caret-down"></i></a>
					<ul class="dropdown-menu dropdown-user">
		<?php
					if(isset($navbarsubtab["link"])){
						foreach ($navbarsubtab["link"] as $link) {
							if(!empty($prefix_url)) {
								$link["url"]=urlencode($link["url"]);
							}
		?>
							<li>
								<a href="<?php echo $prefix_url.$link["url"]; ?>">
									<?php echo getLabel($link["name"]); ?>
								</a>
							</li>
		<?php
						}
					}
		?>
					</ul>
				</li>
		<?php
			}
		}
		?>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa fa-user fa-fw"></i> <?php echo $_COOKIE['user_name']; ?> <i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-user">
				 
				<li><a href="/module/module_password/index.php"><i class="fa fa-user fa-fw"></i> <?php echo getLabel("menu.user.profile"); ?></a>
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

	<div id="sidebar-wrapper">
		<div class="navbar-default sidebar masked" role="navigation">
			<div class="sidebar-nav navbar-collapse">
				<ul id="side-menu" class="nav in">
					<?php if($_COOKIE['user_limitation'] == 0) : ?>
					<li class="sidebar-search">
						<form id="sideMenuSearch" method="get" action="<?php echo $path_frame; ?>" style="margin-bottom: 0;">
							<div class="input-group custom-search-form">
								<input name="s0_value" id="s0_value" class="form-control" type="text" placeholder="<?php echo getLabel("label.input.placeholder.search"); ?>" autocomplete="off" onFocus="my_ajax_search();">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</span>
							</div>
						</form>
					</li>
					<?php endif; ?>
					<?php 
					// check if there's menutabs (user not limited)
					if(isset($menus['menutab'])){
						// loop on each menutab
						foreach($menus["menutab"] as $menutab) { 	
							// Verify group rights
							$tab_request = "SELECT tab_".$menutab["id"]." FROM groupright WHERE group_id=?";
							$tab_right = sql($database_eonweb, $tab_request, array($_COOKIE['group_id']));				
							$tab_right = $tab_right[0];				
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
							// loop on level 2 to identify links ans subtabs
							foreach($menutab as $key => $value){
								// we have links
								if($key == "link"){
									foreach($value as $index => $item){	
										$url=$item["url"];
										if($item["target"]=="_blank") { $url=$url.'" target="_blank'; }
										elseif($item["target"]=="frame") { $url=$path_frame.urlencode($item["url"]); }
							?>
							<li><a href="<?php echo $url; ?>"><?php echo getLabel($item["name"]); ?></a></li>
							<?php
									}
								// we have subtabas
								} elseif($key == "menusubtab") {
							?>
							
								<?php 
								// loop on level 3
								foreach($menutab["menusubtab"] as $menusubtab) {
								?>
								<li>
									<a href="#"><?php echo getLabel($menusubtab["name"]); ?> <span class="fa arrow"></span> </a>
									<ul class="nav nav-third-level collapse">
										<?php 
										// loop on level 4 (subtab's links)
										foreach($menusubtab["link"] as $menulink) { 
										?>
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
								<?php }
							
								}
							
						} ?>
							</ul>
						</li>
						<?php }
					} else {
						// no menutabs (user limited)
						foreach ($menus["link"] as $key => $value) {
							if($value["target"]=="_blank") { $value['url'].='" target="_blank'; }
							elseif($value["target"]=="frame") { $value['url']=$path_frame.urlencode($value['url']); }
						?>
							<li>
								<a href="<?php echo $value['url']; ?>"><?php echo getLabel($value["name"]); ?></a>
							</li>
						<?php
						}
					}
					?>
				</ul>
			</div>
			<!-- /.sidebar-collapse -->
		</div>
		<!-- /.navbar-static-side -->
	</div>
</nav>

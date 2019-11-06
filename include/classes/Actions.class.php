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

/**
 * Actions class for all eonweb's pages
 */
class Actions
{

	/**
	 * Ged Acknowledge
	 */
	public function ged_acknowledge($selected_events, $queue)
	{
		return true;
	}

	/**
	 * Ged Edit
	 */
	public function ged_edit($selected_events, $queue, $comments)
	{
		return true;
	}
	
	/**
	 * Ged Own
	 */
	public function ged_own($selected_events, $queue, $global_action)
	{
		return true;
	}
	
}

?>

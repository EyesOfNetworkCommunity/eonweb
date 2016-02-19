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
<link rel="stylesheet" type="text/css" href="../../css/jquery/ui.all.css" />
<?php
class LogBrowser {

        private $myresult;
        private $myfields;

        public function __construct() {

                $this->myresult=null;
                $this->myfields=null;

        }

	public function showSearch($result) {
                $this->myresult=$result;
		echo '<div id="search">';
		echo 'period : <input type="text" id="datepicker" class="datepicker" name="date">&nbsp;';
		echo '<img src="/images/actions/delete.png" alt="delete" style="cursor:pointer;" onClick="$(\'#datepicker\').attr(\'value\',\'\');" />&nbsp;|&nbsp;';
			
		$cpt=0;
		while ( $i = mysqli_fetch_field($result)) {
			$col= $i->name;
			if($col!="date" && $cpt!=0)
				echo $col.' : <input type="text" id="'.$col.'" name="'.$col.'" class="value">&nbsp;|&nbsp;';
			$myfields[$cpt]=$col;
			$cpt++;
		}
		echo '<input type="submit" class="button" value="search" />';
		echo '</div>';
		$this->myfields=$myfields;
	}

	public function showMsg() {

		echo '
		<div id="loading">
        		<h2>Loading, please wait ...</h2><br>
        		<img src="/images/actions/ajax-loader.gif" alt="ajax-loader">
		</div>';

	}
	
	private function showTableTH($type) {

		echo '
		<'.$type.'>
		<tr>';
		foreach($this->myfields as $field) {
			echo '<th>'.$field.'</th>';	
		}
		echo '
		</tr>
		</'.$type.'>
		';

	}

	public function showTablePager() {

		global $maxlines;

		echo '
		<div id="pager" class="pager">
        	<form>
                	<img src="/images/actions/first.png" class="first"/>
                	<img src="/images/actions/prev.png" class="prev"/>
                	<input type="text" class="pagedisplay" disabled/>
                	<img src="/images/actions/next.png" class="next"/>
                	<img src="/images/actions/last.png" class="last"/>
                	<select class="pagesize">
                        	<option selected="selected" value="15">15</option>
                        	<option value="50">50</option>
                        	<option value="100">100</option>
                        	<option value="'.$maxlines.'">'.$maxlines.'</option>
                	</select>
        	</form>
		</div>
		</div>
		</div>
		';

	}	

	public function showTable($result=false,$fileds=false) {

		global $maxlines;
		global $dateformat;

		if($result) {
			$cpt = 1;
			while ( $i = mysqli_fetch_field($result) ) {
				$myfields[$cpt]=$i->name;
				$cpt++;
			}
			$this->myfields=$myfields;
			$this->myresult=$result;
		}

		echo '
                <div id="gedtable">
                <div id="showtable" style="display:none;">
                <h2>result : '.mysqli_num_rows($this->myresult).' event(s) found.</h2>';
		if(mysqli_num_rows($this->myresult)>$maxlines-1)
				echo "<h2>You have more than $maxlines lines, adjust your search.</h2>";
		echo '<table class="tablesorter" cellspacing="1">
		';

		$this->showTableTH("thead");
		$this->showTableTH("tfoot");
	
		echo '<tbody>';
		for($i=0;$i< mysqli_num_rows($this->myresult);$i++){
			if($i>$maxlines-1){
					break;
			}

			echo '
			<tr class="blanc">';
			for($j=0;$j<mysqli_num_fields($this->myresult);$j++){
				echo '<td>';
				if(mysqli_fetch_field_direct($this->myresult,$j)->name != "date") 
					echo mysqli_result($this->myresult,$i,$j);
				else
					echo date($dateformat,mysqli_result($this->myresult,$i,$j));
				echo '</td>';
			}
			echo '
			</tr>';
		}
	        echo '
		</tbody>
		</table>';

	}

}

?>
<?php
/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
# DEV NAME : Jeremy HOARAU
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
//ini_set('display_errors','on');
//error_reporting(E_ALL);

include("../../header.php");
include("../../side.php");
include("function_itsm.php");
include("classes/Itsm.php");
include("classes/ItsmPeer.php");

    $state          = get_itsm_state();
    $itsm_acquit    = (get_config_var("itsm_acquit")      == false ) ? "" : get_config_var("itsm_acquit");
    $itsm_create    = (get_config_var("itsm_create")      == false ) ? "" : get_config_var("itsm_create");
    $itsm_champ_ged = (new ItsmPeer())->getListChampGed();
    $itsm_parents   = (new ItsmPeer())->get_all_itsm();
    $itsm=false;
    if(isset($_GET["url"])){
        $itsm  = (new ItsmPeer())->getItsmByUrl($_GET["url"]);
    }

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_itsm.title"); ?> <span class="badge badge-dark">béta</span></h1>
		</div>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">
                <?php echo getLabel("label.admin_itsm.itsm_setting"); ?>
            </h4>
            <div class="btn-group pull-right">
                    <div class="btn-group" id="result_state_itsm">
                        <?php echo $state; ?>
                    
                    <a href="index.php" class="btn btn-info" id="btn_return" role="button">
                    <i class="fa fa-reply"></i>
					</a>
                    </div>
            </div>
        </div>
        <div class="panel-body">
            <form  class="form-horizontal" id="myForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="itsm_url"><?php echo getLabel("label.admin_itsm.url"); ?> :</label>
                    <div class="col-sm-6"> 
                        <input class="form-control" name="itsm_url_id" <?php if($itsm!=false){echo 'value="'.$itsm->getItsm_id().'"';} ?> type="hidden">
                        <input type="text" class="form-control" id="itsm_url" name="itsm_url" placeholder="http://url.com" <?php if($itsm!=false){echo 'value="'.$itsm->getItsm_url().'"';}?> required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" ><?php echo getLabel("label.admin_itsm.file"); ?> :</label>
                    <div class="col-sm-6">
                        <div class="input-group">
                            <label class="input-group-btn">
                                <span class="btn btn-primary" id="btn_import">
                                    <?php echo getLabel("action.import"); ?>&hellip; <input id="input_file" name="fileName" type="file" accept=".json,.xml" style="display: none;" >
                                </span>
                            </label>
                            <input type="text" id="file_label" class="form-control" name="input_file_name" placeholder="File type : xml , json" <?php if($itsm!=false){echo 'value="'.$itsm->getItsm_file().'"';}?> readonly>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="itsm_url"><?php echo getLabel("label.admin_itsm.type_request"); ?> :</label>
                    <div class="col-sm-6"> 
                    <select class="form-control select_champ" name="itsm_type_request" >
                    <?php if($itsm!=false){
                            $type = $itsm->getItsm_type_request();
                            if(isset($type) &&  $type== "post"){
                                echo "<option value=\"post\" selected>POST</option>
                                <option value=\"get\" >GET</option>";
                            }else{
                                echo"<option value=\"post\" >POST</option>
                                <option value=\"get\" selected>GET</option>";
                            }
                    }else echo "<option value=\"post\" >POST</option> <option value=\"get\" selected>GET</option>";
                    ?>               
                                        
                    </select>
                    </div>
                </div>
                <div id="dynamic_fields_header">
                    <div class="form-group">
                        <label class="control-label col-sm-2 first"><?php echo getLabel("label.admin_itsm.header").":"; ?></label>        
                        <div class="col-sm-1"><button type="button" id="add_empty_header" class="btn"><i class="fa fa-plus"></i></button></div>
                    </div>
                    <?php 
                        $nb = 0;
                        if($itsm!=false && count($itsm->getItsm_headers())>0){
                            
                            foreach($itsm->getItsm_headers() as $key=>$value){
                                echo "<div class=\"form-group\">
                                        <label class=\"control-label col-sm-2\"></label>
                                        <div class=\"col-sm-6\"> 
                                            <input type=\"text\" class=\"form-control itsm_header\" id=\"itsm_header_".$nb."\" name=\"itsm_header[]\" placeholder=\"SoapAction : mc...\" value=\"".$value."\">
                                        </div>
                                        <div class=\"col-sm-2\"><button type=\"button\" id=\"delete_header_".$key."\" class=\"btn btn-danger delete-header\" ><i class=\"fa fa-trash\"></i></button></div>
                                    </div>";
                                $nb ++;
                            }
                            

                        }else{
                            echo "<div class=\"form-group\">
                            <label class=\"control-label col-sm-2\"></label>
                                <div class=\"col-sm-6\"> 
                                    <input type=\"text\" class=\"form-control itsm_header\" id=\"itsm_header_".$nb."\" name=\"itsm_header[]\" placeholder=\"SoapAction : mc...\" >
                                </div>
                                <div class=\"col-sm-2\"><button type=\"button\" class=\"btn btn-danger delete-header\" ><i class=\"fa fa-trash\"></i></button></div>
                            </div>";
                        }
                    ?>
                </div>

                <div id="dynamic_fields_var">
                    <div class="form-group">
                            <label class="control-label col-sm-2"><?php echo getLabel("label.admin_itsm.var").":"; ?></label>
                            <div class="col-sm-1"><button type="button" id="add_empty_var" class="btn"><i class="fa fa-plus"></i></button></div>
                    </div>

                    <?php 
                        $nb = 0;
                        if($itsm!=false && count($itsm->getItsm_vars())>0 ){
                            $var_key_list = array_keys($itsm->getItsm_vars());
                            $last_key = end($var_key_list);
                            foreach($itsm->getItsm_vars() as $key=>$value){
                                echo "<div class=\"form-group\">
                                        <label class=\"control-label col-sm-2\"></label>
                                        <div class=\"col-sm-3 input\"> 
                                            <input type=\"text\" class=\"form-control \" id=\"itsm_var_".$nb."\" name=\"itsm_var[".$nb."][var_name]\" placeholder=\"%COMMENTAIRE%\" value=\"".$key."\">
                                        </div>
                                        <div class=\"col-sm-3 select\"> 
                                        <select class=\"form-control select_champ\" name=\"itsm_var[".$nb."][champ_ged_id]\" >";
                                        foreach($itsm_champ_ged as $key2=>$value2){
                                            if(strval($value)==strval($key2)){
                                                echo "<option value=\"".$key2."\" selected>".$value2."</option>";
                                            }else{
                                                echo "<option value=\"".$key2."\">".$value2."</option>";
                                            }
                                            
                                        }
                                   
                                echo "</select>
                                    </div><div class=\"col-sm-2\"><button type=\"button\" class=\"btn btn-danger delete-var\" ><i class=\"fa fa-trash\"></i></button></div> </div>";
                                $nb ++;
                            }
                            

                        }else{
                            echo "<div class=\"form-group\">
                                    <label class=\"control-label col-sm-2\"></label>
                                <div class=\"col-sm-3 input\"> 
                                    <input type=\"text\" class=\"form-control itsm_var\" id=\"itsm_var_".$nb."\" name=\"itsm_var[".$nb."][var_name]\" placeholder=\"%DETAILS%\" >
                                </div>
                                <div class=\"col-sm-3 select\"> 
                                    <select class=\"form-control select_champ\" name=\"itsm_var[".$nb."][champ_ged_id]\" >
                                    <option ></option>";
                                    foreach($itsm_champ_ged as $key=>$value){
                                        echo "<option value=\"".$key."\">".$value."</option>";
                                    }
                                   
                            echo " </select>
                            </div>
                            <div class=\"col-sm-2\"><button type=\"button\" class=\"btn btn-danger delete-var\" ><i class=\"fa fa-trash\"></i></button></div>
                            </div>";
                        }
                    ?>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="itsm_parent"><?php echo getLabel("label.admin_itsm.parent").":"; ?></label>
                    <div class="col-sm-6"> 
                        <select class="form-control" id="itsm_parent" name="itsm_parent" >
                            <option></option>
                            <?php
                            if($itsm!=false){
                                foreach($itsm_parents as $parent){
                                    $id_parent = $itsm->getItsm_parent();
                                    
                                    if(isset($id_parent) && $parent->getItsm_id() == $id_parent ){
                                        echo "<option value=\"".$id_parent."\" selected>".$parent->getItsm_url()."</option>";
                                    }else{
                                        echo "<option value=\"".$parent->getItsm_id()."\">".$parent->getItsm_url()."</option>";
                                    }
                                }
                            }
                            ?>
                            
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="itsm_request_return_value"><?php echo getLabel("label.admin_itsm.return_val").":"; ?></label>
                    <div class="col-sm-6" id="itsm_request_return_value">
                        <?php 
                            if($itsm!=false){
                                echo "<input list=\"champs_generate\"  class=\"form-control\" id=\"itsm_generate_champ\" name=\"itsm_return_champ\" value=\"".$itsm->getItsm_return_champ()."\">";
    
                            }else{
                                echo "<input list=\"champs_generate\"  class=\"form-control\" id=\"itsm_generate_champ\" name=\"itsm_return_champ\">";
                            }
                            
                        ?>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" id="generate_list" class="btn btn-info"><?php echo getLabel("label.admin_itsm.btn_generate"); ?></button>
                    </div>
                    
                </div>
                
               
                
            </form>
            <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-4">
                        <button class="btn btn-primary" id="btn_form" type="submit">
                            <?php echo getLabel("action.apply"); ?>
                        </button>
                    </div>
                    <div  id="info_options" class="col-sm-4 alert alert-warning" hidden>
                        <strong><?php echo getLabel("label.admin_itsm.warn"); ?> ! </strong> <?php echo getLabel("label.admin_itsm.warn_text"); ?>
                    </div>
                </div>
            

        </div>
    </div>
 

	<br/>
	
	<!-- Result here ! -->
	<div id="result"></div>
	
</div>

<?php include("../../footer.php"); ?>

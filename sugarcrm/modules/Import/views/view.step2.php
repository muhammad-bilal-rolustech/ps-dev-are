<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2007 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: view.step2.php 31561 2008-02-04 18:41:10Z jmertic $
 * Description: view handler for step 2 of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/

require_once('modules/Import/views/ImportView.php');

class ImportViewStep2 extends ImportView
{
 	protected $pageTitleKey = 'LBL_STEP_2_TITLE';

 	/**
     * @see SugarView::getMenu()
     */
    public function getMenu(
        $module = null
        )
    {
        global $mod_strings, $current_language;
        
        if ( empty($module) )
            $module = $_REQUEST['import_module'];
        
        $old_mod_strings = $mod_strings;
        $mod_strings = return_module_language($current_language, $module);
        $returnMenu = parent::getMenu($module);
        $mod_strings = $old_mod_strings;
        
        return $returnMenu;
    }
    
 	/**
     * @see SugarView::_getModuleTab()
     */
 	protected function _getModuleTab()
    {
        global $app_list_strings, $moduleTabMap;
        
 		// Need to figure out what tab this module belongs to, most modules have their own tabs, but there are exceptions.
        if ( !empty($_REQUEST['module_tab']) )
            return $_REQUEST['module_tab'];
        elseif ( isset($moduleTabMap[$_REQUEST['import_module']]) )
            return $moduleTabMap[$_REQUEST['import_module']];
        // Default anonymous pages to be under Home
        elseif ( !isset($app_list_strings['moduleList'][$_REQUEST['import_module']]) )
            return 'Home';
        else
            return $_REQUEST['import_module'];
 	}
 	
 	/**
	 * @see SugarView::_getModuleTitleParams()
	 */
	protected function _getModuleTitleParams()
	{
	    global $mod_strings, $app_list_strings;
	    
	    $iconPath = $this->getModuleTitleIconPath($this->module);
	    $returnArray = array();
	    if (!empty($iconPath) && !$browserTitle) {
	        $returnArray[] = "<a href='index.php?module={$_REQUEST['import_module']}&action=index'><!--not_in_theme!--><img src='{$iconPath}' alt='{$app_list_strings['moduleList'][$_REQUEST['import_module']]}' title='{$app_list_strings['moduleList'][$_REQUEST['import_module']]}' align='absmiddle'></a>";
    	}
    	else {
    	    $returnArray[] = $app_list_strings['moduleList'][$_REQUEST['import_module']];
    	}
	    $returnArray[] = "<a href='index.php?module=Import&action=Step1&import_module={$_REQUEST['import_module']}'>".$mod_strings['LBL_MODULE_NAME']."</a>";
	    $returnArray[] = $mod_strings['LBL_STEP_2_TITLE'];
    	
	    return $returnArray;
    }


 	/** 
     * @see SugarView::display()
     */
 	public function display()
    {
        global $mod_strings, $app_list_strings, $app_strings, $current_user, $import_bean_map;
        global $import_mod_strings;        
        
        $this->instruction = 'LBL_SELECT_UPLOAD_INSTRUCTION';
        $this->ss->assign('INSTRUCTION', $this->getInstruction());

        $this->ss->assign("MODULE_TITLE", json_encode($this->getModuleTitle(false)));
        $this->ss->assign("IMP", $import_mod_strings);
        $this->ss->assign("CURRENT_STEP", $this->currentStep);
        $this->ss->assign("TYPE",( !empty($_REQUEST['type']) ? $_REQUEST['type'] : "import" ));
        $this->ss->assign("CUSTOM_DELIMITER", ( !empty($_REQUEST['custom_delimiter']) ? $_REQUEST['custom_delimiter'] : "," ));
        $this->ss->assign("CUSTOM_ENCLOSURE",htmlentities(
            ( !empty($_REQUEST['custom_enclosure']) && $_REQUEST['custom_enclosure'] != 'other' 
                ? $_REQUEST['custom_enclosure'] : 
                ( !empty($_REQUEST['custom_enclosure_other']) 
                    ? $_REQUEST['custom_enclosure_other'] : "" ) )));
        
        $this->ss->assign("IMPORT_MODULE", $_REQUEST['import_module']);
        $this->ss->assign("HEADER", $app_strings['LBL_IMPORT']." ". $mod_strings['LBL_MODULE_NAME']);
        $this->ss->assign("JS", json_encode($this->_getJS()));
        $this->ss->assign("SAMPLE_URL", "<a href=\"javascript: void(0);\" onclick=\"window.location.href='index.php?entryPoint=export&module=".$_REQUEST['import_module']."&action=index&all=true&sample=true'\" >".$mod_strings['LBL_EXAMPLE_FILE']."</a>");

        $displayBackBttn = isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != 'index'? TRUE : FALSE;
        $this->ss->assign("displayBackBttn", $displayBackBttn);
        
        $importSource = isset($_REQUEST['source']) ? $_REQUEST['source'] : 'csv' ;
        //Start custom mapping
        // show any custom mappings
        if (sugar_is_dir('custom/modules/Import') && $dir = opendir('custom/modules/Import'))
        {
            while (($file = readdir($dir)) !== false)
            {
                if (sugar_is_file("custom/modules/Import/{$file}") && strpos($file,".php") !== false)
                {
	                require_once("custom/modules/Import/{$file}");
	                $classname = str_replace('.php','',$file);
	                $mappingClass = new $classname;
	                $custom_mappings[] = $mappingClass->name;
                }
            }
        }
        // get user defined import maps
        $is_admin = is_admin($current_user);
        if($is_admin)
            $savedMappingHelpText = $mod_strings['LBL_MY_SAVED_ADMIN_HELP'];
        else
            $savedMappingHelpText = $mod_strings['LBL_MY_SAVED_HELP'];

        $this->ss->assign('savedMappingHelpText',$savedMappingHelpText);
        $this->ss->assign('is_admin',$is_admin);

        $import_map_seed = new ImportMap();
        $custom_imports_arr = $import_map_seed->retrieve_all_by_string_fields( array('assigned_user_id' => $current_user->id, 'is_published' => 'no','module' => $_REQUEST['import_module']));

        if( count($custom_imports_arr) )
        {
            $custom = array();
            foreach ( $custom_imports_arr as $import)
            {
                $custom[] = array( "IMPORT_NAME" => $import->name,"IMPORT_ID"   => $import->id);
            }
            $this->ss->assign('custom_imports',$custom);
        }
        
        // get globally defined import maps
        $published_imports_arr = $import_map_seed->retrieve_all_by_string_fields(array('is_published' => 'yes', 'module' => $_REQUEST['import_module'],) );
        if ( count($published_imports_arr) )
        {
            $published = array();
            foreach ( $published_imports_arr as $import)
            {
                $published[] = array("IMPORT_NAME" => $import->name, "IMPORT_ID"   => $import->id);
            }
            $this->ss->assign('published_imports',$published);
        }
        //End custom mapping

        // add instructions for anything other than custom_delimited
        $instructions = array();
        $lang_key = "CUSTOM";

        for ($i = 1; isset($mod_strings["LBL_{$lang_key}_NUM_$i"]);$i++)
        {
            $instructions[] = array(
                "STEP_NUM"         => $mod_strings["LBL_NUM_$i"],
                "INSTRUCTION_STEP" => $mod_strings["LBL_{$lang_key}_NUM_$i"],
            );
        }
        $this->ss->assign("INSTRUCTIONS_TITLE",$mod_strings["LBL_IMPORT_{$lang_key}_TITLE"]);
        $this->ss->assign("instructions",$instructions);

        $content = $this->ss->fetch('modules/Import/tpls/step2.tpl');
        
        $this->ss->assign("CONTENT",json_encode($content));
        $submitContent = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"right\">";
        $submitContent .= "<input title=\"".$mod_strings['LBL_IMPORT_COMPLETE']."\" onclick=\"SUGAR.importWizard.closeDialog();\" accessKey=\"\" class=\"button\" type=\"submit\" name=\"finished\" value=\"  ".$mod_strings['LBL_IMPORT_COMPLETE']."  \" id=\"finished\">";
        if ($displayBackBttn) {
            $submitContent .= "<input title=\"".$mod_strings['LBL_BACK']."\" accessKey=\"\" class=\"button\" type=\"submit\" name=\"button\" value=\"  ".$mod_strings['LBL_BACK']."  \" id=\"goback\">";
        }
	    $submitContent .= "<input title=\"".$mod_strings['LBL_NEXT']."\" accessKey=\"\" class=\"button primary\" type=\"submit\" name=\"button\" value=\"  ".$mod_strings['LBL_NEXT']."  \" id=\"gonext\"></td></tr></table></form>";
        $this->ss->assign("SUBMITCONTENT",json_encode($submitContent));
        
       
        
        $this->ss->display('modules/Import/tpls/wizardWrapper.tpl');
    }
    
    /**
     * Returns JS used in this view
     */
    private function _getJS()
    {
        global $mod_strings;
    
        return <<<EOJAVASCRIPT

if( document.getElementById('goback') )
{
    document.getElementById('goback').onclick = function(){
        document.getElementById('importstep2').action.value = 'Step1';
        
       	var success = function(data) {		
			eval(data.responseText);
			importWizardDialogDiv = document.getElementById('importWizardDialogDiv');
			importWizardDialogTitle = document.getElementById('importWizardDialogTitle');
			submitDiv = document.getElementById('submitDiv');
			importWizardDialogDiv.innerHTML = response['html'];
			importWizardDialogTitle.innerHTML = response['title'];
			submitDiv.innerHTML = response['submitContent'];
			eval(response['script']);
		} 
        var formObject = document.getElementById('importstep2');
		YAHOO.util.Connect.setForm(formObject);
		var cObj = YAHOO.util.Connect.asyncRequest('POST', "index.php", {success: success, failure: success});
			
			
    }
}

document.getElementById('gonext').onclick = function(){
    // warning message that tells user that updates can not be undone
    if(document.getElementById('import_update').checked)
    {
        ret = confirm(SUGAR.language.get("Import", 'LBL_CONFIRM_IMPORT'));
        if (!ret) {
            return false;
        }
    }
    clear_all_errors();
    var isError = false;
    // be sure we specify a file to upload
    if (document.getElementById('importstep2').userfile.value == "") {
        add_error_style(document.getElementById('importstep2').name,'userfile',"{$mod_strings['ERR_MISSING_REQUIRED_FIELDS']} {$mod_strings['ERR_SELECT_FILE']}");
        isError = true;
    }
    

    if (!isError) {
	var uploadHandler = {
	  	    upload: function(data) {		
				eval(data.responseText);
				importWizardDialogDiv = document.getElementById('importWizardDialogDiv');
				importWizardDialogTitle = document.getElementById('importWizardDialogTitle');
				submitDiv = document.getElementById('submitDiv');
				if(response['html'] != "") {
					importWizardDialogDiv.innerHTML = response['html'];
					importWizardDialogTitle.innerHTML = response['title'];
					submitDiv.innerHTML = response['submitContent'];
				}
				
				eval(response['script']);
			} 
	};
	        var formObject = document.getElementById('importstep2');
			YAHOO.util.Connect.setForm(formObject,true);
			var cObj = YAHOO.util.Connect.asyncRequest('POST', "index.php", uploadHandler);
		
    	} else {
			return false;
		}
}

function publishMapping(elem, publish, mappingId)
{
    if( typeof(elem.publish) != 'undefined' )
        publish = elem.publish;
        
    var url = 'index.php?action=mapping&module=Import&publish=' + publish + '&import_map_id=' + mappingId;
    var callback = {
                        success: function(o)
                        {
                            var r = YAHOO.lang.JSON.parse(o.responseText);
                            if( r.message != '')
                                alert(r.message);
                        },
                        failure: function(o) {}
                   };
    YAHOO.util.Connect.asyncRequest('GET', url, callback);
    //Toggle the button title
    if(publish == 'yes')
    {
        var newTitle = SUGAR.language.get('Import','LBL_UNPUBLISH');
        var newPublish = 'no';
    }
    else
    {
        var newTitle = SUGAR.language.get('Import','LBL_PUBLISH');
        var newPublish = 'yes';
    }
        
    elem.value = newTitle;
    elem.publish = newPublish;

}
function deleteMapping(elemId, mappingId )
{
    var elem = document.getElementById(elemId);
    var table = elem.parentNode;
    table.deleteRow(elem.rowIndex);

    var url = 'index.php?action=mapping&module=Import&delete_map_id=' + mappingId;
    var callback = {
                        success: function(o)
                        {
                            var r = YAHOO.lang.JSON.parse(o.responseText);
                            if( r.message != '')
                                alert(r.message);
                        },
                        failure: function(o) {}
                   };
    YAHOO.util.Connect.asyncRequest('GET', url, callback);
}
var deselectEl = document.getElementById('deselect');
if(deselectEl)
{
    deselectEl.onclick = function() {
        var els = document.getElementsByName('source');
        for(i=0;i<els.length;i++)
        {
            els[i].checked = false;
        }
    }
}

EOJAVASCRIPT;
    }
}

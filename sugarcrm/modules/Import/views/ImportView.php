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
require_once('include/MVC/View/SugarView.php');


class ImportView extends SugarView
{
    protected $currentStep;
    protected $pageTitleKey;
    protected $instruction;

    public function __construct($bean = null, $view_object_map = array())
    {
        global $mod_strings;

        parent::__construct($bean, $view_object_map);

        if (isset($_REQUEST['button']) && trim($_REQUEST['button']) == htmlentities($mod_strings['LBL_BACK'])) {
            // if the request comes from the "Back" button, decrease the step count
            $this->currentStep = isset($_REQUEST['current_step']) ? ($_REQUEST['current_step'] - 1) : 1;
        } else {
            $this->currentStep = isset($_REQUEST['current_step']) ? ($_REQUEST['current_step'] + 1) : 1;
        }
        $this->importModule = isset($_REQUEST['import_module']) ? $_REQUEST['import_module'] : '';
        
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_javascript'] = false;
    }

    /**
     * @see SugarView::getMenu()
     */
    public function getMenu($module = null)
    {
        global $mod_strings, $current_language;

        if ( empty($module) )
            $module = $this->importModule;

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
        elseif ( isset($moduleTabMap[$this->importModule]) )
            return $moduleTabMap[$this->importModule];
        // Default anonymous pages to be under Home
        elseif ( !isset($app_list_strings['moduleList'][$this->importModule]) )
            return 'Home';
        else
            return $this->importModule;
 	}

    /**
	 * @see SugarView::_getModuleTitleParams()
	 */
	protected function _getModuleTitleParams($browserTitle = false)
	{
	    global $mod_strings, $app_list_strings;

	    $returnArray = array();
    	$returnArray[] = $app_list_strings['moduleList'][$this->importModule];
	    $returnArray[] = $mod_strings['LBL_MODULE_NAME'];
	    $returnArray[] = string_format($mod_strings[$this->pageTitleKey], array($this->currentStep));

	    return $returnArray;
    }

    protected function getInstruction()
    {
        global $mod_strings;

        $ins = '';
        
        if ($this->instruction) {
            $ins_string = $mod_strings[$this->instruction];
            $ins = '<div class="import_instruction">' . $ins_string . '</div>';
        }

        return $ins;
    }

     /**
     * Displays the Smarty template for an error
     *
     * @param string $message error message to show
     * @param string $module what module we were importing into
     * @param string $action what page we should go back to
     */
    protected function _showImportError($message,$module,$action = 'Step1')
    {
        $ss = new Sugar_Smarty();
        
        global $mod_strings;

		$themeObject = SugarThemeRegistry::current();
		$css = $themeObject->getCSS();
        $favicon = $themeObject->getImageURL('sugar_icon.ico',false);
        $ss->assign('FAVICON_URL',getJSPath($favicon));
        $ss->assign('SUGAR_CSS', $css);
        $ss->assign('PAGETITLE', $mod_strings[$this->pageTitleKey]);
        
        

        $ss->assign("MESSAGE",$message);
        $ss->assign("ACTION",$action);
        $ss->assign("IMPORT_MODULE",$module);
        $ss->assign("MOD", $GLOBALS['mod_strings']);
        $ss->assign("SOURCE","");
        if ( isset($_REQUEST['source']) )
            $ss->assign("SOURCE", $_REQUEST['source']);

        $content = $this->ss->fetch('modules/Import/tpls/error.tpl');
        $this->ss->assign("CONTENT",$content);
        
        ob_start();
        $this->options['show_javascript'] = true;
        $this->renderJavascript();
        $this->options['show_javascript'] = false;
        $ss->assign("SUGAR_JS",ob_get_contents().$themeObject->getJS());
        ob_end_clean();
        
        $this->ss->display('modules/Import/tpls/wizardWrapper.tpl');
    }
}
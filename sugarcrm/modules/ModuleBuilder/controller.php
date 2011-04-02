<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise End User
 * License Agreement ("License") which can be viewed at
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
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php') ;
require_once ('modules/ModuleBuilder/parsers/ParserFactory.php') ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class ModuleBuilderController extends SugarController
{
    var $action_remap = array ( ) ;

    /**
     * Used by the _getModuleTitleParams() method calls in ModuleBuilder views to get the correct string
     * for the section you are in
     *
     * @return string
     */
    public static function getModuleTitle()
    {
        global $mod_strings;

        if ( $_REQUEST['type'] == 'studio' ) {
            return $mod_strings['LBL_STUDIO'];
	    }
	    elseif ( $_REQUEST['type'] == 'sugarportal' ) {
            return $mod_strings['LBL_SUGARPORTAL'];
	    }
	    elseif ( $_REQUEST['type'] == 'mb' ) {
            return $mod_strings['LBL_MODULEBUILDER'];
        }
        elseif ( $_REQUEST['type'] == 'dropdowns') {
            return $mod_strings['LBL_DROPDOWNEDITOR'];
        }
	    elseif ( $_REQUEST['type'] == 'home' ) {
            return $mod_strings['LBL_HOME'];
        }
        else {
            return $mod_strings['LBL_DEVELOPER_TOOLS'];
        }
    }

    function fromModuleBuilder ()
    {
        return (isset ( $_REQUEST [ 'MB' ] ) && ($_REQUEST [ 'MB' ] == '1')) ;
    }

    function process(){
    	$GLOBALS [ 'log' ]->info ( get_class($this).":" ) ;
        global $current_user;
        $access = get_admin_modules_for_user($current_user);
		//BEGIN SUGARCRM flav=sales ONLY
		if(!empty($_REQUEST['type']) && $_REQUEST['type'] == 'mb'){
			$this->hasAccess = false;
		}else
		//END SUGARCRM flav=sales ONLY
		if(is_admin($current_user) || (is_admin_for_any_module($current_user) && !isset($_REQUEST['view_module']) && (isset($_REQUEST['action']) && $_REQUEST['action'] != 'package'))||
          (isset($_REQUEST['view_module']) && (in_array($_REQUEST['view_module'], $access)|| empty($_REQUEST['view_module']))) ||
          (isset($_REQUEST['type']) && (($_REQUEST['type']=='dropdowns' && is_admin_for_any_module($current_user))||
          ($_REQUEST['type']=='studio' && displayStudioForCurrentUser() == true))))
        {
            $this->hasAccess = true;
        }
        else
        {
            $this->hasAccess = false;
        }
        parent::process();
    }


    function action_editLayout ()
    {
        switch ( strtolower ( $_REQUEST [ 'view' ] ))
        {
            case MB_EDITVIEW :
            case MB_DETAILVIEW :
            case MB_QUICKCREATE :
            //BEGIN SUGARCRM flav=pro || flav=sales ONLY
            case MB_WIRELESSEDITVIEW :
            case MB_WIRELESSDETAILVIEW :
            //END SUGARCRM flav=pro || flav=sales ONLY
                $this->view = 'layoutView' ;
                break ;
            case MB_LISTVIEW :
            //BEGIN SUGARCRM flav=pro || flav=sales ONLY
            case MB_WIRELESSLISTVIEW :
            //END SUGARCRM flav=pro || flav=sales ONLY
                $this->view = 'listView' ;
                break ;
            case MB_BASICSEARCH :
            case MB_ADVANCEDSEARCH :
            //BEGIN SUGARCRM flav=pro || flav=sales ONLY
            case MB_WIRELESSBASICSEARCH :
            case MB_WIRELESSADVANCEDSEARCH :
            //END SUGARCRM flav=pro || flav=sales ONLY
                $this->view = 'searchView' ;
                break ;
            case MB_DASHLET :
            case MB_DASHLETSEARCH :
                $this->view = 'dashlet' ;
                break ;
            case MB_POPUPLIST :
            case MB_POPUPSEARCH :
                $this->view = 'popupview' ;
                break ;
            default :
                $GLOBALS [ 'log' ]->fatal ( 'Action = editLayout with unknown view=' . $_REQUEST [ 'view' ] ) ;
        }
    }


    function action_ViewTree ()
    {
        require_once ('modules/ModuleBuilder/MB/AjaxCompose.php') ;
        switch ( $_REQUEST [ 'tree' ])
        {
            case 'ModuleBuilder' :
                require_once ('modules/ModuleBuilder/MB/MBPackageTree.php') ;
                $mbt = new MBPackageTree ( ) ;
                break ;
            case 'Studio' :
                require_once ('modules/ModuleBuilder/Module/StudioTree.php') ;
                $mbt = new StudioTree ( ) ;
        }
        $ajax = new AjaxCompose ( ) ;
        $ajax->addSection ( 'west', $mbt->getName (), $mbt->fetchNodes () ) ;
        echo $ajax->getJavascript () ;

        sugar_cleanup ( true ) ;

    }

    function action_SavePackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = (! empty ( $_REQUEST [ 'original_name' ] )) ? $_REQUEST [ 'original_name' ] : $_REQUEST [ 'name' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $load ) ;

            if (! empty ( $_REQUEST [ 'duplicate' ] ))
            {
                $result = $mb->packages [ $load ]->copy ( $_REQUEST [ 'name' ] ) ;
                $load = $mb->packages [ $load ]->name ;
                $mb->getPackage ( $load ) ;
            }
            $mb->packages [ $load ]->populateFromPost () ;
            $mb->packages [ $load ]->loadModules () ;
            $mb->save () ;
            if (! empty ( $_REQUEST [ 'original_name' ] ) && $_REQUEST [ 'original_name' ] != $_REQUEST [ 'name' ])
            {
                if (! $mb->packages [ $load ]->rename ( $_REQUEST [ 'name' ] ))
                {
                    $mb->packages [ $load ]->name = $_REQUEST [ 'original_name' ] ;
                    $_REQUEST [ 'name' ] = $_REQUEST [ 'original_name' ] ;
                }
            }
            $_REQUEST [ 'package' ] = $_REQUEST [ 'name' ] ;
            $this->view = 'package' ;
        }
    }

    function action_BuildPackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = $_REQUEST [ 'name' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $load ) ;
            $mb->packages [ $load ]->build () ;
        }
    }

    function action_DeployPackage ()
    {
    	if(defined('TEMPLATE_URL')){
    		sugar_cache_reset();
    		SugarTemplateUtilities::disableCache();
    	}

        $mb = new ModuleBuilder ( ) ;
        $load = $_REQUEST [ 'package' ] ;
        $message = $GLOBALS [ 'mod_strings' ] [ 'LBL_MODULE_DEPLOYED' ] ;
        if (! empty ( $load ))
        {
            $zip = $mb->getPackage ( $load ) ;
            require_once ('ModuleInstall/PackageManager/PackageManager.php') ;
            $pm = new PackageManager ( ) ;
            $info = $mb->packages [ $load ]->build ( false ) ;
            mkdir_recursive ( $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/upload/upgrades/module/') ;
            rename ( $info [ 'zip' ], $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/' . 'upload/upgrades/module/' . $info [ 'name' ] . '.zip' ) ;
            copy ( $info [ 'manifest' ], $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/' . 'upload/upgrades/module/' . $info [ 'name' ] . '-manifest.php' ) ;
            $_REQUEST [ 'install_file' ] = $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/' . 'upload/upgrades/module/' . $info [ 'name' ] . '.zip' ;
            $GLOBALS [ 'mi_remove_tables' ] = false ;
            $pm->performUninstall ( $load ) ;
			 //#23177 , js cache clear
			 clearAllJsAndJsLangFilesWithoutOutput();
    		//#30747, clear the cache in memory
    		$cache_key = 'app_list_strings.'.$GLOBALS['current_language'];
    		sugar_cache_clear($cache_key );
    		sugar_cache_reset();
    		//clear end
            $pm->performInstall ( $_REQUEST [ 'install_file' ] , true) ;

            //clear the unified_search_module.php file
            require_once('modules/Home/UnifiedSearchAdvanced.php');
            UnifiedSearchAdvanced::unlinkUnifiedSearchModulesFile();
        }
        echo 'complete' ;

    }

    function action_ExportPackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = $_REQUEST [ 'name' ] ;
        $author = $_REQUEST [ 'author' ] ;
        $description = $_REQUEST [ 'description' ] ;
        $readme = $_REQUEST [ 'readme' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $load ) ;
            $mb->packages [ $load ]->author = $author ;
            $mb->packages [ $load ]->description = $description ;
            $mb->packages [ $load ]->exportProject () ;
            $mb->packages [ $load ]->readme = $readme ;
        }
    }

    function action_DeletePackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $mb->getPackage ( $_REQUEST [ 'package' ] ) ;
        $mb->packages [ $_REQUEST [ 'package' ] ]->delete () ;
        $this->view = 'deletepackage' ;
    }

    function action_SaveModule ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = (! empty ( $_REQUEST [ 'original_name' ] )) ? $_REQUEST [ 'original_name' ] : $_REQUEST [ 'name' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $_REQUEST [ 'package' ] ) ;
            $mb->packages [ $_REQUEST [ 'package' ] ]->getModule ( $load ) ;
            $module = & $mb->packages [ $_REQUEST [ 'package' ] ]->modules [ $load ] ;
            $module->populateFromPost () ;
            $mb->save () ;
            if (! empty ( $_REQUEST [ 'duplicate' ] ))
            {
                $module->copy ( $_REQUEST [ 'name' ] ) ;
            } else if (! empty ( $_REQUEST [ 'original_name' ] ) && $_REQUEST [ 'original_name' ] != $_REQUEST [ 'name' ])
            {
                if (! $module->rename ( $_REQUEST [ 'name' ] ))
                {
                    $module->name = $_REQUEST [ 'original_name' ] ;
                    $_REQUEST [ 'name' ] = $_REQUEST [ 'original_name' ] ;
                }
            }

            $_REQUEST [ 'view_package' ] = $_REQUEST [ 'package' ] ;
            $_REQUEST [ 'view_module' ] = $module->name ;
            $this->view = 'module' ;
        }
    }

    function action_DeleteModule ()
    {
        $mb = new ModuleBuilder ( ) ;
        $module = & $mb->getPackageModule ( $_REQUEST [ 'package' ], $_REQUEST [ 'view_module' ] ) ;
        $module->delete () ;
        $this->view = 'package' ;
    }

    function action_saveLabels ()
    {
        require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
        $parser = new ParserLabel ( $_REQUEST['view_module'] , isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
        $parser->handleSave ( $_REQUEST, $_REQUEST [ 'selected_lang' ] ) ;
        if (isset ( $_REQUEST [ 'view_package' ] )) //MODULE BUILDER
        {
            $this->view = 'modulelabels' ;
        } else //STUDIO
        {
            $this->view = isset ( $_REQUEST [ 'view' ] ) ? 'edit' : 'labels' ; // detect if we are being called by the LayoutEditor rather than the LabelEditor (set in view.layoutlabel.php)
        }
    }

    function action_SaveLabel ()
    {
        if (! empty ( $_REQUEST [ 'view_module' ] ) && !empty($_REQUEST [ 'labelValue' ]))
        {
            $_REQUEST [ "label_" . $_REQUEST [ 'label' ] ] = $_REQUEST [ 'labelValue' ] ;
            require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
            $parser = new ParserLabel ( $_REQUEST['view_module'] , isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
            $parser->handleSave ( $_REQUEST, $GLOBALS [ 'current_language' ] ) ;

        }
        $this->view = 'modulefields' ;
    }

    function action_ExportCustom ()
    {
        $modules = $_REQUEST [ 'modules' ] ;
        $name = $_REQUEST [ 'name' ] ;
        $author = $_REQUEST [ 'author' ] ;
        $description = $_REQUEST [ 'description' ] ;
        ob_clean () ;
        if (! empty ( $modules ) && ! empty ( $name ))
        {
            require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php') ;
            $mb = new MBPackage ( $name ) ;
            $mb->author = $author ;
            $mb->description = $description ;
            $mb->exportCustom ( $modules, true, true ) ;
        }
    }

    function action_SaveHook()
    {
        if(isset($_REQUEST['type']) && isset($_REQUEST['view_package']) && isset($_REQUEST['view_module'])) {
            $mb = new ModuleBuilder ( ) ;
            $module = $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            if(isset($_REQUEST['hook'])) {
                $module->hooks[$_REQUEST['type']][$_REQUEST['hook']][0] = $_REQUEST['order'];
                $module->hooks[$_REQUEST['type']][$_REQUEST['hook']][3] = $_REQUEST['class'];
                $module->hooks[$_REQUEST['type']][$_REQUEST['hook']][4] = $_REQUEST['func'];
            } else {
                $fname = $_FILES['file']['tmp_name'];
                if(is_uploaded_file($fname)) {
                    $lhname = "modules/".$module->key_name."/LogicHooks/".basename($_FILES['file']['name']);
                    if(!file_exists($module->path."/LogicHooks")) {
                       mkdir_recursive($module->path."/LogicHooks");
                    }
                    move_uploaded_file($fname, $module->path."/LogicHooks/".basename($_FILES['file']['name']));
                    if(empty($_REQUEST['order'])) {
                        $_REQUEST['order'] = 1;
                    }
                    $module->hooks[$_REQUEST['type']][] = array($_REQUEST['order'], $module->key_name, $lhname,  $_REQUEST['class'],  $_REQUEST['func']);
                }
            }
            $module->saveHooks();
        }
        $this->view = 'modulehooks' ;
    }

    function action_SaveField ()
    {
        require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_REQUEST [ 'type' ] ) ;
        $_REQUEST [ 'name' ] = trim ( $_REQUEST [ 'name' ] ) ;

        $field->populateFromPost () ;

        if (!isset ( $_REQUEST [ 'view_package' ] ))
        {
            require_once ('modules/DynamicFields/DynamicField.php') ;
            if (! empty ( $_REQUEST [ 'view_module' ] ))
            {
                $module = $_REQUEST [ 'view_module' ] ;

                $bean = loadBean($module);
                if(!empty($bean))
                {
	                $field_defs = $bean->field_defs;
	                if(isset($field_defs[$field->name. '_c']))
	                {
						$GLOBALS['log']->error($GLOBALS['mod_strings']['ERROR_ALREADY_EXISTS'] . '[' . $field->name . ']');
						sugar_die($GLOBALS['mod_strings']['ERROR_ALREADY_EXISTS']);
	                }
                }

                $df = new DynamicField ( $module ) ;
                $class_name = $GLOBALS [ 'beanList' ] [ $module ] ;
                require_once ($GLOBALS [ 'beanFiles' ] [ $class_name ]) ;
                $mod = new $class_name ( ) ;
                $df->setup ( $mod ) ;

                $field->save ( $df ) ;
                $this->action_SaveLabel () ;
                include_once ('modules/Administration/QuickRepairAndRebuild.php') ;
        		global $mod_strings;
                $mod_strings['LBL_ALL_MODULES'] = 'all_modules';
                $repair = new RepairAndClear();
		        $repair->repairAndClearAll(array('rebuildExtensions', 'clearVardefs', 'clearTpls'), array($class_name), true, false);
		        //#28707 ,clear all the js files in cache
		        $repair->module_list = array();
		        $repair->clearJsFiles();
            }
        } else
        {
            $mb = new ModuleBuilder ( ) ;
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            $field->save ( $module ) ;
            $module->mbvardefs->save () ;
            // get the module again to refresh the labels we might have saved with the $field->save (e.g., for address fields)
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            if (isset ( $_REQUEST [ 'label' ] ) && isset ( $_REQUEST [ 'labelValue' ] ))
                $module->setLabel ( $GLOBALS [ 'current_language' ], $_REQUEST [ 'label' ], $_REQUEST [ 'labelValue' ] ) ;
            $module->save();
        }
        $this->view = 'modulefields' ;
    }

    function action_saveSugarField ()
    {
    	global $mod_strings;
    	require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_REQUEST [ 'type' ] ) ;
        $_REQUEST [ 'name' ] = trim ( $_POST [ 'name' ] ) ;

        $field->populateFromPost () ;
        require_once ('modules/ModuleBuilder/parsers/StandardField.php') ;
        $module = $_REQUEST [ 'view_module' ] ;
        $df = new StandardField ( $module ) ;
        $class_name = $GLOBALS [ 'beanList' ] [ $module ] ;
        require_once ($GLOBALS [ 'beanFiles' ] [ $class_name ]) ;
        $mod = new $class_name ( ) ;
        $df->setup ( $mod ) ;

        $field->module = $mod;
        $field->save ( $df ) ;
        $this->action_SaveLabel () ;

        $MBmodStrings = $mod_strings;
        $GLOBALS [ 'mod_strings' ] = return_module_language ( '', 'Administration' ) ;

       	include_once ('modules/Administration/QuickRepairAndRebuild.php') ;
        $GLOBALS [ 'mod_strings' ]['LBL_ALL_MODULES'] = 'all_modules';
        $_REQUEST['execute_sql'] = true;

        $repair = new RepairAndClear();
        $repair->repairAndClearAll(array('rebuildExtensions', 'clearVardefs', 'clearTpls'), array($class_name), true, false);
        //#28707 ,clear all the js files in cache
        $repair->module_list = array();
        $repair->clearJsFiles();


        // now clear the cache so that the results are immediately visible
        include_once ('include/TemplateHandler/TemplateHandler.php') ;
        TemplateHandler::clearCache ( $module ) ;

        $GLOBALS [ 'mod_strings' ] = $MBmodStrings;
    }

    function action_RefreshField ()
    {
        require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_POST [ 'type' ] ) ;
        $field->populateFromPost () ;
        $this->view = 'modulefield' ;
    }

    function action_saveVisibility ()
    {
		$packageName = (isset ( $_REQUEST [ 'view_package' ] ) && (strtolower($_REQUEST['view_package']) != 'studio')) ? $_REQUEST [ 'view_package' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( MB_VISIBILITY, $_REQUEST [ 'view_module' ], $packageName ) ;

        $json = getJSONobj();
        $visibility_grid = $json->decode(html_entity_decode(rawurldecode($_REQUEST [ 'visibility_grid' ]), ENT_QUOTES) );
		$parser->saveVisibility ( $_REQUEST [ 'fieldname' ] , $_REQUEST [ 'trigger' ] , $visibility_grid ) ;

        echo $json->encode(array( "visibility_editor_{$_REQUEST['fieldname']}" => array("action" => "deactivate")));
    }

	function action_SaveRelationshipLabel (){
            $selected_lang = (!empty($_REQUEST['relationship_lang'])?$_REQUEST['relationship_lang']:$_SESSION['authenticated_user_language']);
		 if (empty($_REQUEST [ 'view_package' ])){
            require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php' ;
            $relationships = new DeployedRelationships ( $_REQUEST [ 'view_module' ] ) ;
            if (! empty ( $_REQUEST [ 'relationship_name' ] ))
	        {
	            if ($relationship = $relationships->get ( $_REQUEST [ 'relationship_name' ] )){
	            	$metadata = $relationship->buildLabels(true);
	            	 require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
			        $parser = new ParserLabel ( $_REQUEST['view_module'] ) ;
			        $parser->handleSaveRelationshipLabels ( $metadata, $selected_lang ) ;
	            }
            }
        }
        else {
            //TODO FOR MB
        }
        $this->view = 'relationships' ;
	}

    function action_SaveRelationship ()
    {
        if(!empty($GLOBALS['current_user']) && empty($GLOBALS['modListHeader']))
        {
            $GLOBALS['modListHeader'] = query_module_access_list($GLOBALS['current_user']);
        }

        if (empty($_REQUEST [ 'view_package' ]))
        {
            require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php' ;
            $relationships = new DeployedRelationships ( $_REQUEST [ 'view_module' ] ) ;
        } else
        {
            $mb = new ModuleBuilder ( ) ;
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            require_once 'modules/ModuleBuilder/parsers/relationships/UndeployedRelationships.php' ;
            $relationships = new UndeployedRelationships ( $module->getModuleDir () ) ;
        }

        $relationships->addFromPost () ;
        $relationships->save () ;
        $GLOBALS['log']->debug("\n\nSTART BUILD");
        if (empty($_REQUEST [ 'view_package' ])) {
            $relationships->build () ;

            LanguageManager::clearLanguageCache($_REQUEST [ 'view_module' ]);
        }
        $GLOBALS['log']->debug("\n\nEND BUILD");

        $this->view = 'relationships' ;
    }

    function action_DeleteRelationship ()
    {
        if (isset ( $_REQUEST [ 'relationship_name' ] ))
        {
            if (empty($_REQUEST [ 'view_package' ] ))
            {
                require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php' ;
                if (!empty($_REQUEST['remove_tables']))
				    $GLOBALS['mi_remove_tables'] = $_REQUEST['remove_tables'];
                $relationships = new DeployedRelationships ( $_REQUEST [ 'view_module' ] ) ;
            } else
            {
                $mb = new ModuleBuilder ( ) ;
                $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
                require_once 'modules/ModuleBuilder/parsers/relationships/UndeployedRelationships.php' ;
                $relationships = new UndeployedRelationships ( $module->getModuleDir () ) ;
            }
            $relationships->delete ( $_REQUEST [ 'relationship_name' ] ) ;
            $relationships->save () ;
        }
        $this->view = 'relationships' ;
    }

    function action_SaveDropDown ()
    {
        require_once 'modules/ModuleBuilder/parsers/parser.dropdown.php' ;
        $parser = new ParserDropDown ( ) ;
        $parser->saveDropDown ( $_REQUEST ) ;
        $this->view = 'dropdowns' ;
    }

    function action_DeleteField ()
    {
        require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_REQUEST [ 'type' ] ) ;
        $field->name = $_REQUEST [ 'name' ] ;
        if (!isset ( $_REQUEST [ 'view_package' ] ))
        {
            if (! empty ( $_REQUEST [ 'name' ] ) && ! empty ( $_REQUEST [ 'view_module' ] ))
            {
                require_once ('modules/DynamicFields/DynamicField.php') ;
                $moduleName = $_REQUEST [ 'view_module' ] ;
                $class_name = $GLOBALS [ 'beanList' ] [ $moduleName ] ;
                require_once ($GLOBALS [ 'beanFiles' ] [ $class_name ]) ;
                $seed = new $class_name ( ) ;
                $df = new DynamicField ( $moduleName ) ;
                $df->setup ( $seed ) ;
                //Need to load the entire field_meta_data for some field types
                $field = $df->getFieldWidget($moduleName, $field->name);
                $field->delete ( $df ) ;

                $GLOBALS [ 'mod_strings' ]['LBL_ALL_MODULES'] = 'all_modules';
                $_REQUEST['execute_sql'] = true;
                include_once ('modules/Administration/QuickRepairAndRebuild.php') ;
                $repair = new RepairAndClear();
                $repair->repairAndClearAll(array('rebuildExtensions', 'clearVardefs', 'clearTpls'), array($class_name), true, false);
                require_once 'modules/ModuleBuilder/Module/StudioModuleFactory.php' ;
                $module = StudioModuleFactory::getStudioModule( $moduleName ) ;
            }
        }
        else
        {
            $mb = new ModuleBuilder ( ) ;
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            $field->delete ( $module ) ;
            $mb->save () ;
        }
        $module->removeFieldFromLayouts( $field->name );
        $this->view = 'modulefields' ;
    }

    function action_CloneField ()
    {
        $this->view_object_map [ 'field_name' ] = $_REQUEST [ 'name' ] ;
        $this->view_object_map [ 'is_clone' ] = true ;
        $this->view = 'modulefield' ;
    }

    function action_SaveAssistantPref ()
    {
        global $current_user ;
        if (isset ( $_REQUEST [ 'pref_value' ] ))
        {
            if ($_REQUEST [ 'pref_value' ] == 'ignore')
            {
                $current_user->setPreference ( 'mb_assist', 'DISABLED', 0, 'Assistant' ) ;
            } else
            {
                $current_user->setPreference ( 'mb_assist', 'ENABLED', 0, 'Assistant' ) ;
            }
            $current_pref = $current_user->getPreference ( 'mb_assist', 'Assistant' ) ;
            echo "Assistant.processUserPref('$current_pref')" ;
            sugar_cleanup ( true ) ; //push preferences to DB.
        }
    }

    // Studio2 Actions


    function action_EditProperty ()
    {
        $this->view = 'property' ;
    }

    function action_saveProperty ()
    {
        require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
        $modules = $_REQUEST['view_module'];
        if(!empty($_REQUEST['subpanel'])){
        	$modules = $_REQUEST['subpanel'];
        }
        $parser = new ParserLabel ( $modules , isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
        // if no language provided, then use the user's current language which is most likely what they intended
        $language = (isset($_REQUEST [ 'selected_lang' ])) ? $_REQUEST [ 'selected_lang' ] : $GLOBALS['current_language'] ;
        $parser->handleSave ( $_REQUEST, $language ) ;
        $json = getJSONobj();
        echo $json->encode(array("east" => array("action" => "deactivate")));
    }

    function action_editModule ()
    {
        $this->view = 'module' ;
    }

    function action_wizard ()
    {
        $this->view = 'wizard' ;
    }

    /**
     * Receive a layout through $_REQUEST and save it out to the working files directory
     * Expects a series of $_REQUEST parameters all in the format $_REQUEST['slot-panel#-slot#-property']=value
     */

    function action_saveLayout ()
    {
        //BEGIN SUGARCRM flav=ent ONLY
        if (isset ( $_REQUEST [ 'PORTAL' ] ))
        {
            $parser = ParserFactory::getParser ( 'portallayoutview', $_REQUEST [ 'view_module' ] ) ;
            $parser->init( $_REQUEST [ 'view_module' ] , $_REQUEST [ 'view' ], true ) ;
            $this->view = 'portallayoutview' ;
        }
        else
        {
        //END SUGARCRM flav=ent ONLY
            $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
            $this->view = 'layoutview' ;
        //BEGIN SUGARCRM flav=ent ONLY
        }
        //END SUGARCRM flav=ent ONLY
        $parser->writeWorkingFile () ;

    	if(!empty($_REQUEST [ 'sync_detail_and_edit' ]) && $_REQUEST['sync_detail_and_edit'] != false && $_REQUEST['sync_detail_and_edit'] != "false"){
	        if(strtolower ($parser->_view) == MB_EDITVIEW){
	        	$parser2 = ParserFactory::getParser ( MB_DETAILVIEW, $_REQUEST [ 'view_module' ], isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
	        	$parser2->setUseTabs($parser->getUseTabs());
                $parser2->writeWorkingFile () ;
	        }
        }
    }

    function action_saveAndPublishLayout ()
    {
        //BEGIN SUGARCRM flav=ent ONLY
        if (isset ( $_REQUEST [ 'PORTAL' ] ))
        {
            $parser = ParserFactory::getParser ( 'portallayoutview', $_REQUEST [ 'view_module' ] ) ;
            $parser->init( $_REQUEST [ 'view_module' ] , $_REQUEST [ 'view' ], true ) ;
            $this->view = 'portallayoutview' ;
            $parser->writeWorkingFile () ;
        }
        else
        {
        //END SUGARCRM flav=ent ONLY
            $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
            $this->view = 'layoutview' ;
        //BEGIN SUGARCRM flav=ent ONLY
        }
        //END SUGARCRM flav=ent ONLY
        $parser->handleSave () ;

        if(!empty($_REQUEST [ 'sync_detail_and_edit' ]) && $_REQUEST['sync_detail_and_edit'] != false && $_REQUEST['sync_detail_and_edit'] != "false"){
	        if(strtolower ($parser->_view) == MB_EDITVIEW){
	        	$parser2 = ParserFactory::getParser ( MB_DETAILVIEW, $_REQUEST [ 'view_module' ], isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
	        	$parser2->setUseTabs($parser->getUseTabs());
                $parser2->handleSave () ;
	        }
        }
    }

    function action_manageBackups ()
    {

    }

    function action_listViewSave ()
    {
    	$GLOBALS [ 'log' ]->info ( "action_listViewSave" ) ;
        //BEGIN SUGARCRM flav=ent ONLY
        if (isset ( $_REQUEST [ 'PORTAL' ] ))
        {
            $this->view = 'portallistview' ;
            $parser = ParserFactory::getParser ( $this->view, $_REQUEST [ 'view_module' ] ) ;
            $parser->init ( $_REQUEST [ 'view_module' ] ) ;
        } else
        {
            //END SUGARCRM flav=ent ONLY

        $packageName = (isset ( $_REQUEST [ 'view_package' ] ) && (strtolower($_REQUEST['view_package']) != 'studio')) ? $_REQUEST [ 'view_package' ] : null ;
        $subpanelName = (! empty ( $_REQUEST [ 'subpanel' ] )) ? $_REQUEST [ 'subpanel' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], $packageName, $subpanelName ) ;
        $this->view = 'listView' ;
            //BEGIN SUGARCRM flav=ent ONLY
        }
        //END SUGARCRM flav=ent ONLY
        $parser->handleSave () ;

    }

    function action_dashletSave () {
        $this->view = 'dashlet' ;
        $packageName = (isset ( $_REQUEST [ 'view_package' ] ) && (strtolower($_REQUEST['view_package']) != 'studio')) ? $_REQUEST [ 'view_package' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], $packageName ) ;
        $parser->handleSave () ;
    }

	function action_popupSave(){
		$this->view = 'popupview' ;
        $packageName = (isset ( $_REQUEST [ 'view_package' ] ) && (strtolower($_REQUEST['view_package']) != 'studio')) ? $_REQUEST [ 'view_package' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], $packageName ) ;
        $parser->handleSave () ;
        if(empty($packageName)){
        	include_once ('modules/Administration/QuickRepairAndRebuild.php') ;
			global $mod_strings;
	        $mod_strings['LBL_ALL_MODULES'] = 'all_modules';
	        $repair = new RepairAndClear();
			$repair->show_output = false;
			$class_name = $GLOBALS [ 'beanList' ] [ $_REQUEST [ 'view_module' ] ] ;
			$repair->module_list = array($class_name);
			$repair->clearTpls();
        }

	}

    function action_searchViewSave ()
    {
        $packageName = (isset ( $_REQUEST [ 'view_package' ] )) ? $_REQUEST [ 'view_package' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/views/SearchViewMetaDataParser.php' ;
        $parser = new SearchViewMetaDataParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], $packageName ) ;
        $parser->handleSave () ;
        $this->view = 'searchView' ;
    }

    function action_editLabels ()
    {
        if (isset ( $_REQUEST [ 'view_package' ] )) //MODULE BUILDER
        {
            $this->view = 'modulelabels';
        }else{ //STUDIO
            $this->view = 'labels';
        }
    }

    function action_get_app_list_string ()
    {
        require_once ('include/JSON.php') ;
        $json = new JSON ( ) ;
        if (isset ( $_REQUEST [ 'key' ] ) && ! empty ( $_REQUEST [ 'key' ] ))
        {
            $key = $_REQUEST [ 'key' ] ;
            $value = array ( ) ;
            if (! empty ( $GLOBALS [ 'app_list_strings' ] [ $key ] ))
            {
                $value = $GLOBALS [ 'app_list_strings' ] [ $key ] ;
            } else
            {
                $package_strings = array ( ) ;
                if (! empty ( $_REQUEST [ 'view_package' ] ) && $_REQUEST [ 'view_package' ] != 'studio' && ! empty ( $_REQUEST [ 'view_module' ] ))
                {
                    require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php') ;
                    $mb = new ModuleBuilder ( ) ;
                    $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
                    $lang = $GLOBALS [ 'current_language' ] ;
                    $module->mblanguage->generateAppStrings ( false ) ;
                    $package_strings = $module->mblanguage->appListStrings [ $lang . '.lang.php' ] ;
                    if (isset ( $package_strings [ $key ] ) && is_array ( $package_strings [ $key ] ))
                    {
                        $value = $package_strings [ $key ] ;
                    }
                }
            }
            echo $json->encode ( $value ) ;
        }
    }

    function action_history ()
    {
        $this->view = 'history' ;
    }

    function resetmodule()
    {
    	$this->view = 'resetmodule';
    }

    //BEGIN SUGARCRM flav=ent ONLY
    function action_editPortal ()
    {
        switch ( strtolower ( $_REQUEST [ 'view' ] ))
        {
            case 'editview' :
            case 'detailview' :
                $this->view = 'portallayoutView' ;
                break ;
            case 'listview' :
                $this->view = 'portallistView' ;
                break ;
            case 'searchview' :
                $this->view = 'portalsearchView' ;
                break ;
            default :
                $GLOBALS [ 'log' ]->fatal ( 'Action = editPortal with unknown view=' . $_REQUEST [ 'view' ] ) ;
        }
    }

    function action_portalstylesave ()
    {
        if (strcmp ( strtolower ( $_FILES [ 'filename' ] [ 'type' ] ), 'text/css' ) == 0)
        {
            mkdir_recursive ( 'custom/portal/custom' ) ;
            move_uploaded_file ( $_FILES [ 'filename' ] [ 'tmp_name' ], 'custom/portal/custom/style.css' ) ;
            $_REQUEST [ 'label' ] = 'LBL_SP_UPLOADED' ;
            $GLOBALS [ 'log' ]->debug ( 'action_portalstylesave(): saving file ' . $_FILES [ 'filename' ] [ 'name' ] ) ;
        } else
        {
            $_REQUEST [ 'label' ] = 'ERROR_SP_UPLOADED' ;
            $GLOBALS [ 'log' ]->error ( 'action_portalstylesave(): error uploading ' . $_FILES [ 'filename' ] [ 'tmp_name' ] ) ;
        }

        @unlink ( $_FILES [ 'filename' ] [ 'tmp_name' ] ) ;
        $this->view = 'portalstyleupload' ;
    }

    function action_portalsyncsync ()
    {
        $option = str_replace ( array ( 'soap.php' , 'index.php' , 'portal_sync.php' ), '', $_REQUEST [ 'portalURL' ] ) ;
        $GLOBALS [ 'system_config' ]->saveSetting ( 'system', 'portal_url', $option ) ;
        $GLOBALS [ 'system_config' ]->settings [ 'system_portal_url' ] = $option ;
        $this->view = 'portalsync' ;
    }
    //END SUGARCRM flav=ent ONLY


}
?>
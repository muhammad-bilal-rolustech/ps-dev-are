<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('include/MetaDataManager/MetaDataManager.php');

class AdministrationController extends SugarController
{
    public function action_savetabs()
    {
        require_once('include/SubPanel/SubPanelDefinitions.php');
        require_once('modules/MySettings/TabController.php');


        global $current_user, $app_strings, $modInvisList;

        if (!is_admin($current_user)) {
            sugar_die($app_strings['ERR_NOT_ADMIN']);
        }

        // handle the tabs listing
        $toDecode = html_entity_decode($_REQUEST['enabled_tabs'], ENT_QUOTES);
        $enabled_tabs = json_decode($toDecode);
        // Add Home back in so that it always appears first in Sugar 7
        array_unshift($enabled_tabs, 'Home');
        $tabs = new TabController();
        $tabs->set_system_tabs($enabled_tabs);
        $tabs->set_users_can_edit(isset($_REQUEST['user_edit_tabs']) && $_REQUEST['user_edit_tabs'] == 1);

        // handle the subpanels
        if (isset($_REQUEST['disabled_tabs'])) {
            $disabledTabs = json_decode(html_entity_decode($_REQUEST['disabled_tabs'], ENT_QUOTES));
            $disabledTabsKeyArray = TabController::get_key_array($disabledTabs);
            //Never show Project subpanels if Project module is hidden
            if (!in_array('project', $disabledTabsKeyArray) && in_array('Project', $modInvisList)) {
                $disabledTabsKeyArray[] = 'project';
            }
            SubPanelDefinitions::set_hidden_subpanels($disabledTabsKeyArray);
        }
        
        // BR-29 When changing module tabs the megamenu is not updated on the client
        MetaDataManager::refreshCache(array('base'));

        if (!headers_sent()) {
            header("Location: index.php?module=Administration&action=ConfigureTabs");
        }
    }

    public function action_savelanguages()
    {
        global $sugar_config;
        $toDecode = html_entity_decode  ($_REQUEST['disabled_langs'], ENT_QUOTES);
        $disabled_langs = json_decode($toDecode);
        $toDecode = html_entity_decode  ($_REQUEST['enabled_langs'], ENT_QUOTES);
        $enabled_langs = json_decode($toDecode);

        if (count($sugar_config['languages']) === count($disabled_langs)) {
            sugar_die(translate('LBL_CAN_NOT_DISABLE_ALL_LANG'));
        } else {
            $cfg = new Configurator();
            if (in_array($sugar_config['default_language'], $disabled_langs)) {
                reset($enabled_langs);
                $cfg->config['default_language'] = current($enabled_langs);
            }
            if (in_array($GLOBALS['current_user']->preferred_language, $disabled_langs)) {
                $GLOBALS['current_user']->preferred_language = current($enabled_langs);
                $GLOBALS['current_user']->save();
            }
            $cfg->config['disabled_languages'] = join(',', $disabled_langs);
            // TODO: find way to enforce order
            $cfg->handleOverride();

            // Clear the metadata cache so changes to languages are picked up right away
            MetaDataManager::refreshLanguagesCache($enabled_langs);
        }

        //Call Ping API to refresh the language list.
        die("
            <script>
            var app = window.parent.SUGAR.App;
            app.api.call('read', app.api.buildURL('ping'));
            app.router.navigate('#bwc/index.php?module=Administration&action=Languages', {trigger:true, replace:true});
            </script>"
        );
    }

    //BEGIN SUGARCRM flav=pro ONLY
    public function action_updatewirelessenabledmodules()
    {
        require_once('modules/Administration/Forms.php');

        global $app_strings, $current_user, $moduleList;

        if (!is_admin($current_user)) sugar_die($app_strings['ERR_NOT_ADMIN']);

        require_once('modules/Configurator/Configurator.php');
        $configurator = new Configurator();
        $configurator->saveConfig();

        if ( isset( $_REQUEST['enabled_modules'] ) && ! empty ($_REQUEST['enabled_modules'] ))
        {
            $updated_enabled_modules = array () ;
            foreach ( explode (',', $_REQUEST['enabled_modules'] ) as $e )
            {
                $updated_enabled_modules [ $e ] = array () ;
            }

            // transfer across any pre-existing definitions for the enabled modules from the current module registry
            if (file_exists('include/MVC/Controller/wireless_module_registry.php'))
            {
                require('include/MVC/Controller/wireless_module_registry.php');
                if ( ! empty ( $wireless_module_registry ) )
                {
                    foreach ( $updated_enabled_modules as $e => $def )
                    {
                        if ( isset ( $wireless_module_registry [ $e ] ) )
                        {
                            $updated_enabled_modules [ $e ] = $wireless_module_registry [ $e ] ;
                        }

                    }
                }
            }

            $filename = create_custom_directory('include/MVC/Controller/wireless_module_registry.php');

            mkdir_recursive ( dirname ( $filename ) ) ;
            write_array_to_file ( 'wireless_module_registry', $updated_enabled_modules, $filename );
            foreach($moduleList as $mod){
                sugar_cache_clear("CONTROLLER_wireless_module_registry_$mod");
            }
            //Users doesn't appear in the normal module list, but its value is cached on login.
            sugar_cache_clear("CONTROLLER_wireless_module_registry_Users");
            sugar_cache_reset();
            
            // Bug 59121 - Clear the metadata cache for the mobile platform
            MetaDataManager::refreshCache(array('mobile'));
        }

        echo "true";
    }

    /**
     * Save the FTS settings for the system and any modules that may be enabled/disabled
     * by the administrator.
     */
    public function action_ScheduleFTSIndex()
    {
        $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';
        $host = !empty($_REQUEST['host']) ? $_REQUEST['host'] : '';
        $port = !empty($_REQUEST['port']) ? $_REQUEST['port'] : '';
        $clearData = !empty($_REQUEST['clearData']) ? true : false;
        $modules = !empty($_REQUEST['modules']) ? explode(",", $_REQUEST['modules']) : array();
        $scheduleIndex = !empty($_REQUEST['sched']) ? true : false;

        // merge current config with new parameters
        $ftsConfig = $this->mergeFtsConfig($type, array('host' => $host, 'port' => $port));

        $this->cfg = new Configurator();
        $this->cfg->config['full_text_engine'] = '';
        $this->cfg->saveConfig();
        $this->cfg->config['full_text_engine'] = array($type => $ftsConfig);
        $this->cfg->handleOverride();
        $scheduled = false;
        if($scheduleIndex)
        {
            SugarAutoLoader::requireWithCustom('include/SugarSearchEngine/SugarSearchEngineFullIndexer.php');
            $indexerClass = SugarAutoLoader::customClass('SugarSearchEngineFullIndexer');
            $indexer = new $indexerClass();
            $indexer->initiateFTSIndexer($modules, $clearData);
            $scheduled = true;
        }
        echo json_encode(array('success' => $scheduled));
    }

    public function action_checkFTSConnection()
    {
        $type = !empty($_REQUEST['type']) ? urldecode($_REQUEST['type']) : '';
        $host = !empty($_REQUEST['host']) ? urldecode($_REQUEST['host']) : '';
        $port = !empty($_REQUEST['port']) ? urldecode($_REQUEST['port']) : '';

        if(!empty($type) && !empty($host) && !empty($port))
        {
            $ftsConfig = $this->mergeFtsConfig($type, array('port' => $port, 'host' => $host));
            require_once('include/SugarSearchEngine/SugarSearchEngineFactory.php');
            $searchEngine = SugarSearchEngineFactory::getInstance($type, $ftsConfig);
            $result = $searchEngine->getServerStatus();
            if ($result['valid']) {
                $result['status'] = $GLOBALS['mod_strings']['LBL_FTS_CONN_SUCCESS'];
            } else {
                if (!empty($result['status']['message']) && is_string($result['status']['message'])) {
                    $result['status'] = $result['status']['message'];
                } else {
                    $result['status'] = $GLOBALS['mod_strings']['LBL_FTS_CONN_UNKNOWN_FAILURE'];
                }
            }
            echo json_encode($result);
        }
        else
        {
            echo json_encode(array('valid' => FALSE));
        }
        sugar_cleanup(TRUE);
    }
    //END SUGARCRM flav=pro ONLY

    /**
     * action_saveglobalsearchsettings
     *
     * This method handles saving the selected modules to display in the Global Search Settings.
     * It instantiates an instance of UnifiedSearchAdvanced and then calls the saveGlobalSearchSettings
     * method.
     *
     */
    public function action_saveglobalsearchsettings()
    {
		 global $current_user, $app_strings;

		 if (!is_admin($current_user))
		 {
		     sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
		 }

    	 try
         {
	    	 require_once('modules/Home/UnifiedSearchAdvanced.php');
	    	 $unifiedSearchAdvanced = new UnifiedSearchAdvanced();
	    	 $unifiedSearchAdvanced->saveGlobalSearchSettings();
             //BEGIN SUGARCRM flav=pro ONLY
             //Save FTS Settings
             $type = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';
             $host = !empty($_REQUEST['host']) ? $_REQUEST['host'] : '';
             $port = !empty($_REQUEST['port']) ? $_REQUEST['port'] : '';

             $ftsConfig = $this->mergeFtsConfig($type, array('port' => $port, 'host' => $host));

             $this->cfg = new Configurator();
             $this->cfg->config['full_text_engine'] = '';
             $this->cfg->saveConfig();
             $ftsConnectionValid = TRUE;

             if( !empty($type) )
             {
                 //Check if the connection is valid on save:
                 require_once('include/SugarSearchEngine/SugarSearchEngineFactory.php');
                 $searchEngine = SugarSearchEngineFactory::getInstance($type, $ftsConfig);
                 $result = $searchEngine->getServerStatus();
                 if( !$result['valid'] )
                     $ftsConnectionValid = FALSE;

                 // bug 54274 -- only bother with an override if we have data to place there, empty string breaks Sugar On-Demand!
                 $ftsConfig['valid'] = $ftsConnectionValid;
                 $this->cfg->config['full_text_engine'] = array($type => $ftsConfig);
                 $this->cfg->handleOverride();
             }

             // Refresh the server info & module list sections of the metadata
             MetaDataManager::refreshSectionCache(array(MetaDataManager::MM_SERVERINFO, MetaDataManager::MM_MODULES));

             if(!$ftsConnectionValid)
                 echo $GLOBALS['mod_strings']['LBL_FTS_CONNECTION_INVALID'];
             else
             //END SUGARCRM flav=pro ONLY
	    	    echo "true";
    	 }
         catch (Exception $ex)
         {
    	 	 echo "false";
    	 }
    }

    /**
     *
     * Merge current FTS config with the new passed parameters:
     *
     * We want to merge the current $sugar_config settings with those passed in
     * to be able to add additional parameters which are currently not supported
     * in the UI (i.e. additional curl settings for elastic search for auth)
     *
     * @param array $config
     * @return array
     */
    protected function mergeFtsConfig($type, $newConfig)
    {
        $currentConfig = SugarConfig::getInstance()->get("full_text_engine.{$type}", array());
        return array_merge($currentConfig, $newConfig);
    }
/*
    public function action_UpdateAjaxUI()
    {
        // TODO check if we need to use this to update the bwc widget.
//        require_once('modules/Configurator/Configurator.php');
//        $cfg = new Configurator();
//        $disabled = json_decode(html_entity_decode  ($_REQUEST['disabled_modules'], ENT_QUOTES));
//        $cfg->config['addAjaxBannedModules'] = empty($disabled) ? FALSE : $disabled;
//        $cfg->handleOverride();
//        $this->view = "configureajaxui";
    }
*/

    /*
     * action_callRebuildSprites
     *
     * This method is responsible for actually running the SugarSpriteBuilder class to rebuild the sprites.
     * It is called from the ajax request issued by RebuildSprites.php.
     */
    public function action_callRebuildSprites()
    {
        global $current_user;
        $this->view = 'ajax';
        if(function_exists('imagecreatetruecolor'))
        {
            if(is_admin($current_user))
            {
                require_once('modules/UpgradeWizard/uw_utils.php');
                rebuildSprites(false);
            }
        } else {
            echo $mod_strings['LBL_SPRITES_NOT_SUPPORTED'];
            $GLOBALS['log']->error($mod_strings['LBL_SPRITES_NOT_SUPPORTED']);
        }
    }
}

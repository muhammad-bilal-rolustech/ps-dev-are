<?php
/*********************************************************************************
 * The contents of this file are subject to
 * *******************************************************************************/
/*
 * Created on Mar 21, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once('include/MVC/Controller/ControllerFactory.php');
require_once('include/MVC/View/ViewFactory.php');

class SugarApplication
{
 	var $controller = null;
 	var $headerDisplayed = false;
 	var $default_module = 'Home';
 	var $default_action = 'index';

 	function SugarApplication()
 	{}

 	/**
 	 * Perform execution of the application. This method is called from index2.php
 	 */
	function execute(){
		global $sugar_config;
		if(!empty($sugar_config['default_module']))
			$this->default_module = $sugar_config['default_module'];
		$module = $this->default_module;
		if(!empty($_REQUEST['module']))$module = $_REQUEST['module'];
		insert_charset_header();
		$this->setupPrint();
		$this->controller = ControllerFactory::getController($module);
        // if the entry point is defined to not need auth, then don't authenicate
		if( empty($_REQUEST['entryPoint'])
                || $this->controller->checkEntryPointRequiresAuth($_REQUEST['entryPoint']) ){
            $this->loadUser();
            $this->ACLFilter();
            $this->preProcess();
            $this->controller->preProcess();
            $this->checkHTTPReferer();
        }

        SugarThemeRegistry::buildRegistry();
        $this->loadLanguages();
		//BEGIN SUGARCRM flav=int ONLY
		/*
		//END SUGARCRM flav=int ONLY
		$this->checkDatabaseVersion();
		//BEGIN SUGARCRM flav=int ONLY
		*/
		//END SUGARCRM flav=int ONLY
		$this->loadDisplaySettings();
		$this->loadLicense();
		$this->loadGlobals();
		$this->setupResourceManagement($module);
		$this->controller->execute();
		sugar_cleanup();
	}

	/**
	 * Load the authenticated user. If there is not an authenticated user then redirect to login screen.
	 */
	function loadUser(){
		global $authController, $sugar_config;
		// Double check the server's unique key is in the session.  Make sure this is not an attempt to hijack a session
		$user_unique_key = (isset($_SESSION['unique_key'])) ? $_SESSION['unique_key'] : '';
		$server_unique_key = (isset($sugar_config['unique_key'])) ? $sugar_config['unique_key'] : '';
		$allowed_actions = (!empty($this->controller->allowed_actions)) ? $this->controller->allowed_actions : $allowed_actions = array('Authenticate', 'Login',);

		if(($user_unique_key != $server_unique_key) && (!in_array($this->controller->action, $allowed_actions)) &&
		   (!isset($_SESSION['login_error'])))
		   {
			session_destroy();
			$post_login_nav = '';

			if(!empty($this->controller->module)){
				$post_login_nav .= '&login_module='.$this->controller->module;
			}
			if(!empty($this->controller->action)){
			    if(in_array(strtolower($this->controller->action), array('delete')))
			        $post_login_nav .= '&login_action=DetailView';
			    elseif(in_array(strtolower($this->controller->action), array('save')))
			        $post_login_nav .= '&login_action=EditView';
			    elseif(isset($_REQUEST['massupdate'])|| isset($_GET['massupdate']) || isset($_POST['massupdate']))
			        $post_login_nav .= '&login_action=index';
			    else
				    $post_login_nav .= '&login_action='.$this->controller->action;
			}
			if(!empty($this->controller->record)){
				$post_login_nav .= '&login_record='.$this->controller->record;
			}

			header('Location: index.php?action=Login&module=Users'.$post_login_nav);
			exit ();
		}

		$authController = new AuthenticationController((!empty($GLOBALS['sugar_config']['authenticationClass'])? $GLOBALS['sugar_config']['authenticationClass'] : 'SugarAuthenticate'));
		$GLOBALS['current_user'] = new User();
		if(isset($_SESSION['authenticated_user_id'])){
			// set in modules/Users/Authenticate.php
			if(!$authController->sessionAuthenticate()){
				 // if the object we get back is null for some reason, this will break - like user prefs are corrupted
				$GLOBALS['log']->fatal('User retrieval for ID: ('.$_SESSION['authenticated_user_id'].') does not exist in database or retrieval failed catastrophically.  Calling session_destroy() and sending user to Login page.');
				session_destroy();
				SugarApplication::redirect('index.php?action=Login&module=Users');
				die();
                //BEGIN SUGARCRM flav=pro ONLY
            } else {
                $trackerManager = TrackerManager::getInstance();
                $monitor = $trackerManager->getMonitor('tracker_sessions');
                $active = $monitor->getValue('active');
                if ( $active == 0 && ( !isset($GLOBALS['current_user']->portal_only) || $GLOBALS['current_user']->portal_only != 1) ) {
                    // We are starting a new session
                    $result = $GLOBALS['db']->query("SELECT id FROM ".$monitor->name." WHERE user_id = '".$GLOBALS['db']->quote($GLOBALS['current_user']->id)."' AND active = 1 AND session_id <> '".$GLOBALS['db']->quote($monitor->getValue('session_id'))."' ORDER BY date_end DESC");
                    $activeCount = 0;
                    while ( $row = $GLOBALS['db']->fetchByAssoc($result) ) {
                        $activeCount++;
                        if ( $activeCount > 1 ) {
                            $GLOBALS['db']->query("UPDATE ".$monitor->name." SET active = 0 WHERE id = '".$GLOBALS['db']->quote($row['id'])."'");
                        }
                    }
                }
                //END SUGARCRM flav=pro ONLY
			}//fi
		}elseif(!($this->controller->module == 'Users' && in_array($this->controller->action, $allowed_actions))){
			session_destroy();
			SugarApplication::redirect('index.php?action=Login&module=Users');
			die();
		}
		$GLOBALS['log']->debug('Current user is: '.$GLOBALS['current_user']->user_name);

		//set cookies
		if(isset($_SESSION['authenticated_user_id'])){
			$GLOBALS['log']->debug("setting cookie ck_login_id_20 to ".$_SESSION['authenticated_user_id']);
			self::setCookie('ck_login_id_20', $_SESSION['authenticated_user_id'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_theme'])){
			$GLOBALS['log']->debug("setting cookie ck_login_theme_20 to ".$_SESSION['authenticated_user_theme']);
			self::setCookie('ck_login_theme_20', $_SESSION['authenticated_user_theme'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_theme_color'])){
			$GLOBALS['log']->debug("setting cookie ck_login_theme_color_20 to ".$_SESSION['authenticated_user_theme_color']);
			self::setCookie('ck_login_theme_color_20', $_SESSION['authenticated_user_theme_color'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_theme_font'])){
			$GLOBALS['log']->debug("setting cookie ck_login_theme_font_20 to ".$_SESSION['authenticated_user_theme_font']);
			self::setCookie('ck_login_theme_font_20', $_SESSION['authenticated_user_theme_font'], time() + 86400 * 90);
		}
		if(isset($_SESSION['authenticated_user_language'])){
			$GLOBALS['log']->debug("setting cookie ck_login_language_20 to ".$_SESSION['authenticated_user_language']);
			self::setCookie('ck_login_language_20', $_SESSION['authenticated_user_language'], time() + 86400 * 90);
		}
		//check if user can access

	}

	function ACLFilter(){
		ACLController :: filterModuleList($GLOBALS['moduleList']);
	}

	/**
	 * setupResourceManagement
	 * This function initialize the ResourceManager and calls the setup method
	 * on the ResourceManager instance.
	 *
	 */
	function setupResourceManagement($module) {
		require_once('include/resource/ResourceManager.php');
		$resourceManager = ResourceManager::getInstance();
		$resourceManager->setup($module);
	}

	function setupPrint() {
		$GLOBALS['request_string'] = '';

		// merge _GET and _POST, but keep the results local
		// this handles the issues where values come in one way or the other
		// without affecting the main super globals
		$merged = array_merge($_GET, $_POST);
		foreach ($merged as $key => $val)
		{
		   if(is_array($val))
		   {
		       foreach ($val as $k => $v)
		       {
                           //If an array, then skip the urlencoding. This should be handled with stringify instead.
                           if(is_array($v))
                                continue;

                           $GLOBALS['request_string'] .= urlencode($key).'['.$k.']='.urlencode($v).'&';
		       }
		   }
		   else
		   {
		       $GLOBALS['request_string'] .= urlencode($key).'='.urlencode($val).'&';
		   }
		}
		$GLOBALS['request_string'] .= 'print=true';
	}

	function preProcess(){
		//BEGIN SUGARCRM flav=sales ONLY
		// Create a module whitelist of all modules in Administration
		$ss_admin_whitelist = getSugarSalesAdminWhiteList();
		if(!in_array($this->controller->module, $ss_admin_whitelist['modules'])
		   && !in_array($this->controller->action, $ss_admin_whitelist['actions'])
		   && is_admin($GLOBALS['current_user'])){
			self::redirect("index.php?module=Administration&action=index");
		}
		//END SUGARCRM flav=sales ONLY
	    $config = new Administration;
	    $config->retrieveSettings();
		if(!empty($_SESSION['authenticated_user_id'])){
			if(isset($_SESSION['hasExpiredPassword']) && $_SESSION['hasExpiredPassword'] == '1'){
				if( $this->controller->action!= 'Save' && $this->controller->action != 'Logout') {
	                $this->controller->module = 'Users';
	                $this->controller->action = 'ChangePassword';
	                $record = $GLOBALS['current_user']->id;
	             }else{
					$this->handleOfflineClient();
				 }
			}else{
				$ut = $GLOBALS['current_user']->getPreference('ut');
			    if(empty($ut)
			            && $this->controller->action != 'AdminWizard'
			            && $this->controller->action != 'EmailUIAjax'
			            && $this->controller->action != 'Wizard'
			            && $this->controller->action != 'SaveAdminWizard'
			            && $this->controller->action != 'SaveUserWizard'
			            && $this->controller->action != 'SaveTimezone'
			            && $this->controller->action != 'Logout') {
					$this->controller->module = 'Users';
					$this->controller->action = 'SetTimezone';
					$record = $GLOBALS['current_user']->id;
				}else{
					if($this->controller->action != 'AdminWizard'
			            && $this->controller->action != 'EmailUIAjax'
			            && $this->controller->action != 'Wizard'
			            && $this->controller->action != 'SaveAdminWizard'
			            && $this->controller->action != 'SaveUserWizard'){
							$this->handleOfflineClient();
			            }
				}
			}
		}
		$this->handleAccessControl();
	}

	function handleOfflineClient(){
		if(isset($GLOBALS['sugar_config']['disc_client']) && $GLOBALS['sugar_config']['disc_client']){
			if(isset($_REQUEST['action']) && $_REQUEST['action'] != 'SaveTimezone'){
				if (!file_exists('modules/Sync/file_config.php')){
					if($_REQUEST['action'] != 'InitialSync' && $_REQUEST['action'] != 'Logout' &&
					   ($_REQUEST['action'] != 'Popup' && $_REQUEST['module'] != 'Sync')){
						//echo $_REQUEST['action'];
						//die();
					   		$this->controller->module = 'Sync';
							$this->controller->action = 'InitialSync';
						}
		    	}else{
		    		require_once ('modules/Sync/file_config.php');
		    		if(isset($file_sync_info['is_first_sync']) && $file_sync_info['is_first_sync']){
		    			if($_REQUEST['action'] != 'InitialSync' && $_REQUEST['action'] != 'Logout' &&
		    			   ( $_REQUEST['action'] != 'Popup' && $_REQUEST['module'] != 'Sync')){
								$this->controller->module = 'Sync';
								$this->controller->action = 'InitialSync';
						}
		    		}
		    	}
			}
			global $moduleList, $sugar_config, $sync_modules;
			require_once('modules/Sync/SyncController.php');
			$GLOBALS['current_user']->is_admin = '0'; //No admins for disc client
		}
	}

	/**
	 * Handles everything related to authorization.
	 */
	function handleAccessControl(){
		if(is_admin($GLOBALS['current_user']) || is_admin_for_any_module($GLOBALS['current_user']))
			return;
	    if(!empty($_REQUEST['action']) && $_REQUEST['action']=="RetrieveEmail")
            return;
		if(!is_admin($GLOBALS['current_user']) && !empty($GLOBALS['adminOnlyList'][$this->controller->module])
		&& !empty($GLOBALS['adminOnlyList'][$this->controller->module]['all'])
		&& (empty($GLOBALS['adminOnlyList'][$this->controller->module][$this->controller->action]) || $GLOBALS['adminOnlyList'][$this->controller->module][$this->controller->action] != 'allow')) {
			$this->controller->hasAccess = false;
			return;
		}

		// Bug 20916 - Special case for check ACL access rights for Subpanel QuickCreates
		if(isset($_POST['action']) && $_POST['action'] == 'SubpanelCreates') {
            $actual_module = $_POST['target_module'];
            if(!empty($GLOBALS['modListHeader']) && !in_array($actual_module,$GLOBALS['modListHeader'])) {
                $this->controller->hasAccess = false;
            }
            return;
        }


		if(!empty($GLOBALS['current_user']) && empty($GLOBALS['modListHeader']))
			$GLOBALS['modListHeader'] = query_module_access_list($GLOBALS['current_user']);

		if(in_array($this->controller->module, $GLOBALS['modInvisList']) &&
			((in_array('Activities', $GLOBALS['moduleList'])              &&
			in_array('Calendar',$GLOBALS['moduleList']))                 &&
			in_array($this->controller->module, $GLOBALS['modInvisListActivities']))
			){
				$this->controller->hasAccess = false;
				return;
		}
	}

	/**
	 * Load only bare minimum of language that can be done before user init and MVC stuff
	 */
	static function preLoadLanguages()
	{
		if(!empty($_SESSION['authenticated_user_language'])) {
			$GLOBALS['current_language'] = $_SESSION['authenticated_user_language'];
		}
		else {
			$GLOBALS['current_language'] = $GLOBALS['sugar_config']['default_language'];
		}
		$GLOBALS['log']->debug('current_language is: '.$GLOBALS['current_language']);
		//set module and application string arrays based upon selected language
		$GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
	}

	/**
	 * Load application wide languages as well as module based languages so they are accessible
	 * from the module.
	 */
	function loadLanguages(){
		if(!empty($_SESSION['authenticated_user_language'])) {
			$GLOBALS['current_language'] = $_SESSION['authenticated_user_language'];
		}
		else {
			$GLOBALS['current_language'] = $GLOBALS['sugar_config']['default_language'];
		}
		$GLOBALS['log']->debug('current_language is: '.$GLOBALS['current_language']);
		//set module and application string arrays based upon selected language
		$GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
		if(empty($GLOBALS['current_user']->id))$GLOBALS['app_strings']['NTC_WELCOME'] = '';
		if(!empty($GLOBALS['system_config']->settings['system_name']))$GLOBALS['app_strings']['LBL_BROWSER_TITLE'] = $GLOBALS['system_config']->settings['system_name'];
		$GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
		$GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], $this->controller->module);
	}
	//BEGIN SUGARCRM flav=sugarmdle ONLY
	/**
     * Retrieves the applications language file and returns the array of strings included.
     *
     * @param  string $language optional, defaults to the current application language
     * @return array
     */
    public static function getLanguageStrings(
	    $language = null
	    )
	{
	    global $app_strings, $sugar_config;

        $cache_key = 'app_strings.'.$language;

        // Check for cached value
        $cache_entry = sugar_cache_retrieve($cache_key);
        if(!empty($cache_entry))
        {
            return $cache_entry;
        }

        $temp_app_strings = $app_strings;
        if(empty($language))
            $language = $GLOBALS['current_language'];
        $language_used = $language;
        $default_language = $sugar_config['default_language'];

        // cn: bug 6048 - merge en_us with requested language
        include("include/language/en_us.lang.php");
        if(file_exists("custom/include/language/en_us.lang.php")) {
            include("custom/include/language/en_us.lang.php");
        }
        $en_app_strings = array();
        if($language_used != $default_language)
        $en_app_strings = $app_strings;

        if(!empty($language)) {
            include("include/language/$language.lang.php");
        }

        if(file_exists("include/language/$language.lang.override.php")) {
            include("include/language/$language.lang.override.php");
        }
        if(file_exists("include/language/$language.lang.php.override")) {
            include("include/language/$language.lang.php.override");
        }
        if(file_exists("custom/application/Ext/Language/$language.lang.ext.php")) {
            include("custom/application/Ext/Language/$language.lang.ext.php");
            $GLOBALS['log']->info("Found extended language file: $language.lang.ext.php");
        }
        if(file_exists("custom/include/language/$language.lang.php")) {
            include("custom/include/language/$language.lang.php");
            $GLOBALS['log']->info("Found custom language file: $language.lang.php");
        }


        if(!isset($app_strings)) {
            $GLOBALS['log']->warn("Unable to find the application language file for language: ".$language);
            require("include/language/$default_language.lang.php");
            if(file_exists("include/language/$default_language.lang.override.php")) {
                include("include/language/$default_language.lang.override.php");
            }
            if(file_exists("include/language/$default_language.lang.php.override")) {
                include("include/language/$default_language.lang.php.override");
            }

            if(file_exists("custom/application/Ext/Language/$default_language.lang.ext.php")) {
                include("custom/application/Ext/Language/$default_language.lang.ext.php");
                $GLOBALS['log']->info("Found extended language file: $default_language.lang.ext.php");
            }
            $language_used = $default_language;
        }

        if(!isset($app_strings)) {
            $GLOBALS['log']->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
            return null;
        }

        // cn: bug 6048 - merge en_us with requested language
        $app_strings = sugarArrayMerge($en_app_strings, $app_strings);

        // If we are in debug mode for translating, turn on the prefix now!
        if($sugar_config['translation_string_prefix']) {
            foreach($app_strings as $entry_key=>$entry_value) {
                $app_strings[$entry_key] = $language_used.' '.$entry_value;
            }
        }
        if(isset($_SESSION['show_deleted'])) {
            $app_strings['LBL_DELETE_BUTTON'] = $app_strings['LBL_UNDELETE_BUTTON'];
            $app_strings['LBL_DELETE_BUTTON_LABEL'] = $app_strings['LBL_UNDELETE_BUTTON_LABEL'];
            $app_strings['LBL_DELETE_BUTTON_TITLE'] = $app_strings['LBL_UNDELETE_BUTTON_TITLE'];
            $app_strings['LBL_DELETE'] = $app_strings['LBL_UNDELETE'];
        }

        $app_strings['LBL_ALT_HOT_KEY'] = get_alt_hot_key();

        $return_value = $app_strings;
        $app_strings = $temp_app_strings;

        sugar_cache_put($cache_key, $return_value);
        return $return_value;
	}

	/**
     * Retrieves the applications language file and returns the array of list strings included.
     *
     * @param  string $language optional, defaults to the current application language
     * @return array
     */
    public static function getLanguageListStrings(
        $language = null
        )
    {
        global $app_list_strings;
        global $sugar_config;

        $cache_key = 'app_list_strings.'.$language;

        // Check for cached value
        $cache_entry = sugar_cache_retrieve($cache_key);
        if(!empty($cache_entry))
        {
            return $cache_entry;
        }

        $default_language = $sugar_config['default_language'];
        $temp_app_list_strings = $app_list_strings;
        if(empty($language))
            $language = $GLOBALS['current_language'];
        $language_used = $language;

        include("include/language/en_us.lang.php");

        $en_app_list_strings = array();
        if($language_used != $default_language){
            require("include/language/$default_language.lang.php");

            if(file_exists("include/language/$default_language.lang.override.php")) {
                include("include/language/$default_language.lang.override.php");
            }

            if(file_exists("include/language/$default_language.lang.php.override")) {
                include("include/language/$default_language.lang.php.override");
            }

            $en_app_list_strings = $app_list_strings;
        }

        if(file_exists("include/language/$language.lang.php")) {
        include("include/language/$language.lang.php");
        }

        if(file_exists("include/language/$language.lang.override.php")) {
            include("include/language/$language.lang.override.php");
        }

        if(file_exists("include/language/$language.lang.php.override")) {
            include("include/language/$language.lang.php.override");
        }

        // cn: bug 6048 - merge en_us with requested language
        if (!empty($en_app_list_strings)) {
            $app_list_strings = sugarArrayMerge($en_app_list_strings, $app_list_strings);
        }

        if (file_exists("custom/application/Ext/Language/en_us.lang.ext.php")){
            $app_list_strings =  self::_mergeCustomAppListStrings("custom/application/Ext/Language/en_us.lang.ext.php" , $app_list_strings) ;
       }

       if($language_used != $default_language){
             if(file_exists("custom/application/Ext/Language/$default_language.lang.ext.php")) {
                $app_list_strings =  self::_mergeCustomAppListStrings("custom/application/Ext/Language/$default_language.lang.ext.php" , $app_list_strings);
                $GLOBALS['log']->info("Found extended language file: $default_language.lang.ext.php");
            }
            if(file_exists("custom/include/language/$default_language.lang.php")) {
                include("custom/include/language/$default_language.lang.php");
                $GLOBALS['log']->info("Found custom language file: $default_language.lang.php");
            }
        }

        if(file_exists("custom/application/Ext/Language/$language.lang.ext.php")) {
            $app_list_strings = self::_mergeCustomAppListStrings("custom/application/Ext/Language/$language.lang.ext.php" , $app_list_strings);
           $GLOBALS['log']->info("Found extended language file: $language.lang.ext.php");
        }

        if(file_exists("custom/include/language/$language.lang.php")) {
            include("custom/include/language/$language.lang.php");
            $GLOBALS['log']->info("Found custom language file: $language.lang.php");
        }

        if(!isset($app_list_strings)) {
            $GLOBALS['log']->warn("Unable to find the application language file for language: ".$language);
            $language_used = $default_language;
            $app_list_strings = $en_app_list_strings;
        }

        if(!isset($app_list_strings)) {
            $GLOBALS['log']->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
            return null;
        }

        $return_value = $app_list_strings;
        $app_list_strings = $temp_app_list_strings;

        sugar_cache_put($cache_key, $return_value);

        return $return_value;
    }

    /**
     * The dropdown items in custom language files is $app_list_strings['$key']['$second_key'] = $value not
     * $GLOBALS['app_list_strings']['$key'] = $value, so we have to delete the original ones in app_list_strings and relace it with the custom ones.
     *
     * @param file string the language that you want include,
     * @param app_list_strings array the golbal strings
     * @return array
     */
    private static function _mergeCustomAppListStrings(
        $file ,
        $app_list_strings
        )
    {
        $app_list_strings_original = $app_list_strings;
        unset($app_list_strings);
        include($file);
        if(!isset($app_list_strings) || !is_array($app_list_strings)){
            return $app_list_strings_original;
        }
        //Bug 25347: We should not merge custom dropdown fields unless they relate to parent fields or the module list.
        foreach($app_list_strings as $key=>$value)
        {
            $exemptDropdowns = array("moduleList", "parent_type_display", "record_type_display", "record_type_display_notes");
            if (!in_array($key, $exemptDropdowns) && array_key_exists($key, $app_list_strings_original))
            {
                unset($app_list_strings_original["$key"]);
            }
       }
       $app_list_strings = sugarArrayMergeRecursive($app_list_strings_original , $app_list_strings);
       return $app_list_strings;
    }
	//END SUGARCRM flav=sugarmdle ONLY
	/**
	* checkDatabaseVersion
	* Check the db version sugar_version.php and compare to what the version is stored in the config table.
	* Ensure that both are the same.
	*/
 	function checkDatabaseVersion($dieOnFailure = true)
 	{
 	    $row_count = sugar_cache_retrieve('checkDatabaseVersion_row_count');
 	    if ( empty($row_count) ) {
            global $sugar_db_version;
            $version_query = 'SELECT count(*) as the_count FROM config WHERE category=\'info\' AND name=\'sugar_version\'';

            if($GLOBALS['db']->dbType == 'oci8'){
                //BEGIN SUGARCRM flav=ent ONLY
                $version_query .= " AND to_char(value) = '$sugar_db_version'";
                //END SUGARCRM flav=ent ONLY
            }
            else if ($GLOBALS['db']->dbType == 'mssql'){
                $version_query .= " AND CAST(value AS varchar(8000)) = '$sugar_db_version'";
            }
            else {
                $version_query .= " AND value = '$sugar_db_version'";
            }

            $result = $GLOBALS['db']->query($version_query);
            $row = $GLOBALS['db']->fetchByAssoc($result, -1, true);
            $row_count = $row['the_count'];
            sugar_cache_put('checkDatabaseVersion_row_count', $row_count);
        }

		if($row_count == 0 && empty($GLOBALS['sugar_config']['disc_client'])){
			$sugar_version = $GLOBALS['sugar_version'];
			if ( $dieOnFailure )
				sugar_die("Sugar CRM $sugar_version Files May Only Be Used With A Sugar CRM $sugar_db_version Database.");
			else
			    return false;
		}

		return true;
	}

	/**
	 * Load the themes/images.
	 */
	function loadDisplaySettings()
    {
        global $theme;

        // load the user's default theme
        $theme = $GLOBALS['current_user']->getPreference('user_theme');

        if (is_null($theme)) {
            $theme = $GLOBALS['sugar_config']['default_theme'];
            if(!empty($_SESSION['authenticated_user_theme'])){
                $theme = $_SESSION['authenticated_user_theme'];
            }
            else if(!empty($_COOKIE['sugar_user_theme'])){
                $theme = $_COOKIE['sugar_user_theme'];
            }

			if(isset($_SESSION['authenticated_user_theme']) && $_SESSION['authenticated_user_theme'] != '') {
				$_SESSION['theme_changed'] = false;
			}
		}

        if(!is_null($theme) && !headers_sent())
        {
            setcookie('sugar_user_theme', $theme, time() + 31536000); // expires in a year
        }

        SugarThemeRegistry::set($theme);
        require_once('include/utils/layout_utils.php');
        $GLOBALS['image_path'] = SugarThemeRegistry::current()->getImagePath().'/';
        if ( defined('TEMPLATE_URL') )
            $GLOBALS['image_path'] = TEMPLATE_URL . '/'. $GLOBALS['image_path'];

        if ( isset($GLOBALS['current_user']) ) {
            $GLOBALS['gridline'] = (int) ($GLOBALS['current_user']->getPreference('gridline') == 'on');
            $GLOBALS['current_user']->setPreference('user_theme', $theme, 0, 'global');
        }
	}

	function loadLicense(){
		loadLicense();
		global $user_unique_key, $server_unique_key;
		$user_unique_key = (isset($_SESSION['unique_key'])) ? $_SESSION['unique_key'] : '';
		$server_unique_key = (isset($sugar_config['unique_key'])) ? $sugar_config['unique_key'] : '';
	}

	function loadGlobals(){
		global $currentModule;
		$currentModule = $this->controller->module;
		if($this->controller->module == $this->default_module){
			$_REQUEST['module'] = $this->controller->module;
			if(empty($_REQUEST['action']))
			$_REQUEST['action'] = $this->default_action;
		}
	}

	/**
	 * Actions that modify data in this controller's instance and thus require referrers
	 * @var array
	 */
	protected $modifyActions = array();
	/**
	 * Actions that always modify data and thus require referrers
	 * save* and delete* hardcoded as modified
	 * @var array
	 */
	private $globalModifyActions = array(
		'massupdate', 'configuredashlet', 'import', 'importvcardsave', 'inlinefieldsave',
	    'wlsave', 'quicksave'
	);

	/**
	 * Modules that modify data and thus require referrers for all actions
	 */
	private $modifyModules = array(
		'Administration' => true,
		'UpgradeWizard' => true,
		'Configurator' => true,
		'Studio' => true,
		'ModuleBuilder' => true,
		'Emails' => true,
	    'DCETemplates' => true,
		'DCEInstances' => true,
		'DCEActions' => true,
		'Trackers' => array('trackersettings'),
	    'SugarFavorites' => array('tag'),
	    'Import' => array('last', 'undo'),
	);

	protected function isModifyAction()
	{
	    $action = strtolower($this->controller->action);
	    if(substr($action, 0, 4) == "save" || substr($action, 0, 6) == "delete") {
	        return true;
	    }
	    if(isset($this->modifyModules[$this->controller->module])) {
	        if($this->modifyModules[$this->controller->module] == true) {
	            return true;
	        }
	        if(in_array($this->controller->action, $this->modifyModules[$this->controller->module])) {
	            return true;

	        }
	    }
	    if(in_array($this->controller->action, $this->globalModifyActions)) {
            return true;
        }
	    if(in_array($this->controller->action, $this->modifyActions)) {
            return true;
        }
        return false;
	}

	/**
	 *
	 * Checks a request to ensure the request is coming from a valid source or it is for one of the white listed actions
	 */
	protected function checkHTTPReferer($dieIfInvalid = true)
	{
		global $sugar_config;
		$whiteListActions = (!empty($sugar_config['http_referer']['actions']))?$sugar_config['http_referer']['actions']:array('index', 'ListView', 'DetailView', 'EditView','oauth', 'Authenticate', 'Login');

		$strong = empty($sugar_config['http_referer']['weak']);

		// Bug 39691 - Make sure localhost and 127.0.0.1 are always valid HTTP referers
		$whiteListReferers = array('127.0.0.1','localhost');
		if(!empty($_SERVER['SERVER_ADDR']))$whiteListReferers[]  = $_SERVER['SERVER_ADDR'];
		if ( !empty($sugar_config['http_referer']['list']) ) {
			$whiteListReferers = array_merge($whiteListReferers,$sugar_config['http_referer']['list']);
		}

		if($strong && empty($_SERVER['HTTP_REFERER']) && !in_array($this->controller->action, $whiteListActions) && $this->isModifyAction()) {
		    $http_host = explode(':', $_SERVER['HTTP_HOST']);

			$whiteListActions[] = $this->controller->action;
			$whiteListString = "'" . implode("', '", $whiteListActions) . "'";
            if ( $dieIfInvalid ) {
                header("Cache-Control: no-cache, must-revalidate");
                $ss = new Sugar_Smarty;
                $ss->assign('host', $http_host[0]);
                $ss->assign('action',$this->controller->action);
                $ss->assign('whiteListString',$whiteListString);
                $ss->display('include/MVC/View/tpls/xsrf.tpl');
                sugar_cleanup(true);
            }
            return false;
		} else
		if(!empty($_SERVER['HTTP_REFERER']) && !empty($_SERVER['SERVER_NAME'])){
			$http_ref = parse_url($_SERVER['HTTP_REFERER']);
			if($http_ref['host'] !== $_SERVER['SERVER_NAME']  && !in_array($this->controller->action, $whiteListActions) &&

				(empty($whiteListReferers) || !in_array($http_ref['host'], $whiteListReferers))){
                if ( $dieIfInvalid ) {
                    header("Cache-Control: no-cache, must-revalidate");
                    $whiteListActions[] = $this->controller->action;
                    $whiteListString = "'" . implode("', '", $whiteListActions) . "'";

                    $ss = new Sugar_Smarty;
                    $ss->assign('host',$http_ref['host']);
                    $ss->assign('action',$this->controller->action);
                    $ss->assign('whiteListString',$whiteListString);
                    $ss->display('include/MVC/View/tpls/xsrf.tpl');
                    sugar_cleanup(true);
                }
                return false;
			}
		}
         return true;
	}
	function startSession()
	{
	    $sessionIdCookie = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : null;
	    if(isset($_REQUEST['MSID'])) {
			session_id($_REQUEST['MSID']);
			session_start();
			if(isset($_SESSION['user_id']) && isset($_SESSION['seamless_login'])){
				unset ($_SESSION['seamless_login']);
			}else{
				if(isset($_COOKIE['PHPSESSID'])){
	       			self::setCookie('PHPSESSID', '', time()-42000, '/');
        		}
	    		sugar_cleanup(false);
	    		session_destroy();
	    		exit('Not a valid entry method');
			}
		}else{
			if(can_start_session()){
				session_start();
			}
		}

		if ( isset($_REQUEST['login_module']) && isset($_REQUEST['login_action'])
		        && !($_REQUEST['login_module'] == 'Home' && $_REQUEST['login_action'] == 'index') ) {
            if ( !is_null($sessionIdCookie) && empty($_SESSION) ) {
                self::setCookie('loginErrorMessage', 'LBL_SESSION_EXPIRED', time()+30, '/');
            }
        }

		//BEGIN SUGARCRM flav=pro ONLY

	    $trackerManager = TrackerManager::getInstance();
	    if($monitor = $trackerManager->getMonitor('tracker_sessions')){
		    $db = DBManagerFactory::getInstance();
		    $session_id = $monitor->getValue('session_id');
	        $query = "SELECT date_start, round_trips, active FROM $monitor->name WHERE session_id = '".$db->quote($session_id)."'";
	        $result = $db->query($query);

			if(isset($_SERVER['REMOTE_ADDR'])) {
	           $monitor->setValue('client_ip', $_SERVER['REMOTE_ADDR']);
			}

		    if(($row = $db->fetchByAssoc($result))) {
                if ( $row['active'] != 1 && !empty($_SESSION['authenticated_user_id']) ) {
                    $GLOBALS['log']->error('User ID: ('.$_SESSION['authenticated_user_id'].') has too many active sessions. Calling session_destroy() and sending user to Login page.');
                    session_destroy();
                    $msg_name = 'TO'.'O_MANY_'.'CONCUR'.'RENT';
                    SugarApplication::redirect('index.php?action=Login&module=Users&loginErrorMessage=LBL_'.$msg_name);
                    die();
                }
		    	$monitor->setValue('date_start', $row['date_start']);
		    	$monitor->setValue('round_trips', $row['round_trips'] + 1);
                $monitor->setValue('active', 1);
		    } else {
                // We are creating a new session
                // Don't set the session as active until we have made sure it checks out.
                $monitor->setValue('active', 0);
				$monitor->setValue('date_start', TimeDate::getInstance()->nowDb());
		        $monitor->setValue('round_trips', 1);
		    }
        }
	    //END SUGARCRM flav=pro ONLY
	}

	function endSession(){
		//BEGIN SUGARCRM flav=pro ONLY

	    $trackerManager = TrackerManager::getInstance();
		if($monitor = $trackerManager->getMonitor('tracker_sessions')){
			$monitor->setValue('date_end', TimeDate::getInstance()->nowDb());
			$seconds = strtotime($monitor->date_end) - strtotime($monitor->date_start);
			$monitor->setValue('seconds', $seconds);
			$monitor->setValue('active', 0);
		}
		//END SUGARCRM flav=pro ONLY
		session_destroy();
	}
 	/**
	 * Redirect to another URL
	 *
	 * @access	public
	 * @param	string	$url	The URL to redirect to
	 */
 	function redirect(
 	    $url
 	    )
	{
		/*
		 * If the headers have been sent, then we cannot send an additional location header
		 * so we will output a javascript redirect statement.
		 */
		if (!empty($_REQUEST['ajax_load']))
        {
            ob_get_clean();
            $ajax_ret = array(
                 'content' => "<script>SUGAR.ajaxLoadContent('$url');</script>\n",
                 'menu' => array(
                     'module' => $_REQUEST['module'],
                     'label' => translate($_REQUEST['module']),
                 ),
            );
            $json = getJSONobj();
            echo $json->encode($ajax_ret);
        } else {
            if (headers_sent()) {
                echo "<script>SUGAR.ajaxLoadContent('$url');</script>\n";
            } else {
                //@ob_end_clean(); // clear output buffer
                session_write_close();
                header( 'HTTP/1.1 301 Moved Permanently' );
                header( "Location: ". $url );
            }
        }
		exit();
	}

    /**
	 * Redirect to another URL
	 *
	 * @access	public
	 * @param	string	$url	The URL to redirect to
	 */
 	public static function appendErrorMessage($error_message)
	{
        if (empty($_SESSION['user_error_message']) || !is_array($_SESSION['user_error_message'])){
            $_SESSION['user_error_message'] = array();
        }
		$_SESSION['user_error_message'][] = $error_message;
	}

    public static function getErrorMessages()
	{
		if (isset($_SESSION['user_error_message']) && is_array($_SESSION['user_error_message']) ) {
            $msgs = $_SESSION['user_error_message'];
            unset($_SESSION['user_error_message']);
            return $msgs;
        }else{
            return array();
        }
	}

	/**
	 * Wrapper for the PHP setcookie() function, to handle cases where headers have
	 * already been sent
	 */
	public static function setCookie(
	    $name,
	    $value,
	    $expire = 0,
	    $path = '/',
	    $domain = null,
	    $secure = false,
	    $httponly = false
	    )
	{
	    if ( is_null($domain) )
	        if ( isset($_SERVER["HTTP_HOST"]) )
	            $domain = $_SERVER["HTTP_HOST"];
	        else
	            $domain = 'localhost';

	    if (!headers_sent())
	        setcookie($name,$value,$expire,$path,$domain,$secure,$httponly);

	    $_COOKIE[$name] = $value;
	}
}

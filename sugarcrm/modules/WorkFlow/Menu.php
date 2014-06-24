<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point'); 
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/*********************************************************************************
 * $Id: Menu.php 42645 2008-12-18 21:41:08Z awu $
 * Description:  
 ********************************************************************************/

global $mod_strings;
$module_menu = Array(
	Array("index.php?module=WorkFlow&action=EditView&return_module=WorkFlow&return_action=DetailView", $mod_strings['LNK_NEW_WORKFLOW'],"CreateWorkflowDefinition"),
	Array("index.php?module=WorkFlow&action=index&return_module=WorkFlow&return_action=DetailView", $mod_strings['LNK_WORKFLOW'],"WorkFlow"),
	Array("index.php?module=WorkFlow&action=WorkFlowListView&return_module=WorkFlow&return_action=index", $mod_strings['LNK_ALERT_TEMPLATES'],"AlertEmailTemplates"),
	Array("index.php?module=WorkFlow&action=ProcessListView&return_module=WorkFlow&return_action=index", $mod_strings['LNK_PROCESS_VIEW'],"WorkflowSequence"),

	);

?>

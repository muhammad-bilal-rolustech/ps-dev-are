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
 * $Id: Menu.php 52897 2009-12-01 22:49:29Z mitani $
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $mod_strings;
global $current_user;
$module_menu=Array();

if( empty($_REQUEST['record']) ) { $employee_id = ''; }
else { $employee_id = $_REQUEST['record']; }

if( is_admin($current_user) )
{
$module_menu[] = Array("index.php?module=Employees&action=EditView&return_module=Employees&return_action=DetailView", $mod_strings['LNK_NEW_EMPLOYEE'],"CreateEmployees");
}
	
$module_menu[] = Array("index.php?module=Employees&action=index&return_module=Employees&return_action=DetailView", $mod_strings['LNK_EMPLOYEE_LIST'],"Employees");


?>

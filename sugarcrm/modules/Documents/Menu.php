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
/*********************************************************************************
 * $Id: Menu.php 45763 2009-04-01 19:16:18Z majed $
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $mod_strings;
global $current_user;


if(ACLController::checkAccess('Documents', 'edit', true))$module_menu[]=Array("index.php?module=Documents&action=EditView&return_module=Documents&return_action=DetailView", $mod_strings['LNK_NEW_DOCUMENT'],"CreateDocuments");
if(ACLController::checkAccess('Documents', 'list', true))$module_menu[]=Array("index.php?module=Documents&action=index", $mod_strings['LNK_DOCUMENT_LIST'],"Documents");
if(ACLController::checkAccess('Documents', 'edit', true)){
	
	$admin = Administration::getSettings();
	$user_merge = $current_user->getPreference('mailmerge_on');
	if ($user_merge == 'on' && isset($admin->settings['system_mailmerge_on']) && $admin->settings['system_mailmerge_on']){
		$module_menu[]=Array("index.php?module=MailMerge&action=index&reset=true", $mod_strings['LNK_NEW_MAIL_MERGE'],"Documents");
	}
}
?>

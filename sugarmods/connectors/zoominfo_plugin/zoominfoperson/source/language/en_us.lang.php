<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-professional-eula.html
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
 * by SugarCRM are Copyright (C) 2004-2008 SugarCRM, Inc.; All Rights Reserved.
********************************************************************************/
/*********************************************************************************

* Description:  Defines the English language pack for the base application.
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
* All Rights Reserved.
* Contributor(s): ______________________________________..
********************************************************************************/

$connector_strings = array (
    //licensing information shown in config screen
    'LBL_LICENSING_INFO' => '<table border="0" cellspacing="1"><tr><td valign="top" width="35%" class="dataLabel"><image src="' . getWebPath('custom/modules/Connectors/connectors/sources/ext/rest/zoominfoperson/images/zoominfo.gif') . '" border="0"></td><td width="65%" valign="top" class="dataLabel">' .
                            'ZoomInfo&#169; provides deep information on over 45 million business people at over 5 million companies.  Learn more.  <a target="_blank" href="http://www.zoominfo.com/about">http://www.zoominfo.com/about</a></td></tr></table>',    
    
    'LBL_SEARCH_FIELDS_INFO' => 'The following fields are supported by the Zoominfo&#169 Person; API: First Name, Last Name and Email Address.',    
    
    //vardef labels
	'LBL_ID' => 'ID',
	'LBL_EMAIL' => 'Email Address',
	'LBL_FIRST_NAME' => 'First Name',
	'LBL_LAST_NAME' => 'Last Name',
	'LBL_JOB_TITLE' => 'Job Title',
	'LBL_IMAGE_URL' => 'Image URL',
	'LBL_SUMMARY_URL' => 'Summary URL',
	'LBL_COMPANY_NAME' => 'Company Name',
	
	//Configuration labels
	'url' => 'URL',
	'api_key' => 'API Key',
	
	//Other labels
	'ERROR_LBL_CONNECTION_PROBLEM' => 'Error: Unable to connect to the server for Zoominfo - Person connector.',
);

?>

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
 * $Id: en_us.lang.php 56510 2010-05-17 18:54:49Z jenny $
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = array (
  'LBL_MODULE_NAME' => 'Bugs',
  'LBL_MODULE_NAME_SINGULAR'	=> 'Bug',
  'LBL_MODULE_TITLE' => 'Bug Tracker: Home',
  'LBL_MODULE_ID' => 'Bugs',
  'LBL_SEARCH_FORM_TITLE' => 'Bug Search',
  'LBL_LIST_FORM_TITLE' => 'Bug List',
  'LBL_NEW_FORM_TITLE' => 'New Bug',
  'LBL_CONTACT_BUG_TITLE' => 'Contact-Bug:',
  'LBL_SUBJECT' => 'Subject:',
  'LBL_BUG' => 'Bug:',
  'LBL_BUG_NUMBER' => 'Bug Number:',
  'LBL_NUMBER' => 'Number:',
  'LBL_STATUS' => 'Status:',
  'LBL_PRIORITY' => 'Priority:',
  'LBL_DESCRIPTION' => 'Description:',
  'LBL_CONTACT_NAME' => 'Contact Name:',
  'LBL_BUG_SUBJECT' => 'Bug Subject:',
  'LBL_CONTACT_ROLE' => 'Role:',
  'LBL_LIST_NUMBER' => 'Num.',
  'LBL_LIST_SUBJECT' => 'Subject',
  'LBL_LIST_STATUS' => 'Status',
  'LBL_LIST_PRIORITY' => 'Priority',
  'LBL_LIST_RELEASE' => 'Release',
  'LBL_LIST_RESOLUTION' => 'Resolution',
  'LBL_LIST_LAST_MODIFIED' => 'Last Modified',
  'LBL_INVITEE' => 'Contacts',
  'LBL_TYPE' => 'Type:',
  'LBL_LIST_TYPE' => 'Type',
  'LBL_RESOLUTION' => 'Resolution:',
  'LBL_RELEASE' => 'Release:',
  'LNK_NEW_BUG' => 'Report Bug',
  'LNK_CREATE'  => 'Report Bug',
  'LNK_CREATE_WHEN_EMPTY'    => 'Report a Bug now.',
  'LNK_BUG_LIST' => 'View Bugs',
  'LBL_SHOW_MORE' => 'Show More Bugs',
  'NTC_REMOVE_INVITEE' => 'Are you sure you want to remove this contact from the bug?',
  'NTC_REMOVE_ACCOUNT_CONFIRMATION' => 'Are you sure you want to remove this bug from this account?',
  'ERR_DELETE_RECORD' => 'You must specify a record number in order to delete the bug.',
  'LBL_LIST_MY_BUGS' => 'My Assigned Bugs',
  'LNK_IMPORT_BUGS' => 'Import Bugs',
  'LBL_FOUND_IN_RELEASE' => 'Found in Release:',
  'LBL_FIXED_IN_RELEASE' => 'Fixed in Release:',
  'LBL_LIST_FIXED_IN_RELEASE' => 'Fixed in Release',
  'LBL_WORK_LOG' => 'Work Log:',
  'LBL_SOURCE' => 'Source:',
  'LBL_PRODUCT_CATEGORY' => 'Category:',

  'LBL_CREATED_BY' => 'Created by:',
  'LBL_DATE_CREATED' => 'Create Date:',
  'LBL_MODIFIED_BY' => 'Last Modified by:',
  'LBL_DATE_LAST_MODIFIED' => 'Modify Date:',

  'LBL_LIST_EMAIL_ADDRESS' => 'Email Address',
  'LBL_LIST_CONTACT_NAME' => 'Contact Name',
  'LBL_LIST_ACCOUNT_NAME' => 'Account Name',
  'LBL_LIST_PHONE' => 'Phone',
  'NTC_DELETE_CONFIRMATION' => 'Are you sure you want to remove this contact from this bug?',

  'LBL_DEFAULT_SUBPANEL_TITLE' => 'Bug Tracker',
  'LBL_ACTIVITIES_SUBPANEL_TITLE'=>'Activities',
  'LBL_HISTORY_SUBPANEL_TITLE'=>'History',
  'LBL_CONTACTS_SUBPANEL_TITLE' => 'Contacts',
  'LBL_ACCOUNTS_SUBPANEL_TITLE' => 'Accounts',
  'LBL_CASES_SUBPANEL_TITLE' => 'Cases',
  'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'Documents',
  'LBL_SYSTEM_ID' => 'System ID',
  'LBL_LIST_ASSIGNED_TO_NAME' => 'Assigned User',
	'LBL_ASSIGNED_TO_NAME' => 'Assigned to',

	//BEGIN SUGARCRM flav=pro ONLY
	'LNK_BUG_REPORTS' => 'View Bug Reports',
	'LBL_SHOW_IN_PORTAL' => 'Show in Portal',
	//END SUGARCRM flav=pro ONLY
	'LBL_BUG_INFORMATION' => 'Overview',

    //For export labels
	'LBL_FOUND_IN_RELEASE_NAME' => 'Found In Release Name',
    'LBL_PORTAL_VIEWABLE' => 'Portal Viewable',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'Assigned User Name',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'Assigned User ID',
    'LBL_EXPORT_FIXED_IN_RELEASE_NAMR' => 'Fixed in Release Name',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'Modified By ID',
    'LBL_EXPORT_CREATED_BY' => 'Created By ID',


    //Dashlet
    'LBL_DASHLET_LISTVIEW_NAME' => 'My Assigned Bugs',
    'LBL_DASHLET_LISTVIEW_DESCRIPTION' => 'Bugs assigned to you',

    //BEGIN SUGARCRM flav=ent ONLY
    //Tour content
    'LBL_PORTAL_TOUR_RECORDS_INTRO' => 'The Bugs module is for viewing and reporting bugs.  Use the arrows below to go through a quick tour.',
    'LBL_PORTAL_TOUR_RECORDS_PAGE' => 'This page shows the list of existing published Bugs.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER' => 'You can filter down the list of Bugs by providing a search term.',
    'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE' => 'For example, you might use this to find a bug that has been previously reported.',
    'LBL_PORTAL_TOUR_RECORDS_CREATE' => 'If you have found a new Bug you would like to report, you can click here to report a new Bug.',
    'LBL_PORTAL_TOUR_RECORDS_RETURN' => 'Clicking here will return you to this view at any time.',
    //END SUGARCRM flav=ent ONLY


  );


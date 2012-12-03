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
 *of a third party.Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

$dictionary['Email'] = array(
    'table' => 'emails',
    'acl_fields'=>false,
    'comment' => 'Contains a record of emails sent to and from the Sugar application',
	'fields' => array (
		'id' => array (
			'name' => 'id',
			'vname' => 'LBL_ID',
			'type' => 'id',
			'required' => true,
			'reportable'=>true,
			'comment' => 'Unique identifier',
		),
		'date_entered' => array (
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
			'required'=>true,
			'comment' => 'Date record created',
		),
		'date_modified' => array (
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
			'required'=>true,
			'comment' => 'Date record last modified',
		),
		'assigned_user_id' => array (
			'name' => 'assigned_user_id',
			'rname' => 'user_name',
			'id_name' => 'assigned_user_id',
			'vname' => 'LBL_ASSIGNED_TO',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'reportable'=>true,
			'dbType' => 'id',
			'comment' => 'User ID that last modified record',
		),
		'assigned_user_name' => array (
			'name' => 'assigned_user_name',
			'vname' => 'LBL_ASSIGNED_TO',
			'type' => 'varchar',
			'reportable'=>false,
			'source'=> 'non-db',
			'table' => 'users',
		),
		'modified_user_id' => array (
			'name' => 'modified_user_id',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_MODIFIED_BY',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'reportable'=>true,
			'dbType' => 'id',
			'comment' => 'User ID that last modified record',
		),
	   'modified_by_name' =>
    	  array (
    	    'name' => 'modified_by_name',
    	    'vname' => 'LBL_MODIFIED_NAME',
    	    'type' => 'relate',
    	    'reportable'=>false,
    	    'source'=>'non-db',
    	    'rname'=>'user_name',
    	    'table' => 'users',
    	    'id_name' => 'modified_user_id',
    	    'module'=>'Users',
    	    'link'=>'modified_user_link',
    	    'duplicate_merge'=>'disabled',
            'massupdate' => false,
    	  ),
  	    'created_by' => array (
			'name' => 'created_by',
			'vname' => 'LBL_CREATED_BY',
			'type' => 'id',
			'len'=> '36',
			'reportable' => false,
			'comment' => 'User name who created record',
		),
	  	'created_by_name' =>
    	  array (
    	    'name' => 'created_by_name',
    		'vname' => 'LBL_CREATED',
    		'type' => 'relate',
    		'reportable'=>false,
    	    'link' => 'created_by_link',
    	    'rname' => 'user_name',
    		'source'=>'non-db',
    		'table' => 'users',
    		'id_name' => 'created_by',
    		'module'=>'Users',
    		'duplicate_merge'=>'disabled',
            'importable' => 'false',
            'massupdate' => false,
    	),
	'deleted' => array (
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required' => false,
			'reportable'=>false,
			'comment' => 'Record deletion indicator',
		),
/**
 * DEPRECATED FOR 5.0
		'from_addr' => array (
			'name' => 'from_addr',
			'vname' => 'LBL_FROM',
			'type' => 'id',
			'comment' => 'Email address of the person sending the email',
		),
		'reply_to_addr' => array (
			'name' => 'reply_to_addr',
			'vname' => 'LBL_REPLY_TO_ADDRESS',
			'type' => 'id',
			'comment' => 'Email address of person indicated in the Reply-to email field',
		),
		'to_addrs' => array (
			'name' => 'to_addrs',
			'vname' => 'LBL_TO',
			'type' => 'id',
			'comment' => 'Email address(es) of person(s) to receive the email',
		),
		'cc_addrs' => array (
			'name' => 'cc_addrs',
			'vname' => 'LBL_CC',
			'type' => 'id',
			'comment' => 'Email address(es) of person(s) to receive a carbon copy of the email',
		),
		'bcc_addrs' => array (
			'name' => 'bcc_addrs',
			'vname' => 'LBL_BCC',
			'type' => 'id',
			'comment' => 'Email address(es) of person(s) to receive a blind carbon copy of the email',
		),
*/


		'from_addr_name' => array (
			'name' => 'from_addr_name',
			'type' => 'varchar',
			'vname' => 'from_addr_name',
			'source'=> 'non-db',
		),
		'reply_to_addr' => array (
			'name' => 'reply_to_addr',
			'type' => 'varchar',
			'vname' => 'reply_to_addr',
			'source'=> 'non-db',
		),
		'to_addrs_names' => array (
			'name' => 'to_addrs_names',
			'type' => 'varchar',
			'vname' => 'to_addrs_names',
			'source'=> 'non-db',
		),
		'cc_addrs_names' => array (
			'name' => 'cc_addrs_names',
			'type' => 'varchar',
			'vname' => 'cc_addrs_names',
			'source'=> 'non-db',
		),
		'bcc_addrs_names' => array (
			'name' => 'bcc_addrs_names',
			'type' => 'varchar',
			'vname' => 'bcc_addrs_names',
			'source'=> 'non-db',
		),
		'raw_source' => array (
			'name' => 'raw_source',
			'type' => 'varchar',
			'vname' => 'raw_source',
			'source'=> 'non-db',
		),
		'description_html' => array (
			'name' => 'description_html',
			'type' => 'varchar',
			'vname' => 'description_html',
			'source'=> 'non-db',
		),
		'description' => array (
			'name' => 'description',
			'type' => 'varchar',
			'vname' => 'description',
			'source'=> 'non-db',
		),
		'date_sent' => array (
			'name'			=> 'date_sent',
			'vname'			=> 'LBL_DATE_SENT',
			'type'			=> 'datetime',
			'type'			=> 'datetime',
		),
		'message_id' => array (
			'name'		=> 'message_id',
			'vname' 	=> 'LBL_MESSAGE_ID',
			'type'		=> 'varchar',
			'len'		=> 255,
			'comment' => 'ID of the email item obtained from the email transport system',
		),
        // Bug #45395 : Deleted emails from a group inbox does not move the emails to the Trash folder for Google Apps
        'message_uid' => array (
            'name'		=> 'message_uid',
            'vname' 	=> 'LBL_MESSAGE_UID',
            'type'		=> 'varchar',
            'len'		=> 64,
            'comment' => 'UID of the email item obtained from the email transport system',
        ),
		'name' => array (
			'name' => 'name',
			'vname' => 'LBL_SUBJECT',
			'type' => 'varchar',
			'required' => false,
			'len' => '255',
			'comment' => 'The subject of the email',
		),
		'type' => array (
			'name' => 'type',
			'vname' => 'LBL_LIST_TYPE',
			'type' => 'enum',
			'options' => 'dom_email_types',
			'len' => 100,
			'massupdate'=>false,
			'comment' => 'Type of email (ex: draft)',
		),
		'status' => array (
			'name' => 'status',
			'vname' => 'LBL_STATUS',
			'type' => 'enum',
			'len' => 100,
			'options' => 'dom_email_status',
		),
		'flagged' => array (
			'name' => 'flagged',
			'vname' => 'LBL_EMAIL_FLAGGED',
			'type' => 'bool',
			'required' => false,
			'reportable'=>false,
			'comment' => 'flagged status',
		),
		'reply_to_status' => array (
			'name' => 'reply_to_status',
			'vname' => 'LBL_EMAIL_REPLY_TO_STATUS',
			'type' => 'bool',
			'required' => false,
			'reportable'=>false,
			'comment' => 'I you reply to an email then reply to status of original email is set',
		),
		'intent' => array (
			'name'	=> 'intent',
			'vname' => 'LBL_INTENT',
			'type'	=> 'varchar',
			'len' => 100,
			'default'	=> 'pick',
			'comment' => 'Target of action used in Inbound Email assignment',
		),
		'mailbox_id' => array (
			'name' => 'mailbox_id',
			'vname' => 'LBL_MAILBOX_ID',
			'type' => 'id',
			'len'=> '36',
			'reportable' => false,
		),
		'created_by_link' => array (
			'name' => 'created_by_link',
			'type' => 'link',
			'relationship' => 'emails_created_by',
			'vname' => 'LBL_CREATED_BY_USER',
			'link_type' => 'one',
			'module'=> 'Users',
			'bean_name'=> 'User',
			'source'=> 'non-db',
		),
		'modified_user_link' => array (
			'name' => 'modified_user_link',
			'type' => 'link',
			'relationship' => 'emails_modified_user',
			'vname' => 'LBL_MODIFIED_BY_USER',
			'link_type' => 'one',
			'module'=> 'Users',
			'bean_name'=> 'User',
			'source'=> 'non-db',
		),
		'assigned_user_link' => array (
			'name' => 'assigned_user_link',
			'type' => 'link',
			'relationship' => 'emails_assigned_user',
			'vname' => 'LBL_ASSIGNED_TO_USER',
			'link_type' => 'one',
			'module'=> 'Users',
			'bean_name'=> 'User',
			'source'=> 'non-db',
		),

		'parent_name' => array (
			'name' => 'parent_name',
			'type' => 'varchar',
			'reportable'=>false,
			'source'=> 'non-db',
		),
		'parent_type' => array (
			'name' => 'parent_type',
			'type' => 'varchar',
			'reportable'=>false,
			'len' => 100,
			'comment' => 'Identifier of Sugar module to which this email is associated (deprecated as of 4.2)',
		),
		'parent_id' => array (
			'name' => 'parent_id',
			'type' => 'id',
			'len' => '36',
			'reportable'=>false,
			'comment' => 'ID of Sugar object referenced by parent_type (deprecated as of 4.2)',
		),

		/* relationship collection attributes */
		/* added to support InboundEmail */
		'accounts'	=> array (
			'name'			=> 'accounts',
			'vname'			=> 'LBL_EMAILS_ACCOUNTS_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_accounts_rel',
			'module'		=> 'Accounts',
			'bean_name'		=> 'Account',
			'source'		=> 'non-db',
		),
		'bugs'	=> array (
			'name'			=> 'bugs',
			'vname'			=> 'LBL_EMAILS_BUGS_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_bugs_rel',
			'module'		=> 'Bugs',
			'bean_name'		=> 'Bug',
			'source'		=> 'non-db',
		),
		'cases'	=> array (
			'name'			=> 'cases',
			'vname'			=> 'LBL_EMAILS_CASES_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_cases_rel',
			'module'		=> 'Cases',
			'bean_name'		=> 'Case',
			'source'		=> 'non-db',
		),
		'contacts'	=> array (
			'name'			=> 'contacts',
			'vname'			=> 'LBL_EMAILS_CONTACTS_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_contacts_rel',
			'module'		=> 'Contacts',
			'bean_name'		=> 'Contact',
			'source'		=> 'non-db',
		),
		'leads'	=> array (
			'name'			=> 'leads',
			'vname'			=> 'LBL_EMAILS_LEADS_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_leads_rel',
			'module'		=> 'Leads',
			'bean_name'		=> 'Lead',
			'source'		=> 'non-db',
		),
		'opportunities'	=> array (
			'name'			=> 'opportunities',
			'vname'			=> 'LBL_EMAILS_OPPORTUNITIES_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_opportunities_rel',
			'module'		=> 'Opportunities',
			'bean_name'		=> 'Opportunity',
			'source'		=> 'non-db',
		),
		'project'=> array(
			'name'			=> 'project',
			'vname'			=> 'LBL_EMAILS_PROJECT_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_projects_rel',
			'module'		=> 'Project',
			'bean_name'		=> 'Project',
			'source'		=> 'non-db',
		),
		'projecttask'=> array(
			'name'			=> 'projecttask',
			'vname'			=> 'LBL_EMAILS_PROJECT_TASK_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_project_task_rel',
			'module'		=> 'ProjectTask',
			'bean_name'		=> 'ProjectTask',
			'source'		=> 'non-db',
		),
		'prospects'=> array(
			'name'			=> 'prospects',
			'vname'			=> 'LBL_EMAILS_PROSPECT_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_prospects_rel',
			'module'		=> 'Prospects',
			'bean_name'		=> 'Prospect',
			'source'		=> 'non-db',
		),


//BEGIN SUGARCRM flav=pro ONLY

		'quotes'=> array(
			'name'			=> 'quotes',
			'vname'			=> 'LBL_EMAILS_QUOTES_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_quotes',
			'module'		=> 'Quotes',
			'bean_name'		=> 'Quote',
			'source'		=> 'non-db',
		),
//END SUGARCRM flav=pro ONLY
		'tasks'=> array(
			'name'			=> 'tasks',
			'vname'			=> 'LBL_EMAILS_TASKS_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_tasks_rel',
			'module'		=> 'Tasks',
			'bean_name'		=> 'Task',
			'source'		=> 'non-db',
		),
		'users'=> array(
			'name'			=> 'users',
			'vname'			=> 'LBL_EMAILS_USERS_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_users_rel',
			'module'		=> 'Users',
			'bean_name'		=> 'User',
			'source'		=> 'non-db',
		),
		'notes' => array(
			'name'			=> 'notes',
			'vname'			=> 'LBL_EMAILS_NOTES_REL',
			'type'			=> 'link',
			'relationship'	=> 'emails_notes_rel',
			'module'		=> 'Notes',
			'bean_name'		=> 'Note',
			'source'		=> 'non-db',
		),
		// SNIP
		'meetings' => array(
            'name'			=> 'meetings',
            'vname'			=> 'LBL_EMAILS_MEETINGS_REL',
            'type'			=> 'link',
            'relationship'  => 'emails_meetings_rel',
            'module'		=> 'Meetings',
            'bean_name'		=> 'Meeting',
            'source'		=> 'non-db',
		),
		/* end relationship collections */

	), /* end fields() array */
	'relationships' => array(
		'emails_assigned_user'	=> array(
			'lhs_module'		=> 'Users',
			'lhs_table'			=> 'users',
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Emails',
			'rhs_table'			=> 'emails',
			'rhs_key'			=> 'assigned_user_id',
			'relationship_type'	=> 'one-to-many'
		),
		'emails_modified_user'	=> array(
			'lhs_module'		=> 'Users',
			'lhs_table'			=> 'users',
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Emails',
			'rhs_table'			=> 'emails',
			'rhs_key'			=> 'modified_user_id',
			'relationship_type'	=> 'one-to-many'
		),
		'emails_created_by'		=> array(
			'lhs_module'		=> 'Users',
			'lhs_table'			=> 'users',
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Emails',
			'rhs_table'			=> 'emails',
			'rhs_key'			=> 'created_by',
			'relationship_type'	=> 'one-to-many'
		),
		'emails_notes_rel' => array(
			'lhs_module'		=> 'Emails',
			'lhs_table'			=> 'emails',
			'lhs_key'			=> 'id',
			'rhs_module'		=> 'Notes',
			'rhs_table'			=> 'notes',
			'rhs_key'			=> 'parent_id',
			'relationship_type'	=> 'one-to-many',
		),

		// SNIP
		'emails_meetings_rel' => array(
			'lhs_module'    		 => 'Emails',
			'lhs_table'          => 'emails',
			'lhs_key'            => 'id',
			'rhs_module'         => 'Meetings',
			'rhs_table'          => 'meetings',
			'rhs_key'            => 'parent_id',
			'relationship_type'  => 'one-to-many',
		),
	), // end relationships
	'indices' => array (
		array(
			'name'				=> 'emailspk',
			'type'				=> 'primary',
			'fields'			=> array('id'),
		),
 		array(
			'name'				=> 'idx_email_name',
			'type'				=> 'index',
			'fields'			=> array('name')
		),
 		array(
			'name'				=> 'idx_message_id',
			'type'				=> 'index',
			'fields'			=> array('message_id')
		),
 		array(
			'name'				=> 'idx_email_parent_id',
			'type'				=> 'index',
			'fields'			=> array('parent_id')
		),
 		array(
			'name'				=> 'idx_email_assigned',
			'type'				=> 'index',
			'fields'			=> array('assigned_user_id', 'type','status')
		),
	) // end indices
);

VardefManager::createVardef('Emails','Email', array(
//BEGIN SUGARCRM flav=pro ONLY
'team_security',
//END SUGARCRM flav=pro ONLY
));
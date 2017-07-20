<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$dictionary['Email'] = array(
    'favorites' => true,
    'table' => 'emails',
    'acls' => array(
        'SugarACLEmails' => true,
        'SugarACLDraftEmails' => true,
        'SugarACLArchivedEmails' => true,
    ),
    'full_text_search' => true,
    'activity_enabled' => true,
    'comment' => 'Contains a record of emails sent to and from the Sugar application',
    'fields' => array(
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier',
        ),
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record created',
            'readonly' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                'aggregations' => array(
                    'date_entered' => array(
                        'type' => 'DateRange',
                    ),
                ),
            ),
        ),
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'required' => true,
            'comment' => 'Date record last modified',
            'readonly' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                'aggregations' => array(
                    'date_modified' => array(
                        'type' => 'DateRange',
                    ),
                ),
            ),
        ),
        'assigned_user_id' => array(
            'name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'type' => 'id',
            'isnull' => false,
            'reportable' => false,
            'comment' => 'User ID that last modified record',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                'aggregations' => array(
                    'assigned_user_id' => array(
                        'type' => 'MyItems',
                        'label' => 'LBL_AGG_ASSIGNED_TO_ME',
                    ),
                ),
            ),
        ),
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'id_name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO',
            'link'=>'assigned_user_link' ,
            'rname' => 'full_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'module' => 'Users',
        ),
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED_BY',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => false,
            'reportable' => true,
            'dbType' => 'id',
            'comment' => 'User ID that last modified record',
            'readonly' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                'type' => 'id',
                'aggregations' => array(
                    'modified_user_id' => array(
                        'type' => 'MyItems',
                        'label' => 'LBL_AGG_MODIFIED_BY_ME',
                    ),
                ),
            ),
        ),
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_NAME',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'full_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
        ),
        'created_by' => array(
            'name' => 'created_by',
            'vname' => 'LBL_CREATED_BY',
            'type' => 'id',
            'len' => '36',
            'reportable' => false,
            'comment' => 'User name who created record',
            'readonly' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                'type' => 'id',
                'aggregations' => array(
                    'created_by' => array(
                        'type' => 'MyItems',
                        'label' => 'LBL_AGG_CREATED_BY_ME',
                    ),
                ),
            ),
        ),
        'created_by_name' => array(
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'link' => 'created_by_link',
            'rname' => 'full_name',
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'importable' => false,
            'massupdate' => false,
        ),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
            'comment' => 'Record deletion indicator',
        ),
        'from_addr_name' => array(
            'name' => 'from_addr_name',
            'type' => 'varchar',
            'vname' => 'LBL_FROM',
            'source' => 'non-db',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
            ),
        ),
        'reply_to_addr' => array(
            'name' => 'reply_to_addr',
            'type' => 'varchar',
            'vname' => 'reply_to_addr',
            'source' => 'non-db',
        ),
        'to_addrs_names' => array(
            'name' => 'to_addrs_names',
            'type' => 'varchar',
            'vname' => 'LBL_TO_ADDRS',
            'source' => 'non-db',
            'reportable' => false,
        ),
        'cc_addrs_names' => array(
            'name' => 'cc_addrs_names',
            'type' => 'varchar',
            'vname' => 'LBL_CC',
            'source' => 'non-db',
            'reportable' => false,
        ),
        'bcc_addrs_names' => array(
            'name' => 'bcc_addrs_names',
            'type' => 'varchar',
            'vname' => 'LBL_BCC',
            'source' => 'non-db',
            'reportable' => false,
        ),
        'raw_source' => array(
            'name' => 'raw_source',
            'type' => 'varchar',
            'vname' => 'raw_source',
            'source' => 'non-db',
        ),
        'description_html' => array(
            'name' => 'description_html',
            'type' => 'varchar',
            'vname' => 'description_html',
            'source' => 'non-db',
        ),
        'description' => array(
            'name' => 'description',
            'type' => 'varchar',
            'vname' => 'LBL_TEXT_BODY',
            'source' => 'non-db',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'type' => 'text',
            ),
        ),
        'date_sent' => array(
            'name' => 'date_sent',
            'vname' => 'LBL_DATE_SENT',
            'type' => 'datetime',
            'massupdate' => false,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
            ),
            'hideacl' => true,
        ),
        'message_id' => array(
            'name' => 'message_id',
            'vname' => 'LBL_MESSAGE_ID',
            'type' => 'varchar',
            'len' => 255,
            'comment' => 'ID of the email item obtained from the email transport system',
            'hideacl' => true,
        ),
        // Bug #45395 : Deleted emails from a group inbox does not move the emails to the Trash folder for Google Apps
        'message_uid' => array(
            'name' => 'message_uid',
            'vname' => 'LBL_MESSAGE_UID',
            'type' => 'varchar',
            'len' => 64,
            'comment' => 'UID of the email item obtained from the email transport system',
            'hideacl' => true,
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_SUBJECT',
            'type' => 'name',
            'dbType' => 'varchar',
            'required' => false,
            'len' => '255',
            'comment' => 'The subject of the email',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
            ),
            'hideacl' => true,
        ),
        'type' => array(
            'name' => 'type',
            'vname' => 'LBL_LIST_TYPE',
            'type' => 'enum',
            'options' => 'dom_email_types',
            'len' => 100,
            'massupdate' => false,
            'comment' => 'Type of email (ex: draft)',
            'hideacl' => true,
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'dom_email_status',
            'massupdate' => false,
            'hideacl' => true,
        ),
        'flagged' => array(
            'name' => 'flagged',
            'vname' => 'LBL_EMAIL_FLAGGED',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
            'comment' => 'flagged status',
            'massupdate' => false,
        ),
        'reply_to_status' => array(
            'name' => 'reply_to_status',
            'vname' => 'LBL_EMAIL_REPLY_TO_STATUS',
            'type' => 'bool',
            'required' => false,
            'reportable' => false,
            'comment' => 'I you reply to an email then reply to status of original email is set',
            'massupdate' => false,
            'hideacl' => true,
        ),
        'intent' => array(
            'name' => 'intent',
            'vname' => 'LBL_INTENT',
            'type' => 'varchar',
            'len' => 100,
            'default' => 'pick',
            'comment' => 'Target of action used in Inbound Email assignment',
            'hideacl' => true,
        ),
        'mailbox_id' => array(
            'name' => 'mailbox_id',
            'vname' => 'LBL_MAILBOX_ID',
            'type' => 'id',
            'len' => '36',
            'reportable' => false,
        ),
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => 'emails_created_by',
            'vname' => 'LBL_CREATED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ),
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'link',
            'relationship' => 'emails_modified_user',
            'vname' => 'LBL_MODIFIED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ),
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => 'emails_assigned_user',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ),
        'state' => array(
            'name' => 'state',
            'vname' => 'LBL_EMAIL_STATE',
            'type' => 'enum',
            'options' => 'dom_email_states',
            'len' => 100,
            'required' => true,
            'default' => 'Archived',
            'massupdate' => false,
            'comment' => 'An email is either a draft or archived',
            'reportable' => false,
            'hideacl' => true,
        ),
        'reply_to_id' => array(
            'name' => 'reply_to_id',
            'vname' => 'LBL_EMAIL_REPLY_TO_ID',
            'type' => 'id',
            'len' => '36',
            'reportable' => false,
            'duplicate_on_record_copy' => 'no',
            'importable' => false,
            'comment' => 'Identifier of email record that this email was a reply to',
        ),
        'parent_name' => array(
            'name' => 'parent_name',
            'parent_type' => 'record_type_display_emails',
            'type_name' => 'parent_type',
            'id_name' => 'parent_id',
            'vname' => 'LBL_LIST_RELATED_TO',
            'type' => 'parent',
            'group' => 'parent_name',
            'reportable' => false,
            'source' => 'non-db',
            'options' => 'record_type_display_emails',
        ),
        'parent_type' => array(
            'name' => 'parent_type',
            'vname' => 'LBL_PARENT_TYPE',
            'type' => 'parent_type',
            'dbType' => 'varchar',
            'group' => 'parent_name',
            'options' => 'record_type_display_emails',
            'reportable' => false,
            'comment' => 'Identifier of Sugar module to which this email is associated',
        ),
        'parent_id' => array(
            'name' => 'parent_id',
            'vname' => 'LBL_PARENT_ID',
            'type' => 'id',
            'group' => 'parent_name',
            'reportable' => false,
            'comment' => 'ID of Sugar object referenced by parent_type',
        ),
        /* relationship collection attributes */
        /* added to support InboundEmail */
        'accounts' => array(
            'name' => 'accounts',
            'vname' => 'LBL_EMAILS_ACCOUNTS_REL',
            'type' => 'link',
            'relationship' => 'emails_accounts_rel',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'source' => 'non-db',
        ),
        'bugs' => array(
            'name' => 'bugs',
            'vname' => 'LBL_EMAILS_BUGS_REL',
            'type' => 'link',
            'relationship' => 'emails_bugs_rel',
            'module' => 'Bugs',
            'bean_name' => 'Bug',
            'source' => 'non-db',
        ),
        'cases' => array(
            'name' => 'cases',
            'vname' => 'LBL_EMAILS_CASES_REL',
            'type' => 'link',
            'relationship' => 'emails_cases_rel',
            'module' => 'Cases',
            'bean_name' => 'Case',
            'source' => 'non-db',
        ),
        'contacts' => array(
            'name' => 'contacts',
            'vname' => 'LBL_EMAILS_CONTACTS_REL',
            'type' => 'link',
            'relationship' => 'emails_contacts_rel',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'source' => 'non-db',
        ),
        'leads' => array(
            'name' => 'leads',
            'vname' => 'LBL_EMAILS_LEADS_REL',
            'type' => 'link',
            'relationship' => 'emails_leads_rel',
            'module' => 'Leads',
            'bean_name' => 'Lead',
            'source' => 'non-db',
        ),
        'opportunities' => array(
            'name' => 'opportunities',
            'vname' => 'LBL_EMAILS_OPPORTUNITIES_REL',
            'type' => 'link',
            'relationship' => 'emails_opportunities_rel',
            'module' => 'Opportunities',
            'bean_name' => 'Opportunity',
            'source' => 'non-db',
        ),
        'project' => array(
            'name' => 'project',
            'vname' => 'LBL_EMAILS_PROJECT_REL',
            'type' => 'link',
            'relationship' => 'emails_projects_rel',
            'module' => 'Project',
            'bean_name' => 'Project',
            'source' => 'non-db',
        ),
        'projecttask' => array(
            'name' => 'projecttask',
            'vname' => 'LBL_EMAILS_PROJECT_TASK_REL',
            'type' => 'link',
            'relationship' => 'emails_project_task_rel',
            'module' => 'ProjectTask',
            'bean_name' => 'ProjectTask',
            'source' => 'non-db',
        ),
        'prospects' => array(
            'name' => 'prospects',
            'vname' => 'LBL_EMAILS_PROSPECT_REL',
            'type' => 'link',
            'relationship' => 'emails_prospects_rel',
            'module' => 'Prospects',
            'bean_name' => 'Prospect',
            'source' => 'non-db',
        ),
        'quotes' => array(
            'name' => 'quotes',
            'vname' => 'LBL_EMAILS_QUOTES_REL',
            'type' => 'link',
            'relationship' => 'emails_quotes',
            'module' => 'Quotes',
            'bean_name' => 'Quote',
            'source' => 'non-db',
        ),
        'revenuelineitems' => array(
            'name' => 'revenuelineitems',
            'vname' => 'LBL_EMAILS_REVENUELINEITEMS_REL',
            'type' => 'link',
            'relationship' => 'emails_revenuelineitems_rel',
            'module' => 'RevenueLineItems',
            'bean_name' => 'RevenueLineItem',
            'source' => 'non-db',
            'workflow' => false
        ),
        'products' => array(
            'name' => 'products',
            'vname' => 'LBL_EMAILS_PRODUCTS_REL',
            'type' => 'link',
            'relationship' => 'emails_products_rel',
            'module' => 'Products',
            'bean_name' => 'Product',
            'source' => 'non-db',
        ),
        'tasks' => array(
            'name' => 'tasks',
            'vname' => 'LBL_EMAILS_TASKS_REL',
            'type' => 'link',
            'relationship' => 'emails_tasks_rel',
            'module' => 'Tasks',
            'bean_name' => 'Task',
            'source' => 'non-db',
        ),
        'users' => array(
            'name' => 'users',
            'vname' => 'LBL_EMAILS_USERS_REL',
            'type' => 'link',
            'relationship' => 'emails_users_rel',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
        ),
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_EMAILS_NOTES_REL',
            'type' => 'link',
            'relationship' => 'emails_notes_rel',
            'module' => 'Notes',
            'bean_name' => 'Note',
            'source' => 'non-db',
        ),
        'attachments' => array(
            'bean_name' => 'Note',
            'module' => 'Notes',
            'name' => 'attachments',
            'relationship' => 'emails_attachments',
            'source' => 'non-db',
            'type' => 'link',
            'vname' => 'LBL_ATTACHMENTS',
            'reportable' => false,
            'readonly' => true,
        ),
        'attachments_collection' => array(
            'name' => 'attachments_collection',
            'links' => array(
                'attachments',
            ),
            'order_by' => 'name:asc',
            'source' => 'non-db',
            'studio' => false,
            'type' => 'collection',
            'vname' => 'LBL_ATTACHMENTS',
            'reportable' => false,
        ),
        'total_attachments' => array(
            'name' => 'total_attachments',
            'vname' => 'LBL_TOTAL_ATTACHMENTS',
            'type' => 'int',
            'formula' => 'count($attachments)',
            'calculated' => true,
            'enforced' => true,
            'studio' => false,
            'workflow' => false,
            'importable' => false,
            'reportable' => false,
            'hideacl' => true,
        ),
        'outbound_email_id' => array(
            'name' => 'outbound_email_id',
            'comment' => 'The configuration used to send an email, only used for emails sent using SugarCRM',
            'type' => 'enum',
            'dbType' => 'id',
            'required' => false,
            'vname' => 'LBL_OUTBOUND_EMAIL_ID',
            'function' => 'getOutboundEmailDropdown',
            'function_bean' => 'Emails',
            'reportable' => false,
        ),
        'from' => array(
            'name' => 'from',
            'links' => array(
                'from_link',
            ),
            'order_by' => 'parent_name:asc',
            'source' => 'non-db',
            'studio' => false,
            'type' => 'collection',
            'vname' => 'LBL_FROM',
            'reportable' => false,
        ),
        'to' => array(
            'name' => 'to',
            'links' => array(
                'to_link',
            ),
            'order_by' => 'parent_name:asc',
            'source' => 'non-db',
            'studio' => false,
            'type' => 'collection',
            'vname' => 'LBL_TO_ADDRS',
            'reportable' => false,
        ),
        'cc' => array(
            'name' => 'cc',
            'links' => array(
                'cc_link',
            ),
            'order_by' => 'parent_name:asc',
            'source' => 'non-db',
            'studio' => false,
            'type' => 'collection',
            'vname' => 'LBL_CC',
            'reportable' => false,
        ),
        'bcc' => array(
            'name' => 'bcc',
            'links' => array(
                'bcc_link',
            ),
            'order_by' => 'parent_name:asc',
            'source' => 'non-db',
            'studio' => false,
            'type' => 'collection',
            'vname' => 'LBL_BCC',
            'reportable' => false,
        ),
        'from_link' => array(
            'name' => 'from_link',
            'relationship' => 'emails_from',
            'source' => 'non-db',
            'type' => 'link',
            'vname' => 'LBL_FROM',
            'reportable' => false,
            'readonly' => true,
        ),
        'to_link' => array(
            'name' => 'to_link',
            'relationship' => 'emails_to',
            'source' => 'non-db',
            'type' => 'link',
            'vname' => 'LBL_TO_ADDRS',
            'reportable' => false,
            'readonly' => true,
        ),
        'cc_link' => array(
            'name' => 'cc_link',
            'relationship' => 'emails_cc',
            'source' => 'non-db',
            'type' => 'link',
            'vname' => 'LBL_CC',
            'reportable' => false,
            'readonly' => true,
        ),
        'bcc_link' => array(
            'name' => 'bcc_link',
            'relationship' => 'emails_bcc',
            'source' => 'non-db',
            'type' => 'link',
            'vname' => 'LBL_BCC',
            'reportable' => false,
            'readonly' => true,
        ),
        // SNIP
        'meetings' => array(
            'name' => 'meetings',
            'vname' => 'LBL_EMAILS_MEETINGS_REL',
            'type' => 'link',
            'relationship' => 'emails_meetings_rel',
            'module' => 'Meetings',
            'bean_name' => 'Meeting',
            'source' => 'non-db',
        ),
        /* end relationship collections */
    ), /* end fields() array */
    'relationships' => array(
        'emails_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Emails',
            'rhs_table' => 'emails',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'emails_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Emails',
            'rhs_table' => 'emails',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'emails_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Emails',
            'rhs_table' => 'emails',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
        'emails_attachments' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'email_id',
            'relationship_type' => 'one-to-many',
            'relationship_class' => 'EmailAttachmentRelationship',
            'relationship_file' => 'modules/Emails/EmailAttachmentRelationship.php',
            'relationship_role_column' => 'email_type',
            'relationship_role_column_value' => 'Emails',
        ),
        'emails_notes_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Notes',
        ),
        'emails_contacts_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Contacts',
            'rhs_table' => 'contacts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Contacts',
        ),
        'emails_accounts_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Accounts',
            'rhs_table' => 'accounts',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Accounts',
        ),
        'emails_leads_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Leads',
            'rhs_table' => 'leads',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Leads',
        ),
        'emails_revenuelineitems_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'RevenueLineItems',
            'rhs_table' => 'revenue_line_items',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'RevenueLineItems',
        ),
        'emails_products_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Products',
            'rhs_table' => 'products',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'emails_beans',
            'join_key_lhs' => 'email_id',
            'join_key_rhs' => 'bean_id',
            'relationship_role_column' => 'bean_module',
            'relationship_role_column_value' => 'Products',
        ),
        // SNIP
        'emails_meetings_rel' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'Meetings',
            'rhs_table' => 'meetings',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
        ),
        'emails_from' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailParticipants',
            'rhs_table' => 'emails_email_addr_rel',
            'rhs_key' => 'email_id',
            'relationship_type' => 'one-to-one',
            'relationship_class' => 'EmailSenderRelationship',
            'relationship_file' => 'modules/Emails/EmailSenderRelationship.php',
            'relationship_role_columns' => array(
                'address_type' => 'from',
            ),
        ),
        'emails_to' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailParticipants',
            'rhs_table' => 'emails_email_addr_rel',
            'rhs_key' => 'email_id',
            'relationship_type' => 'one-to-many',
            'relationship_class' => 'EmailRecipientRelationship',
            'relationship_file' => 'modules/Emails/EmailRecipientRelationship.php',
            'relationship_role_columns' => array(
                'address_type' => 'to',
            ),
        ),
        'emails_cc' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailParticipants',
            'rhs_table' => 'emails_email_addr_rel',
            'rhs_key' => 'email_id',
            'relationship_type' => 'one-to-many',
            'relationship_class' => 'EmailRecipientRelationship',
            'relationship_file' => 'modules/Emails/EmailRecipientRelationship.php',
            'relationship_role_columns' => array(
                'address_type' => 'cc',
            ),
        ),
        'emails_bcc' => array(
            'lhs_module' => 'Emails',
            'lhs_table' => 'emails',
            'lhs_key' => 'id',
            'rhs_module' => 'EmailParticipants',
            'rhs_table' => 'emails_email_addr_rel',
            'rhs_key' => 'email_id',
            'relationship_type' => 'one-to-many',
            'relationship_class' => 'EmailRecipientRelationship',
            'relationship_file' => 'modules/Emails/EmailRecipientRelationship.php',
            'relationship_role_columns' => array(
                'address_type' => 'bcc',
            ),
        ),
    ), // end relationships
    'indices' => array(
        array(
            'name' => 'emailspk',
            'type' => 'primary',
            'fields' => array('id'),
        ),
        array(
            'name' => 'idx_email_name',
            'type' => 'index',
            'fields' => array('name')
        ),
        array(
            'name' => 'idx_message_id',
            'type' => 'index',
            'fields' => array('message_id')
        ),
        array(
            'name' => 'idx_email_parent_id',
            'type' => 'index',
            'fields' => array('parent_id')
        ),
        array(
            'name' => 'idx_email_assigned',
            'type' => 'index',
            'fields' => array('assigned_user_id', 'type', 'status')
        ),
        array(
            'name' => 'idx_date_modified',
            'type' => 'index',
            'fields' => array('date_modified'),
        ),
    ), // end indices
    'uses' => array(
        'favorite',
        'following',
        'taggable',
    ),
);

VardefManager::createVardef(
    'Emails',
    'Email',
    array('team_security')
);

// Temporary disable Email description field indexing until the analyzers are sorted out
// to properly cope with larger fields. This impacts indexing performance and additional
// adds a heavy taxation on the required disk space usage as well.
$dictionary['Email']['fields']['description']['full_text_search']['enabled'] = false;
$dictionary['Email']['fields']['description']['full_text_search']['searchable'] = false;

$dictionary['Email']['visibility']['EmailsVisibility'] = true;

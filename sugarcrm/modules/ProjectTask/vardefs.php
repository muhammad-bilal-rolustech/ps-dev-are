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

// $Id: vardefs.php 56510 2010-05-17 18:54:49Z jenny $
$dictionary['ProjectTask'] = array('audited'=>true,
    'table' => 'project_task',
    'favorites' => false,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => false,
	'fields' => array(
		'id' => array(
			'name' => 'id',
			'vname' => 'LBL_ID',
			'required' => true,
			'type' => 'id',
			'reportable'=>true,
		),
		'date_entered' => array(
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
		    'enable_range_search' => true,
		    'options' => 'date_range_search_dom',
            'readonly' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                // Disabled until UX component is available
                //'aggregations' => array(
                //    'date_entered' => array(
                //        'type' => 'DateRange',
                //    ),
                //),
            ),
		),
		'date_modified' => array(
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
		    'enable_range_search' => true,
		    'options' => 'date_range_search_dom',
            'readonly' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
                // Disabled until UX component is available
                //'aggregations' => array(
                //    'date_modified' => array(
                //        'type' => 'DateRange',
                //    ),
                //),
            ),
		),
        'project_id' => array(
            'name' => 'project_id',
            'vname' => 'LBL_PROJECT_ID',
            'required' => false,
            'type' => 'id',
            'reportable' => false,
            'importable' => 'required',
            'required' => true,
        ),
        'project_task_id' => array(
            'name' => 'project_task_id',
            'vname' => 'LBL_PROJECT_TASK_ID',
            'required' => false,
            'type' => 'int',
            'reportable' => false,
        ),
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'required' => true,
            'dbType' => 'varchar',
            'type' => 'name',
            'len' => 50,
            'unified_search' => true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'boost' => 0.81,
            ),
            'importable' => 'required',
            'required' => true,
        ),
		'status' => array(
			'name' => 'status',
			'vname' => 'LBL_STATUS',
			'type' => 'enum',
			'required' => false,
			'options' => 'project_task_status_options',
			'audited'=>true,
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
            ),
		),
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'required' => false,
            'type' => 'text',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'boost' => 0.45,
            ),
        ),
        'resource_id' => array(
            'name' => 'resource_id',
            'vname' => 'LBL_RESOURCE_ID',
            'required' => false,
            'type' => 'text',
            'hidden' => true,
        ),
        'resource_name' => array(
            'name' => 'resource_name',
            'vname' => 'LBL_RESOURCE',
            'required' => false,
            'type' => 'text',
            'source' => 'function',
            'function_class' => 'ProjectTask',
            'function_name' => 'getResourceName',
            'function_params' => array('resource_id'),
            'function_params_source' => 'this',
        ),
        'predecessors' => array(
            'name' => 'predecessors',
            'vname' => 'LBL_PREDECESSORS',
            'required' => false,
            'type' => 'text',
        ),
        'date_start' => array(
            'name' => 'date_start',
            'vname' => 'LBL_DATE_START',
            'type' => 'date',
            'validation'=>array('type' => 'isbefore', 'compareto'=>'date_finish', 'blank' => true),
            'audited'=>true,
            'enable_range_search' => true,
        ),
        'time_start' => array(
            'name' => 'time_start',
            'vname' => 'LBL_TIME_START',
            'type' => 'int',
            'reportable'=>false,
            //'validation'=>array('type' => 'isbefore', 'compareto'=>'date_due', 'blank' => true),
            //'audited'=>true,
        ),
        'time_finish' => array(
            'name' => 'time_finish',
            'vname' => 'LBL_TIME_FINISH',
            'type' => 'int',
            'reportable'=>false,
           // 'validation'=>array('type' => 'isbefore', 'compareto'=>'date_due', 'blank' => true),
           // 'audited'=>true,
        ),
        'date_finish' => array(
            'name' => 'date_finish',
            'vname' => 'LBL_DATE_FINISH',
            'type' => 'date',
            'validation'=>array('type' => 'isafter', 'compareto'=>'date_start', 'blank' => true),
            'audited'=>true,
            'enable_range_search' => true,
        ),
        'duration' => array(
            'name' => 'duration',
            'vname' => 'LBL_DURATION',
            'required' => true,
            'type' => 'int',
        ),
        'duration_unit' => array(
            'name' => 'duration_unit',
            'vname' => 'LBL_DURATION_UNIT',
            'options' => 'project_duration_units_dom',
            'type' => 'text',
        ),
        'actual_duration' => array(
            'name' => 'actual_duration',
            'vname' => 'LBL_ACTUAL_DURATION',
            'required' => false,
            'type' => 'int',
        ),
        'percent_complete' => array(
            'name' => 'percent_complete',
            'vname' => 'LBL_PERCENT_COMPLETE',
            'type' => 'int',
            'required' => false,
            'audited'=>true,
        ),
        'date_due' => array(
            'name' => 'date_due',
            'vname' => 'LBL_DATE_DUE',
            'type' => 'date',
            'rel_field' => 'time_due',
            'audited' => true
        ),
        'time_due' => array(
                    'name' => 'time_due',
                    'vname' => 'LBL_TIME_DUE',
                    'type' => 'time',
                    'rel_field' => 'date_due',
                    'audited' => true
        ),
        'parent_task_id' => array(
            'name' => 'parent_task_id',
            'vname' => 'LBL_PARENT_TASK_ID',
            'required' => false,
            'type' => 'int',
            'reportable'=>true,
        ),

		'assigned_user_id' => array(
			'name' => 'assigned_user_id',
            'type' => 'id',
			'vname' => 'LBL_ASSIGNED_USER_ID',
			'required' => false,
			'isnull' => false,
			'reportable'=>false,
			'audited'=>true,
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
		'modified_user_id' => array(
			'name' => 'modified_user_id',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_MODIFIED_USER_ID',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'reportable'=>true,
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
		'modified_by_name' =>
	  array (
	    'name' => 'modified_by_name',
	    'vname' => 'LBL_MODIFIED_NAME',
	    'type' => 'relate',
	    'reportable'=>false,
	    'source'=>'non-db',
        'rname' => 'full_name',
	    'table' => 'users',
	    'id_name' => 'modified_user_id',
	    'module'=>'Users',
	    'link'=>'modified_user_link',
	    'duplicate_merge'=>'disabled'
	  ),
        'priority' => array(
            'name' => 'priority',
            'vname' => 'LBL_PRIORITY',
            'type' => 'enum',
            'options' => 'project_task_priority_options',
        ),
		'created_by' => array(
			'name' => 'created_by',
			'rname' => 'user_name',
			'id_name' => 'modified_user_id',
			'vname' => 'LBL_CREATED_BY',
			'type' => 'assigned_user_name',
			'table' => 'users',
			'isnull' => 'false',
			'dbType' => 'id',
			'reportable'=>true,
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
		'created_by_name' =>
	  array (
	    'name' => 'created_by_name',
		'vname' => 'LBL_CREATED',
		'type' => 'relate',
		'reportable'=>false,
	    'link' => 'created_by_link',
        'rname' => 'full_name',
		'source'=>'non-db',
		'table' => 'users',
		'id_name' => 'created_by',
		'module'=>'Users',
		'duplicate_merge'=>'disabled',
        'importable' => 'false',
	),
		'milestone_flag' => array(
			'name' => 'milestone_flag',
			'vname' => 'LBL_MILESTONE_FLAG',
			'type' =>'bool',
			'required' => false,
		),
		'order_number' => array(
			'name' => 'order_number',
			'vname' => 'LBL_ORDER_NUMBER',
			'required' => false,
			'type' => 'int',
			'default' => '1',
		),
		'task_number' => array(
			'name' => 'task_number',
			'vname' => 'LBL_TASK_NUMBER',
			'required' => false,
			'type' => 'int',
		),
		'estimated_effort' => array(
			'name' => 'estimated_effort',
			'vname' => 'LBL_ESTIMATED_EFFORT',
			'required' => false,
			'type' => 'int',
		),
		'actual_effort' => array(
			'name' => 'actual_effort',
			'vname' => 'LBL_ACTUAL_EFFORT',
			'required' => false,
			'type' => 'int',
		),
		'deleted' => array(
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required' => false,
			'default' => '0',
			'reportable'=>false,
		),
		'utilization' => array(
			'name' => 'utilization',
			'vname' => 'LBL_UTILIZATION',
			'required' => false,
			'type' => 'int',
			'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
			//'function' => 'getUtilizationDropdown',
    		'function' => array('name'=>'getUtilizationDropdown', 'returns'=>'html', 'include'=>'modules/ProjectTask/ProjectTask.php'),
			'default' => 100,
		),

		'project_name'=>    array(
			'name'=>'project_name',
			'rname'=>'name',
			'id_name'=>'project_id',
			'vname'=>'LBL_PARENT_NAME',
			'type'=>'relate',
            'join_name'=>'project',
			'table'=>'project',
			'isnull'=>'true',
			'module'=>'Project',
            'link'=>'project_name_link',
			'massupdate'=>false,
			'source'=>'non-db'),

  		'notes' =>
  		array (
  			'name' => 'notes',
    		'type' => 'link',
    		'relationship' => 'project_tasks_notes',
    		'source'=>'non-db',
				'vname'=>'LBL_NOTES',
  		),
		'tasks' =>
  			array (
  			'name' => 'tasks',
    		'type' => 'link',
    		'relationship' => 'project_tasks_tasks',
    		'source'=>'non-db',
				'vname'=>'LBL_TASKS',
  		),
  		'meetings' =>
  			array (
  			'name' => 'meetings',
    		'type' => 'link',
    		'relationship' => 'project_tasks_meetings',
    		'source'=>'non-db',
				'vname'=>'LBL_MEETINGS',
  		),
		'calls' =>
  			array (
  			'name' => 'calls',
    		'type' => 'link',
    		'relationship' => 'project_tasks_calls',
    		'source'=>'non-db',
				'vname'=>'LBL_CALLS',
  		),

  		'emails' =>
  			array (
  			'name' => 'emails',
    		'type' => 'link',
    		'relationship' => 'emails_project_task_rel',/* reldef in emails */
    		'source'=>'non-db',
				'vname'=>'LBL_EMAILS',
  		),
        'projects' =>
            array (
            'name' => 'projects',
            'type' => 'link',
            'relationship' => 'projects_project_tasks',
            'source'=>'non-db',
                'vname'=>'LBL_LIST_PARENT_NAME',
        ),
  'created_by_link' =>
  array (
        'name' => 'created_by_link',
    'type' => 'link',
    'relationship' => 'project_tasks_created_by',
    'vname' => 'LBL_CREATED_BY_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
  ),
  'modified_user_link' =>
  array (
        'name' => 'modified_user_link',
    'type' => 'link',
    'relationship' => 'project_tasks_modified_user',
    'vname' => 'LBL_MODIFIED_BY_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
  ),
  'project_name_link' =>
  array (
    'name' => 'project_name_link',
    'type' => 'link',
    'relationship' => 'projects_project_tasks',
    'vname' => 'LBL_PROJECT_NAME',
    'link_type' => 'one',
    'module'=>'Project',
    'bean_name'=>'Project',
    'source'=>'non-db',
  ),
  'assigned_user_link' =>
  array (
        'name' => 'assigned_user_link',
    'type' => 'link',
    'relationship' => 'project_tasks_assigned_user',
    'vname' => 'LBL_ASSIGNED_TO_USER',
    'link_type' => 'one',
    'module'=>'Users',
    'bean_name'=>'User',
    'source'=>'non-db',
  ),
'assigned_user_name' =>
array (
	'name' => 'assigned_user_name',
	'rname' => 'user_name',
	'id_name' => 'assigned_user_id',
	'vname' => 'LBL_ASSIGNED_USER_NAME',
	'type' => 'relate',
	'table' => 'users',
	'module' => 'Users',
	'dbType' => 'varchar',
    'link'=>'assigned_user_link',
	'len' => '255',
	'source'=>'non-db',
    'exportable'=> true,
	),

	),
	'indices' => array(
		array(
			'name' =>'proj_tasks_primary_key_idx',
			'type' =>'primary',
			'fields'=>array('id')
		),
	),

 'relationships' => array (
	'project_tasks_notes' => array('lhs_module'=> 'ProjectTask', 'lhs_table'=> 'project_task', 'lhs_key' => 'id',
							  'rhs_module'=> 'Notes', 'rhs_table'=> 'notes', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'ProjectTask')
	,'project_tasks_tasks' => array('lhs_module'=> 'ProjectTask', 'lhs_table'=> 'project_task', 'lhs_key' => 'id',
							  'rhs_module'=> 'Tasks', 'rhs_table'=> 'tasks', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'ProjectTask')
    ,'project_tasks_meetings' => array('lhs_module'=> 'ProjectTask', 'lhs_table'=> 'project_task', 'lhs_key' => 'id',
							  'rhs_module'=> 'Meetings', 'rhs_table'=> 'meetings', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'ProjectTask')
	,'project_tasks_calls' => array('lhs_module'=> 'ProjectTask', 'lhs_table'=> 'project_task', 'lhs_key' => 'id',
							  'rhs_module'=> 'Calls', 'rhs_table'=> 'calls', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'ProjectTask')
	,'project_tasks_emails' => array('lhs_module'=> 'ProjectTask', 'lhs_table'=> 'project_task', 'lhs_key' => 'id',
							  'rhs_module'=> 'Emails', 'rhs_table'=> 'emails', 'rhs_key' => 'parent_id',
							  'relationship_type'=>'one-to-many', 'relationship_role_column'=>'parent_type',
							  'relationship_role_column_value'=>'ProjectTask')

  ,'project_tasks_assigned_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'ProjectTask', 'rhs_table'=> 'project_task', 'rhs_key' => 'assigned_user_id',
   'relationship_type'=>'one-to-many')

   ,'project_tasks_modified_user' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'ProjectTask', 'rhs_table'=> 'project_task', 'rhs_key' => 'modified_user_id',
   'relationship_type'=>'one-to-many')

   ,'project_tasks_created_by' =>
   array('lhs_module'=> 'Users', 'lhs_table'=> 'users', 'lhs_key' => 'id',
   'rhs_module'=> 'ProjectTask', 'rhs_table'=> 'project_task', 'rhs_key' => 'created_by',
   'relationship_type'=>'one-to-many')
),
);

VardefManager::createVardef('ProjectTask','ProjectTask', array(
'team_security',
));

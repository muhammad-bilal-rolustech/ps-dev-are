<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * Table definition file for the project_task table
 *
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2005 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: vardefs.php 16769 2006-09-21 00:59:27 +0000 (Thu, 21 Sep 2006) jenny $
$dictionary['ProjectTask'] = array('audited'=>true,
	'table' => 'project_task',
	'unified_search' => true,
	'fields' => array(
		'id' => array(
			'name' => 'id',
			'vname' => 'LBL_ID',
			'required' => true,
			'type' => 'id',
			'reportable'=>false,
		),
		'date_entered' => array(
			'name' => 'date_entered',
			'vname' => 'LBL_DATE_ENTERED',
			'type' => 'datetime',
			'required' => true,
		),
		'date_modified' => array(
			'name' => 'date_modified',
			'vname' => 'LBL_DATE_MODIFIED',
			'type' => 'datetime',
			'required' => true,
		),
        'project_id' => array(
            'name' => 'project_id',
            'vname' => 'LBL_PROJECT_ID',
            'required' => false,
            'type' => 'id',
        ),    
        'project_task_id' => array(
            'name' => 'project_task_id',
            'vname' => 'LBL_PROJECT_TASK_ID',
            'required' => true,
            'type' => 'int',
        ),    
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'required' => true,
            'dbType' => 'varchar',
            'type' => 'name',
            'len' => 50,
            'unified_search' => true,
        ),        
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'required' => false,
            'type' => 'text',
        ),     
        'resource_id' => array(
            'name' => 'resource_id',
            'vname' => 'LBL_RESOURCE',
            'required' => false,
            'type' => 'text',
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
            'validation'=>array('type' => 'isbefore', 'compareto'=>'date_due', 'blank' => true),
            'audited'=>true,
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
            'validation'=>array('type' => 'isbefore', 'compareto'=>'date_due', 'blank' => true),
            'audited'=>true,
            
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
            'required' => true,
            'type' => 'text',
        ),              
        'actual_duration' => array(
            'name' => 'actual_duration',
            'vname' => 'LBL_DURATION',
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
        'parent_task_id' => array(
            'name' => 'parent_task_id',
            'vname' => ' LBL_PARENT_TASK_ID',
            'required' => false,
            'type' => 'int',
        ), 
        
		'assigned_user_id' => array(
			'name' => 'assigned_user_id',
			'rname' => 'user_name',
			'id_name' => 'assigned_user_id',
			'type' => 'assigned_user_name',
			'vname' => 'LBL_ASSIGNED_USER_ID',
			'required' => false,
			'dbType' => 'id',
			'table' => 'users',
			'isnull' => false,
			'reportable'=>true,
			'audited'=>true,
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
		),
		//BEGIN SUGARCRM flav=pro ONLY 
		'team_id' => array(
			'name' => 'team_id',
			'vname' => 'LBL_TEAM_ID',
			'type' => 'id',
			'audited'=>true,
		),
      'team_name' => 
          array (
            'name' => 'team_name',
            'rname' => 'name',
            'id_name' => 'team_id',
            'vname' => 'LBL_TEAM',
            'type' => 'relate',
            'table' => 'teams',
            'isnull' => 'true',
            'module' => 'Teams',
            'link'=>'team_link',
            'massupdate' => false,
            'source'=>'non-db',
            'dbType' => 'varchar',
            'len' => 36,
          ),        
		//END SUGARCRM flav=pro ONLY 
/*
		'date_due' => array(
			'name' => 'date_due',
			'vname' => 'LBL_DATE_DUE',
			'type' => 'date',
			'rel_field' => 'time_due',
			'audited'=>true,
			
		),
		'time_due' => array(
			'name' => 'time_due',
			'vname' => 'LBL_TIME_DUE',
			'type' => 'time',
			'rel_field' => 'date_due',
			'reportable'=>false,
		),

		'time_start' => array(
			'name' => 'time_start',
			'vname' => 'LBL_TIME_START',
			'type' => 'time',
			'rel_field' => 'date_start',
			'reportable'=>false,
		),
		'parent_id' => array(
			'name' => 'parent_id',
			'vname' => 'LBL_PARENT_ID',
			'required' => true,
			'type' => 'id',
			'reportable'=>false,
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
		'depends_on_id' => array(
			'name' => 'depends_on_id',
			'vname' => 'LBL_DEPENDS_ON_ID',
			'required' => false,
			'type' => 'id',
			'reportable'=>false,
		),
        */
		'milestone_flag' => array(
			'name' => 'milestone_flag',
			'vname' => 'LBL_MILESTONE_FLAG',
			'type' =>'bool',
			'dbType'=>'enum',
			'options'=>'1|0',
			'required' => false,
		),
        /*
		'estimated_effort' => array(
			'name' => 'estimated_effort',
			'vname' => 'LBL_ESTIMATED_EFFORT',
			'required' => false,
			'type' => 'int',
		),
        */
		'actual_effort' => array(
			'name' => 'actual_effort',
			'vname' => 'LBL_ACTUAL_EFFORT',
			'required' => false,
			'type' => 'int',
		),
        /*
		'utilization' => array(
			'name' => 'utilization',
			'vname' => 'LBL_UTILIZATION',
			'required' => false,
			'type' => 'int',
			'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
			'default' => 100,
		),
		'percent_complete' => array(
			'name' => 'percent_complete',
			'vname' => 'LBL_PERCENT_COMPLETE',
			'required' => false,
			'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
			'default' => 0,
			'type' => 'int',
			'audited'=>true,
			
		),*/
		'deleted' => array(
			'name' => 'deleted',
			'vname' => 'LBL_DELETED',
			'type' => 'bool',
			'required' => true,
			'default' => '0',
			'reportable'=>false,
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
                'vname'=>'LBL_LIST_PROJECT_NAME',
        ),         
// BEGIN SUGARCRM flav=pro ONLY 
  'team_link' =>
  array (
        'name' => 'team_link',
    'type' => 'link',
    'relationship' => 'project_tasks_team',
    'vname' => 'LBL_TEAMS_LINK',
    'link_type' => 'one',
    'module'=>'Teams',
    'bean_name'=>'Team',
    'source'=>'non-db',
  ),
// END SUGARCRM flav=pro ONLY 
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
    'module'=>'Projects',
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
	'link'=>'users',
	'len' => '255',
	'source'=>'non-db',
	),
        'activities' => array(
            'name' => 'activities',
            'type' => 'link',
            'relationship' => 'project_tasks_activities',
            'vname' => 'LBL_ACTIVITIES',
            'link_type' => 'many',
            'module' => 'Activities',
            'bean_name' => 'Activity',
            'source' => 'non-db',
        ),
	),
	'indices' => array(
		array(
			'name' =>'project_tasks_primary_key_index',
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
// BEGIN SUGARCRM flav=pro ONLY 
   ,'project_tasks_team' =>
   array('lhs_module'=> 'Teams', 'lhs_table'=> 'teams', 'lhs_key' => 'id',
   'rhs_module'=> 'ProjectTask', 'rhs_table'=> 'project_task', 'rhs_key' => 'team_id',
   'relationship_type'=>'one-to-many')
// END SUGARCRM flav=pro ONLY 

),
);

?>

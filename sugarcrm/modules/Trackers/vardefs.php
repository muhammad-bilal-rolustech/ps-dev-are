<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
$dictionary['Tracker'] = array(
    'table' => 'tracker',
    'fields' => array(
        'id'=>array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'int',
            'len' => '11',
            'isnull' => 'false',
            'auto_increment' => true,
            'readonly' => true,
            'reportable'=>true,
        ),
	    'monitor_id'=>array (
		    'name' => 'monitor_id',
		    'vname' => 'LBL_MONITOR_ID',
		    'type' => 'id',
		    'required'=>true,
		    'reportable'=>false,
	    ),
        'user_id'=>array(
            'name' => 'user_id',
            'vname' => 'LBL_USER_ID',
			'type' => 'id',
            'len' => '36',
            'isnull' => 'false',
        ),
        'module_name'=>array(
            'name' => 'module_name',
            'vname' => 'LBL_MODULE_NAME',
            'type' => 'varchar',
            'len' => '255',
            'isnull' => 'false',
        ),
        'item_id'=>array(
            'name' => 'item_id',
            'vname' => 'LBL_ITEM_ID',
            'type' => 'id',
            'len' => '36',
            'isnull' => 'false',
        ),
        'item_summary'=>array(
            'name' => 'item_summary',
            'vname' => 'LBL_ITEM_SUMMARY',
            'type' => 'varchar',
            'len' => '255',
            'isnull' => 'false',
        ),
		//BEGIN SUGARCRM flav=pro ONLY
		'team_id'=>array(
			'name' => 'team_id',
			'vname' => 'LBL_TEAM_ID',
			'type' => 'id',
			'len' => '36',
		),
		//END SUGARCRM flav=pro ONLY
        'date_modified'=>array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_LAST_ACTION',
            'type' => 'datetime',
            'isnull' => 'false',
        ),
        'action'=>array(
            'name' => 'action',
            'vname' => 'LBL_ACTION',
            'type' => 'varchar',
            'len' => '255',
            'isnull' => 'false',
        ),
        'session_id'=>array(
            'name' => 'session_id',
            'vname' => 'LBL_SESSION_ID',
            'type' => 'varchar',
            'len' => '36',
            'isnull' => 'true',
        ),
        'visible'=>array(
            'name' => 'visible',
            'vname' => 'LBL_VISIBLE',
            'type' => 'bool',
            'len' => '1',
            'default' => '0',
        ),
	    'deleted' =>array (
		    'name' => 'deleted',
		    'vname' => 'LBL_DELETED',
		    'type' => 'bool',
		    'default' => '0',
		    'reportable'=>false,
		    'comment' => 'Record deletion indicator'
		),
//BEGIN SUGARCRM flav=pro ONLY
		'assigned_user_link'=>array (
		    'name' => 'assigned_user_link',
		    'type' => 'link',
		    'relationship' => 'tracker_user_id',
		    'vname' => 'LBL_ASSIGNED_TO_USER',
		    'link_type' => 'one',
		    'module'=>'Users',
		    'bean_name'=>'User',
		    'source'=>'non-db',
		),
//END SUGARCRM flav=pro ONLY        
		'monitor_id_link'=>array (
		    'name' => 'monitor_id_link',
		    'type' => 'link',
		    'relationship' => 'tracker_monitor_id',
		    'vname' => 'LBL_MONITOR_ID',
		    'link_type' => 'one',
		    'module'=>'TrackerPerfs',
		    'bean_name'=>'TrackerPerf',
		    'source'=>'non-db',
		),
    ) ,

    //indices
    'indices' => array(
        array(
            'name' => 'tracker_pk',
            'type' => 'primary',
            'fields' => array(
                'id'
            )
        ) ,
        array(
            'name' => 'idx_tracker_iid',
            'type' => 'index',
            'fields' => array(
                'item_id',
            ),
        ),
        array(
            // shortened name to comply with Oracle length restriction
            'name' => 'idx_tracker_userid_vis_id',
            'type' => 'index',
            'fields' => array(
                'user_id',
                'visible',
                'id',
            ),
        ),
        array(
        	// shortened name to comply with Oracle length restriction
            'name' => 'idx_tracker_userid_itemid_vis',
            'type' => 'index',
            'fields' => array(
                'user_id',
                'item_id',
                'visible'
            ),
        ),
        array(
            'name' => 'idx_tracker_monitor_id',
            'type' => 'index',
            'fields' => array(
                'monitor_id',
            ),
        ),
        array(
            'name' => 'idx_tracker_date_modified',
            'type' => 'index',
            'fields' => array(
                'date_modified',
            ),
        ),
    ),

    //relationships
 	'relationships' => array (
	  'tracker_monitor_id' =>
		   array(
				'lhs_module'=> 'TrackerPerfs', 'lhs_table'=> 'tracker_perf', 'lhs_key' => 'monitor_id',
		   		'rhs_module'=> 'Trackers', 'rhs_table'=> 'tracker', 'rhs_key' => 'monitor_id',
		   		'relationship_type'=>'one-to-one'
		   )
   	),
);
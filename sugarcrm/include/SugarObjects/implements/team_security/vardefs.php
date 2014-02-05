<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
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
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

$vardefs = array(
  'visibility' => array('TeamSecurity' => true),
  'fields' => array(
	 //BEGIN SUGARCRM flav=pro ONLY
	'team_id' =>
		array (
			'name' => 'team_id',
			'vname' => 'LBL_TEAM_ID',
			'group'=>'team_name',
			'reportable'=>false,
			'dbType' => 'id',
			'type' => 'team_list',
			'audited'=>true,
			/*
			'source' => 'non-db',
			*/
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Team ID for the account'
		),
		'team_set_id' =>
		array (
			'name' => 'team_set_id',
			'rname' => 'id',
			'id_name' => 'team_set_id',
			'vname' => 'LBL_TEAM_SET_ID',
			'type' => 'id',
		    'audited' => true,
		    'studio' => 'false',
			'dbType' => 'id',
            'duplicate_on_record_copy' => 'always',
            'full_text_search' => array('enabled' => true),
        ),
		'team_count' =>
		array (
			'name' => 'team_count',
			'rname' => 'team_count',
			'id_name' => 'team_id',
			'vname' => 'LBL_TEAMS',
			'join_name'=>'ts1',
			'table' => 'team_sets',
			'type' => 'relate',
            'required' => 'true',
			'table' => 'teams',
			'isnull' => 'true',
			'module' => 'Teams',
			'link' => 'team_count_link',
			'massupdate' => false,
			'dbType' => 'int',
			'source' => 'non-db',
			'importable' => 'false',
			'reportable'=>false,
		    'duplicate_merge' => 'disabled',
            'duplicate_on_record_copy' => 'always',
            'studio' => 'false',
		    'hideacl' => true,
		),
		'team_name' =>
		array (
			'name' => 'team_name',
			'db_concat_fields'=> array(0=>'name', 1=>'name_2'),
		    'sort_on' => 'tj.name',
		    'join_name' => 'tj',
			'rname' => 'name',
			'id_name' => 'team_id',
			'vname' => 'LBL_TEAMS',
			'type' => 'relate',
            'required' => 'true',
			'table' => 'teams',
			'isnull' => 'true',
			'module' => 'Teams',
			'link' => 'team_link',
			'massupdate' => true,
			'dbType' => 'varchar',
			'source' => 'non-db',
			'len' => 36,
			'custom_type' => 'teamset',
            'studio' => array(
                   //BEGIN SUGARCRM flav=pro ONLY
                   // Bug 56832 - Exclude list/detail/edit view from portal
                   'portallistview' => false,
                   'portalrecordview' => false,
                   //END SUGARCRM flav=pro ONLY
               ), // don't show in studio fields list
            'duplicate_on_record_copy' => 'always',
        ),
		'team_link' =>
	    array (
	      'name' => 'team_link',
	      'type' => 'link',
	      'relationship' => strtolower($module). '_team',
	      'vname' => 'LBL_TEAMS_LINK',
	      'link_type' => 'one',
	      'module' => 'Teams',
	      'bean_name' => 'Team',
	      'source' => 'non-db',
	      'duplicate_merge' => 'disabled',
	      'studio' => 'false',
        ),
	    'team_count_link' =>
  			array (
  			'name' => 'team_count_link',
    		'type' => 'link',
    		'relationship' => strtolower($module).'_team_count_relationship',
            'link_type' => 'one',
		    'module' => 'Teams',
		    'bean_name' => 'TeamSet',
		    'source' => 'non-db',
		    'duplicate_merge' => 'disabled',
  			'reportable'=>false,
  			'studio' => 'false',
            ),
  		'teams' =>
		array (
		'name' => 'teams',
        'type' => 'link',
		'relationship' => strtolower($module).'_teams',
		'bean_filter_field' => 'team_set_id',
		'rhs_key_override' => true,
        'source' => 'non-db',
		'vname' => 'LBL_TEAMS',
		'link_class' => 'TeamSetLink',
		'link_file' => 'modules/Teams/TeamSetLink.php',
		'studio' => 'false',
		'reportable'=>false,
        ),
	//END SUGARCRM flav=pro ONLY
),

'relationships'=>array(
	strtolower($module).'_team_count_relationship' =>
		 array(
		 	'lhs_module'=> 'Teams',
		 	'lhs_table'=> 'team_sets',
		 	'lhs_key' => 'id',
    		'rhs_module'=> $module,
    		'rhs_table'=> $table_name,
    		'rhs_key' => 'team_set_id',
   			'relationship_type'=>'one-to-many'
		 ),
	strtolower($module).'_teams' =>
		array (
			'lhs_module'        => $module,
            'lhs_table'         => $table_name,
            'lhs_key'           => 'team_set_id',
            'rhs_module'        => 'Teams',
            'rhs_table'         => 'teams',
            'rhs_key'           => 'id',
            'relationship_type' => 'many-to-many',
            'join_table'        => 'team_sets_teams',
            'join_key_lhs'      => 'team_set_id',
            'join_key_rhs'      => 'team_id',
		),
//BEGIN SUGARCRM flav=pro ONLY
   strtolower($module). '_team' =>
   array('lhs_module'=> 'Teams', 'lhs_table'=> 'teams', 'lhs_key' => 'id',
    'rhs_module'=> $module, 'rhs_table'=> $table_name, 'rhs_key' => 'team_id',
   'relationship_type'=>'one-to-many'),
//END SUGARCRM flav=pro ONLY
),
'indices' => array(
		'team_set_'.strtolower($table_name) => array(
			'name' => 'idx_'.strtolower($table_name).'_tmst_id',
			'type' => 'index',
			'fields' => array('team_set_id')
		),
	)
);
?>

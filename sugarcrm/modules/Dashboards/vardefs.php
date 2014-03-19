<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

$dictionary['Dashboard'] = array(
  'table' => 'dashboards',
  'fields' => 
  array (
    'dashboard_module' =>
    array (
      'required' => false,
      'name' => 'dashboard_module',
      'vname' => 'LBL_DASHBOARD_MODULE',
      'type' => 'varchar',
      'dbType' => 'varchar',
      'len' => 100,
      'massupdate' => 0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
      ),
    'view_name' =>
    array (
      'required' => false,
      'name' => 'view_name',
      'vname' => 'LBL_VIEW',
      'type' => 'varchar',
      'dbType' => 'varchar',
      'len' => 100,
      'massupdate' => 0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
    ),
    'metadata' => 
    array (
      'required' => false,
      'name' => 'metadata',
      'vname' => 'LBL_METADATA',
      'type' => 'json',
      'dbType' => 'text',
      'massupdate' => 0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'calculated' => false,
    ),
      'dashboard_type' => array(
          'name' => 'dashboard_type',
          'vname' => 'LBL_DASHBOARD_TYPE',
          'type' => 'varchar',
          'len' => '100',
          'comment' => 'The type of dashboard: dashboard, help-dashboard, etc',
          'default' => 'dashboard'
      ),
  ),
  'indices' => array (
    array ('name' => 'user_module_view', 'type' => 'index', 'fields' => array('assigned_user_id','dashboard_module', 'view_name')),
  ),
  'relationships' => 
  array (
  ),      
);

if (!class_exists('VardefManager')){
    require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('Dashboards','Dashboard', array('basic', 'assignable'));

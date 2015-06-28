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


$dictionary['pmse_Project'] = array(
	'table'=>'pmse_project',
	'audited'=>false,
	'activity_enabled'=>true,
		'duplicate_merge'=>true,
		'fields'=>array (
  'prj_uid' => 
  array (
    'required' => true,
    'name' => 'prj_uid',
    'vname' => 'LBL_PRJ_UID',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '36',
    'size' => '20',
  ),
  'prj_target_namespace' => 
  array (
    'required' => false,
    'name' => 'prj_target_namespace',
    'vname' => 'LBL_PRJ_TARGET_NAMESPACE',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_expression_language' => 
  array (
    'required' => false,
    'name' => 'prj_expression_language',
    'vname' => 'LBL_PRJ_EXPRESSION_LANGUAGE',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_type_language' => 
  array (
    'required' => false,
    'name' => 'prj_type_language',
    'vname' => 'LBL_PRJ_TYPE_LANGUAGE',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_exporter' => 
  array (
    'required' => false,
    'name' => 'prj_exporter',
    'vname' => 'LBL_PRJ_EXPORTER',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_exporter_version' => 
  array (
    'required' => false,
    'name' => 'prj_exporter_version',
    'vname' => 'LBL_PRJ_EXPORTER_VERSION',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_author' => 
  array (
    'required' => false,
    'name' => 'prj_author',
    'vname' => 'LBL_PRJ_AUTHOR',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_author_version' => 
  array (
    'required' => false,
    'name' => 'prj_author_version',
    'vname' => 'LBL_PRJ_AUTHOR_VERSION',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'prj_original_source' => 
  array (
    'required' => false,
    'name' => 'prj_original_source',
    'vname' => 'LBL_PRJ_ORIGINAL_SOURCE',
    'type' => 'varchar',
    'massupdate' => false,
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
    'len' => '255',
    'size' => '20',
  ),
  'name' => 
  array (
    'name' => 'name',
    'vname' => 'LBL_NAME',
    'type' => 'name',
    'dbType' => 'varchar',
    'len' => '255',
    'unified_search' => false,
    'full_text_search' => 
    array (
      'boost' => '3',
    ),
    'required' => true,
    'importable' => 'required',
    'duplicate_merge' => 'enabled',
    'merge_filter' => 'selected',
    'duplicate_on_record_copy' => 'always',
    'massupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'duplicate_merge_dom_value' => '3',
    'audited' => false,
    'reportable' => true,
    'calculated' => false,
    'size' => '20',
  ),
  'prj_status' =>
      array (
        'name' => 'prj_status',
        'vname' => 'LBL_PRJ_STATUS',
        'type' => 'enum',
        'len' => 10,
        'options' => 'process_definition_status_dom',
        'comment' => 'Process Definition status (ex: Enabled, Disabled)',
        'default' => 'INACTIVE',
        'duplicate_on_record_copy' => 'no',
      ),
  'prj_module' => 
  array (
    'required' => true,
    'name' => 'prj_module',
    'vname' => 'LBL_PRJ_MODULE',
    'type' => 'enum',
    'massupdate' => true,
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
    'len' => 100,
    'size' => '20',
    'options' => '',
    'studio' => 'visible',
    'dependency' => false,
    'function' =>
      array (
        'name' => 'getTargetsModules',
        'include' => 'modules/pmse_Project/pmse_ProjectHelper.php',
      ),
  ),
),
	'relationships'=>array (
),
    'optimistic_locking' => true,
    'unified_search' => true,
    'acls' => array(
        'SugarACLDeveloperForTarget' => array(
            'targetModuleField' => 'prj_module', 'allowUserRead' => false
        )
    ),
    'visibility' => array(
        'TargetModuleDeveloperVisibility' => array('targetModuleField' => 'prj_module')
    ),
    'hidden_to_role_assignment' => true,
    // @TODO Fix the Default and Basic SugarObject templates so that Basic
    // implements Default. This would allow the application of various
    // implementations on Basic without forcing Default to have those so that
    // situations like this - implementing taggable - doesn't have to apply to
    // EVERYTHING. Since there is no distinction between basic and default for
    // sugar objects templates yet, we need to forecefully remove the taggable
    // implementation fields. Once there is a separation of default and basic
    // templates we can safely remove these as this module will implement
    // default instead of basic.
    'ignore_templates' => array(
        'taggable',
    ),
);
if (!class_exists('VardefManager')){
        require_once 'include/SugarObjects/VardefManager.php';
}
VardefManager::createVardef('pmse_Project','pmse_Project', array('basic','team_security','assignable'));

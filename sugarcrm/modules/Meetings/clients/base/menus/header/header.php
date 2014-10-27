<?php
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
$module_name = 'Meetings';
$viewdefs[$module_name]['base']['menu']['header'] = array(
    array(
        'route' => "#{$module_name}/create",
        'label' => 'LNK_NEW_MEETING',
        'acl_action' => 'create',
        'acl_module' => $module_name,
        'icon' => 'icon-plus',
    ),
    array(
        'route' => "#{$module_name}",
        'label' => 'LNK_MEETING_LIST',
        'acl_action' => 'list',
        'acl_module' => $module_name,
        'icon' => 'icon-reorder',
    ),
    array(
        'route' => "#bwc/index.php?module=Import&action=Step1&import_module={$module_name}&return_module={$module_name}&return_action=index",
        'label' => 'LNK_IMPORT_MEETINGS',
        'acl_action' => 'import',
        'acl_module' => $module_name,
        'icon' => 'icon-upload',
    ),
    //BEGIN SUGARCRM flav=int ONLY
    array(
        'route' => "#bwc/index.php?module=Reports&action=index&view="
        . strtolower($module_name)
        . "&query=true&report_module={$module_name}",
        'label' => 'LBL_ACTIVITIES_REPORTS',
        'acl_action' => '',
        'acl_module' => '',
        'icon' => 'icon-bar-chart',
    ),
    //END SUGARCRM flav=int ONLY
);

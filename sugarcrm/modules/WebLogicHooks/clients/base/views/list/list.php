<?php

/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

$viewdefs['WebLogicHooks']['base']['view']['list'] = array(
    'panels' => array(
        array(
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => array(
                array(
                    'name' => 'name',
                    'enabled' => true,
                    'sortable' => true,
                    'link' => true,
                ),
                array(
                    'name' => 'url',
                    'enabled' => true,
                    'sortable' => true,
                ),
                array(
                    'name' => 'webhook_target_module',
                    'enabled' => true,
                    'sortable' => true,
                ),
                array(
                    'name' => 'trigger_event',
                    'enabled' => true,
                    'sortable' => true,
                ),
                array(
                    'name' => 'request_method',
                    'enabled' => true,
                    'sortable' => true,
                ),
            )
        ),
    ),
    'dependencies' => array(
        array(
            'hooks' => array('all'),
            'trigger' => 'true',
            'triggerFields' => array('trigger_event'),
            'onload' => true,
            'actions' => array(
                array(
                    'action' => 'SetVisibility',
                    'params' => array(
                        'target' => 'webhook_target_module',
                        'value' => 'not(isInList($trigger_event, createList("after_login", "after_logout", "login_failed")))'
                    )
                ),
                array(
                    'action' => 'SetValue',
                    'params' => array(
                        'target' => 'webhook_target_module',
                        'value' => '"Users"'
                    )
                )
            )
        )
    )
);

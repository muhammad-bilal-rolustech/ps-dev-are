<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc.  All rights reserved.
 ********************************************************************************/

$viewdefs['Emails']['base']['view']['subpanel-for-opportunities'] = array(
    'hide_history_contacts_emails' => true,
    'panels' =>
        array(
            array(
                'name' => 'panel_header',
                'label' => 'LBL_PANEL_1',
                'fields' =>
                    array(
                        array(
                            'label' => 'LBL_LIST_SUBJECT',
                            'enabled' => true,
                            'default' => true,
                            'name' => 'name',
                            'link' => 'true'
                        ),
                        array(
                            'label' => 'LBL_LIST_STATUS',
                            'enabled' => true,
                            'default' => true,
                            'name' => 'status',
                        ),
                        array(
                            'label' => 'LBL_DATE_CREATED',
                            'enabled' => true,
                            'default' => true,
                            'name' => 'date_entered',
                        ),
                        array(
                            'label' => 'LBL_DATE_MODIFIED',
                            'enabled' => true,
                            'default' => true,
                            'name' => 'date_modified',
                        ),
                        array(
                            'name' => 'assigned_user_name',
                            'target_record_key' => 'assigned_user_id',
                            'target_module' => 'Employees',
                            'label' => 'LBL_LIST_ASSIGNED_TO_NAME',
                            'enabled' => true,
                            'default' => true,
                        ),
                    ),
            ),
        ),
);

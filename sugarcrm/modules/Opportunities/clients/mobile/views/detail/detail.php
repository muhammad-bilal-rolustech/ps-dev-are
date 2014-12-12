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
/*********************************************************************************
 * $Id$
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$fields = array(
    array(
        'name' => 'name',
        'displayParams' => array(
            'required' => true,
            'wireless_edit_only' => true,
        )
    ),
    'amount',
    'account_name',
    'date_closed',
    'sales_status',
    //'sales_stage',
    'assigned_user_name',
    'team_name',
);

// here we add `sales_stage` for PRO/CORP flavors
//BEGIN SUGARCRM flav=pro && flav!=ent && flav!=ult ONLY
$fields = array(
    array(
        'name' => 'name',
        'displayParams' => array(
            'required' => true,
            'wireless_edit_only' => true,
        )
    ),
    'amount',
    'account_name',
    'date_closed',
    // enable sales stage for `pro` and `corp` editions
    'sales_stage',
    //'sales_status',
    'assigned_user_name',
    'team_name',
);
//END SUGARCRM flav=pro && flav!=ent && flav!=ult ONLY

$viewdefs['Opportunities']['mobile']['view']['detail'] = array(
    'templateMeta' => array(
        'maxColumns' => '1',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
        ),
    ),
    'panels' => array(
        array(
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => $fields
        )
    ),
);
?>

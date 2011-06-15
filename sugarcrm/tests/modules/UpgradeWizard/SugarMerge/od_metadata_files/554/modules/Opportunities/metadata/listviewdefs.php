<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/
$listViewDefs['Opportunities'] = array(
	'NAME' => array(
		'width'   => '30',  
		'label'   => 'LBL_LIST_OPPORTUNITY_NAME', 
		'link'    => true,
        'default' => true),
	'ACCOUNT_NAME' => array(
		'width'   => '20', 
		'label'   => 'LBL_LIST_ACCOUNT_NAME', 
		'id'      => 'ACCOUNT_ID',
        'module'  => 'Accounts',
		'link'    => true,
        'default' => true,
        'sortable'=> true,
        'ACLTag' => 'ACCOUNT',
        'contextMenu' => array('objectType' => 'sugarAccount', 
                               'metaData' => array('return_module' => 'Contacts', 
                                                   'return_action' => 'ListView', 
                                                   'module' => 'Accounts',
                                                   'return_action' => 'ListView', 
                                                   'parent_id' => '{$ACCOUNT_ID}', 
                                                   'parent_name' => '{$ACCOUNT_NAME}', 
                                                   'account_id' => '{$ACCOUNT_ID}', 
                                                   'account_name' => '{$ACCOUNT_NAME}',
                                                   ),
                              ),
        'related_fields' => array('account_id')),
	'SALES_STAGE' => array(
		'width'   => '10',  
		'label'   => 'LBL_LIST_SALES_STAGE',
        'default' => true), 
	'AMOUNT_USDOLLAR' => array(
		'width'   => '10', 
		'label'   => 'LBL_LIST_AMOUNT',
        'align'   => 'right',
        'default' => true,
        'currency_format' => true,
	),  
    'OPPORTUNITY_TYPE' => array(
        'width' => '15', 
        'label' => 'LBL_TYPE'),
    'LEAD_SOURCE' => array(
        'width' => '15', 
        'label' => 'LBL_LEAD_SOURCE'),
    'NEXT_STEP' => array(
        'width' => '10', 
        'label' => 'LBL_NEXT_STEP'),
    'PROBABILITY' => array(
        'width' => '10', 
        'label' => 'LBL_PROBABILITY'),
	'DATE_CLOSED' => array(
		'width' => '10', 
		'label' => 'LBL_LIST_DATE_CLOSED',
        'default' => true),
    'DATE_ENTERED' => array(
        'width' => '10', 
        'label' => 'LBL_DATE_ENTERED'),
    'CREATED_BY_NAME' => array(
        'width' => '10', 
        'label' => 'LBL_CREATED'),
	'TEAM_NAME' => array(
		'width' => '5', 
		'label' => 'LBL_LIST_TEAM',
        'default' => false),
	'ASSIGNED_USER_NAME' => array(
		'width' => '5', 
		'label' => 'LBL_LIST_ASSIGNED_USER',
        'default' => true),
    'MODIFIED_BY_NAME' => array(
        'width' => '5', 
        'label' => 'LBL_MODIFIED')
);

?>
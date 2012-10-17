<?php
//FILE SUGARCRM flav=pro || flav=sales ONLY
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['Bugs']['base']['view']['record'] = array(
    'buttons' =>
    array(
        array(
            'name' => 'edit_button',
            'type' => 'button',
            'label' => 'Edit',
            'route' => array(
                'action' => 'edit'
            ),
            'primary' => true,
        ),
    ),
    'panels' => array(
        array(
            'name' => 'panel_head',
            'label' => 'LBL_PANEL_1',
            'columns' => 2,
            'labels' => false,
            'labelsOnTop' => false,
            'placeholders' => true,
            'fields' => array(
                array('name' => 'img', 'noedit' => true, 'span' => 2, 'label' => ''),
                array('name' => 'name', 'cell_css' => 'bigcell', 'css' => 'big', 'span' => 10, 'label' => '', 'placeholder' => 'LBL_NAME'),
            )
        ),
        array(
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_2',
            'columns' => 2,
            'labels' => false,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => array(
                array('name'=>'bug_number', 'noedit'=>true),
                array('name'=>'name', 'displayParams' => array ('size'=>75),),
                'priority',
                'type',
                'source',
                'status',
                'product_category',
                'resolution',
                'found_in_release',
                'fixed_in_release',
                array ('name' => 'description', 'nl2br' => true,),
                array ('name' => 'work_log', 'nl2br' => true,),
//BEGIN SUGARCRM flav=ent ONLY
                // hideIf is a legacy smarty thing .. seems that hideIf is mainly used for this specific check
                // semantically meaning: "hide unless portal enabled" .. TODO: implement equivalent functionality in sidecar 
                // perhaps create an hbt helper that can leverage app.cofig.on
                array('name'=>'portal_viewable', 'label' => 'LBL_SHOW_IN_PORTAL', 'hideIf' => 'empty($PORTAL_ENABLED)'),
//END SUGARCRM flav=ent ONLY
                'assigned_user_name',
//BEGIN SUGARCRM flav=pro ONLY
                array('name'=>'team_name', 'displayParams'=>array('required'=>true)),
//END SUGARCRM flav=pro ONLY
            ),
        ),
    ),
);
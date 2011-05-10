<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and 
 * (ii) the SugarCRM copyright notice 
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
require_once('modules/Reports/config.php');

class ReportsViewBuildreportmoduletree extends SugarView
{
    /**
     * @see SugarView::display()
     */
    public function display()
    {
        global $beanFiles, $beanList;
        if(empty($beanFiles)) {
            include('include/modules.php');
        }
        
        $ACLAllowedModules = getACLAllowedModules();
        $module_array = array();
        
        $module = SugarModule::get($_REQUEST['report_module'])->loadBean();
        $bean_name = $module->object_name;
        $linked_fields = $module->get_linked_fields();
        
        foreach($linked_fields as $linked_field)
        {
            $module->load_relationship($linked_field['name']);
            $field = $linked_field['name'];
            if(empty($module->$field) || (isset($linked_field['reportable']) &&
               $linked_field['reportable'] == false))
            {
                continue;
            }	
            $relationship = $module->$field->_relationship;
            if(empty($beanList[$relationship->lhs_module]) || empty($beanList[$relationship->rhs_module]))
            {
                continue;
            }
            // Bug 37311 - Don't allow reporting on relationships to the currencies module
            if($relationship->lhs_module == 'Currencies' || $relationship->rhs_module == 'Currencies') {
                continue;
            }
            
            $bean_is_lhs = $module->$field->_get_bean_position();
            if($bean_is_lhs == true && isset($beanList[$relationship->rhs_module])) {
                $link_bean = $beanList[$relationship->rhs_module];
                $link_module = $relationship->rhs_module;
            }
            else if (isset($beanList[$relationship->lhs_module])){
                $link_bean =  $beanList[$relationship->lhs_module];
                $link_module = $relationship->lhs_module;
            }
            if (!isset($ACLAllowedModules[$link_module])) {
                continue;
            }
            if(! empty($linked_field['vname']))
            {
                $linked_field['label'] = translate($linked_field['vname'], $_REQUEST['report_module']);
            } else {
                $linked_field['label'] =$linked_field['name'];
            }
            $linked_field['label'] = preg_replace('/:$/','',$linked_field['label']);
            $linked_field['label'] = addslashes($linked_field['label']);
            
            $module_array[] = $this->_populateNodeItem($bean_name,$link_module,$linked_field);
        }	
        
        // Sort alphabetically
        function compare_text($a, $b) { 
            return strnatcmp($a['text'], $b['text']); 
        } 
        usort($module_array, 'compare_text');
        
        $json = getJSONobj();
        echo $json->encode($module_array);
    }
    
    protected function _populateNodeItem($bean_name,$link_module,$linked_field)
    {
        $node = array();
        
        $node['text'] = $linked_field['label'];
        $node['href'] = "javascript:SUGAR.reports.populateFieldGrid('". $link_module . "','".$linked_field['relationship']."','".$bean_name."','".str_replace(array('&#039;', '&#39;'), "\'", $linked_field['label'])."');";
        $node['leaf'] = false;
        $node['category'] = $link_module;
        $node['relationship_name'] = $linked_field['relationship'];
        $node['link_name'] = $linked_field['name'];
        $node['link_module'] = $link_module;
        
        return $node;
    }
}
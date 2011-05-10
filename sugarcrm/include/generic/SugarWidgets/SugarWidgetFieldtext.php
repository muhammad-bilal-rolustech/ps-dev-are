<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
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

class SugarWidgetFieldText extends SugarWidgetFieldVarchar
{
    function SugarWidgetFieldText(&$layout_manager) {
        parent::SugarWidgetFieldVarchar($layout_manager);
        $this->reporter = $this->layout_manager->getAttribute('reporter');  
    }

    function queryFilterEquals(&$layout_def) {
        if( $this->reporter->db->dbType == 'mysql') {
            return parent::queryFilterEquals($layout_def);
        } 
        elseif( $this->reporter->db->dbType == 'mssql') {
            //return parent::queryFilterEquals($layout_def);
			return $this->_get_column_select($layout_def)." like '".$GLOBALS['db']->quote($layout_def['input_name0'])."'\n";
            
        }
        //BEGIN SUGARCRM flav=ent ONLY
        elseif ( $this->reporter->db->dbType == 'oci8') {
            return 'TO_CHAR(' . $this->_get_column_select($layout_def).") = '".$GLOBALS['db']->quote($layout_def['input_name0'])."'\n";
        }
        //END SUGARCRM flav=ent ONLY
    }

    function queryFilterNot_Equals_Str(&$layout_def) {
        if( $this->reporter->db->dbType == 'mysql') {
            return parent::queryFilterNot_Equals_Str($layout_def);
        } 
        elseif( $this->reporter->db->dbType == 'mssql') {
            return parent::queryFilterNot_Equals_Str($layout_def);
        }
        //BEGIN SUGARCRM flav=ent ONLY
        elseif ( $this->reporter->db->dbType == 'oci8') {
            return 'TO_CHAR(' . $this->_get_column_select($layout_def).") != '".$GLOBALS['db']->quote($layout_def['input_name0'])."'\n";
        }
        //END SUGARCRM flav=ent ONLY
    }
    
    function queryFilterNot_Empty(&$layout_def) {
        if( $this->reporter->db->dbType == 'mysql') {
            return parent::queryFilterNot_Empty($layout_def);
        } 
        elseif( $this->reporter->db->dbType == 'mssql') {
            return '( '.$this->_get_column_select($layout_def).' IS NOT NULL OR DATALENGTH('.$this->_get_column_select($layout_def).") > 0)\n";
        }
        //BEGIN SUGARCRM flav=ent ONLY
        elseif ( $this->reporter->db->dbType == 'oci8') {
            return ' LENGTH('.$this->_get_column_select($layout_def).") > 0\n";
        }
        //END SUGARCRM flav=ent ONLY
    }
    
    function queryFilterEmpty(&$layout_def) {
        if( $this->reporter->db->dbType == 'mysql') {
            return parent::queryFilterEmpty($layout_def);
        } 
        elseif( $this->reporter->db->dbType == 'mssql') {
            return '( '.$this->_get_column_select($layout_def).' IS NULL OR DATALENGTH('.$this->_get_column_select($layout_def).") = 0)\n";
        }
        //BEGIN SUGARCRM flav=ent ONLY
        elseif ( $this->reporter->db->dbType == 'oci8') {
            return ' LENGTH('.$this->_get_column_select($layout_def).") = 0\n";
        }
        //END SUGARCRM flav=ent ONLY

    }
}

?>

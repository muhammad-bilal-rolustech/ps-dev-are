<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
 * $Id$
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once 'include/SugarWireless/SugarWirelessView.php';

/**
 *
 * SugarWirelessListView extends SugarWirelessView and is the base class for wireless list views.
 *
 * This class contains elements that are specific to list views, including loading the list view metadata
 * and establishing filter fields.
 *
 * TODO: complete refactoring of SugarWirelessView to move all listview specific methods into this class
 */

class SugarWirelessListView extends SugarWirelessView{

	protected $displayColumns;

    /**
     * Override the default init() method; load the wireless list view metadata
     *
     * @param $bean
     * @param $view_object_map
     */
    public function init($bean = null, $view_object_map = array())
    {
    	$listViewDefs[$GLOBALS['module']] = $this->getMetaDataFile();
       
 		// retrieve the displayColumns from listview metadata file
        $this->displayColumns = array();
        foreach($listViewDefs[$GLOBALS['module']]['panels'] as $panel) {
            foreach ($panel['fields'] as $field) {
                if(!empty($field['default'])) {
                    $this->displayColumns[strtoupper($field['name'])] = $field;
                }
            }
        }

        parent::init($bean, $view_object_map);
    }

    /**
     * Retrieve the listview defs for this view.
     *
     * @param none
     * @return array Listview defs
     */
    public function getMetaDataFile(){
        // load the wireless list view metadata
        
    	require_once 'modules/ModuleBuilder/parsers/constants.php';
		require $this->wl_get_metadata_location( MB_WIRELESSLISTVIEW );
        $module = $GLOBALS['module'];
        // Check for viewdefs first
        if (isset($viewdefs)) {
            if (isset($viewdefs[$module])) {
                return $viewdefs[$module]['mobile']['view']['list'];
            }

            if (isset($viewdefs['<module_name>']) || isset($viewdefs['<_module_name>']) || isset($viewdefs['<MODULE_NAME>'])) {
                $viewdefs = MetaDataFiles::getModuleMetaDataDefsWithReplacements($module, $viewdefs);
                return $viewdefs[$module]['mobile']['view']['list'];
            }
        }

        // Get our module from the globals array
        $module = $GLOBALS['module'];
        // Handle new format
        if (isset($viewdefs)) {
            if (isset($viewdefs[$module])) {
                return $viewdefs[$module]['mobile']['view']['list'];
            } else {
                if (isset($viewdefs['<module_name>'])) {
                    return $viewdefs['<module_name>']['mobile']['view']['list'];
                }
            }
        }

		// if we loaded the metadata from a SugarObjects template, then switch the template modulename to this module
		//if ( !isset ( $listViewDefs [ $GLOBALS['module'] ] ) &&  isset ( $listViewDefs [ '<module_name>' ] ) ) {
            //$listViewDefs [ $GLOBALS['module'] ] = $listViewDefs [ '<module_name>' ] ;
        //}
        if (!isset($listViewDefs[$module]) && isset($listViewDefs['<module_name>'])) {
            $listViewDefs[$module] = $listViewDefs['<module_name>'];
        }

        return $listViewDefs[$module];
    }
    
	/**
	 * Protected function that returns the filter_fields based on the module's
	 * list view metadata
	 */
 	protected function get_filter_fields($module){
		// code from ListViewDisplay setup(), determines the $filter_fields array based off
		// of the display columns of the listview metadata
		$filter_fields = array ();
        foreach($this->displayColumns as $columnName => $def) {
            $filter_fields[strtolower($columnName)] = true;
            if(!empty($def['related_fields'])) {
                foreach($def['related_fields'] as $field) {
                    //id column is added by query construction function. This addition creates duplicates
                    //and causes issues in oracle. #10165
                    if ($field != 'id') {
                        $filter_fields[$field] = true;
                    }
                }
            }
        }
        return $filter_fields;
 	}

}
?>

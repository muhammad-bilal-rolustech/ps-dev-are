<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * CacheHandler
 *
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
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
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2005 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: CacheHandler.php,v 1.6 2006/06/06 17:57:47 majed Exp $

    $moduleDefs = array();
    $fileName = 'field_arrays.php';

    /************************************************
    * LoadCachedArray
    * PARAMS
    * module_dir - the module directory
    * module - the name of the module
    * key - the type of field array we are referencing, i.e. list_fields,
    *       column_fields, required_fields
    * DESCRIPTION
    * This function is designed to cache references
    * to field arrays that were previously stored in the bean files
    * and have since been moved to seperate files.
    *************************************************/
	function &LoadCachedArray($module_dir, $module, $key)
	{
        global $moduleDefs, $fileName;
	$local_temp = null;
        if(file_exists('modules/'.$module_dir.'/'.$fileName))
        {
           

		    if(!isset($moduleDefs[$module]))
            {
            	include('modules/'.$module_dir.'/'.$fileName);
                $moduleDefs[$module] = $fields_array;
		    }
            if(!isset($moduleDefs[$module]))
            {
                return  $local_temp;
            }
            return $moduleDefs[$module][$module][$key];
        }
        else
        {
            return  $local_temp;
        }
	}
?>
<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * SugarWidgetSubPanelDateTime
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

// $Id: SugarWidgetSubPanelConcat.php 13782 2006-06-06 17:58:55Z majed $

require_once('include/generic/SugarWidgets/SugarWidgetField.php');

class SugarWidgetSubPanelConcat extends SugarWidgetField
{
	function displayList(&$layout_def)
	{
		$value='';
		if (isset($layout_def['source']) and is_array($layout_def['source']) and isset($layout_def['fields']) and is_array($layout_def['fields'])) {
			
			foreach ($layout_def['source'] as $field) {
			
				if (isset($layout_def['fields'][strtoupper($field)])) {
					$value.=$layout_def['fields'][strtoupper($field)];
				} else {
					$value.=$field;
				}	
			}
		}
		return $value;
	}
}
?>
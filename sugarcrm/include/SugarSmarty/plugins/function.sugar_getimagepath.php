<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

/*

Modification information for LGPL compliance

r56990 - 2010-06-16 13:05:36 -0700 (Wed, 16 Jun 2010) - kjing - snapshot "Mango" svn branch to a new one for GitHub sync

r56989 - 2010-06-16 13:01:33 -0700 (Wed, 16 Jun 2010) - kjing - defunt "Mango" svn dev branch before github cutover

r55980 - 2010-04-19 13:31:28 -0700 (Mon, 19 Apr 2010) - kjing - create Mango (6.1) based on windex

r51719 - 2009-10-22 10:18:00 -0700 (Thu, 22 Oct 2009) - mitani - Converted to Build 3  tags and updated the build system 

r51634 - 2009-10-19 13:32:22 -0700 (Mon, 19 Oct 2009) - mitani - Windex is the branch for Sugar Sales 1.0 development

r50375 - 2009-08-24 18:07:43 -0700 (Mon, 24 Aug 2009) - dwong - branch kobe2 from tokyo r50372

r42807 - 2008-12-29 11:16:59 -0800 (Mon, 29 Dec 2008) - dwong - Branch from trunk/sugarcrm r42806 to branches/tokyo/sugarcrm

r40349 - 2008-10-07 12:27:23 -0700 (Tue, 07 Oct 2008) - jmertic - Changes for Iteration 1 of the Themes Improvements:
- Added SugarTheme and SugarThemeRegistry objects, updating everywhere in the app to use them.
- Converted the Sugar Theme to the new style, which involved:
 - moved all PHP and HTML out of the themes, into SugarView or the include/utils/layout_utils.php directory.
 - all images in the images/ directory and all css in the css/ directory.
 - removed config.php and replaced it with themedef.php.

r33134 - 2008-03-21 04:51:32 -0700 (Fri, 21 Mar 2008) - majed - templating changes

r32836 - 2008-03-14 16:48:48 -0700 (Fri, 14 Mar 2008) - majed - adds smarty functions


*/


/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {sugar_include} function plugin
 *
 * Type:     function<br>
 * Name:     sugar_include<br>
 * Purpose:  Handles rendering the global file includes from the metadata files defined
 *           in templateMeta=>includes.
 * 
 * @author Collin Lee {clee@sugarcrm.com}
 * @param array
 * @param Smarty
 */
function smarty_function_sugar_getimagepath($params, &$smarty)
{
	if(!isset($params['file'])) {
		   $smarty->trigger_error($GLOBALS['app_strings']['ERR_MISSING_REQUIRED_FIELDS'] . 'file');
	}
 	return SugarThemeRegistry::current()->getImageURL($params['file']);
}
?>
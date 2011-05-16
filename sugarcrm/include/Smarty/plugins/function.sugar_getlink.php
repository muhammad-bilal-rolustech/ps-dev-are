<?php

/**
 * Smarty {sugar_getlink} function plugin
 *
 * Type:     function
 * Name:     sugar_getlink
 * Purpose:  Returns HTML link <a> with embedded image or normal text
 * 
 * @param array
 * @param Smarty
 */

function smarty_function_sugar_getlink($params, &$smarty) {

	// error checking for required parameters
	if(!isset($params['url'])) 
		$smarty->trigger_error($GLOBALS['app_strings']['ERR_MISSING_REQUIRED_FIELDS'] . 'url');
	if(!isset($params['title']))
		$smarty->trigger_error($GLOBALS['app_strings']['ERR_MISSING_REQUIRED_FIELDS'] . 'title');

	// set defaults
	if(!isset($params['attr']))
		$params['attr'] = '';
	if(!isset($params['img_name'])) 
		$params['img_name'] = '';
	if(!isset($params['img_attr']))
		$params['img_attr'] = '';
	if(!isset($params['img_placement']))
		$params['img_placement'] = '';
	if(!isset($params['img_alt']))
		$params['img_alt'] = '';

	return SugarThemeRegistry::current()->getLink($params['url'], $params['title'], $params['attr'],
			$params['img_name'], $params['img_attr'], $params['img_placement'], $params['img_alt']);	
}
?>

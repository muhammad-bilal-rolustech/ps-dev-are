<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

class SugarWidgetFieldURL extends SugarWidgetFieldVarchar
{
 	/* Display item as link
     * @param array $layout_def definition of field which we want to display as link
     * @return string html code
     */
    function displayList($layout_def) 
    {
        $urlValue = trim($this->_get_list_value($layout_def));
        return '<a target="_blank" href="' . $urlValue . '">' . $urlValue . "</a>";
    }
    
}

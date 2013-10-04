<?php
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


class FormulaHelper
{

    /**
     * Takes an array of field defs and returns a formated list of fields that are valid for use in expressions.
     *
     * @param array $fieldDef
     * @return array
     */
    public static function cleanFields($fieldDef, $includeLinks = true, $forRelatedField = false, $returnKeys = false)
    {
        $fieldArray = array();
        foreach ($fieldDef as $fieldName => $def) {
            if (!is_array($def) || $fieldName == 'deleted' || $fieldName == 'email1' || empty($def['type']))
                continue;
            //Check the studio property of the field def.
            if (isset($def['studio']) && (self::isFalse($def['studio']) || (is_array($def['studio']) && (
                (isset($def['studio']['formula']) && self::isFalse($def['studio']['formula'])) ||
                ($forRelatedField && isset($def['studio']['related']) && self::isFalse($def['studio']['related']))
            ))))
            {
                continue;
            }
            switch ($def['type']) {
                case "int":
                case "float":
                case "decimal":
                case "currency":
                    $fieldArray[$fieldName] = array($fieldName, 'number');
                    break;
                case "bool":
                    $fieldArray[$fieldName] = array($fieldName, 'boolean');
                    break;
                case "varchar":
                case "fullname":
                case "name":
                case "phone":
                case "text":
                case "url":
                case "encrypt":
                case "enum":
                    $fieldArray[$fieldName] = array($fieldName, 'string');
                    break;
                case "date":
                case "datetime":
                case "datetimecombo":
                    $fieldArray[$fieldName] = array($fieldName, 'date');
                    break;
                case "link":
                    if ($includeLinks)
                        $fieldArray[$fieldName] = array($fieldName, 'relate');
                    break;
                default:
                    //Do Nothing
                    break;
            }
        }

        if ($returnKeys)
            return $fieldArray;

        return array_values($fieldArray);
    }

    protected static function isFalse($v){
        if (is_string($v)){
            return strToLower($v) == "false";
        }
        if (is_array($v))
            return false;

        return $v == false;
    }

    public static function getFieldValue($focus, $field)
    {
        //First check if the field exists
        if(!isset($focus->field_defs[$field]) || !isset($focus->$field))
        {
            return "unknown field";
        }
        else if ($focus->field_defs[$field]['type'] == "bool")
        {
            return ($focus->$field ? "true" : "false");
        }
        //Otherwise, send it to the formula builder to evaluate further
        else
        {
            return $focus->$field;
        }
    }



    /**
     * @static
     * @param string $module
     * @param MBPackage $package
     * @return array set of links that are valid for related module function is the formula builder
     */
    public static function getLinksForModule($module, $package = null){
        if (!empty($package))
            return self::getLinksForMBModule($module, $package);
        else
            return self::getLinksForDeployedModule($module);
    }

    protected static function getLinksForDeployedModule($module){
        $links = array();
        $focus = BeanFactory::newBean($module);
        $focus->id = create_guid();
        $fields = FormulaHelper::cleanFields($focus->field_defs);

        //Next, get a list of all links and the related modules
        foreach ($fields as $val) {
            $name = $val[0];
            $def = $focus->field_defs[$name];
            if ($val[1] == "relate" && $focus->load_relationship($name)) {
                $relatedModule = $focus->$name->getRelatedModuleName();
                //MB will sometimes produce extra link fields that we need to ignore
                //We also should not display the EmailAddress Module
                if ($relatedModule == "EmailAddresses" ||
                    (!empty($def['side']) && (substr($name, -4) == "_ida" || substr($name, -4) == "_idb"))
                ){
                    continue;
                }

                $label = empty($def['vname']) ? $name : translate($def['vname'], $module);
                $links[$name] = array(
                    "label" => "$relatedModule ($label)",
                    "module" => $relatedModule
                );
            }
        }

        return $links;
    }

    /**
     * For In module builder, the vardef and link fields are not yet solidifed, so we need to run through the
     * undeployed relationships and get the link fields from there
     */
    protected static function getLinksForMBModule($module, $package){
        $links = array();
        $mbModule = $package->getModule ($module);
        $linksFields = $mbModule->getLinkFields();
        $fields = FormulaHelper::cleanFields($linksFields);
        foreach ($fields as $val) {
            $name = $val[0];
            $def = $linksFields[$name];
            if ($val[1] == "relate") {
                $relatedModule = $def['module'];
                //MB will sometimes produce extra link fields that we need to ignore
                //We also should not display the EmailAddress Module
                if ($relatedModule == "EmailAddresses" ||
                    (!empty($def['side']) && (substr($name, -4) == "_ida" || substr($name, -4) == "_idb"))
                ){
                    continue;
                }

                $label = $def['translated_label'];
                $links[$name] = array(
                    "label" => "$relatedModule ($label)",
                    "module" => $relatedModule
                );
            }
        }

        return $links;
    }

    /**
     * @static
     * @param array $link
     * @param MBPackage $package
     * @param array $allowedTypes list of types to allow as related fields
     * @return array
     */
    public static function getRelatableFieldsForLink($link, $package = null, $allowedTypes = array())
    {
        $rfields = array();
        $relatedModule = $link["module"];
        $mbModule = null;
        if (!empty($package))
            $mbModule = $package->getModuleByFullName($relatedModule);
        //First, create a dummy bean to access the relationship info
        if (empty($mbModule)) {
            $relatedBean = BeanFactory::getBean($relatedModule);
            $field_defs = $relatedBean->field_defs;
        } else {
            $field_defs = $mbModule->getVardefs(false);
            $field_defs = $field_defs['fields'];
        }

        $relatedFields = FormulaHelper::cleanFields($field_defs, false, true);
        foreach ($relatedFields as $val) {
            $name = $val[0];
            //Rollups must be either a number or a possible number (like a string) to roll up
            if (!empty($allowedTypes) && !in_array($val[1], $allowedTypes))
                continue;

            $def = $field_defs[$name];
            if (empty($mbModule))
                $rfields[$name] = empty($def['vname']) ? $name : translate($def['vname'], $relatedModule);
            else
                $rfields[$name] = empty($def['vname']) ? $name : $mbModule->mblanguage->translate($def['vname']);
            //Strip the ":" from any labels that have one
            if (substr($rfields[$name], -1) == ":")
                $rfields[$name] = substr($rfields[$name], 0, strlen($rfields[$name]) - 1);
        }

        return $rfields;
    }
}
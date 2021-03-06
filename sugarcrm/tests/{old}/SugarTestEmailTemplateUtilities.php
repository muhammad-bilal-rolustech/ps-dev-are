<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

require_once 'modules/EmailTemplates/EmailTemplate.php';

class SugarTestEmailTemplateUtilities
{
    private static $createdEmailTemplates = array();

    private function __construct()
    {
    }

    /**
     * @return EmailTemplate
     */
    public static function createEmailTemplate($id = '', $emailTemplateValues = array())
    {
        $time = mt_rand();
        $emailTemplate = BeanFactory::newBean('EmailTemplates');

        $emailTemplateValues = array_merge(array(
            'name' => 'SugarEmailTemplate' . $time,
        ), $emailTemplateValues);

        foreach ($emailTemplateValues as $property => $value) {
            $emailTemplate->$property = $value;
        }

        if (!empty($id)) {
            $emailTemplate->new_with_id = true;
            $emailTemplate->id = $id;
        }
        $emailTemplate->save();
        $GLOBALS['db']->commit();
        self::$createdEmailTemplates[] = $emailTemplate;
        return $emailTemplate;
    }

    public static function removeAllCreatedEmailTemplates()
    {
        $emailTemplate_ids = self::getCreatedEmailTemplateIds();
        $GLOBALS['db']->query('DELETE FROM email_templates WHERE id IN (\'' .
            implode("', '", $emailTemplate_ids) . '\')');
    }

    public static function getCreatedEmailTemplateIds()
    {
        $emailTemplate_ids = array();
        foreach (self::$createdEmailTemplates as $emailTemplate) {
            $emailTemplate_ids[] = $emailTemplate->id;
        }
        return $emailTemplate_ids;
    }
}

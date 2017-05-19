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

class SugarUpgradeAssignSystemTemplate extends UpgradeScript
{
    public $order = 2100;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritdoc}
     *
     * Sets the `email_templates.type` column to "system" for OOTB email templates.
     *
     * This upgrade script only runs when upgrading from a version prior to 7.10.
     */
    public function run()
    {
        if (!version_compare($this->from_version, '7.10', '<')) {
            return;
        }

        $this->log('Set email_templates.type to system');
        $lostPasswordTemplateId = $GLOBALS['sugar_config']['passwordsetting']['lostpasswordtmpl'];
        $generatePasswordTemplateId = $GLOBALS['sugar_config']['passwordsetting']['generatepasswordtmpl'];

        $sql = "UPDATE email_templates SET type='system' WHERE id=?";
        $this->runUpdate($sql, array(
            $lostPasswordTemplateId,
        ));

        $sql = "UPDATE email_templates SET type='system' WHERE id=?";
        $this->runUpdate($sql, array(
            $generatePasswordTemplateId,
        ));
    }

    /**
     * Executes an update query and logs the number of affected rows or the error.
     *
     * @param string $sql The query to execute.
     * @param array $params The parameters to pass into the query to be escaped
     */
    protected function runUpdate($sql, array $params)
    {
        try {
            $rows = DBManagerFactory::getConnection()->executeUpdate($sql, $params);
            $this->log("Number of affected rows: {$rows}");
        } catch (DBALException $error) {
            $this->log("Error: {$error}");
        }
    }
}

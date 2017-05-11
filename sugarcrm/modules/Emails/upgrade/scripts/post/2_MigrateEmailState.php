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

class SugarUpgradeMigrateEmailState extends UpgradeScript
{
    public $order = 2100;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritdoc}
     *
     * Sets the `emails.state` column to "Archived" or "Draft" for all rows in emails.
     *
     * This upgrade script only runs when upgrading from a version prior to 7.10.
     */
    public function run()
    {
        if (!version_compare($this->from_version, '7.10', '<')) {
            return;
        }

        $this->log('Set emails.state to Draft where emails.type is draft or emails.status is send_error');
        $sql = "UPDATE emails SET state='Draft' WHERE type='draft' OR status='send_error'";
        $this->runUpdate($sql);

        $sql = "SELECT COUNT(id) FROM emails WHERE state IS NULL OR state=''";
        $num = $this->db->getConnection()->executeQuery($sql)->fetchColumn();
        $this->log("{$num} emails remain with an empty state");
    }

    /**
     * Executes an update query and logs the number of affected rows or the error.
     *
     * @param string $sql The query to execute.
     */
    protected function runUpdate($sql)
    {
        try {
            $rows = DBManagerFactory::getConnection()->executeUpdate($sql);
            $this->log("Number of affected rows: {$rows}");
        } catch (DBALException $error) {
            $this->log("Error: {$error}");
        }
    }
}

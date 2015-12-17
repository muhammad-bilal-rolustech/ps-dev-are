<?php
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

namespace Sugarcrm\Sugarcrm\Dav\Cal\Hook;

use \Sugarcrm\Sugarcrm\JobQueue\Manager\Manager as JQManager;
use \Sugarcrm\Sugarcrm\Dav\Cal\Adapter\Factory as CalDavAdapterFactory;
use \Sugarcrm\Sugarcrm\Dav\Cal\Handler as CalDavHandler;

/**
 *
 * Logic hook handler
 *
 */
class Handler
{
    /**
     * @param \CalDavEventCollection $bean
     * @param string $calDavData
     */
    public function import(\CalDavEventCollection $bean, $calDavData)
    {
        $diff = $bean->getDiffStructure($calDavData);
        $saveCounter = $bean->getSynchronizationObject()->setSaveCounter();
        $this->getManager()->calDavImport($diff, $saveCounter);
    }

    /**
     * @param \SugarBean $bean
     * @param array $changedFields
     * @param array $invitesBefore
     * @param array $invitesAfter
     * @param bool $insert
     */
    public function export(
        \SugarBean $bean,
        $changedFields = array(),
        $invitesBefore = array(),
        $invitesAfter = array(),
        $insert = false
    ) {
        $adapter = $this->getAdapterFactory()->getAdapter($bean->module_name);
        if ($adapter) {
            $preparedData = $adapter->prepareForExport(
                $bean,
                $changedFields,
                $invitesBefore,
                $invitesAfter,
                $insert
            );
            $handler = $this->getCalDavHandler();
            $event = $handler->getDavBean($bean);
            $saveCounter = $event->getSynchronizationObject()->setSaveCounter();
            $this->getManager()->calDavExport($preparedData, $saveCounter);
        }
    }

    /**
     * Get manager object for handler processing.
     * @return \Sugarcrm\Sugarcrm\JobQueue\Manager\Manager
     */
    protected function getManager()
    {
        return new JQManager();
    }

    /**
     * @return \Sugarcrm\Sugarcrm\Dav\Cal\Adapter\Factory
     */
    protected function getAdapterFactory()
    {
        return CalDavAdapterFactory::getInstance();
    }

    /**
     * Get CalDavHandler object.
     * @return CalDavHandler
     */
    protected function getCalDavHandler()
    {
        return new CalDavHandler();
    }
}

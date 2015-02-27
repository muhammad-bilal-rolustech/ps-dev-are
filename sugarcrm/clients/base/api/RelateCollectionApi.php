<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

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

require_once 'clients/base/api/CollectionApi.php';

/**
 * Collection API
 */
class RelateCollectionApi extends CollectionApi
{
    /** {@inheritDoc} */
    protected static $sourceKey = '_link';

    /** @var RelateApi */
    protected $relateApi;

    /**
     * Primary bean corresponding to the current API arguments.
     *
     * We need to pass it to SugarApi::getFieldsFromArgs() from within parent::getSourceArguments(),
     * but generic collection interface does not operate primary bean
     *
     * @var SugarBean
     */
    protected $bean;

    /**
     * Registers API
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function registerApiRest()
    {
        return array(
            'getCollection' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'collection', '?'),
                'pathVars' => array('module', 'record', '', 'collection_name'),
                'method' => 'getCollection',
                'shortHelp' => 'Lists collection records.',
                'longHelp' => 'include/api/help/module_record_collection_collection_name_get_help.html',
            ),
        );
    }

    /** {@inheritDoc} */
    protected function getCollectionDefinition(ServiceBase $api, array $args)
    {
        $this->requireArgs($args, array('collection_name'));
        $bean = $this->bean = $this->loadBean($api, $args);

        require_once 'clients/base/api/CollectionApi/CollectionDefinition/RelateCollectionDefinition.php';
        $definition = new RelateCollectionDefinition($bean, $args['collection_name']);

        return $definition;
    }

    /** {@inheritDoc} */
    protected function getSourceData($api, $source, $args)
    {
        $args['link_name'] = $source;
        return $this->getRelateApi()->filterRelated($api, $args);
    }

    /** {@inheritDoc} */
    protected function getDefaultLimit()
    {
        global $sugar_config;
        global $log;

        if (empty($sugar_config['list_max_entries_per_subpanel'])) {
            $log->warn('Default subpanel entry limit is not configured');
            return 5;
        }

        return $sugar_config['list_max_entries_per_subpanel'];
    }

    /**
     * Lazily loads Relate API
     *
     * @return RelateApi
     */
    protected function getRelateApi()
    {
        if (!$this->relateApi) {
            $this->relateApi = new RelateApi();
        }

        return $this->relateApi;
    }
}

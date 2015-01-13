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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Mapping;

use Sugarcrm\Sugarcrm\Elasticsearch\Provider\ProviderInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\ProviderCollection;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\PropertyInterface;

/**
 *
 * Mapping manager is responsible to build the Elasticsearch mapping. The
 * definition of the mapping properties is owned by the different Providers.
 * The Mapping Manager orchestrates the creation of the full mapping and
 * passes it along to the Index Manager which is responsible to send it
 * all to the Elasticsearch backend.
 *
 */
class MappingManager
{
    /**
     * List of loaded property objects
     * @var \Sugarcrm\Sugarcrm\Elasticsearch\Provider\ProviderInterface[]
     */
    protected $loadedProperties = array();

    /**
     * Ctor
     */
    public function __construct()
    {
    }

    /**
     * Build mapping
     * @param ProviderCollection $providers
     * @param array $modules List of modules
     * @return \Sugarcrm\Sugarcrm\Sugarcrm\Elasticsearch\Mapping\MappingCollection
     */
    public function buildMapping(ProviderCollection $providers, array $modules)
    {
        $collection = new MappingCollection($modules);

        foreach ($collection as $mapping) {
            /* @var $mapping Mapping */
            $mapping->buildMapping($providers);
        }
        return $collection;
    }

    /**
     * Get mapping for given module
     * @param string $module
     * @param \Sugarcrm\Sugarcrm\Elasticsearch\Provider\ProviderInterface[] $providers
     * @return \Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping
     */
    protected function getMappingForModule($module, array $providers)
    {
        // TODO - do we still need this ?
        // load data from MetaDataHelper
        //$metaDataHelper = $this->container->metaDataHelper;
        //$ftsFields = $metaDataHelper->getFtsFields($module);
        //$fieldDefs = $metaDataHelper->getFieldDefs($module);

        // build mapping per module
        $mapping = new Mapping($module);
        $mapping->buildMapping($providers);

        return $mapping;
    }
}

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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\Handler;

use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;
use Sugarcrm\Sugarcrm\Elasticsearch\Query\QueryBuilder;
use Sugarcrm\Sugarcrm\Elasticsearch\Query\Aggregation\ModuleAggregation;
use Sugarcrm\Sugarcrm\Elasticsearch\Query\Aggregation\AggregationFactory;
use Sugarcrm\Sugarcrm\Elasticsearch\Provider\GlobalSearch\GlobalSearch;

/**
 *
 * Cross module aggregation handler
 *
 * TODO: Fix dependency injection on container as its not accessible
 * during setProvider call to initialize the object loading the agg
 * definitions in a clean way.
 *
 */
class CrossModuleAggHandler extends AbstractHandler implements
    MappingHandlerInterface,
    AggregationHandlerInterface
{
    /**
     * Aggregation definitions
     * @var array
     */
    protected $aggDefs;

    /**
     * {@inheritdoc}
     */
    public function buildMapping(Mapping $mapping, $field, array $defs)
    {
        // load aggregation defs
        $this->loadAggDefs();
        if (!$aggDef = $this->getAggDef($field)) {
            return;
        }

        // instantiate implementation class to handle the mapping
        $agg = AggregationFactory::get($aggDef['type']);
        $agg->buildMapping($mapping, $field, $defs);
    }

    /**
     * {@inheritdoc}
     */
    public function addAggregations(QueryBuilder $builder)
    {
        if (!$this->provider->getQueryCrossModuleAggs()) {
            return;
        }

        // implicit module aggregation
        $size = count($builder->getModules());
        $builder->addAggregation('modules', new ModuleAggregation($size));

        // add cross module aggregations
        $this->loadAggDefs();
        foreach ($this->aggDefs as $id => $defs) {

            // create new object
            $agg = AggregationFactory::create($defs['type']);

            // set user context
            $agg->setUser($builder->getUser());

            // cross aggs use the field name as identifier
            $agg->setOption('field', $id);

            // set additional options
            if (!empty($defs['options']) && is_array($defs['options'])) {
                $agg->setOptions($defs['options']);
            }

            // append aggregation on query builder
            $builder->addAggregation($id, $agg);
        }
    }

    /**
     * Get aggregation definition for given field
     * @param string $field
     * @return array|false
     */
    protected function getAggDef($field)
    {
        return isset($this->aggDefs[$field]) ? $this->aggDefs[$field] : false;
    }

    /**
     * Load aggregation definitions
     */
    protected function loadAggDefs()
    {
        if ($this->aggDefs === null) {
            $this->aggDefs = $this->provider->getContainer()->metaDataHelper->getCrossModuleAggregations();
        }
    }
}

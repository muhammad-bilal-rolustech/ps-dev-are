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

use Sugarcrm\Sugarcrm\Elasticsearch\Provider\ProviderCollection;
use Sugarcrm\Sugarcrm\Elasticsearch\Exception\MappingException;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldProperty;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\MultiFieldBaseProperty;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\RawProperty;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\PropertyInterface;
use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Property\ObjectProperty;

/**
 *
 * This class builds the mapping per module (type) based on the available
 * providers.
 *
 */
class Mapping
{
    /**
     * Module name prefix separator
     * @var string
     */
    const PREFIX_SEP = '__';

    /**
     * @var string Module name
     */
    protected $module;

    /**
     * @var array Elasticsearch mapping properties
     */
    protected $properties = array();

    /**
     * Base mapping used for all multi fields
     * @var array
     */
    protected $multiFieldBase = array(
        'type' => 'string',
        'index' => 'not_analyzed',
        'include_in_all' => false,
    );

    /**
     * @param string $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Build mapping
     * @param ProviderCollection $providers
     */
    public function buildMapping(ProviderCollection $providers)
    {
        foreach ($providers as $provider) {
            $provider->buildMapping($this);
        }
    }

    /**
     * Get module
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Compile mapping properties
     *
     * @return array
     */
    public function compile()
    {
        $compiled = array();
        foreach ($this->properties as $field => $property) {
            $compiled[$this->normalizeFieldName($field)] = $property->getMapping();
        }
        return $compiled;
    }

    /**
     * Add a not_analyzed string field to the mapping. As every multi field
     * has a not_analyzed base definition we can just add this as is. Other
     * providers can still register additional multi fields on top of this
     * not analyzed field.
     *
     * @param string $field Field name
     */
    public function addNotAnalyzedField($field)
    {
        $this->createMultiFieldBase($field);
    }

    /**
     * Add multi field mapping. This should be the primary method to be used
     * to build the mapping as most fields are string based. Multi fields
     * have the ability to define different analyzers for every sub field.
     * During indexing the value for each multi field only has to be send once
     * instead of being duplicated.
     *
     * @param string $baseField Base field name
     * @param string $field Name of the multi field
     * @param MultiFieldProperty $property
     */
    public function addMultiField($baseField, $field, MultiFieldProperty $property)
    {
        $this->createMultiFieldBase($baseField)->addField($field, $property);
    }

    /**
     * Add object (or nested) property mapping.
     *
     * @param string $field
     * @param ObjectProperty $property
     */
    public function addObjectProperty($field, ObjectProperty $property)
    {
        $this->addProperty($field, $property);
    }

    /**
     * Add raw property mapping. It is encouraged to use higher level property
     * objects instead and the respective methods on this class to configure
     * them instead of using a raw property. Use this method with caution.
     *
     * @param string $field
     * @param RawProperty $property
     */
    public function addRawProperty($field, RawProperty $property)
    {
        $this->addProperty($field, $property);
    }

    /**
     * Create base multi field object for given field.
     *
     * @param string $field
     * @return MultiFieldBaseProperty
     * @throws MappingException
     */
    protected function createMultiFieldBase($field)
    {
        // create multi field base if not set yet
        if (!isset($this->properties[$field])) {
            $property = new MultiFieldBaseProperty();
            $property->setMapping($this->multiFieldBase);
            $this->addProperty($field, $property);
        }

        // make sure we have a base multi field
        if (!$this->properties[$field] instanceof MultiFieldBaseProperty) {
            throw new MappingException("Field '{$field}' is not a multi field");
        }

        return $this->properties[$field];
    }

    /**
     * Low level wrapper to add mapping properties
     *
     * @param string $field
     * @param PropertyInterface $property
     * @throws MappingException
     */
    protected function addProperty($field, PropertyInterface $property)
    {
        if (isset($this->properties[$field])) {
            throw new MappingException("Cannot redeclare field '{$field}' for module '{$this->module}'");
        }
        $this->properties[$field] = $property;
    }

    /**
     * Prefix field name using module name. In certain cases Elasticsearch
     * has problems using disambigious field names when a given field exists
     * across multiple modules (i.e. multi_match has this behavior). Therefor
     * we prefix all main fields with the module name to mitigate this problem.
     *
     * @param string $field
     * @return string
     */
    protected function normalizeFieldName($field)
    {
        return $this->module . self::PREFIX_SEP . $field;
    }
}
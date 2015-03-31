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

namespace Sugarcrm\Sugarcrm\Elasticsearch\Query\Highlighter;

use Sugarcrm\Sugarcrm\Elasticsearch\Mapping\Mapping;

/**
 *
 * Abstract Highlighter
 *
 */
abstract class AbstractHighlighter implements HighlighterInterface
{
    /**
     * Global highlighter properties not explicitly
     * available on this object.
     * @var array
     */
    protected $globalProps = array();

    /**
     * @var array List of fields and its highlighter settings
     */
    protected $fields = array();

    /**
     * @var array Field arguments applied to every field
     */
    protected $defaultFieldArgs = array();

    /**
     * @var array List of pre tags
     */
    protected $preTags = array('<strong>');

    /**
     * @var array List of post tags
     */
    protected $postTags = array('</strong>');

    /**
     * @var integer Number of fragments
     */
    protected $numberOfFrags = 5;

    /**
     * @var integer Fragment size
     */
    protected $fragSize = 20;

    /**
     * @var boolean Require field match
     */
    protected $requireFieldMatch = true;

    /**
     * @var string Field encoder, accepts html or default
     */
    protected $encoder = 'html';

    /**
     * @var string Order highlights, defaults to score
     */
    protected $order = 'score';

    /**
     * @var array Remapped fields
     */
    protected $fieldRemap = array();

    /**
     * Set fields
     * @param array $fields
     * @return AbstractHighlighter
     */
    public function setFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }

    /**
     * Set field arguments which are applied on every field
     * @param array $args
     * @return AbstractHighlighter
     */
    public function setDefaultFieldArgs(array $args)
    {
        $this->defaultFieldArgs = $args;
        return $this;
    }

    /**
     * Set list of pre tags
     * @param array $tags
     * @return AbstractHighlighter
     */
    public function setPreTags(array $tags)
    {
        $this->preTags = $tags;
        return $this;
    }

    /**
     * Set list of post tags
     * @param array $tags
     * @return \Sugarcrm\Sugarcrm\Elasticsearch\Component\Highlighter
     */
    public function setPostTags(array $tags)
    {
        $this->postTags = $tags;
        return $this;
    }

    /**
     * Enable/disable required field match
     * @param boolean $toggle
     * @return AbstractHighlighter
     */
    public function setRequiredFieldMatch($toggle)
    {
        $this->requireFieldMatch = $toggle;
        return $this;
    }

    /**
     * Set global number of fragments
     * @param integer $value
     * @return AbstractHighlighter
     */
    public function setNumberOfFrags($value)
    {
        $this->numberOfFrags = (int) $value;
        return $this;
    }

     /**
      * Set global fragment size
      * @param integer $value
      * @return AbstractHighlighter
      */
    public function setFragSize($value)
    {
        $this->fragSize = (int) $value;
        return $this;
    }

    /**
     * Set field remapping
     * @param array $remap
     * @return AbstractHighlighter
     */
    public function setFieldRemap(array $remap)
    {
        $this->fieldRemap = array_merge($this->fieldRemap, $remap);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        // generate global properties
        $properties = array(
            'pre_tags' => $this->preTags,
            'post_tags' => $this->postTags,
            'require_field_match' => $this->requireFieldMatch,
            'number_of_fragments' => $this->numberOfFrags,
            'fragment_size' => $this->fragSize,
            'encoder' => $this->encoder,
            'order' => $this->order,
        );
        $properties = array_merge($this->globalProps, $properties);

        // generate fields
        $fields = array();
        foreach ($this->fields as $field => $args) {
            $fields[$field] = array_merge($this->defaultFieldArgs, $args);
        }

        $properties['fields'] = $fields;

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResults(array $highlights)
    {
        $parsed = array();
        foreach ($highlights as $field => $value) {

            // Normalize the field name
            $normField = $this->normalizeFieldName($field);

            // Multiple highlights can be returned for the same field, if so we
            // add them and filter out any duplicates.
            if (isset($parsed[$normField])) {
                $parsed[$normField] = array_unique(array_merge($parsed[$normField], $value), SORT_STRING);
            } else {
                $parsed[$normField] = $value;
            }
        }
        return $parsed;
    }

    /**
     * Normalize field name, removes multi field notation and applies
     * the field remapping if defined for given field.
     * @param string $field
     * @return string
     */
    public function normalizeFieldName($field)
    {
        // Strip of the module name and keep the main field only. If no match
        // is found we continue with the field value as is.
        if (preg_match('/^.*' . Mapping::PREFIX_SEP . '([^.]*).*$/', $field, $matches)) {
            $field = $matches[1];
        }

        // apply remap if any defined
        return isset($this->fieldRemap[$field]) ? $this->fieldRemap[$field] : $field;
    }
}
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

namespace Sugarcrm\Sugarcrm\Security\Validator;

use Sugarcrm\Sugarcrm\Security\Validator\ConstraintReturnValueInterface;

/**
 *
 * @see ConstraintReturnValueInterface
 *
 */
trait ConstraintReturnValueTrait
{
    /**
     * @var mixed
     */
    protected $formattedReturnValue;

    /**
     * {@inheritdoc}
     */
    public function getFormattedReturnValue()
    {
        return $this->formattedReturnValue;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormattedReturnValue($value)
    {
        $this->formattedReturnValue = $value;
    }
}
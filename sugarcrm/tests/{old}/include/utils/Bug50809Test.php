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

use PHPUnit\Framework\TestCase;

require_once 'include/utils/db_utils.php';

/**
 * @issue 50809
 */
class Bug50809Test extends TestCase
{
    public function testFromHtml()
    {
        $this->markTestIncomplete("HTML entities are case sensitive, this test is probably invalid");
        $this->assertEquals('FRIEND"S', from_html('FRIEND&QUOT;S'));
    }
}

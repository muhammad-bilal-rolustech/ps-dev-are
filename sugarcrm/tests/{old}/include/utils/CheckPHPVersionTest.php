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

class CheckPHPVersionTest extends TestCase
{
    public function providerPhpVersion()
    {
        return array(
            array('5.6.0', -1, 'Minimum valid version check failed.'),
            array('7.1.0-dev', -1, 'Minimum valid version check failed.'),
            array('7.1.0', 1, 'Supported version check Failed'),
            array('7.2.0', -1, 'Threshold Check Failed'),
            array('7.2.0-dev', -1, 'Threshold Check Failed'),
        );
    }

    /**
     * @dataProvider providerPhpVersion
     * @ticket 33202
     */
    public function testPhpVersion(
        $ver,
        $expected_retval,
        $message
    ) {
        $this->assertEquals($expected_retval, check_php_version($ver), $message);
    }

}

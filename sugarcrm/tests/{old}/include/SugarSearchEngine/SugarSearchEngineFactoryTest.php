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

class SugarSearchEngineFactoryTest extends TestCase
{
    /**
     * @dataProvider factoryProvider
     * @param string $engineName
     * @param string $expectedClass
     */
    public function testFactoryMethod($engineName, $expectedClass)
    {
        $instance = SugarSearchEngineFactory::getInstance($engineName);
        $this->assertContains($expectedClass, get_class($instance));
    }

    /**
     * SugarSearchEngine factory test
     * @return array
     */
    public static function factoryProvider()
    {
        switch(SugarSearchEngineFactory::getFTSEngineNameFromConfig()) {
            case 'Elastic'  : $default = 'SugarSearchEngineElastic'; break;
            default         : $default = 'SugarSearchEngine';
        }

        return array(
            // depends on config, disabled array('','SugarSearchEngine'),
            array('Elastic','SugarSearchEngineElastic'),
            //Fallback to default.
            array('BadClassName','SugarSearchEngine')
        );
    }
}

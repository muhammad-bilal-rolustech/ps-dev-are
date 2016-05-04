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

namespace Sugarcrm\SugarcrmTestsUnit\Console;

use Sugarcrm\Sugarcrm\Console\Application;
use Sugarcrm\Sugarcrm\Console\CommandRegistry\CommandRegistry;
use Sugarcrm\SugarcrmTestsUnit\Console\Fixtures\ApplicationTestCommandA;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 *
 * @coversDefaultClass \Sugarcrm\Sugarcrm\Console\Application
 *
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create
     * @dataProvider providerTestAvailableStockCommands
     */
    public function testAvailableStockCommands($mode, array $expected)
    {
        $app = Application::create($mode);
        $commands = $app->all();

        $this->assertEquals(
            count($expected),
            count($commands),
            'Amount of stock commands does not match expected count'
        );

        foreach ($expected as $name => $class) {
            $this->assertArrayHasKey($name, $commands);
            $this->assertInstanceOf($class, $commands[$name]);
        }
    }

    public function providerTestAvailableStockCommands()
    {
        return array(
            array(
                CommandRegistry::MODE_STANDALONE,
                array(
                    'help' => 'Symfony\Component\Console\Command\HelpCommand',
                    'list' => 'Symfony\Component\Console\Command\ListCommand',
                ),
            ),
            array(
                CommandRegistry::MODE_INSTANCE,
                array(
                    'help' => 'Symfony\Component\Console\Command\HelpCommand',
                    'list' => 'Symfony\Component\Console\Command\ListCommand',
                    'elastic:indices' => 'Sugarcrm\Sugarcrm\Console\Command\Api\ElasticsearchIndicesCommand',
                    'elastic:queue' => 'Sugarcrm\Sugarcrm\Console\Command\Api\ElasticsearchQueueCommand',
                    'elastic:routing' => 'Sugarcrm\Sugarcrm\Console\Command\Api\ElasticsearchRoutingCommand',
                    'search:fields' => 'Sugarcrm\Sugarcrm\Console\Command\Api\SearchFieldsCommand',
                    'search:reindex' => 'Sugarcrm\Sugarcrm\Console\Command\Api\SearchReindexCommand',
                    'search:status' => 'Sugarcrm\Sugarcrm\Console\Command\Api\SearchStatusCommand',
                ),
            ),
        );
    }

    /**
     * @covers ::getDefaultInputDefinition
     */
    public function testProfileInputDefinition()
    {
        $app = new Application(new CommandRegistry(), true);
        $this->assertTrue($app->getDefinition()->hasOption('profile'));
    }

    /**
     * @covers ::__construct
     * @covers ::doRun
     */
    public function testDoRun()
    {
        $app = new Application();
        $app->add(new ApplicationTestCommandA());
        $app->setAutoExit(false);

        $this->assertSame('SugarCRM Console', $app->getName());
        $this->assertSame('[standalone mode]', $app->getVersion());

        $tester = new ApplicationTester($app);

        // regular execution
        $tester->run(array('command' => 'apptest:A'));
        $this->assertEquals(
            'Success Application Test A' . PHP_EOL,
            $tester->getDisplay()
        );

        // execution with profiling
        $tester->run(array('command' => 'apptest:A', '--profile' => true));
        $this->assertRegExp(
            '/^Success Application Test A\n\nMemory usage: (.*) MB \(peak: (.*) MB\), time: (.*)s\n$/',
            $tester->getDisplay()
        );
    }

    /**
     * @covers ::getSugarVersion
     */
    public function testGetSugarVersion()
    {
        // make a backup of the current file
        $sugarVersionFile = SUGAR_BASE_DIR . '/sugar_version.php';
        $backupFile = SUGAR_BASE_DIR . '/sugar_version.testsunit';
        copy($sugarVersionFile, $backupFile);

        // version from source tree
        $this->setupSugarVersionFixture('sugar_version_source');
        $app = new Application();
        $this->assertEquals('[standalone mode]', $app->getVersion());

        // version from installed sugar
        $this->setupSugarVersionFixture('sugar_version_installed');
        $app = new Application();
        $this->assertEquals('7.7.0.0-ULT-1234', $app->getVersion());

        // corrupt sugar_version file
        $this->setupSugarVersionFixture('sugar_version_corrupt');
        $app = new Application();
        $this->assertEquals('[standalone mode]', $app->getVersion());

        // missing sugar version file (shouldnt happen, but got it covered)
        unlink($sugarVersionFile);
        $app = new Application();
        $this->assertEquals('[standalone mode]', $app->getVersion());

        // restore original version file
        copy($backupFile, $sugarVersionFile);
        unlink($backupFile);
    }

    /**
     * Setup sugar_verion.php fixture
     * @param string $file
     */
    protected function setupSugarVersionFixture($file)
    {
        $file = __DIR__ . '/Fixtures/' . $file . '.txt';
        copy($file, SUGAR_BASE_DIR . '/sugar_version.php');
    }

    /**
     * @covers ::setMode
     * @covers ::getMode
     */
    public function testSetGetMode()
    {
        $app = new Application();
        $this->assertSame('', $app->getMode());
        $app->setMode('yeaha');
        $this->assertSame('yeaha', $app->getMode());
    }
}

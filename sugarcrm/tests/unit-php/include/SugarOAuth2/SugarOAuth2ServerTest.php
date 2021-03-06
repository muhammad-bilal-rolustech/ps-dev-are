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

namespace Sugarcrm\SugarcrmTestsUnit\inc\SugarOAuth2;

use PHPUnit\Framework\TestCase;
use Sugarcrm\SugarcrmTestsUnit\TestReflection;

/**
 * @coversDefaultClass \SugarOAuth2Server
 */
class SugarOAuth2ServerTest extends TestCase
{
    /**
     * @var \SugarConfig
     */
    protected $sugarConfig;

    /**
     * @var \User
     */
    protected $currentUser = null;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->sugarConfig = \SugarConfig::getInstance();

        if (!empty($GLOBALS['current_user'])) {
            $this->currentUser = $GLOBALS['current_user'];
        }

        $GLOBALS['current_user'] = $this->createMock(\User::class);
    }

    protected function tearDown()
    {
        $this->sugarConfig->_cached_values = [];
        $GLOBALS['current_user'] = $this->currentUser;
    }
    /**
     * Provides data for testGetOAuth2Server
     * @return array
     */
    public function getOAuth2ServerProvider()
    {
        return [
            'oldOAuthServer' => [
                'idmMode' => [],
                'platform' => 'base',
                'expectedServerClass' => \SugarOAuth2Server::class,
                'expectedStorageClass' => \SugarOAuth2Storage::class,
            ],
            'oidcOAuthServer' => [
                'idmMode' => [
                    'clientId' => 'testLocal',
                    'clientSecret' => 'testLocalSecret',
                    'stsUrl' => 'http://localhost:4444',
                    'idpUrl' => 'http://sugar.dolbik.dev/idm205idp/web/',
                    'stsKeySetId' => 'testkey2',
                ],
                'platform' => 'base',
                'expectedServerClass' => \SugarOAuth2ServerOIDC::class,
                'expectedStorageClass' => \SugarOAuth2StorageOIDC::class,
            ],
            'portalPlatform' => [
                'idmMode' => [
                    'clientId' => 'testLocal',
                    'clientSecret' => 'testLocalSecret',
                    'stsUrl' => 'http://localhost:4444',
                    'idpUrl' => 'http://sugar.dolbik.dev/idm205idp/web/',
                    'stsKeySetId' => 'testkey2',
                ],
                'platform' => 'portal',
                'expectedServerClass' => \SugarOAuth2Server::class,
                'expectedStorageClass' => \SugarOAuth2Storage::class,
            ],
        ];
    }

    /**
     * @param array $idmMode
     * @param string $platform
     * @param $expectedServerClass
     * @param $expectedStorageClass
     *
     * @dataProvider getOAuth2ServerProvider
     * @covers ::getOAuth2Server
     */
    public function testGetOAuth2Server(array $idmMode, string $platform, $expectedServerClass, $expectedStorageClass)
    {
        $this->sugarConfig->_cached_values['idm_mode'] = $idmMode;
        $oAuthServer = SugarOAuth2ServerMock::getOAuth2Server($platform);
        $this->assertInstanceOf($expectedServerClass, $oAuthServer);
        $this->assertInstanceOf(
            $expectedStorageClass,
            TestReflection::getProtectedValue($oAuthServer, 'storage')
        );
    }

    /**
     * @expectedException \SugarApiExceptionNotFound
     * @covers ::getSudoToken
     */
    public function testGetSudoTokenStorageThrowException()
    {
        $storage = $this->createMock(\SugarOAuth2Storage::class);
        $ouath2Server = new \SugarOAuth2Server($storage, []);
        $storage->expects($this->once())
                ->method('loadUserFromName')
                ->with('testUser')
                ->willThrowException(new \SugarApiExceptionNeedLogin());
        $ouath2Server->getSudoToken('testUser', 'testClient', 'base');
    }

    /**
     * @expectedException \SugarApiExceptionNotFound
     * @covers ::getSudoToken
     */
    public function testGetSudoTokenStorageReturnNull()
    {
        $storage = $this->createMock(\SugarOAuth2Storage::class);
        $ouath2Server = new \SugarOAuth2Server($storage, []);
        $storage->expects($this->once())
            ->method('loadUserFromName')
            ->with('testUser')
            ->willReturn(null);
        $ouath2Server->getSudoToken('testUser', 'testClient', 'base');
    }
}

/**
 * Mock for SugarOAuth2Server to prevent caching
 */
class SugarOAuth2ServerMock extends \SugarOAuth2Server
{
    /**
     * @param string $platform
     * @return \SugarOAuth2Server
     */
    public static function getOAuth2Server($platform = null)
    {
        parent::$currentOAuth2Server = null;
        return parent::getOAuth2Server($platform);
    }
}

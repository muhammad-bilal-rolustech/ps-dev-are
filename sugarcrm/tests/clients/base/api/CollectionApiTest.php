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

require_once 'clients/base/api/CollectionApi.php';
require_once 'clients/base/api/CollectionApi/CollectionDefinition/CollectionDefinitionInterface.php';

/**
 * @covers CollectionApi
 */
class CollectionApiTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $api;

    public function setUp()
    {
        $this->api = $this->getMockForAbstractClass('CollectionApi');
    }

    /**
     * @dataProvider buildResponseProvider
     */
    public function testBuildResponse(array $records, array $offsets, array $errors, array $response)
    {
        $actual = SugarTestReflection::callProtectedMethod(
            $this->api,
            'buildResponse',
            array($records, $offsets, $errors)
        );

        $this->assertEquals($response, $actual);
    }

    public function buildResponseProvider()
    {
        $exception = new SugarApiExceptionNotAuthorized('SUGAR_API_EXCEPTION_RECORD_NOT_AUTHORIZED', array('view'));

        return array(
            'no_errors' => array(
                array(
                    array(
                        'a' => 'x',
                        '_link' => 'l1',
                    ),
                    array(
                        'a' => 'y',
                        '_link' => 'l2',
                    ),
                ),
                array(
                    'l1' => 1,
                    'l2' => -1,
                ),
                array(),
                array(
                    'records' => array(
                        array(
                            'a' => 'x',
                            '_link' => 'l1',
                        ),
                        array(
                            'a' => 'y',
                            '_link' => 'l2',
                        ),
                    ),
                    'next_offset' => array(
                        'l1' => 1,
                        'l2' => -1,
                    ),
                ),
            ),
            'all_errors' => array(
                array(),
                array(
                    'l1' => -1,
                    'l2' => -1,
                ),
                array(
                    'l1' => array(
                        'code' => $exception->getHttpCode(),
                        'error' => $exception->getErrorLabel(),
                        'error_message' => $exception->getMessage(),
                    ),
                    'l2' => array(
                        'code' => $exception->getHttpCode(),
                        'error' => $exception->getErrorLabel(),
                        'error_message' => $exception->getMessage(),
                    ),
                ),
                array(
                    'records' => array(),
                    'next_offset' => array(
                        'l1' => -1,
                        'l2' => -1,
                    ),
                    'errors' => array(
                        'l1' => array(
                            'code' => $exception->getHttpCode(),
                            'error' => $exception->getErrorLabel(),
                            'error_message' => $exception->getMessage(),
                        ),
                        'l2' => array(
                            'code' => $exception->getHttpCode(),
                            'error' => $exception->getErrorLabel(),
                            'error_message' => $exception->getMessage(),
                        ),
                    ),
                ),
            ),
            'some_errors' => array(
                array(
                    array(
                        'a' => 'x',
                        '_link' => 'l1',
                    ),
                    array(
                        'a' => 'y',
                        '_link' => 'l2',
                    ),
                ),
                array(
                    'l1' => 1,
                    'l2' => -1,
                ),
                array(
                    'l2' => array(
                        'code' => $exception->getHttpCode(),
                        'error' => $exception->getErrorLabel(),
                        'error_message' => $exception->getMessage(),
                    ),
                ),
                array(
                    'records' => array(
                        array(
                            'a' => 'x',
                            '_link' => 'l1',
                        ),
                        array(
                            'a' => 'y',
                            '_link' => 'l2',
                        ),
                    ),
                    'next_offset' => array(
                        'l1' => 1,
                        'l2' => -1,
                    ),
                    'errors' => array(
                        'l2' => array(
                            'code' => $exception->getHttpCode(),
                            'error' => $exception->getErrorLabel(),
                            'error_message' => $exception->getMessage(),
                        ),
                    ),
                ),
            ),
        );
    }

    public function testGetData()
    {
        /** @var CollectionApi|PHPUnit_Framework_MockObject_MockObject $api */
        $api = $this->getMockBuilder('CollectionApi')
            ->disableOriginalConstructor()
            ->setMethods(array('getSourceArguments', 'getSourceData'))
            ->getMockForAbstractClass();
        $api->expects($this->exactly(2))
            ->method('getSourceArguments')
            ->will($this->returnCallback(function () {
                return array();
            }));
        $api->expects($this->exactly(2))
            ->method('getSourceData')
            ->will($this->onConsecutiveCalls(array(
                'records' => array(
                    array('name' => 'a'),
                ),
            ), array(
                'records' => array(
                    array('name' => 'c1'),
                    array('name' => 'c2'),
                ),
            )));

        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->once())
            ->method('getSources')
            ->willReturn(array('a', 'b', 'c'));

        $service = SugarTestRestUtilities::getRestServiceMock();

        $actual = SugarTestReflection::callProtectedMethod(
            $api,
            'getData',
            array($service, array(
                'offset' => array(
                    'a' => 0,
                    'b' => -1,
                    'c' => 1,
                ),
            ), $definition, array(
                'a' => array(),
                'b' => array(),
                'c' => array(),
            ))
        );

        $this->assertEquals(array(
            'a' => array(
                'records' => array(
                    array('name' => 'a'),
                ),
            ),
            'c' => array(
                'records' => array(
                    array('name' => 'c1'),
                    array('name' => 'c2'),
                ),
            ),
        ), $actual);
    }

    public function testGetData_AllSubRequestsThrowExceptions()
    {
        $exception = new SugarApiExceptionNotAuthorized('SUGAR_API_EXCEPTION_RECORD_NOT_AUTHORIZED', array('view'));
        $error = array(
            'next_offset' => -1,
            'records' => array(),
            'error' => array(
                'code' => $exception->getHttpCode(),
                'error' => $exception->getErrorLabel(),
                'error_message' => $exception->getMessage(),
            ),
        );

        /** @var CollectionApi|PHPUnit_Framework_MockObject_MockObject $api */
        $api = $this->getMockBuilder('CollectionApi')
            ->disableOriginalConstructor()
            ->setMethods(array('getSourceArguments'))
            ->getMockForAbstractClass();
        $api->expects($this->any())
            ->method('getSourceArguments')
            ->will($this->returnValue(array()));
        $api->expects($this->any())
            ->method('getSourceData')
            ->will($this->throwException($exception));

        $service = SugarTestRestUtilities::getRestServiceMock();

        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->once())
            ->method('getSources')
            ->willReturn(array('a', 'b', 'c'));

        $actual = SugarTestReflection::callProtectedMethod(
            $api,
            'getData',
            array(
                $service,
                array(
                    'offset' => array(
                        'a' => 0,
                        'b' => 0,
                        'c' => 0,
                    ),
                ),
                $definition,
                array(
                    'a' => array(),
                    'b' => array(),
                    'c' => array(),
                ),
            )
        );

        $this->assertEquals(
            array(
                'a' => $error,
                'b' => $error,
                'c' => $error,
            ),
            $actual
        );
    }

    public function testGetData_SomeSubRequestsThrowExceptions()
    {
        $exception = new SugarApiExceptionNotAuthorized('SUGAR_API_EXCEPTION_RECORD_NOT_AUTHORIZED', array('view'));
        $error = array(
            'next_offset' => -1,
            'records' => array(),
            'error' => array(
                'code' => $exception->getHttpCode(),
                'error' => $exception->getErrorLabel(),
                'error_message' => $exception->getMessage(),
            ),
        );
        $records = array(
            'next_offset' => -1,
            'records' => array(
                array('name' => 'a'),
                array('name' => 'b'),
                array('name' => 'c'),
            ),
        );

        /** @var CollectionApi|PHPUnit_Framework_MockObject_MockObject $api */
        $api = $this->getMockBuilder('CollectionApi')
            ->disableOriginalConstructor()
            ->setMethods(array('getSourceArguments', 'getSourceData'))
            ->getMockForAbstractClass();
        $api->expects($this->any())
            ->method('getSourceArguments')
            ->will($this->returnValue(array()));
        $api->expects($this->at(1))
            ->method('getSourceData')
            ->will($this->throwException($exception));
        $api->expects($this->at(3))
            ->method('getSourceData')
            ->will($this->returnValue($records));
        $api->expects($this->at(5))
            ->method('getSourceData')
            ->will($this->throwException($exception));

        $service = SugarTestRestUtilities::getRestServiceMock();

        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->once())
            ->method('getSources')
            ->willReturn(array('a', 'b', 'c'));

        $actual = SugarTestReflection::callProtectedMethod(
            $api,
            'getData',
            array(
                $service,
                array(
                    'offset' => array(
                        'a' => 0,
                        'b' => 0,
                        'c' => 0,
                    ),
                ),
                $definition,
                array(
                    'a' => array(),
                    'b' => array(),
                    'c' => array(),
                ),
            )
        );

        $this->assertEquals(
            array(
                'a' => $error,
                'b' => $records,
                'c' => $error,
            ),
            $actual
        );
    }

    /**
     * @dataProvider getSourceArgumentsProvider
     */
    public function testGetSourceArguments(array $args, $source, $sortFields, array $expected)
    {
        $service = SugarTestRestUtilities::getRestServiceMock();

        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->once())
            ->method('hasFieldMap')
            ->willReturn(true);
        $definition->expects($this->once())
            ->method('getFieldMap')
            ->willReturn(array(
                'alias' => 'field',
            ));

        $actual = SugarTestReflection::callProtectedMethod(
            $this->api,
            'getSourceArguments',
            array($service, $args, $definition, $source, $sortFields)
        );

        $this->assertEquals($expected, $actual);
    }

    public static function getSourceArgumentsProvider()
    {
        return array(
            array(
                array(
                    'fields' => array('alias', 'another_field'),
                    'filter' => array(
                        '$or' => array(
                            'alias' => 'a',
                            'another_field' => 'b',
                        ),
                    ),
                    'order_by' => array(
                        'alias' => true,
                        'another_field' => false,
                    ),
                    'offset' => array(
                        'test_source' => 10,
                    ),
                    'max_num' => 20,
                ),
                'test_source',
                array('sort_field'),
                array(
                    'fields' => array('field', 'another_field', 'sort_field'),
                    'filter' => array(
                        '$or' => array(
                            'field' => 'a',
                            'another_field' => 'b',
                        ),
                    ),
                    'order_by' => 'field,another_field:desc',
                    'offset' => 10,
                    'max_num' => 20,
                ),
            )
        );
    }

    /**
     * @dataProvider normalizeArgumentsProvider
     */
    public function testNormalizeArguments(array $args, $orderBy, $expected)
    {
        /** @var CollectionApi|PHPUnit_Framework_MockObject_MockObject $api */
        $api = $this->getMockBuilder('CollectionApi')
            ->disableOriginalConstructor()
            ->setMethods(array('normalizeOffset', 'getDefaultLimit', 'getDefaultOrderBy'))
            ->getMockForAbstractClass();
        $api->expects($this->any())
            ->method('normalizeOffset')
            ->will($this->returnCallback(function () {
                return 'from-normalize-offset';
            }));
        $api->expects($this->any())
            ->method('getDefaultLimit')
            ->will($this->returnCallback(function () {
                return 'from-default-limit';
            }));
        $api->expects($this->any())
            ->method('getDefaultOrderBy')
            ->will($this->returnCallback(function () {
                return 'from-default-order-by';
            }));

        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->any())
            ->method('getOrderBy')
            ->willReturn($orderBy);

        $actual = SugarTestReflection::callProtectedMethod(
            $api,
            'normalizeArguments',
            array($args, $definition)
        );

        $this->assertEquals($expected, $actual);
    }

    public static function normalizeArgumentsProvider()
    {
        return array(
            'defaults' => array(
                array(),
                null,
                array(
                    'offset' => 'from-normalize-offset',
                    'max_num' => 'from-default-limit',
                    'order_by' => 'from-default-order-by',
                ),
            ),
            'from-arguments' => array(
                array(
                    'order_by' => 'order,by',
                    'max_num' => 25,
                ),
                null,
                array(
                    'order_by' => array(
                        'order' => true,
                        'by' => true,
                    ),
                    'max_num' => 25,
                    'offset' => 'from-normalize-offset',
                ),
            ),
            'from-link-definition' => array(
                array(),
                'defined,in:desc,link',
                array(
                    'offset' => 'from-normalize-offset',
                    'max_num' => 'from-default-limit',
                    'order_by' => array(
                        'defined' => true,
                        'in' => false,
                        'link' => true,
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider normalizeOffsetSuccess
     */
    public function testNormalizeOffsetSuccess($offset, array $expected)
    {
        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->once())
            ->method('getSources')
            ->willReturn(array('a'));

        $actual = SugarTestReflection::callProtectedMethod(
            $this->api,
            'normalizeOffset',
            array(
                array('offset' => $offset),
                $definition,
            )
        );

        $this->assertEquals($expected, $actual);
    }

    public static function normalizeOffsetSuccess()
    {
        return array(
            'default' => array(
                null,
                array(
                    'a' => 0,
                ),
            ),
            'integer' => array(
                array('a' => 1),
                array('a' => 1),
            ),
            'numeric-string' => array(
                array('a' => '-1'),
                array('a' => -1),
            ),
            'non-numeric-string' => array(
                array('a' => 'non-numeric-string'),
                array('a' => 0),
            ),
            'negative' => array(
                array('a' => -2),
                array('a' => -1),
            ),
            'irrelevant' => array(
                array(
                    'a' => 1,
                    'b' => 2,
                ),
                array('a' => 1),
            ),
        );
    }

    /**
     * @dataProvider normalizeOffsetFailure
     * @expectedException SugarApiExceptionInvalidParameter
     */
    public function testNormalizeOffsetFailure(array $offset)
    {
        $definition = $this->getMock('CollectionDefinitionInterface');
        SugarTestReflection::callProtectedMethod(
            $this->api,
            'normalizeOffset',
            array($offset, $definition)
        );
    }

    public static function normalizeOffsetFailure()
    {
        return array(
            'non-array' => array(
                array(
                    'offset' => 'a',
                ),
            ),
        );
    }

    /**
     * @dataProvider extractErrorsProvider
     */
    public function testExtractErrors(array $data, array $expectedData, array $expectedErrors)
    {
        $errors = SugarTestReflection::callProtectedMethod($this->api, 'extractErrors', array(&$data));

        $this->assertEquals($expectedData, $data);
        $this->assertEquals($expectedErrors, $errors);
    }

    public function extractErrorsProvider()
    {
        $exception = new SugarApiExceptionNotAuthorized('SUGAR_API_EXCEPTION_RECORD_NOT_AUTHORIZED', array('view'));

        return array(
            'no_errors' => array(
                array(
                    'l1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                            ),
                            array(
                                'a' => 'z',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    'l2' => array(
                        'records' => array(
                            array(
                                'a' => 'y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    'l1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                            ),
                            array(
                                'a' => 'z',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    'l2' => array(
                        'records' => array(
                            array(
                                'a' => 'y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(),
            ),
            'one_success_and_one_error' => array(
                array(
                    'l1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                            ),
                            array(
                                'a' => 'z',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    'l2' => array(
                        'records' => array(),
                        'next_offset' => -1,
                        'error' => array(
                            'code' => $exception->getHttpCode(),
                            'error' => $exception->getErrorLabel(),
                            'error_message' => $exception->getMessage(),
                        ),
                    ),
                ),
                array(
                    'l1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                            ),
                            array(
                                'a' => 'z',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    'l2' => array(
                        'records' => array(),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    'l2' => array(
                        'code' => $exception->getHttpCode(),
                        'error' => $exception->getErrorLabel(),
                        'error_message' => $exception->getMessage(),
                    ),
                ),
            ),
            'all_errors' => array(
                array(
                    'l1' => array(
                        'records' => array(),
                        'next_offset' => -1,
                        'error' => array(
                            'code' => $exception->getHttpCode(),
                            'error' => $exception->getErrorLabel(),
                            'error_message' => $exception->getMessage(),
                        ),
                    ),
                    'l2' => array(
                        'records' => array(),
                        'next_offset' => -1,
                        'error' => array(
                            'code' => $exception->getHttpCode(),
                            'error' => $exception->getErrorLabel(),
                            'error_message' => $exception->getMessage(),
                        ),
                    ),
                ),
                array(
                    'l1' => array(
                        'records' => array(),
                        'next_offset' => -1,
                    ),
                    'l2' => array(
                        'records' => array(),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    'l1' => array(
                        'code' => $exception->getHttpCode(),
                        'error' => $exception->getErrorLabel(),
                        'error_message' => $exception->getMessage(),
                    ),
                    'l2' => array(
                        'code' => $exception->getHttpCode(),
                        'error' => $exception->getErrorLabel(),
                        'error_message' => $exception->getMessage(),
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider sortDataProvider
     */
    public function testSortData(
        array $data,
        array $spec,
        $limit,
        array $offset,
        array $expectedRecords,
        array $expectedNextOffset
    ) {
        $this->assertNotEquals($expectedNextOffset, $offset);

        $records = SugarTestReflection::callProtectedMethod(
            $this->api,
            'sortData',
            array($data, $spec, $offset, $limit, &$nextOffset)
        );

        $this->assertEquals($expectedRecords, $records);
        $this->assertEquals($expectedNextOffset, $nextOffset);
    }

    public function sortDataProvider()
    {
        return array(
            'strings' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                            ),
                            array(
                                'a' => 'z',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'a' => 'Y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('a'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    )
                ),
                3,
                array(
                    's1' => 0,
                    's2' => 0,
                ),
                array(
                    array(
                        'a' => 'x',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => 'Y',
                        '_source' => 's2',
                    ),
                    array(
                        'a' => 'z',
                        '_source' => 's1',
                    ),
                ),
                array(
                    's1' => -1,
                    's2' => -1,
                ),
            ),
            'numbers' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => '10',
                            ),
                            array(
                                'a' => '100',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'a' => '11',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('a'),
                        ),
                        'is_numeric' => true,
                        'direction' => true,
                    )
                ),
                3,
                array(
                    's1' => 0,
                    's2' => 0,
                ),
                array(
                    array(
                        'a' => '10',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => '11',
                        '_source' => 's2',
                    ),
                    array(
                        'a' => '100',
                        '_source' => 's1',
                    ),
                ),
                array(
                    's1' => -1,
                    's2' => -1,
                ),
            ),
            'reverse' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => 'z',
                            ),
                            array(
                                'a' => 'x',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'a' => 'Y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('a'),
                        ),
                        'is_numeric' => false,
                        'direction' => false,
                    )
                ),
                3,
                array(
                    's1' => 0,
                    's2' => 0,
                ),
                array(
                    array(
                        'a' => 'z',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => 'Y',
                        '_source' => 's2',
                    ),
                    array(
                        'a' => 'x',
                        '_source' => 's1',
                    ),
                ),
                array(
                    's1' => -1,
                    's2' => -1,
                ),
            ),
            'multiple-sources-and-aliasing' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'b' => 'z',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's3' => array(
                        'records' => array(
                            array(
                                'c' => 'y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('b'),
                            's3' => array('c'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    )
                ),
                3,
                array(
                    's1' => 0,
                    's2' => 0,
                    's3' => 0,
                ),
                array(
                    array(
                        'a' => 'x',
                        '_source' => 's1',
                    ),
                    array(
                        'c' => 'y',
                        '_source' => 's3',
                    ),
                    array(
                        'b' => 'z',
                        '_source' => 's2',
                    ),
                ),
                array(
                    's1' => -1,
                    's2' => -1,
                    's3' => -1,
                ),
            ),
            'multiple-columns' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                                'b' => 'x',
                            ),
                            array(
                                'a' => 'y',
                                'b' => 'y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'a' => 'x',
                                'b' => 'y',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('a'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    ),
                    array(
                        'map' => array(
                            's1' => array('b'),
                            's2' => array('b'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    ),
                ),
                3,
                array(
                    's1' => 0,
                    's2' => 0,
                ),
                array(
                    array(
                        'a' => 'x',
                        'b' => 'x',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => 'x',
                        'b' => 'y',
                        '_source' => 's2',
                    ),
                    array(
                        'a' => 'y',
                        'b' => 'y',
                        '_source' => 's1',
                    ),
                ),
                array(
                    's1' => -1,
                    's2' => -1,
                ),
            ),
            'multiple-fields-in-sort-on' => array(
                array(
                    'accounts' => array(
                        'records' => array(
                            array(
                                'name' => 'Alpha Bank',
                            ),
                            array(
                                'name' => 'General Electric',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    'contacts' => array(
                        'records' => array(
                            array(
                                'first_name' => 'John',
                                'last_name' => 'Doe',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            'accounts' => array('name'),
                            'contacts' => array('last_name', 'first_name'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    ),
                ),
                3,
                array(
                    'accounts' => 0,
                    'contacts' => 0,
                ),
                array(
                    array(
                        'name' => 'Alpha Bank',
                        '_source' => 'accounts',
                    ),
                    array(
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        '_source' => 'contacts',
                    ),
                    array(
                        'name' => 'General Electric',
                        '_source' => 'accounts',
                    ),
                ),
                array(
                    'accounts' => -1,
                    'contacts' => -1,
                ),
            ),
            'limit-and-offset' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => 'a',
                            ),
                            array(
                                'a' => 'c',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'a' => 'b',
                            ),
                            array(
                                'a' => 'd',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's3' => array(
                        'records' => array(
                            array(
                                'a' => 'e',
                            ),
                            array(
                                'a' => 'f',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('a'),
                            's3' => array('a'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    )
                ),
                2,
                array(
                    's1' => 1,
                    's2' => 2,
                    's3' => 0,
                    's4' => -1,
                ),
                array(
                    array(
                        'a' => 'a',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => 'b',
                        '_source' => 's2',
                    ),
                ),
                array(
                    's1' => 2,
                    's2' => 3,
                    's3' => 0,
                    's4' => -1,
                ),
            ),
            'database-order-preserved' => array(
                array(
                    's1' => array(
                        'records' => array(
                            array(
                                'a' => 'ä',
                            ),
                            array(
                                'a' => 'a',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                    's2' => array(
                        'records' => array(
                            array(
                                'a' => 'ü',
                            ),
                            array(
                                'a' => 'u',
                            ),
                        ),
                        'next_offset' => -1,
                    ),
                ),
                array(
                    array(
                        'map' => array(
                            's1' => array('a'),
                            's2' => array('a'),
                        ),
                        'is_numeric' => false,
                        'direction' => true,
                    )
                ),
                4,
                array(
                    's1' => 0,
                    's2' => 0,
                ),
                array(
                    array(
                        'a' => 'ä',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => 'a',
                        '_source' => 's1',
                    ),
                    array(
                        'a' => 'ü',
                        '_source' => 's2',
                    ),
                    array(
                        'a' => 'u',
                        '_source' => 's2',
                    ),
                ),
                array(
                    's1' => -1,
                    's2' => -1,
                ),
            ),
        );
    }

    /**
     * @dataProvider mapFilterSuccessProvider
     */
    public function testMapFilterSuccess(array $filter, array $fieldMap, array $expected)
    {
        $actual = SugarTestReflection::callProtectedMethod($this->api, 'mapFilter', array($filter, $fieldMap));
        $this->assertEquals($expected, $actual);
    }

    public static function mapFilterSuccessProvider()
    {
        return array(
            'simple' => array(
                array(
                    'a-alias' => 1,
                ),
                array(
                    'a-alias' => 'a',
                ),
                array(
                    'a' => 1,
                ),
            ),
            'cyclic' => array(
                array(
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                ),
                array(
                    'b' => 'a',
                    'c' => 'b',
                    'a' => 'c',
                    'd' => 'e',
                ),
                array(
                    'c' => 1,
                    'a' => 2,
                    'b' => 3,
                    'e' => 4,
                ),
            ),
            'recursive' => array(
                array(
                    'q' => 1,
                    '$or' => array(
                        'r' => array(
                            '$and' => array(
                                's' => 2,
                                't' => 3,
                            )
                        ),
                        'u' => 4,
                    ),
                ),
                array(
                    'q' => 'a',
                    'r' => 'b',
                    's' => 'c',
                    't' => 'd',
                    'u' => 'e',
                ),
                array(
                    'a' => 1,
                    '$or' => array(
                        'b' => array(
                            '$and' => array(
                                'c' => 2,
                                'd' => 3,
                            )
                        ),
                        'e' => 4,
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider mapFilterFailureProvider
     * @expectedException SugarApiExceptionInvalidParameter
     */
    public function testMapFilterFailure(array $filter, array $fieldMap)
    {
        SugarTestReflection::callProtectedMethod($this->api, 'mapFilter', array($filter, $fieldMap));
    }

    public static function mapFilterFailureProvider()
    {
        return array(
            'alias-conflict' => array(
                array(
                    'a' => 1,
                    'b' => 1,
                ),
                array(
                    'a' => 'c',
                    'b' => 'c',
                ),
            ),
        );
    }

    /**
     * @dataProvider mapOrderBySuccessProvider
     */
    public function testMapOrderBySuccess(array $orderBy, array $fieldMap, $expected)
    {
        $actual = SugarTestReflection::callProtectedMethod($this->api, 'mapOrderBy', array($orderBy, $fieldMap));
        $this->assertEquals($expected, $actual);
    }

    public static function mapOrderBySuccessProvider()
    {
        return array(
            array(
                array(
                    'a' => true,
                    'b' => false,
                    'c' => true,
                ),
                array(
                    'b' => 'a',
                    'a' => 'b',
                ),
                array(
                    'b' => true,
                    'a' => false,
                    'c' => true,
                ),
            ),
        );
    }

    /**
     * @dataProvider mapOrderByFailureProvider
     * @expectedException SugarApiExceptionInvalidParameter
     */
    public function testMapOrderByFailure(array $orderBy, array $fieldMap)
    {
        SugarTestReflection::callProtectedMethod($this->api, 'mapOrderBy', array($orderBy, $fieldMap));
    }

    public static function mapOrderByFailureProvider()
    {
        return array(
            'alias-conflict' => array(
                array(
                    'a' => true,
                    'b' => false,
                ),
                array(
                    'a' => 'c',
                    'b' => 'c',
                ),
            ),
        );
    }

    /**
     * @dataProvider mapFieldsProvider
     */
    public function testMapFields(array $fields, array $fieldMap, array $expected)
    {
        $actual = SugarTestReflection::callProtectedMethod($this->api, 'mapFields', array($fields, $fieldMap));
        $this->assertEquals($expected, $actual);
    }

    public static function mapFieldsProvider()
    {
        return array(
            array(
                array('a-alias', 'b-alias', 'c'),
                array(
                    'a-alias' => 'a',
                    'b-alias' => 'b',
                ),
                array('a', 'b', 'c'),
            ),
        );
    }

    /**
     * @dataProvider formatOrderByProvider
     */
    public function testFormatOrderBy($string, array $array)
    {
        $actual = SugarTestReflection::callProtectedMethod($this->api, 'formatOrderBy', array($array));
        $this->assertEquals($string, $actual);
    }

    public static function formatOrderByProvider()
    {
        return array(
            array(
                'a,b:desc',
                array(
                    'a' => true,
                    'b' => false,
                ),
            ),
        );
    }

    /**
     * @dataProvider getAdditionalSortFieldsProvider
     */
    public function testGetAdditionalSortFields(array $args, array $sources, array $sortSpec, array $expected)
    {
        $definition = $this->getMock('CollectionDefinitionInterface');
        $definition->expects($this->once())
            ->method('getSources')
            ->willReturn($sources);

        $actual = SugarTestReflection::callProtectedMethod(
            $this->api,
            'getAdditionalSortFields',
            array($args, $definition, $sortSpec)
        );

        $this->assertEquals($expected, $actual, 'Incorrect additional sort fields generated');

    }

    public static function getAdditionalSortFieldsProvider()
    {
        return array(
            array(
                array(
                    'fields' => array('id', 'name', 'date_entered'),
                ),
                array(
                    'accounts',
                    'contacts',
                ),
                array(
                    array(
                        'map' => array(
                            'accounts' => array(),
                            'contacts' => array(
                                'last_name',
                            ),
                        ),
                    ),
                    array(
                        'map' => array(
                            'accounts' => array(
                                'date_entered',
                            ),
                            'contacts' => array(
                                'date_entered',
                            ),
                        ),
                    ),
                ),
                array(
                    'accounts' => array(),
                    'contacts' => array(
                        'last_name',
                    ),
                ),
            ),
        );
    }

    /**
     * @dataProvider cleanDataProvider
     */
    public function testCleanData($records, $sortFields, $expected)
    {
        $actual = SugarTestReflection::callProtectedMethod(
            $this->api,
            'cleanData',
            array($records, $sortFields)
        );

        $this->assertEquals($expected, $actual,'Unrequested fields not removed from return data.');
    }

    public static function cleanDataProvider()
    {
        return array(
            array(
                array(
                    array(
                        'id' => 123,
                        'title' => 'Sales Executive',
                        'name' => 'John Smith',
                        'last_name' => 'Smith',
                        '_source' => 'contacts',
                    ),
                    array(
                        'id' => 456,
                        'title' => 'Sgr Manager',
                        'name' => 'Peter Hanks',
                        '_source' => 'users',
                    ),
                ),
                array(
                    'contacts' => array('last_name'),
                    'users' => array(),
                ),
                array(
                    array(
                        'id' => 123,
                        'title' => 'Sales Executive',
                        'name' => 'John Smith',
                        '_source' => 'contacts',
                    ),
                    array(
                        'id' => 456,
                        'title' => 'Sgr Manager',
                        'name' => 'Peter Hanks',
                        '_source' => 'users',
                    ),
                ),
            ),
        );
    }
}

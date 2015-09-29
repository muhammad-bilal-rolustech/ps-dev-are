<?php

namespace Sugarcrm\SugarcrmTestsUnit\MetaData;

use Sugarcrm\Sugarcrm\MetaData\RefreshQueue;

/**
 * @coversDefaultClass RefreshQueue
 */
class RefreshQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RefreshQueue
     */
    private $queue;

    protected function setUp()
    {
        $this->queue = new RefreshQueue();
    }

    /**
     * @dataProvider queueProvider
     * @covers ::enqueue
     * @covers ::dequeue
     */
    public function testQueue(array $input, array $expected)
    {
        foreach ($input as $task) {
            list($category, $item, $params) = $task;
            $this->queue->enqueue($category, $item, $params);
        }

        $output = array();
        while ($task = $this->queue->dequeue()) {
            $output[] = $task;
        }

        $this->assertEquals($expected, $output);
    }

    public static function queueProvider()
    {
        $rebuildAccountsBase = array('rebuild',
            array('Accounts'),
            array(
                'platforms' => array('base'),
            ),
        );

        $rebuildContactsMobile = array('rebuild',
            array('Contacts'),
            array(
                'platforms' => array('mobile'),
            ),
        );

        $rebuildAccountsAll = array('rebuild',
            array('Accounts'),
            array(),
        );

        $rebuildAccountsAndContactsMobile = array('rebuild',
            array('Accounts', 'Contacts'),
            array(
                'platforms' => array('mobile'),
            ),
        );

        $rebuildContactsAndCasesAll = array('rebuild',
            array('Contacts', 'Cases'),
            array(),
        );

        return array(
            'one-task-returned-as-is' => array(
                array(
                    $rebuildAccountsBase,
                ),
                array(
                    $rebuildAccountsBase,
                ),
            ),
            'merge-params-some-and-all' => array(
                array(
                    $rebuildAccountsBase,
                    $rebuildAccountsAll,
                ),
                array(
                    $rebuildAccountsAll,
                ),
            ),
            'merge-params-all-and-some' => array(
                array(
                    $rebuildAccountsAll,
                    $rebuildAccountsBase,
                ),
                array(
                    $rebuildAccountsAll,
                ),
            ),
            'merge-items' => array(
                array(
                    $rebuildAccountsAll,
                    $rebuildContactsAndCasesAll,
                ),
                array(
                    array('rebuild',
                        array('Accounts', 'Contacts', 'Cases'),
                        array(),
                    ),
                ),
            ),
            'extract-item-from-sub-scope' => array(
                array(
                    $rebuildAccountsAndContactsMobile,
                    $rebuildContactsAndCasesAll,
                ),
                array(
                    array('rebuild',
                        array('Accounts'),
                        array(
                            'platforms' => array('mobile'),
                        ),
                    ),
                    array('rebuild',
                        array('Contacts', 'Cases'),
                        array(),
                    ),
                ),
            ),
            'no-intersection' => array(
                array(
                    $rebuildAccountsBase,
                    $rebuildContactsMobile,
                ),
                array(
                    $rebuildAccountsBase,
                    $rebuildContactsMobile,
                ),
            ),
        );
    }
}
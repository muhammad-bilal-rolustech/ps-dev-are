<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


require_once('modules/TimePeriods/TimePeriod.php');

class Quarter544TimePeriodTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected static $tp;
    protected static $leaves;

    public static function setUpBeforeClass() {
        self::$tp = SugarTestTimePeriodUtilities::createITimePeriod("Quarter544", true);
        self::$tp->buildLeaves('Month');
        self::$leaves = self::$tp->getLeaves();

        SugarTestTimePeriodUtilities::addTimePeriod(self::$leaves[0]);
        SugarTestTimePeriodUtilities::addTimePeriod(self::$leaves[1]);
        SugarTestTimePeriodUtilities::addTimePeriod(self::$leaves[2]);
        parent::setUpBeforeClass();
    }


    public function setUp()
    {
        SugarTestHelper::setUp('app_strings');
    }

    public function tearDown()
    {
        SugarTestHelper::tearDown();

    }

    public static function tearDownAfterClass() {
        SugarTestTimePeriodUtilities::removeAllCreatedTimePeriods();

        parent::tearDownAfterClass();
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function testCreateNextTimePeriod()
    {
        global $app_strings;

        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $baseTimePeriod = self::$tp;

        $nextTimePeriod = $baseTimePeriod->createNextTimePeriod();
        $nextTimePeriod->name = "SugarTestCreatedNextQuarter544TimePeriod";
        $nextTimePeriod->save();
        SugarTestTimePeriodUtilities::addTimePeriod($nextTimePeriod);
        $nextTimePeriod = BeanFactory::getBean('Quarter544TimePeriods', $nextTimePeriod->id);

        //next timeperiod (1 year from today)
        $nextStartDate = $timedate->fromDBDate($baseTimePeriod->start_date);
        $nextStartDate = $nextStartDate->modify("+13 week");
        $nextEndDate = $timedate->fromDBDate($baseTimePeriod->start_date);
        $nextEndDate = $nextEndDate->modify("+26 week");
        $nextEndDate = $nextEndDate->modify("-1 day");

        $this->assertEquals($nextStartDate, $timedate->fromDBDate($nextTimePeriod->start_date));

        $this->assertEquals($nextEndDate, $timedate->fromDBDate($nextTimePeriod->end_date));
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function testGetNextPeriod()
    {

        global $app_strings;

        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $baseTimePeriod = self::$tp;

        $nextTimePeriod = $baseTimePeriod->getNextTimePeriod();

        //next timeperiod (1 year from today)
        $nextStartDate = $timedate->fromDBDate($baseTimePeriod->start_date);
        $nextStartDate = $nextStartDate->modify("+13 week");
        $nextEndDate = $timedate->fromDBDate($baseTimePeriod->start_date);
        $nextEndDate = $nextEndDate->modify("+26 week");
        $nextEndDate = $nextEndDate->modify("-1 day");

        //todo make expected on the left
        $this->assertEquals($timedate->asDbDate($nextStartDate), $nextTimePeriod->start_date);
        $this->assertEquals($timedate->asDbDate($nextEndDate), $nextTimePeriod->end_date);
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function testMonthLeavesCreated()
    {
        global $app_strings;

        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $baseTimePeriod = self::$tp;

        $leaves = $baseTimePeriod->getLeaves();
        $this->assertEquals(3, count(self::$leaves), "Incorrect Number Of Leaves Created");
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function test1stMonthLeafBounds()
    {
        global $app_strings;

        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $baseTimePeriod = self::$tp;
        $startDate = $timedate->fromDbDate($baseTimePeriod->start_date);
        $endDate = $timedate->fromDbDate($baseTimePeriod->start_date);
        $endDate = $endDate->modify("+5 week");
        $endDate = $endDate->modify("-1 day");
        $startDate->setTime(0,0,0);
        $endDate->setTime(23,59,59);

        $this->assertEquals($startDate->getTimestamp(), self::$leaves[0]->start_date_timestamp, "1st month start timestamp is wrong");
        $this->assertEquals($endDate->getTimestamp(), self::$leaves[0]->end_date_timestamp, "1st month end timestamp is wrong");
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function test2ndMonthCalendarLeafBounds()
    {
        global $app_strings;

        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $baseTimePeriod = self::$tp;
        $startDate = $timedate->fromDbDate($baseTimePeriod->start_date);
        $endDate = $timedate->fromDbDate($baseTimePeriod->start_date);
        $startDate = $startDate->modify("+5 week");
        $endDate = $endDate->modify("+9 week");
        $endDate = $endDate->modify("-1 day");
        $startDate->setTime(0,0,0);
        $endDate->setTime(23,59,59);

        $this->assertEquals($startDate->getTimestamp(), self::$leaves[1]->start_date_timestamp, "2nd month start timestamp is wrong");
        $this->assertEquals($endDate->getTimestamp(), self::$leaves[1]->end_date_timestamp, "2nd month end timestamp is wrong");
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function test3rdMonthCalendarLeafBounds()
    {
        global $app_strings;

        $timedate = TimeDate::getInstance();
        //get current timeperiod
        $baseTimePeriod = self::$tp;
        $startDate = $timedate->fromDbDate($baseTimePeriod->start_date);
        $endDate = $timedate->fromDbDate($baseTimePeriod->start_date);
        $startDate = $startDate->modify("+9 week");
        $endDate = $endDate->modify("+13 week");
        $endDate = $endDate->modify("-1 day");
        $startDate->setTime(0,0,0);
        $endDate->setTime(23,59,59);

        $this->assertEquals($startDate->getTimestamp(), self::$leaves[2]->start_date_timestamp, "3rd month start timestamp is wrong");
        $this->assertEquals($endDate->getTimestamp(), self::$leaves[2]->end_date_timestamp, "3rd month end timestamp is wrong");
    }
    /**
     * @group forecasts
     * @group timeperiods
     */
    public function testBuildLeavesAlreadyExistException()
    {
        global $app_strings;
        $exceptionThrown = false;
        //get current timeperiod
        $baseTimePeriod = self::$tp;
        try {
            $baseTimePeriod->buildLeaves();
        } catch (Exception $e) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }

    /**
     * @group forecasts
     * @group timeperiods
     */
    public function testBuildLeavesOnLeafException()
    {
        global $app_strings;
        $exceptionThrown = false;
        //get a leaf
        $leaf = self::$leaves[0];
        try {
            $leaf->buildLeaves();
        } catch (Exception $e) {
            $exceptionThrown = true;
        }
        $this->assertTrue($exceptionThrown);
    }

}
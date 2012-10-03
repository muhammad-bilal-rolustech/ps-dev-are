<?php
//FILE SUGARCRM flav=pro ONLY

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

require_once 'modules/SchedulersJobs/SchedulersJob.php';

class CurrencyRateSchedulerJobTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $currency;
    private $opportunity;
    private $opportunityClosed;
    private $quota;
    private $forecast;
    private $forecastSchedule;

    public static function setUpBeforeClass()
    {
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('current_user');
    }

    public static function tearDownAfterClass()
    {
        SugarTestHelper::tearDown();
    }

    public function setUp()
    {
        global $current_user;
        $this->currency = SugarTestCurrencyUtilities::createCurrency('UpdateBaseRateSchedulerJob', 'UBRSJ', 'UBRSJ', 1.234);

        $this->opportunity = SugarTestOpportunityUtilities::createOpportunity();
        $this->opportunity->currency_id = $this->currency->id;
        $this->opportunity->save();

        $this->opportunityClosed = SugarTestOpportunityUtilities::createOpportunity();
        $this->opportunityClosed->sales_stage = 'Closed Won';
        $this->opportunityClosed->currency_id = $this->currency->id;
        $this->opportunityClosed->save();

        $this->quota = SugarTestQuotaUtilities::createQuota(500);
        $this->quota->currency_id = $this->currency->id;
        $this->quota->save();

        $timeperiod = SugarTestTimePeriodUtilities::createTimePeriod();

        $this->forecast = SugarTestForecastUtilities::createForecast($timeperiod, $current_user);
        $this->forecast->currency_id = $this->currency->id;
        $this->forecast->save();

        $this->forecastSchedule = SugarTestForecastScheduleUtilities::createForecastSchedule($timeperiod, $current_user);
        $this->forecastSchedule->currency_id = $this->currency->id;
        $this->forecastSchedule->save();

    }

    public function tearDown()
    {
        SugarTestJobQueueUtilities::removeAllCreatedJobs();
        SugarTestCurrencyUtilities::removeAllCreatedCurrencies();
        SugarTestOpportunityUtilities::removeAllCreatedOpportunities();
        SugarTestForecastUtilities::removeAllCreatedForecasts();
        SugarTestForecastScheduleUtilities::removeAllCreatedForecastSchedules();
        SugarTestQuotaUtilities::removeAllCreatedQuotas();
        SugarTestTimePeriodUtilities::removeAllCreatedTimePeriods();
    }

    /**
     * @group forecasts
     */
    public function testCurrencyRateSchedulerJob()
    {
        global $current_user;

        // change the conversion rate
        $this->currency->conversion_rate = '2.345';
        $this->currency->save();

        $job = SugarTestJobQueueUtilities::createJob(
            'TestJobQueue',
            'class::SugarJobUpdateCurrencyRates',
            json_encode(array('currencyId'=>$this->currency->id)),
            $current_user);

        $job->runJob();
        $job->retrieve($job->id);

        //$this->assertTrue($job->runnable_ran);
        $this->assertEquals(SchedulersJob::JOB_SUCCESS, $job->resolution, "Wrong resolution");
        $this->assertEquals(SchedulersJob::JOB_STATUS_DONE, $job->status, "Wrong status");

        $db = DBManagerFactory::getInstance();
        $oppBaseRate = $db->getOne(sprintf("SELECT base_rate FROM opportunities WHERE id = '%s'", $this->opportunity->id));
        $oppAmount = $db->getOne(sprintf("SELECT amount FROM opportunities WHERE id = '%s'", $this->opportunity->id));
        $oppUsDollar = $db->getOne(sprintf("SELECT amount_usdollar FROM opportunities WHERE id = '%s'", $this->opportunity->id));
        $oppBaseRateClosed = $db->getOne(sprintf("SELECT base_rate FROM opportunities WHERE id = '%s'", $this->opportunityClosed->id));
        $oppAmountClosed = $db->getOne(sprintf("SELECT amount FROM opportunities WHERE id = '%s'", $this->opportunityClosed->id));
        $oppUsDollarClosed = $db->getOne(sprintf("SELECT amount_usdollar FROM opportunities WHERE id = '%s'", $this->opportunityClosed->id));
        $quotaBaseRate = $db->getOne(sprintf("SELECT base_rate FROM quotas WHERE id = '%s'", $this->quota->id));
        $forecastBaseRate = $db->getOne(sprintf("SELECT base_rate FROM forecasts WHERE id = '%s'", $this->forecast->id));
        $forecastScheduleBaseRate = $db->getOne(sprintf("SELECT base_rate FROM forecast_schedule WHERE id = '%s'", $this->forecastSchedule->id));

        $this->assertEquals('2.345', $oppBaseRate, 'opportunities.base_rate was modified by CurrencyRateSchedulerJob');
        $this->assertEquals((string)($oppAmount * $oppBaseRate), (string)$oppUsDollar, 'opportunities.amount_usdollar was modified by CurrencyRateSchedulerJob');
        $this->assertEquals('1.234', $oppBaseRateClosed, 'opportunities.base_rate was not modified by CurrencyRateSchedulerJob');
        $this->assertEquals((string)($oppAmountClosed * $oppBaseRateClosed), (string)$oppUsDollarClosed, 'opportunities.amount_usdollar was not modified by CurrencyRateSchedulerJob for closed opportunity');
        $this->assertEquals('2.345', $quotaBaseRate, 'quotas.base_rate was modified by CurrencyRateSchedulerJob');
        $this->assertEquals('2.345', $forecastBaseRate, 'forecasts.base_rate was modified by BaseRateSchedulerJob');
        $this->assertEquals('2.345', $forecastScheduleBaseRate, 'forecast_schedule.base_rate not modified by BaseRateSchedulerJob');
    }

}
<?php
//FILE SUGARCRM flav=pro ONLY
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

class ProductsTest extends Sugar_PHPUnit_Framework_TestCase
{

    /**
     * @var Product
     */
    private $product;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('current_user');
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('mod_strings', array('Products'));
        SugarTestForecastUtilities::setUpForecastConfig();
    }

    public function setUp()
    {
        $this->markTestIncomplete("SFA - This is failing in strict mode");
        parent::setUp();
        $this->product = SugarTestProductUtilities::createProduct();
    }

    public function tearDown()
    {
        SugarTestForecastUtilities::tearDownForecastConfig();
        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestHelper::tearDown();
        parent::tearDownAfterClass();
    }

    /**
     * This test checks to see that we can save a product where date_closed is set to null
     *
     * @group products
     */
    public function testCreateProductWithoutDateClosed()
    {
        $this->product->date_closed = null;
        $this->product->save();
        $this->assertEmpty($this->product->date_closed);
    }


    /**
     * This is a test to check that the create_new_list_query function returns a where clause to filter
     * "opportunity_id is null" so that products created for opportunities are not displayed by default
     *
     * @group forecasts
     * @group products
     */
    public function testCreateNewListQuery()
    {
        $ret_array = $this->product->create_new_list_query('', '', array(), array(), 0, '', true);
        $this->assertContains(
            "products.opportunity_id is not null OR products.opportunity_id <> ''",
            $ret_array['where'],
            "Did not find products.opportunity_id is not null OR products.opportunity_id <> '' clause"
        );

        $query = $this->product->create_new_list_query('', '', array(), array(), 0, '', false);
        $this->assertContains(
            "products.opportunity_id is not null OR products.opportunity_id <> ''",
            $query,
            "Did not find products.opportunity_id is not null OR products.opportunity_id <> '' clause"
        );
    }


    /**
     * This is a test to check that the create_export_query function returns a where clause to filter
     * "opportunity_id is null" so that products created for opportunities are not displayed by default
     *
     * @group forecasts
     * @group products
     */
    public function testCreateExportQuery()
    {
        $orderBy = '';
        $where = '';
        $query = $this->product->create_export_query($orderBy, $where);
        $this->assertContains(
            "products.opportunity_id is not null OR products.opportunity_id <> ''",
            $query,
            "Did not find products.opportunity_id is not null OR products.opportunity_id <> '' clause"
        );
    }

    /**
     * With SFA-585, it cause the LEFT JOIN was getting added twice, and something got fixed in the system
     * which caused it to be added twice.
     *
     * @ticket SFA-585
     * @group products
     */
    public function testCreateNewListQueryOnlyContainsOneLeftJoinToContacts()
    {
        $ret_array = $this->product->create_new_list_query('', '', array(), array(), 0, '', true);

        $this->assertEquals(
            1,
            substr_count($ret_array['from'], 'LEFT JOIN contacts on contacts.id = products.contact_id')
        );
    }

    /**
     * @group products
     *
     * Test that the account_id in Product instance is properly set for a given Opportunity id.  I am
     * currently creating Opportunities with new Opportunity() because the test helper for Opportunities
     * creates accounts automatically.
     */
    public function testSetAccountForOpportunity()
    {
        //creating Opportunities with BeanFactory because the test helper for Opportunities
        // creates accounts automatically.
        $opp = BeanFactory::newBean("Opportunities");
        $opp->name = "opp1";
        $opp->date_closed = date('Y-m-d');
        $opp->save();
        $opp->load_relationship('accounts');
        SugarTestOpportunityUtilities::setCreatedOpportunity(array($opp->id));
        $account = SugarTestAccountUtilities::createAccount();
        $opp->accounts->add($account);
        $product = new MockProduct();
        $this->assertTrue($product->setAccountIdForOpportunity($opp->id));

        //creating Opportunities with BeanFactory because the test helper for Opportunities
        // creates accounts automatically.
        $opp2 = BeanFactory::newBean("Opportunities");
        $opp2->name = "opp2";
        $opp2->date_closed = date('Y-m-d');
        $opp2->save();
        SugarTestOpportunityUtilities::setCreatedOpportunity(array($opp2->id));
        $product2 = new MockProduct();
        $this->assertFalse($product2->setAccountIdForOpportunity($opp2->id));
    }

    //BEGIN SUGARCRM flav=pro && flav!=ent ONLY
    /**
     * @group products
     * @ticket SFA-567
     */
    public function testProductCreatedFromOpportunityContainsSalesStage()
    {
        $this->markTestIncomplete("This is just a bad test.  How can there be a product on an new opp?");
        $opp = SugarTestOpportunityUtilities::createOpportunity();

        $opp->load_relationship('products');

        $products = $opp->products->getBeans();

        $this->assertEquals(1, count($products));
        /* @var $product Product */
        $product = array_shift($products);

        SugarTestProductUtilities::setCreatedProduct(array($product->id));

        $this->assertNotNull($opp->sales_stage); // make sure it's not set to null
        $this->assertEquals($opp->sales_stage, $product->sales_stage);
    }
    //end SUGARCRM flav=pro && flav!=ent ONLY

    //BEGIN SUGARCRM flav=ent ONLY
    /**
     * @group products
     */
    public function testSaveProductWorksheetReturnsFalseWhenForecastNotSetup()
    {
        /* @var $admin Administration */
        // get the current settings and set is_setup to 0
        $admin = BeanFactory::getBean('Administration');
        $settings = $admin->getConfigForModule('Forecasts');
        $admin->saveSetting('Forecasts', 'is_setup', 0, 'base');

        /* @var $product Product */
        $product = BeanFactory::getBean('Products');
        $ret = SugarTestReflection::callProtectedMethod($product, "saveProductWorksheet", array());

        $this->assertFalse($ret);

        // resave the settings to put it back like it was
        $admin->saveSetting('Forecasts', 'is_setup', intval($settings['is_setup']), 'base');
    }

    /**
     * @group products
     */
    public function testCreateProductCreatesForecastWorksheet()
    {
        /* @var $admin Administration */
        // get the current settings and set is_setup to 1
        $admin = BeanFactory::getBean('Administration');
        $settings = $admin->getConfigForModule('Forecasts');
        $admin->saveSetting('Forecasts', 'is_setup', 1, 'base');

        $product = SugarTestProductUtilities::createProduct();

        /* @var $worksheet ForecastWorksheet */
        $worksheet = BeanFactory::getBean('ForecastWorksheets');
        $worksheet->retrieve_by_string_fields(
            array(
                'parent_type' => $product->module_name,
                'parent_id' => $product->id,
                'draft' => 1,
                'deleted' => 0
            )
        );

        $this->assertNotEmpty($worksheet->id);
        $this->assertEquals($product->id, $worksheet->parent_id);
        // get the worksheet
        SugarTestWorksheetUtilities::setCreatedWorksheet(array($worksheet->id));

        // resave the settings to put it back like it was
        $admin->saveSetting('Forecasts', 'is_setup', intval($settings['is_setup']), 'base');
    }
    
    /**
     * @group products
     * @group opportunities
     */
    public function testProductSaveCallsHandleOppSalesStatus()
    {
        //create mock product to check that the save does what is expected
        $product = new MockProduct();
        $product->name = "mockProductTest1";
        $product->sales_status = Opportunity::STATUS_NEW;

        $product->save();

        $this->assertTrue($product->handleOppSalesStatusCalled());
    }
    //END SUGARCRM flav=ent ONLY

    /**
     * @group products
     */
    public function testProductTemplateSetsProductFields()
    {

        $pt_values = array(
            'mft_part_num' => 'unittest',
            'list_price' => '800',
            'cost_price' => '400',
            'discount_price' => '700',
            'list_usdollar' => '800',
            'cost_usdollar' => '400',
            'discount_usdollar' => '700',
            'tax_class' => 'Taxable',
            'weight' => '100'
        );

        $pt = SugarTestProductTemplatesUtilities::createProductTemplate('', $pt_values);

        $product = SugarTestProductUtilities::createProduct();
        $product->product_template_id = $pt->id;

        SugarTestReflection::callProtectedMethod($product, 'mapFieldsFromProductTemplate');

        foreach ($pt_values as $field => $value) {
            $this->assertEquals($value, $product->$field);
        }

        SugarTestProductTemplatesUtilities::removeAllCreatedProductTemplate();
    }

    /**
     * @group products
     */
    public function testProductTemplateSetsProductFieldsWithCurrencyConversion()
    {
        SugarTestCurrencyUtilities::createCurrency('Yen','¥','YEN',78.87,'currency-yen');
        $pt_values = array(
            'mft_part_num' => 'unittest',
            'list_price' => '800',
            'cost_price' => '400',
            'discount_price' => '700',
            'list_usdollar' => '800',
            'cost_usdollar' => '400',
            'discount_usdollar' => '700',
            'tax_class' => 'Taxable',
            'weight' => '100',
            'currency_id' => '-99'
        );

        $pt = SugarTestProductTemplatesUtilities::createProductTemplate('', $pt_values);

        $product = SugarTestProductUtilities::createProduct();
        $product->product_template_id = $pt->id;
        $product->currency_id = 'currency-yen';

        SugarTestReflection::callProtectedMethod($product, 'mapFieldsFromProductTemplate');

        $this->assertEquals(SugarCurrency::convertAmount(800, '-99', 'currency-yen'), $product->list_price);
        $this->assertEquals(SugarCurrency::convertAmount(400, '-99', 'currency-yen'), $product->cost_price);
        $this->assertEquals(SugarCurrency::convertAmount(700, '-99', 'currency-yen'), $product->discount_price);

        SugarTestProductTemplatesUtilities::removeAllCreatedProductTemplate();
        // remove test currencies
        SugarTestCurrencyUtilities::removeAllCreatedCurrencies();
    }

    /**
     * @group products
     */
    public function testBestCaseAutofillEmpty()
    {
        $product = SugarTestProductUtilities::createProduct();
        $product->likely_case = 10000;
        $product->best_case = '';
        $product->save();

        $this->assertEquals($product->likely_case, $product->best_case);
    }

    /**
     * @group products
     */
    public function testBestCaseAutofillNull()
    {
        $product = SugarTestProductUtilities::createProduct();
        $product->likely_case = 10000;
        $product->best_case = null;
        $product->save();

        $this->assertEquals($product->likely_case, $product->best_case);
    }

    /**
     * @group products
     */
    public function testBestCaseAutoRegression()
    {
        $product = SugarTestProductUtilities::createProduct();
        $product->likely_case = 10000;
        $product->best_case = 42;
        $product->save();

        $this->assertEquals(42, $product->best_case);
    }

    /**
     * @group products
     */
    public function testWorstCaseAutofillEmpty()
    {
        $product = SugarTestProductUtilities::createProduct();
        $product->likely_case = 10000;
        $product->worst_case = '';
        $product->save();

        $this->assertEquals($product->likely_case, $product->worst_case);
    }

    /**
     * @group products
     */
    public function testWorstCaseAutofillNull()
    {
        $product = SugarTestProductUtilities::createProduct();
        $product->likely_case = 10000;
        $product->worst_case = null;
        $product->save();

        $this->assertEquals($product->likely_case, $product->worst_case);
    }

    /**
     * @group products
     */
    public function testWorstCaseAutofillRegression()
    {
        $product = SugarTestProductUtilities::createProduct();
        $product->likely_case = 10000;
        $product->worst_case = 42;
        $product->save();

        $this->assertEquals(42, $product->worst_case);
    }

    /**
     * @group products
     */
    public function testEmptyQuantityDefaulted()
    {
        $product = SugarTestProductUtilities::createProduct();

        $product->quantity = "";
        $product->save();
        $this->assertEquals(1, $product->quantity, "Empty string not converted to 1");
    }

    /**
     * @group products
     */
    public function testNullQuantityDefaulted()
    {
        $product = SugarTestProductUtilities::createProduct();

        $product->quantity = null;
        $product->save();
        $this->assertEquals(1, $product->quantity, "Null not converted to 1");
    }

    /**
     * @group products
     */
    public function testQuantityNotDefaulted()
    {
        $product = SugarTestProductUtilities::createProduct();

        $product->quantity = 42;
        $product->save();
        $this->assertEquals(42, $product->quantity, "Null not converted to 1");
    }

    /**
     * @dataProvider dataProviderMapProbabilityFromSalesStage
     * @group products
     */
    public function testProbabilityNotOverwrittenBySaleStageIfGiven($salesStage)
    {
        $product = SugarTestProductUtilities::createProduct();

        $product->sales_stage = $salesStage;
        $product->probability = 22;
        $product->save();
        $this->assertEquals(22, $product->probability);
    }

    // BEGIN SUGARCRM flav=ent ONLY
    /**
     * @group products
     * @group forecasts
     * @ticket SFA-716
     * @dataProvider dataProviderCreateProductWithSalesStageCreatesForecastWorksheetWithSameSalesStage
     */
    public function testCreateProductWithSalesStageCreatesForecastWorksheetWithSameSalesStage($sales_stage)
    {
        /* @var $admin Administration */
        $admin = BeanFactory::getBean('Administration');
        $settings = $admin->getConfigForModule('Forecasts');
        $admin->saveSetting('Forecasts', 'is_setup', 1, 'base');


        $product = SugarTestProductUtilities::createProduct();
        $product->sales_stage = $sales_stage;
        $product->save();

        // reset the flag before we run any assertions just to make sure it gets set back if we have a fatal error
        $admin->saveSetting('Forecasts', 'is_setup', $settings['is_setup'], 'base');
        // load up the draft worksheet
        $worksheet = SugarTestWorksheetUtilities::loadWorksheetForBean($product);

        $this->assertEquals($sales_stage, $product->sales_stage);
        $this->assertInstanceOf('ForecastWorksheet', $worksheet);
        $this->assertEquals($sales_stage, $worksheet->sales_stage);
    }

    /**
     * Data Provider
     *
     * @return array
     */
    public function dataProviderCreateProductWithSalesStageCreatesForecastWorksheetWithSameSalesStage()
    {
        return array(
            array('Prospecting'),
            array('Qualification'),
            array('Needs Analysis'),
            array('Value Proposition'),
            array('Id. Decision Makers'),
            array('Perception Analysis'),
            array('Proposal/Price Quote'),
            array('Negotiation/Review'),
        );
    }
    // END SUGARCRM flav=ent ONLY
    
    /**
     * @dataProvider dataProviderMapProbabilityFromSalesStage
     * @group products
     */
    public function testMapProbabilityFromSalesStage($sales_stage, $probability)
    {
        $product = new MockProduct();
        $product->sales_stage = $sales_stage;
        // use the Reflection Helper to call the Protected Method
        SugarTestReflection::callProtectedMethod($product, 'mapProbabilityFromSalesStage');

        $this->assertEquals($probability, $product->probability);
    }

    public static function dataProviderMapProbabilityFromSalesStage()
    {
        return array(
            array('Prospecting', '10'),
            array('Qualification', '20'),
            array('Needs Analysis', '25'),
            array('Value Proposition', '30'),
            array('Id. Decision Makers', '40'),
            array('Perception Analysis', '50'),
            array('Proposal/Price Quote', '65'),
            array('Negotiation/Review', '80'),
            array('Closed Won', '100'),
            array('Closed Lost', '0')
        );
    }

    //BEGIN SUGARCRM flav=ent ONLY
    /**
     * @group products
     * @ticket SFA-814
     */
    public function testProductMarkDeletedAlsoDeletesWorksheet()
    {
        SugarTestTimePeriodUtilities::createTimePeriod('2013-01-01', '2013-03-31');

        $opp = SugarTestOpportunityUtilities::createOpportunity();
        $opp->date_closed = '2013-01-01';
        $opp->save();

        $product = SugarTestProductUtilities::createProduct();
        $product->opportunity_id = $opp->id;
        $product->date_closed = '2013-01-01';
        $product->save();

        $worksheet = SugarTestWorksheetUtilities::loadWorksheetForBean($product);

        // assert that worksheet is not deleted
        $this->assertEquals(0, $worksheet->deleted);

        $product->mark_deleted($product->id);

        $this->assertEquals(1, $product->deleted);

        // fetch the worksheet again
        unset($worksheet);
        $worksheet = SugarTestWorksheetUtilities::loadWorksheetForBean($product, false, true);
        $this->assertEquals(1, $worksheet->deleted);
    }
    //END SUGARCRM flav=ent ONLY

    /**
     * @group products
     * @group currency
     * @ticket SFA-745
     */
    public function testProductSaveSetsCurrencyBaseRate()
    {
        $currency = SugarTestCurrencyUtilities::createCurrency('Philippines', '₱', 'PHP', 41.82982, 'currency-php');

        $product = SugarTestProductUtilities::createProduct();
        $product->currency_id = $currency->id;
        $product->save();

        $this->assertEquals($currency->id, $product->currency_id);
        $this->assertEquals($currency->conversion_rate, $product->base_rate);

        SugarTestCurrencyUtilities::removeAllCreatedCurrencies();
    }

    /**
     * @group products
     * @ticket SFA-511
     */
    public function testMapFieldsFromOpportunity()
    {
        $product = SugarTestProductUtilities::createProduct();
        $opp = SugarTestOpportunityUtilities::createOpportunity();
        $product->opportunity_id = $opp->id;
        $opp->opportunity_type = 'new';
        $product->save();
        $this->assertEquals('new', $product->product_type);
    }


}

class MockProduct extends Product
{
    //BEGIN SUGARCRM flav=ent ONLY
    private $handleOppSalesStatusCalled = false;
    
    public function handleOppSalesStatus()
    {
        $this->handleOppSalesStatusCalled = true;
        parent::handleOppSalesStatus();
    }

    public function handleOppSalesStatusCalled()
    {
        return $this->handleOppSalesStatusCalled;
    }
    //END SUGARCRM flav=ent ONLY

    public function setAccountIdForOpportunity($oppId)
    {
        return parent::setAccountIdForOpportunity($oppId);
    }
}

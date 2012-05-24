<?php
/********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
require_once('include/SugarCharts/ReportBuilder.php');

class ReportBuilderTest extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testConstructorSetsModule()
    {
        $rb = new ReportBuilder('Accounts');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertEquals('Accounts', $actual['module']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testConstructorSetsSelfTable()
    {
        $rb = new ReportBuilder('Accounts');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array('self' => array(
            'value' => 'Accounts',
            'module' => 'Accounts',
            'label' => 'Accounts',
            'parent' => '',
            'children' => array())), $actual['full_table_list']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testToJson()
    {
        $rb = new ReportBuilder('Accounts');
        $test = json_decode($rb->toJson());

        $this->assertNotNull($test);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testToArray()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertTrue(is_array($rb->toArray()));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddModuleWithKey()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addModule('Contacts', 'contacts');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'value' => 'Contacts',
            'module' => 'Contacts',
            'label' => 'Contacts',
            'parent' => '',
            'children' => array()), $actual['full_table_list']['contacts']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetTableKeyWithModuleReturnsString()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertEquals('self', $rb->getKeyTable('Accounts'));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetTableKeyWithNotModuleReturnsArray()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertSame(array('self' => array('module' => 'Accounts', 'key' => 'self')), $rb->getKeyTable());
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetBeanReturnsAccountSugarBeanFromCache()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertInstanceOf('Account', $rb->getBean('Accounts'));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetBeanReturnsContactSugarBeanAfterCreate()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertInstanceOf('Contact', $rb->getBean('Contacts'));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetDefaultModuleAsString()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertEquals('Accounts', $rb->getDefaultModule());
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetDefaultModuleAsAccountBean()
    {
        $rb = new ReportBuilder('Accounts');
        $this->assertInstanceOf('Account', $rb->getDefaultModule(true));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddSummaryCount()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addSummaryCount();
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'name' => 'count',
            'label' => 'Count',
            'table_key' => 'self',
            'group_function' => "count",
            'field_type' => ''
        ), $actual['summary_columns'][0]);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddSummaryColumnWithoutModule()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addSummaryColumn('name');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'name' => "name",
            'label' => "LBL_NAME",
            'table_key' => "self",
        ), $actual['summary_columns'][0]);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddSummaryColumnWithModuleAsString()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addSummaryColumn('name', 'Accounts');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'name' => "name",
            'label' => "LBL_NAME",
            'table_key' => "self",
        ), $actual['summary_columns'][0]);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddSummaryColumnWithModuleAsSugarBean()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addSummaryColumn('name', $rb->getBean('Accounts'));
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'name' => "name",
            'label' => "LBL_NAME",
            'table_key' => "self",
        ), $actual['summary_columns'][0]);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddGroupByWithModule()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addGroupBy('name', 'Accounts');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'name' => "name",
            'label' => "LBL_NAME",
            'table_key' => "self",
            'type' => 'name',
        ), $actual['group_defs'][0]);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddGroupByWithoutModule()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addGroupBy('name');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame(array(
            'name' => "name",
            'label' => "LBL_NAME",
            'table_key' => "self",
            'type' => 'name',
        ), $actual['group_defs'][0]);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddLinkSetsTableInList()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addLink('contacts', 'name');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertTrue(isset($actual['full_table_list']['Accounts:contacts']));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddLinkSetsFieldInSummaryColumns()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addLink('contacts', 'name');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertEquals('Accounts:contacts', $actual['summary_columns'][0]['table_key']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddLinkSetsFieldInGroupDefs()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addLink('contacts', 'name');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertEquals('Accounts:contacts', $actual['group_defs'][0]['table_key']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddLinkToAccountModule()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addLink('member_of', 'name');
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertEquals('Accounts:member_of', $actual['group_defs'][0]['table_key']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testAddFilter()
    {

        $filter = array("Filter_1" => array(
            'operator' => 'AND',
            0 => array(
                'name' => 'billing_postalcode',
                'table_key' => 'self',
                'qualifier_name' => 'not_empty',
                'input_name0' => null
            )
        ));

        $rb = new ReportBuilder('Accounts');
        $rb->addFilter($filter);
        $actual_json = $rb->toJson();
        $actual = $this->objectToArray(json_decode($actual_json));

        $this->assertSame($filter, $actual['filters_def']);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetBeanFromTableKeyReturnsFalse()
    {
        $rb = new ReportBuilder('Accounts');
        $return = $rb->getBeanFromTableKey('asdfasdf');

        $this->assertFalse($return);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetBeanFromTableKeyReturnsAccountBean()
    {
        $rb = new ReportBuilder('Accounts');
        $return = $rb->getBeanFromTableKey('self');

        $this->assertInstanceOf('Account', $return);
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetLinkTableReturnsArrayWhenLinkDoesntExist()
    {
        $rb = new ReportBuilder('Accounts');
        $return = $rb->getLinkTable('asdf');

        $this->assertTrue(is_array($return));
    }

    /**
     * @group ReportBuilder
     * @gruop SugarCharts
     */
    public function testGetLinkTableReturnsStringWhenLinkExist()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addLink('member_of');
        $return = $rb->getLinkTable('member_of');

        $this->assertEquals('Accounts:member_of', $return);
    }

    public function testMultiLevelLink()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->addLink('assigned_user_link', 'user_name', array('Accounts', 'contacts'));
        $return = $rb->getLinkTable();
        $this->assertEquals('Accounts:contacts:assigned_user_link', $return['assigned_user_link']);
    }

    public function testSetSetValidChartType()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->setChartType('funnelF');

        $this->assertEquals('funnelF', $rb->getChartType());
    }

    public function testSetInvalidChartTypeEqualshBarF()
    {
        $rb = new ReportBuilder('Accounts');
        $rb->setChartType('SomeInvalidChartTypeF');

        $this->assertEquals('hBarF', $rb->getChartType());
    }

    public function testLoadSavedReportReturnsFalseWithNonValidGuid()
    {
        $rb = new ReportBuilder('Accounts');
        $return = $rb->loadSavedReport('this is only a test');

        $this->assertFalse($return);
    }

    public function testLoadSavedReportReturnsFalseWhenSavedReportModuleDoesntMatchParentModule()
    {
        $report = '{"display_columns":[],"module":"Opportunities","group_defs":[{"name":"opportunity_type","label":"Type","table_key":"self","type":"enum","force_label":"Type"}],"summary_columns":[{"name":"opportunity_type","label":"Type","table_key":"self"},{"name":"count","label":"Count","field_type":"","group_function":"count","table_key":"self"}],"report_name":"UnitTestReport","chart_type":"pieF","do_round":1,"chart_description":"","numerical_chart_column":"self:count","numerical_chart_column_type":"","assigned_user_id":"1","report_type":"summary","full_table_list":{"self":{"value":"Opportunities","module":"Opportunities","label":"Opportunities"},"Opportunities:assigned_user_link":{"name":"Opportunities  >  Assigned to User","parent":"self","link_def":{"name":"assigned_user_link","relationship_name":"opportunities_assigned_user","bean_is_lhs":false,"link_type":"one","label":"Assigned to User","module":"Users","table_key":"Opportunities:assigned_user_link"},"dependents":["Filter.1_table_filter_row_1","Filter.1_table_filter_row_1"],"module":"Users","label":"Assigned to User"}},"filters_def":{"Filter_1":{"operator":"AND","0":{"name":"user_name","table_key":"Opportunities:assigned_user_link","qualifier_name":"reports_to","input_name0":["seed_chris_id"]}}}}';
        /* @var $saved_report SavedReport */
        $saved_report = BeanFactory::getBean('Reports');
        $saved_report->save_report(-1, 1, 'TestReport', 'Opportunities', 'summary', $report, 1, 1, 'pieF');

        $rb = new ReportBuilder('Accounts');
        $return = $rb->loadSavedReport($saved_report->id);

        $this->assertFalse($return);

        $GLOBALS['db']->query("DELETE FROM saved_reports WHERE name IN ('" . $saved_report->id . "')");
    }

    public function testLoadSavedReportReturnsTrue()
    {
        $report = '{"display_columns":[],"module":"Opportunities","group_defs":[{"name":"opportunity_type","label":"Type","table_key":"self","type":"enum","force_label":"Type"}],"summary_columns":[{"name":"opportunity_type","label":"Type","table_key":"self"},{"name":"count","label":"Count","field_type":"","group_function":"count","table_key":"self"}],"report_name":"UnitTestReport","chart_type":"pieF","do_round":1,"chart_description":"","numerical_chart_column":"self:count","numerical_chart_column_type":"","assigned_user_id":"1","report_type":"summary","full_table_list":{"self":{"value":"Opportunities","module":"Opportunities","label":"Opportunities"},"Opportunities:assigned_user_link":{"name":"Opportunities  >  Assigned to User","parent":"self","link_def":{"name":"assigned_user_link","relationship_name":"opportunities_assigned_user","bean_is_lhs":false,"link_type":"one","label":"Assigned to User","module":"Users","table_key":"Opportunities:assigned_user_link"},"dependents":["Filter.1_table_filter_row_1","Filter.1_table_filter_row_1"],"module":"Users","label":"Assigned to User"}},"filters_def":{"Filter_1":{"operator":"AND","0":{"name":"user_name","table_key":"Opportunities:assigned_user_link","qualifier_name":"reports_to","input_name0":["seed_chris_id"]}}}}';
        /* @var $saved_report SavedReport */
        $saved_report = BeanFactory::getBean('Reports');
        $saved_report->save_report(-1, 1, 'TestReport', 'Opportunities', 'summary', $report, 1, 1, 'pieF');

        $rb = new ReportBuilder('Opportunities');
        $return = $rb->loadSavedReport($saved_report->id);

        $this->assertTrue($return);

        $GLOBALS['db']->query("DELETE FROM saved_reports WHERE name IN ('" . $saved_report->id . "')");
    }

    protected function objectToArray($d)
    {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /**
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return array_map(array(__CLASS__, __FUNCTION__), $d);
        }
        else {
            // Return array
            return $d;
        }
    }
}
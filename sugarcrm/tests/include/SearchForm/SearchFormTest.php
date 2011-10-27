<?php
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

/**
 * This is a unit test for the original SearchForm class found in include/SearchForm/SearchForm.php
 *
 */

class SearchFormTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $beanList, $beanFiles;
        include('include/modules.php');
        $this->useOutputBuffering = false;
    }

    /**
     * testGenerateSearchWhere
     *
     * This function tests the generateSearchWhere clause in SearchForm.php
     *
     */
    public function testGenerateSearchWhere()
    {
        require_once('modules/Reports/SavedReport.php');
        $searchFormMock = new SearchFormTestMock();
        $searchFormMock->bean = new SavedReport();
        $searchFormMock->searchFields = array('name'=>array('query_type' => 'default', 'value'=>'QA_Created'));
        $whereClauses = $searchFormMock->generateSearchWhere();
        $this->assertRegExp('/QA_Created\%/', $whereClauses[0], 'Assert that we have added the like (%) character to query');

        /*
        $searchFormMock->searchFields = array('current_user_only'=>array('query_type'=>'default', 'db_field'=>array('assigned_user_id'), 'my_items'=>'1', 'type'=>'bool', 'vname'=>'LBL_CURRENT_USER_FILTER', 'value'=>1));
        $whereClauses = $searchFormMock->generateSearchWhere();
        $this->assertNotRegExp('/\%/', $whereClauses[0], 'Assert that we have have not added the like (%) character to query');
        */
    }

}

require_once 'include/SearchForm/SearchForm.php';
/**
 * SearchFormTestMock
 *
 * This is a mock object to avoid having to setup extraneous member variables not needed by the unit test
 */
class SearchFormTestMock extends SearchForm
{

    public function __construct()
    {

    }

}
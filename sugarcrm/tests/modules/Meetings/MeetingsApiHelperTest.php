<?php
//File SUGARCRM flav=pro ONLY
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
     * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
     ********************************************************************************/


require_once('modules/Meetings/MeetingsApiHelper.php');
require_once('include/api/RestService.php');

class MeetingsApiHelperTest extends Sugar_PHPUnit_Framework_TestCase
{

    protected $bean =null;
    protected $contact = null;

    public function setUp()
    {
        parent::setUp();
        SugarTestHelper::setUp('current_user');
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('app_strings');
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('moduleList');

        // ACL's are junked need to have an admin user
        $GLOBALS['current_user']->is_admin = 1;
        $GLOBALS['current_user']->save();

        $this->bean = BeanFactory::newBean('Meetings');
        $this->bean->id = create_guid();
        $this->bean->name = 'Super Awesome Meetings Time';

        // gotta unfortunately create a contact for this
        $this->contact = SugarTestContactUtilities::createContact();
        $this->bean->contact_id = $this->contact->id;

    }

    public function tearDown()
    {
        unset($this->bean);
        unset($this->contact);
        SugarTestHelper::tearDown();
        SugarTestContactUtilities::removeAllCreatedContacts();
        parent::tearDown();
    }

    public function testFormatForApi() 
    {
        $helper = new MeetingsApiHelper(new MeetingsServiceMockup());
        $data = $helper->formatForApi($this->bean);
        $this->assertEquals($data['contact_name'], $this->contact->full_name, "Calls name does not match");
    }

}

class MeetingsServiceMockup extends ServiceBase
{
    public function __construct() {$this->user = $GLOBALS['current_user'];}
    public function execute() {}
    protected function handleException(Exception $exception) {}
}
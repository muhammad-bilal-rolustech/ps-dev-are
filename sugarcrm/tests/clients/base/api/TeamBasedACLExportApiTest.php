<?php
// FILE SUGARCRM flav=ent ONLY

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

require_once 'clients/base/api/ExportApi.php';

class TeamBasedACLExportApiTest extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * @var ExportApi
     */
    protected $api;

    /**
     * @var string
     */
    protected $module = 'Accounts';

    /**
     * @var RecordListApi
     */
    protected $recordList;

    /**
     * @var string
     */
    protected $recordListId;

    /**
     * @var TeamSet
     */
    protected $teamSetUserIn;

    /**
     * @var TeamSet
     */
    protected $teamSetUserNot;

    /**
     * @var array
     */
    protected $records = array();

    /**
     * @var SugarBean
     */
    protected $beanTBA;

    /**
     * @var SugarBean
     */
    protected $beanNotTBA;

    protected function setUp()
    {
        SugarTestHelper::setUp('current_user', array(true, false));
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('beanList');

        $this->api = new ExportApi();
        $this->recordList = new RecordListApi();
        $tbaConfigurator = new TeamBasedACLConfigurator();

        $teamUserIn = SugarTestTeamUtilities::createAnonymousTeam();
        $teamUserIn->add_user_to_team($GLOBALS['current_user']->id);

        $this->teamSetUserIn = BeanFactory::getBean('TeamSets');
        $this->teamSetUserIn->addTeams(array($teamUserIn->id));

        $teamUserNot = SugarTestTeamUtilities::createAnonymousTeam();

        $this->teamSetUserNot = BeanFactory::getBean('TeamSets');
        $this->teamSetUserNot->addTeams(array($teamUserNot->id));

        $this->beanTBA = SugarTestAccountUtilities::createAccount();
        $this->beanTBA->team_set_selected_id = $this->teamSetUserIn->id;
        $this->beanTBA->save();

        $this->records[] = $this->beanTBA->id;

        $this->beanNotTBA = SugarTestAccountUtilities::createAccount();
        $this->beanNotTBA->team_set_selected_id = $this->teamSetUserNot->id;
        $this->beanNotTBA->save();

        $this->records[] = $this->beanNotTBA->id;

        $listData = $this->recordList->recordListCreate(
            SugarTestRestUtilities::getRestServiceMock(),
            array('module' => $this->module, 'records' => $this->records)
        );
        $this->recordListId = $listData['id'];

        $tbaConfigurator->setGlobal(true);
        $tbaConfigurator->setForModule($this->module, true);

        $aclData = array(
            'module' => array(
                'access' => array(
                    'aclaccess' => ACL_ALLOW_ALL,
                ),
                'export' => array(
                    'aclaccess' => ACL_ALLOW_SELECTED_TEAMS,
                ),
            ),
        );
        ACLAction::setACLData($GLOBALS['current_user']->id, $this->module, $aclData);
    }

    protected function tearDown()
    {
        $this->recordList->recordListDelete(
            SugarTestRestUtilities::getRestServiceMock(),
            array('module' => 'Accounts', 'record_list_id' => $this->recordListId)
        );
        $this->teamSetUserIn->mark_deleted($this->teamSetUserIn->id);
        $this->teamSetUserNot->mark_deleted($this->teamSetUserNot->id);
        SugarTestTeamUtilities::removeAllCreatedAnonymousTeams();
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestHelper::tearDown();
    }

    /**
     * Should export only records whose selected teams in user's teams.
     */
    public function testExportTBA()
    {
        $result = $this->api->export(
            SugarTestRestUtilities::getRestServiceMock(),
            array('module' => 'Accounts', 'record_list_id' => $this->recordListId)
        );

        $this->assertContains($this->beanTBA->id, $result);
        $this->assertNotContains($this->beanNotTBA->id, $result);
    }

}
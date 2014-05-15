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
 
require_once 'SugarTestUserUtilities.php';

/**
 * @group utilities
 */
class SugarTestUserUtilitiesTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_before_snapshot = array();
    
    public function setUp() 
    {
        $this->_before_snapshot = $this->_takeUserDBSnapshot();
    }

    public function tearDown() 
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        SugarTestUserUtilities::removeAllCreatedUserSignatures();
    }

    public function _takeUserDBSnapshot() 
    {
        $snapshot = array();
        $query = 'SELECT * FROM users';
        $result = $GLOBALS['db']->query($query);
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $snapshot[] = $row;
        }
        return $snapshot;
    }

    //BEGIN SUGARCRM flav=pro ONLY
    public function _takeTeamDBSnapshot() 
    {
        $snapshot = array();
        $query = 'SELECT * FROM teams';
        $result = $GLOBALS['db']->query($query);
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            $snapshot[] = $row;
        }
        return $snapshot;
    }
    //END SUGARCRM flav=pro ONLY

    public function _takeSignatureDBSnapshot()
    {
        $snapshot = array();
        $query    = "SELECT * FROM users_signatures";
        $result   = $GLOBALS["db"]->query($query);

        while ($row = $GLOBALS["db"]->fetchByAssoc($result)) {
            $snapshot[] = $row;
        }

        return $snapshot;
    }

    public function testCanCreateAnAnonymousUser() 
    {
        $user = SugarTestUserUtilities::createAnonymousUser();

        $this->assertInstanceOf('User', $user);

        $after_snapshot = $this->_takeUserDBSnapshot();
        $this->assertNotEquals($this->_before_snapshot, $after_snapshot, 
            "Simply insure that something was added");
    }
    
    public function testCanCreateAnAnonymousUserButDoNotSaveIt() 
    {
        $user = SugarTestUserUtilities::createAnonymousUser(false);

        $this->assertInstanceOf('User', $user);

        $after_snapshot = $this->_takeUserDBSnapshot();
        $this->assertEquals($this->_before_snapshot, $after_snapshot, 
            "Simply insure that something was added");
    }

    public function testAnonymousUserHasARandomUserName() 
    {
        $first_user = SugarTestUserUtilities::createAnonymousUser();
        $this->assertTrue(!empty($first_user->user_name), 'team name should not be empty');

        $second_user = SugarTestUserUtilities::createAnonymousUser();
        $this->assertNotEquals($first_user->user_name, $second_user->user_name,
            'each user should have a unique name property');
    }

    public function testCanTearDownAllCreatedAnonymousUsers() 
    {
        $userIds = array();
        //BEGIN SUGARCRM flav=pro ONLY
        $before_snapshot_teams = $this->_takeTeamDBSnapshot();
        //END SUGARCRM flav=pro ONLY
        for ($i = 0; $i < 5; $i++) {
            $userIds[] = SugarTestUserUtilities::createAnonymousUser()->id;
        }
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        
        $this->assertEquals($this->_before_snapshot, $this->_takeUserDBSnapshot(),
            'SugarTest_UserUtilities::removeAllCreatedAnonymousUsers() should have removed the users it added');
        //BEGIN SUGARCRM flav=pro ONLY
        $this->assertEquals($before_snapshot_teams, $this->_takeTeamDBSnapshot(),
            'SugarTest_UserUtilities::removeAllCreatedAnonymousUsers() should have removed the teams it added');
        //END SUGARCRM flav=pro ONLY

        $count = function ($table, $where) {
            $num = 0;
            $sql = "SELECT COUNT(*) c FROM {$table} WHERE {$where}";
            if ($row = $GLOBALS['db']->fetchByAssoc($GLOBALS['db']->query($sql))) {
                $num = $row['c'];
            }
            return $num;
        };

        $in = "'" . implode("', '", $userIds) . "'";
        $sqls = array(
            'email_addresses' => "id IN (SELECT DISTINCT email_address_id FROM email_addr_bean_rel WHERE bean_module ='Users' AND bean_id IN ({$in}))",
            'emails_beans' => "bean_module='Users' AND bean_id IN ({$in})",
            'email_addr_bean_rel' => "bean_module='Users' AND bean_id IN ({$in})",
        );
        foreach ($sqls as $table => $where) {
            $this->assertEquals(
                0,
                $count($table, $where),
                "Email address references should have been deleted from {$table}"
            );
        }
    }

    public function testCanCreateAUserSignature()
    {
        $beforeSnapshot = $this->_takeSignatureDBSnapshot();
        $signature      = SugarTestUserUtilities::createUserSignature();

        $this->assertInstanceOf("UserSignature", $signature);

        $afterSnapshot = $this->_takeSignatureDBSnapshot();
        $this->assertNotEquals($beforeSnapshot, $afterSnapshot, "The user signature was not added");
    }

    public function testGetCreatedUserSignatureIds()
    {
        $signature1 = SugarTestUserUtilities::createUserSignature();
        $signature2 = SugarTestUserUtilities::createUserSignature();

        $expected = array(
            $signature1->id,
            $signature2->id,
        );
        $actual    = SugarTestUserUtilities::getCreatedUserSignatureIds();
        $this->assertEquals($expected, $actual, "The wrong user signature IDs were returned");
    }

    public function testCanTearDownAllCreatedUserSignatures()
    {
        $expected = $this->_takeSignatureDBSnapshot();

        for ($i = 0; $i < 5; $i++) {
            SugarTestUserUtilities::createUserSignature();
        }

        SugarTestUserUtilities::removeAllCreatedUserSignatures();

        $actual = $this->_takeSignatureDBSnapshot();
        $this->assertEquals($expected, $actual, "The user signatures were not removed");
    }
}

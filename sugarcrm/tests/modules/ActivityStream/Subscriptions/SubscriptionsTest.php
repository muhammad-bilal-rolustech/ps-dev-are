<?php

use SugarTestAccountUtilities as AccountHelper;
use SugarTestCommentUtilities as CommentHelper;
use SugarTestActivityUtilities as ActivityHelper;
use SugarTestUserUtilities as UserHelper;

/**
 * @group ActivityStream
 */
class SubscriptionsTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $user;
    private $record;

    public function setUp()
    {
        $this->user = UserHelper::createAnonymousUser();
        // TODO: Hack to avoid ACLController::checkAccessInternal errors. See
        // https://plus.google.com/101248048527720727791/posts/BNzpE6vwncT?cfem=1.
        $GLOBALS['current_user'] = $this->user;

        $this->record = self::getUnsavedRecord();
    }

    public function tearDown()
    {
        unset($GLOBALS['current_user']);
        UserHelper::removeAllCreatedAnonymousUsers();
        AccountHelper::removeAllCreatedAccounts();
        ActivityHelper::removeAllCreatedActivities();
        BeanFactory::setBeanClass('Activities');
        BeanFactory::setBeanClass('Accounts');
    }

    /**
     * @covers Subscription::getSubscribedUsers
     */
    public function testGetSubscribedUsers()
    {
        $kls = BeanFactory::getBeanName('Subscriptions');
        $return = $kls::getSubscribedUsers($this->record);
        $this->assertInternalType('array', $return);
        // TODO: Change this assertion to use assertCount after upgrading to
        // PHPUnit 3.6 or above.
        $this->assertEquals(0, count($return));

        $kls::subscribeUserToRecord($this->user, $this->record);
        $return = $kls::getSubscribedUsers($this->record);
        $this->assertInternalType('array', $return);
        // TODO: Change this assertion to use assertCount after upgrading to
        // PHPUnit 3.6 or above.
        $this->assertEquals(1, count($return));
        $this->assertEquals($return[0]['created_by'], $this->user->id);
    }

    /**
     * @covers Subscription::getSubscribedRecords
     */
    public function testGetSubscribedRecords()
    {
        $kls = BeanFactory::getBeanName('Subscriptions');
        $return = $kls::getSubscribedRecords($this->user);
        $this->assertInternalType('array', $return);
        // TODO: Change this assertion to use assertCount after upgrading to
        // PHPUnit 3.6 or above.
        $this->assertEquals(0, count($return));

        $kls::subscribeUserToRecord($this->user, $this->record);
        $return = $kls::getSubscribedRecords($this->user);
        $this->assertInternalType('array', $return);
        // TODO: Change this assertion to use assertCount after upgrading to
        // PHPUnit 3.6 or above.
        $this->assertEquals(1, count($return));
        $this->assertEquals($return[0]['parent_id'], $this->record->id);
    }

    /**
     * @covers Subscription::checkSubscription
     */
    public function testCheckSubscription()
    {
        $kls = BeanFactory::getBeanName('Subscriptions');
        $return = $kls::checkSubscription($this->user, $this->record);
        $this->assertNull($return, "A subscription shouldn't exist for a new record.");

        $guid = $kls::subscribeUserToRecord($this->user, $this->record);
        $return = $kls::checkSubscription($this->user, $this->record);
        $this->assertEquals($guid, $return);
    }

    /**
     * @covers Subscription::subscribeUserToRecord
     */
    public function testSubscribeUserToRecord()
    {
        $kls = BeanFactory::getBeanName('Subscriptions');
        $return = $kls::subscribeUserToRecord($this->user, $this->record);
        // Expect a Subscription bean GUID if we're creating the subscription.
        $this->assertInternalType('string', $return);

        $return = $kls::subscribeUserToRecord($this->user, $this->record);
        // Expect false if we cannot add another subscription for the user.
        $this->assertFalse($return);
    }

    /**
     * @covers Subscription::addActivitySubscriptions
     */
    public function testAddActivitySubscriptions_FailedToLoadTheRelationship_ExceptionThrown()
    {
        BeanFactory::setBeanClass('Activities', 'MockActivityForSubscriptionsTest');
        $activity = ActivityHelper::createActivity();
        $bean     = AccountHelper::createAccount();
        $data     = array(
            'act_id'        => $activity->id,
            'bean_module'   => $bean->module_name,
            'bean_id'       => $bean->id,
            'user_partials' => array(
                array(
                    'created_by' => $this->user->id,
                ),
            ),
        );
        $this->setExpectedException('Exception');
        $subscription = BeanFactory::newBean('Subscriptions');
        $subscription->addActivitySubscriptions($data);
    }

    /**
     * @covers Subscription::addActivitySubscriptions
     */
    public function testAddActivitySubscriptions_UserDoesNotHaveAccess_UserIsUnsubscribed()
    {
        BeanFactory::setBeanClass('Accounts', 'MockAccountForSubscriptionsTest');
        $activity               = ActivityHelper::createActivity();
        $bean                   = AccountHelper::createAccount();
        $bean->assigned_user_id = $this->user->id;
        $bean->save();
        $data             = array(
            'act_id'        => $activity->id,
            'bean_module'   => $bean->module_name,
            'bean_id'       => $bean->id,
            'user_partials' => array(
                array(
                    'created_by' => $this->user->id,
                ),
            ),
        );
        $mockSubscription = $this->getMockClass('Subscription', array('unsubscribeUserFromRecord'));
        $mockSubscription::staticExpects($this->once())->method('unsubscribeUserFromRecord');
        $subscription = new $mockSubscription;
        $subscription->addActivitySubscriptions($data);
    }

    /**
     * @covers Subscription::addActivitySubscriptions
     */
    public function testAddActivitySubscriptions_TypeOfActivityIsDeleteAndSuccessful_RelationshipIsAdded()
    {
        $activity                = ActivityHelper::createActivity();
        $activity->activity_type = 'delete';
        $activity->save();
        $bean                   = AccountHelper::createAccount();
        $bean->assigned_user_id = $this->user->id;
        $bean->save();
        // simulate deleted bean and associated activity
        BeanFactory::deleteBean($bean->module_name, $bean->id);
        $data         = array(
            'act_id'        => $activity->id,
            'bean_module'   => $bean->module_name,
            'bean_id'       => $bean->id,
            'user_partials' => array(
                array(
                    'created_by' => $this->user->id,
                ),
            ),
        );
        $subscription = BeanFactory::newBean('Subscriptions');
        $subscription->addActivitySubscriptions($data);
        $activity->load_relationship('activities_users');
        $expected = array($this->user->id);
        $actual   = $activity->activities_users->get();
        $this->assertEquals($expected, $actual, 'Should have added the user relationship to the activity.');
    }

    private static function getUnsavedRecord()
    {
        // SugarTestAccountUtilities::createAccount saves the bean, which
        // triggers the OOB subscription logic. For that reason, we create our
        // own record and give it an ID.
        $record = new Account();
        $record->id = "SubscriptionsTest".mt_rand();
        return $record;
    }
}

class MockActivityForSubscriptionsTest extends Activity
{
    public function load_relationship($rel_name)
    {
        return false;
    }
}

class MockAccountForSubscriptionsTest extends Account
{
    public function checkUserAccess(User $user = null, $bf = 'BeanFactory')
    {
        return false;
    }
}

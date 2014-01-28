<?php

/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

require_once 'modules/Meetings/Meeting.php';
require_once 'modules/Meetings/MeetingFormBase.php';
require_once 'modules/Activities/EmailReminder.php';


class MeetingTest extends Sugar_PHPUnit_Framework_TestCase
{
    public $meeting = null;
    public $contact = null;
    public $lead = null;

    protected function setUp()
    {
        global $current_user;
        $current_user = SugarTestUserUtilities::createAnonymousUser();

        $meeting = BeanFactory::newBean('Meetings');
        $meeting->name = 'Test Meeting';
        $meeting->assigned_user_id = $current_user->id;
        $meeting->save();
        $this->meeting = $meeting;

        $contact = BeanFactory::newBean('Contacts');
        $contact->first_name = 'MeetingTest';
        $contact->last_name = 'Contact';
        $contact->save();
        $this->contact = $contact;

        $lead = BeanFactory::newBean('Leads');
        $lead->first_name = 'MeetingTest';
        $lead->last_name = 'Lead';
        $lead->account_name = 'MeetingTest Lead Account';
        $lead->save();
        $this->lead = $lead;
    }

    protected function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['mod_strings']);

        $GLOBALS['db']->query("DELETE FROM meetings WHERE id = '{$this->meeting->id}'");
        unset($this->meeting);

        $GLOBALS['db']->query("DELETE FROM contacts WHERE id = '{$this->contact->id}'");
        unset($this->contact);

        $GLOBALS['db']->query("DELETE FROM leads WHERE id = '{$this->lead->id}'");
        unset($this->lead);

        SugarTestHelper::tearDown();
    }

    public function testMeetingTypeSaveDefault()
    {
        // Assert doc type default is 'Sugar'
        $this->assertEquals($this->meeting->type, 'Sugar');
    }

    public function testMeetingTypeSaveDefaultInDb()
    {
        $query = "SELECT * FROM meetings WHERE id = '{$this->meeting->id}'";
        $result = $GLOBALS['db']->query($query);
        while ($row = $GLOBALS['db']->fetchByAssoc($result)) {
            // Assert doc type default is 'Sugar'
            $this->assertEquals($row['type'], 'Sugar');
        }
    }

    public function testEmailReminder()
    {
        global $current_user;
        $meeting = new Meeting();
        $meeting->email_reminder_time = "20";
        $meeting->name = 'Test Email Reminder';
        $meeting->assigned_user_id = $current_user->id;
        $meeting->status = "Planned";
        $meeting->date_start = $GLOBALS['timedate']->nowDb();
        $meeting->save();

        $er = new EmailReminder();
        $to_remind = $er->getMeetingsForRemind();

        $this->assertTrue(in_array($meeting->id, $to_remind));
        $GLOBALS['db']->query("DELETE FROM meetings WHERE id = '{$meeting->id}'");
    }

    public function testMeetingFormBaseRelationshipsSetTest()
    {
        global $db;
        // setup $_POST
        $_POST = array();
        $_POST['name'] = 'MeetingTestMeeting';
        $_POST['lead_invitees'] = $this->lead->id;
        $_POST['contact_invitees'] = $this->contact->id;
        $_POST['assigned_user_id'] = $GLOBALS['current_user']->id;
        $_POST['date_start'] = date('Y-m-d H:i:s');
        // call handleSave
        $mfb = new MeetingFormBase();
        $meeting = $mfb->handleSave(null, false, false);
        // verify the relationships exist
        $q = "SELECT mu.contact_id FROM meetings_contacts mu WHERE mu.meeting_id = '{$meeting->id}'";
        $r = $db->query($q);
        $a = $db->fetchByAssoc($r);
        $this->assertEquals($this->contact->id, $a['contact_id'], "Contact wasn't set as an invitee");

        $q = "SELECT mu.lead_id FROM meetings_leads mu WHERE mu.meeting_id = '{$meeting->id}'";
        $r = $db->query($q);
        $a = $db->fetchByAssoc($r);
        $this->assertEquals($this->lead->id, $a['lead_id'], "Lead wasn't set as an invitee");

        $q = "SELECT mu.accept_status
              FROM meetings_users mu WHERE mu.meeting_id = '{$meeting->id}' AND user_id = '{$GLOBALS['current_user']->id}'";
        $r = $db->query($q);
        $a = $db->fetchByAssoc($r);
        $this->assertEquals('accept', $a['accept_status'], "Meeting wasn't accepted by the User");


    }

    public function testMeetingContactIdSet()
    {
        global $db, $current_user;
        $meeting = BeanFactory::newBean('Meetings');
        $meeting->name = 'Super Awesome Meeting Town USA';
        $meeting->contact_id = $this->contact->id;
        $meeting->assigned_user_id = $current_user->id;
        $meeting->date_start = date('Y-m-d H:i:s');
        $meeting->save();

        $q = "SELECT mu.contact_id FROM meetings_contacts mu WHERE mu.meeting_id = '{$meeting->id}'";
        $r = $db->query($q);
        $a = $db->fetchByAssoc($r);
        $this->assertEquals($this->contact->id, $a['contact_id'], "Contact wasn't set as an invitee");

    }

    public function testLoadFromRow()
    {
        /** @var Meeting $meeting */
        $meeting = BeanFactory::getBean('Meetings');
        $this->assertEmpty($meeting->reminder_checked);
        $this->assertEmpty($meeting->email_reminder_checked);

        $meeting->loadFromRow(array(
            'reminder_time' => 30,
            'email_reminder_time' => 30,
        ));

        $this->assertTrue($meeting->reminder_checked);
        $this->assertTrue($meeting->email_reminder_checked);
    }
}

<?php
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

use PHPUnit\Framework\TestCase;

/**
 * @group email
 * @group mailer
 */
class SmtpMailerTest extends TestCase
{
    public function setUp()
    {
        SugarTestHelper::setUp('current_user');
    }

    public function tearDown()
    {
        SugarTestHelper::tearDown();
    }

    public function testGetMailTransmissionProtocol_ReturnsSmtp()
    {
        $mailer   = new SmtpMailer(new OutboundSmtpEmailConfiguration($GLOBALS['current_user']));
        $expected = SmtpMailer::MailTransmissionProtocol;
        $actual   = $mailer->getMailTransmissionProtocol();
        $this->assertEquals(
            $expected,
            $actual,
            "The SmtpMailer should have {$expected} for its mail transmission protocol"
        );
    }

    public function testConnect_ConnectionSucceed_MailerSet()
    {
        $config = new OutboundSmtpEmailConfiguration($GLOBALS['current_user']);

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array($config))
            ->setMethods(
                array(
                    'transferConfigurations',
                    'connectToHost',
                )
            )
            ->getMock();

        $mockMailer->expects($this->once())->method('transferConfigurations')->will($this->returnValue(true));
        $mockMailer->expects($this->once())->method('connectToHost')->will($this->returnValue(true));

        $mockMailer->connect();
    }

    /**
     *  @expectedException MailerException
     */
    public function testConnect_ConnectionFails_ExceptionThrown()
    {
        $config = new OutboundSmtpEmailConfiguration($GLOBALS['current_user']);

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array($config))
            ->setMethods(
                array(
                    'transferConfigurations',
                    'connectToHost',
                )
            )
            ->getMock();

        $mockMailer->expects($this->once())->method('transferConfigurations')->will($this->returnValue(true));
        $mockMailer->expects($this->once())->method('connectToHost')->will($this->throwException(new MailerException()));

        $mockMailer->connect();
    }

    public function testClearRecipients_ClearToAndBccButNotCc()
    {
        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setMethods(array(
                 'clearRecipientsTo',
                 'clearRecipientsCc',
                 'clearRecipientsBcc',
            ))
            ->setConstructorArgs(array(new OutboundSmtpEmailConfiguration($GLOBALS['current_user'])))
            ->getmock();

        $mockMailer->expects($this->once())
            ->method('clearRecipientsTo');

        $mockMailer->expects($this->never())
            ->method('clearRecipientsCc');

        $mockMailer->expects($this->once())
            ->method('clearRecipientsBcc');

        $mockMailer->clearRecipients(true, false, true);
    }

    public function testSend_PHPMailerSmtpConnectThrowsException_ConnectToHostCatchesAndThrowsMailerException()
    {
        $mockPhpMailerProxy = $this->createPartialMock('PHPMailerProxy', array('smtpConnect'));

        $mockPhpMailerProxy->expects($this->once())
            ->method('smtpConnect')
            ->will($this->throwException(new phpmailerException()));

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'transferHeaders',
                 'transferRecipients',
                 'transferBody',
                 'transferAttachments',
            ))
            ->setConstructorArgs(array(new OutboundSmtpEmailConfiguration($GLOBALS['current_user'])))
            ->getmock();

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        // connectToHost should fail between transferConfigurations and transferHeaders

        $mockMailer->expects($this->never())
            ->method('transferHeaders');

        $mockMailer->expects($this->never())
            ->method('transferRecipients');

        $mockMailer->expects($this->never())
            ->method('transferBody');

        $mockMailer->expects($this->never())
            ->method('transferAttachments');

        $this->expectException(MailerException::class);
        $mockMailer->send();
    }

    public function testSend_MessageIdHeaderIsSet()
    {
        $config = new OutboundSmtpEmailConfiguration($GLOBALS['current_user']);
        $config->setHostname('mycompany.com');
        $config->setLocale($GLOBALS['locale']);

        $phpMailerProxy = new PHPMailerProxy();
        $phpMailerProxy->addAddress('foo@bar.com');
        $phpMailerProxy->Body = 'baz';

        $mailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array($config))
            ->setMethods(array(
                'generateMailer',
                'connectToHost',
                'transferRecipients',
                'transferBody',
                'transferAttachments',
            ))
            ->getMock();
        $mailer->expects($this->once())->method('generateMailer')->willReturn($phpMailerProxy);
        $mailer->expects($this->once())->method('connectToHost')->willReturn(true);
        $mailer->expects($this->once())->method('transferRecipients')->willReturn(true);
        $mailer->expects($this->once())->method('transferBody')->willReturn(true);
        $mailer->expects($this->once())->method('transferAttachments')->willReturn(true);
        $mailer->setHeader(EmailHeaders::From, new EmailIdentity('sales@mycompany.com'));
        $mailer->setSubject('biz');

        $id = create_guid();
        $mailer->setMessageId($id);
        $expected = $mailer->getHeader(EmailHeaders::MessageId);

        $mailer->send();

        $actual = $mailer->getHeader(EmailHeaders::MessageId);
        $this->assertSame($expected, $actual);
    }

    public function testSend_MessageIdHeaderIsNotSet()
    {
        $config = new OutboundSmtpEmailConfiguration($GLOBALS['current_user']);
        $config->setHostname('mycompany.com');
        $config->setLocale($GLOBALS['locale']);

        $phpMailerProxy = new PHPMailerProxy();
        $phpMailerProxy->addAddress('foo@bar.com');
        $phpMailerProxy->Body = 'baz';

        $mailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array($config))
            ->setMethods(
                array(
                    'generateMailer',
                    'connectToHost',
                    'transferRecipients',
                    'transferBody',
                    'transferAttachments',
                )
            )
            ->getMock();
        $mailer->expects($this->once())->method('generateMailer')->willReturn($phpMailerProxy);
        $mailer->expects($this->once())->method('connectToHost')->willReturn(true);
        $mailer->expects($this->once())->method('transferRecipients')->willReturn(true);
        $mailer->expects($this->once())->method('transferBody')->willReturn(true);
        $mailer->expects($this->once())->method('transferAttachments')->willReturn(true);
        $mailer->setHeader(EmailHeaders::From, new EmailIdentity('sales@mycompany.com'));
        $mailer->setSubject('biz');

        $this->assertEmpty($mailer->getHeader(EmailHeaders::MessageId), 'Should be empty before sending');

        $mailer->send();

        $this->assertNotEmpty($mailer->getHeader(EmailHeaders::MessageId), 'Should not be empty after sending');
    }

    public function testSend_PHPMailerSetFromThrowsException_TransferHeadersThrowsMailerException()
    {
        $packagedEmailHeaders = array(
            EmailHeaders::From => array(
                'foo@bar.com',
                null,
            ),
        );
        $mockEmailHeaders     = $this->createPartialMock('EmailHeaders', array('packageHeaders'));

        $mockEmailHeaders->expects($this->once())
            ->method('packageHeaders')
            ->will($this->returnValue($packagedEmailHeaders));

        $mockPhpMailerProxy = $this->createPartialMock('PHPMailerProxy', array('setFrom'));

        $mockPhpMailerProxy->expects($this->once())
            ->method('setFrom')
            ->will($this->throwException(new phpmailerException()));

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array(new OutboundSmtpEmailConfiguration($GLOBALS['current_user'])))
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'connectToHost',
                 'transferRecipients',
                 'transferBody',
                 'transferAttachments',
            ))
            ->getMock();

        $mockMailer->setHeaders($mockEmailHeaders);

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('connectToHost')
            ->will($this->returnValue(true));

        // transferHeaders should fail between connectToHost and transferRecipients

        $mockMailer->expects($this->never())
            ->method('transferRecipients');

        $mockMailer->expects($this->never())
            ->method('transferBody');

        $mockMailer->expects($this->never())
            ->method('transferAttachments');

        $this->expectException(MailerException::class);
        $mockMailer->send();
    }

    public function testSend_PHPMailerAddReplyToReturnsFalse_TransferHeadersThrowsMailerException()
    {
        $packagedEmailHeaders = array(
            EmailHeaders::ReplyTo => array(
                'foo@bar.com',
                null,
            ),
        );
        $mockEmailHeaders     = $this->createPartialMock('EmailHeaders', array('packageHeaders'));

        $mockEmailHeaders->expects($this->once())
            ->method('packageHeaders')
            ->will($this->returnValue($packagedEmailHeaders));

        $mockPhpMailerProxy = $this->createPartialMock('PHPMailerProxy', array('addReplyTo'));

        $mockPhpMailerProxy->expects($this->once())
            ->method('addReplyTo')
            ->will($this->returnValue(false));

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array(new OutboundSmtpEmailConfiguration($GLOBALS['current_user'])))
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'connectToHost',
                 'transferRecipients',
                 'transferBody',
                 'transferAttachments',
            ))
            ->getMock();

        $mockMailer->setHeaders($mockEmailHeaders);

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('connectToHost')
            ->will($this->returnValue(true));

        // transferHeaders should fail between connectToHost and transferRecipients

        $mockMailer->expects($this->never())
            ->method('transferRecipients');

        $mockMailer->expects($this->never())
            ->method('transferBody');

        $mockMailer->expects($this->never())
            ->method('transferAttachments');

        $this->expectException(MailerException::class);
        $mockMailer->send();
    }

    public function testSend_PHPMailerAddAttachmentThrowsException_TransferAttachmentsThrowsMailerException()
    {
        $mockLocale = $this->getMockBuilder('Localization')->setMethods(array('translateCharset'))->getMock();
        $mockLocale->expects($this->any())
            ->method('translateCharset')
            ->will($this->returnValue('foobar')); // the filename that Localization::translateCharset will return

        $mailerConfiguration = new OutboundSmtpEmailConfiguration($GLOBALS['current_user']);
        $mailerConfiguration->setLocale($mockLocale);

        $mockPhpMailerProxy = $this->getMockBuilder('PHPMailerProxy')->setMethods(array('addAttachment'))->getMock();

        $mockPhpMailerProxy->expects($this->once())
            ->method('addAttachment')
            ->will($this->throwException(new phpmailerException()));

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'connectToHost',
                 'transferHeaders',
                 'transferRecipients',
                 'transferBody',
            ))
            ->setConstructorArgs(array($mailerConfiguration))
            ->getMock();

        $attachment = new Attachment('/foo/bar.txt');
        $mockMailer->addAttachment($attachment);

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('connectToHost')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferRecipients')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferBody')
            ->will($this->returnValue(true));

        // transferAttachments should fail after transferBody and before PHPMailer's Send is called

        $this->expectException(MailerException::class);
        $mockMailer->send();
    }

    public function testSend_PHPMailerAddEmbeddedImageReturnsFalse_TransferAttachmentsThrowsMailerException()
    {
        $mockLocale = $this->getMockBuilder('Localization')->setMethods(array('translateCharset'))->getMock();
        $mockLocale->expects($this->any())
            ->method('translateCharset')
            ->will($this->returnValue('foobar')); // the filename that Localization::translateCharset will return

        $mailerConfiguration = new OutboundSmtpEmailConfiguration($GLOBALS['current_user']);
        $mailerConfiguration->setLocale($mockLocale);

        $mockPhpMailerProxy = $this->getMockBuilder('PHPMailerProxy')->setMethods(array('addEmbeddedImage'))->getMock();

        $mockPhpMailerProxy->expects($this->once())
            ->method('addEmbeddedImage')
            ->will($this->returnValue(false));

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array($mailerConfiguration))
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'connectToHost',
                 'transferHeaders',
                 'transferRecipients',
                 'transferBody',
            ))
            ->getMock();

        $embeddedImage = new EmbeddedImage('foobar', '/foo/bar.txt');
        $mockMailer->addAttachment($embeddedImage);

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('connectToHost')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferRecipients')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferBody')
            ->will($this->returnValue(true));

        // transferAttachments should fail after transferBody and before PHPMailer's Send is called

        $this->expectException(MailerException::class);
        $mockMailer->send();
    }

    public function testSend_PHPMailerSendThrowsException_SendCatchesItAndThrowsMailerException()
    {
        $mockPhpMailerProxy = $this->createPartialMock('PHPMailerProxy', array('send'));

        $mockPhpMailerProxy->expects($this->once())
            ->method('send')
            ->will($this->throwException(new phpmailerException()));

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array(new OutboundSmtpEmailConfiguration($GLOBALS['current_user'])))
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'connectToHost',
                 'transferHeaders',
                 'transferRecipients',
                 'transferBody',
                 'transferAttachments',
            ))
            ->getMock();

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('connectToHost')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferHeaders')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferRecipients')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferBody')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferAttachments')
            ->will($this->returnValue(true));

        $this->expectException(MailerException::class);
        $mockMailer->send();
    }

    public function testSend_AllMethodCallsAreSuccessful_ReturnsSentMessage()
    {
        $mockPhpMailerProxy = $this->createPartialMock('PHPMailerProxy', array('send', 'getSentMIMEMessage'));

        $mockPhpMailerProxy->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));

        $expected = 'the sent email';
        $mockPhpMailerProxy->expects($this->once())->method('getSentMIMEMessage')->willReturn($expected);

        $mockMailer = $this->getMockBuilder('SmtpMailer')
            ->setConstructorArgs(array(new OutboundSmtpEmailConfiguration($GLOBALS['current_user'])))
            ->setMethods(array(
                 'generateMailer',
                 'transferConfigurations',
                 'connectToHost',
                 'transferHeaders',
                 'transferRecipients',
                 'transferBody',
                 'transferAttachments',
            ))
            ->getMock();

        $mockMailer->expects($this->once())
            ->method('generateMailer')
            ->will($this->returnValue($mockPhpMailerProxy));

        $mockMailer->expects($this->once())
            ->method('transferConfigurations')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('connectToHost')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferHeaders')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferRecipients')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferBody')
            ->will($this->returnValue(true));

        $mockMailer->expects($this->once())
            ->method('transferAttachments')
            ->will($this->returnValue(true));

        $actual = $mockMailer->send();
        $this->assertEquals(
            $expected,
            $actual,
            'The sent MIME message should have been returned as confirmation for the send'
        );
    }
}

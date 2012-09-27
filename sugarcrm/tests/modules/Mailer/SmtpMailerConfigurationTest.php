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

require_once "modules/Mailer/SmtpMailerConfiguration.php";

class SmtpMailerConfigurationTest extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * @group mailer
     */
    public function testLoadDefaultConfigs_CharsetIsReset_WordwrapIsInitialized() {
        $mailerConfig = new SmtpMailerConfiguration();

        // change the default charset in order to show that loadDefaultConfigs will reset it
        $mailerConfig->setCharset("asdf"); // some asinine value that wouldn't actually be used

        // test that the charset has been changed from its default
        $expected = "asdf";
        $actual   = $mailerConfig->getCharset();
        self::assertEquals($expected, $actual, "The charset should have been set to {$expected}");

        $mailerConfig->loadDefaultConfigs();

        // test that the charset has been returned to its default
        $expected = "utf-8";
        $actual   = $mailerConfig->getCharset();
        self::assertEquals($expected, $actual, "The charset should have been reset to {$expected}");

        // test that the wordwrap has been initialized correctly
        $expected = 996;
        $actual   = $mailerConfig->getWordwrap();
        self::assertEquals($expected, $actual, "The wordwrap should have been initialized to {$expected}");
    }

    /**
     * @group mailer
     */
    public function testSetEncoding_PassInAValidEncoding_EncodingIsSet() {
        $mailerConfig = new SmtpMailerConfiguration();
        $expected     = Encoding::EightBit;

        $mailerConfig->setEncoding($expected);
        $actual = $mailerConfig->getEncoding();
        self::assertEquals($expected, $actual, "The encoding should have been set to {$expected}");
    }

    /**
     * @group mailer
     */
    public function testSetEncoding_PassInAnInvalidEncoding_ThrowsException() {
        $mailerConfig = new SmtpMailerConfiguration();
        $encoding     = "asdf"; // some asinine value that wouldn't actually be used

        self::setExpectedException("MailerException");
        $mailerConfig->setEncoding($encoding);
    }

    /**
     * @group mailer
     */
    public function testSetCommunicationProtocol_PassInAValidProtocol_CommunicationProtocolIsSet() {
        $mailerConfig = new SmtpMailerConfiguration();
        $expected     = SmtpMailerConfiguration::CommunicationProtocolSsl;

        $mailerConfig->setCommunicationProtocol($expected);
        $actual = $mailerConfig->getCommunicationProtocol();
        self::assertEquals($expected, $actual, "The communication protocol should have been set to {$expected}");
    }

    /**
     * @group mailer
     */
    public function testSetCommunicationProtocol_PassInAnInvalidProtocol_ThrowsException() {
        $mailerConfig          = new SmtpMailerConfiguration();
        $communicationProtocol = "asdf"; // some asinine value that wouldn't actually be used

        self::setExpectedException("MailerException");
        $mailerConfig->setCommunicationProtocol($communicationProtocol);
    }
}

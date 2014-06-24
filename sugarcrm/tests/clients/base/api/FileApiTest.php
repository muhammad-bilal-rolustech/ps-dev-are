<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */



require_once ('include/api/RestService.php');
require_once ("clients/base/api/FileApi.php");


/**
 * @group ApiTests
 */
class FileApiTest extends Sugar_PHPUnit_Framework_TestCase
{
    public $acl = array();
    public $documents;
    public $fileApi;
    public $tempFileFrom = 'tests/clients/base/api/FileApiTempFileFrom.txt';
    public $tempFileTo;

    public function setUp() {
        SugarTestHelper::setUp("current_user");
        SugarTestHelper::setUp("ACLStatic");
        // load up the unifiedSearchApi for good times ahead
        $this->fileApi = new FileApiMockUp();
        $document = BeanFactory::newBean('Documents');
        $document->name = "RelateApi setUp Documents";
        $document->save();
        $this->documents[] = $document;

        // no Document view, delete
        $acldata['module']['view']['aclaccess'] = ACL_ALLOW_NONE;
        $acldata['module']['delete']['aclaccess'] = ACL_ALLOW_NONE;
        ACLAction::setACLData($GLOBALS['current_user']->id, 'Documents', $acldata);
    }

    public function tearDown() {
        // Clean up temp file stuff
        if ($this->tempFileTo && file_exists($this->tempFileTo)) {
            @unlink($this->tempFileTo);
        }

        $GLOBALS['current_user']->is_admin = 1;

        foreach($this->documents AS $document) {
            $document->mark_deleted($document->id);
        }

        SugarTestHelper::tearDown();
        parent::tearDown();
    }

    public function testSaveFilePost()
    {
        $this->setExpectedException(
          'SugarApiExceptionNotAuthorized'
        );
        $this->fileApi->saveFilePost(new FileApiServiceMockUp(), array('module' => 'Documents', 'record' => $this->documents[0]->id, 'field' => 'filename'));
    }

    public function testGetFileList()
    {
        $this->setExpectedException(
          'SugarApiExceptionNotAuthorized'
        );
        $this->fileApi->getFileList(new FileApiServiceMockUp(), array('module' => 'Documents', 'record' => $this->documents[0]->id, 'field' => 'filename'));
    }

    public function testCreateTempFileFromInput()
    {
        // Tests checking encoding requests
        $encoded = $this->fileApi->isFileEncoded(new FileApiServiceMockUp(), array('content_transfer_encoding' => 'base64'));
        $this->assertTrue($encoded, "Encoded request check failed");

        // Handle our test of file encoding
        $this->tempFileTo = $this->fileApi->getTempFileName();
        $this->fileApi->createTempFileFromInput($this->tempFileTo, $this->tempFileFrom, $encoded);

        // Test that the temporary file was created
        $this->assertFileExists($this->tempFileTo, "Temp file was not created");

        // Test that the contents of the new file are the base64_decoded contents of the test file
        $createdContents = file_get_contents($this->tempFileTo);
        $encodedContents = base64_decode(file_get_contents($this->tempFileFrom));
        $this->assertEquals($createdContents, $encodedContents, "Creating temp file from encoded file failed");
    }

    public function testCreateTempFileFromInputNoEncoding()
    {
        // Tests checking encoding requests
        $encoded = $this->fileApi->isFileEncoded(new FileApiServiceMockUp(), array());
        $this->assertFalse($encoded, "Second encoded request check failed");

        // Handle our test of file encoding
        $this->tempFileTo = $this->fileApi->getTempFileName();
        $this->fileApi->createTempFileFromInput($this->tempFileTo, $this->tempFileFrom, $encoded);

        // Test that the temporary file was created
        $this->assertFileExists($this->tempFileTo, "Temp file was not created");

        // Test that the contents of the new file are the same as the contents of the test file
        $createdContents = file_get_contents($this->tempFileTo);
        $encodedContents = file_get_contents($this->tempFileFrom);
        $this->assertEquals($createdContents, $encodedContents, "Creating temp file from encoded file failed");
    }
}

class FileApiServiceMockUp extends RestService
{
    public function execute() {}
    protected function handleException(Exception $exception) {}
}

class FileApiMockUp extends FileApi 
{
    public function createTempFileFromInput($tempfile, $input, $encoded = false)
    {
        parent::createTempFileFromInput($tempfile, $input, $encoded);
    }
    
    public function isFileEncoded($api, $args)
    {
        return parent::isFileEncoded($api, $args);
    }
}

<?php
//FILE SUGARCRM flav=pro ONLY
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

require_once 'modules/ModuleBuilder/controller.php';
require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php';
require_once 'modules/ModuleBuilder/parsers/parser.dropdown.php';
require_once 'tests/rest/RestTestBase.php';

/**
 * Bug 59210 - Editing a modules field in Studio does not take affect immediately
 * in metadata API requests.
 * 
 * This test confirms that when certain metadata containing elements are edited
 * in studio that they indeed clear the metadata cache. While bug 59210 is specific
 * to field edits, the same condition existed among all studio related editable
 * elements, which was the metadata cache was not being invalidated when edits
 * were made.
 * 
 * NOTE: this will be a fairly long running test since it will be testing cache
 * clearing of the API metadata after certain UI tasks are carried out.
 */
class RestClearMetadataCacheTest extends RestTestBase
{
    /**
     * Collection of teardown methods to call after the test is run
     * 
     * @var array
     */
    protected $_teardowns = array();
    
    /**
     * Holder for the current request array
     * 
     * @var array
     */
    protected $_request = array();

    /**
     * Object containing various request arrays 
     * 
     * @var RestCacheClearRequestMock
     */
    protected $_requestMock;

    /**
     * Flag used in handling modListHeader global
     * 
     * @var bool
     */
    protected $_modListHeaderSet = false;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->_requestMock = new RestCacheClearRequestMock;
        
        // User needs to be an admin user
        $this->_user->is_admin = 1;
        $this->_user->save();
        
        // Backup the request
        $this->_request = $_REQUEST;
        
        // Setup one GLOBAL for relationships
        if (!isset($GLOBALS['modListHeader'])) {
            $GLOBALS['modListHeader'] = query_module_access_list($this->_user);
            $this->_modListHeaderSet = true;
        }
        
        // Back up the current file if there is one
        if (file_exists($this->_requestMock->ddlCustomFile)) {
            rename($this->_requestMock->ddlCustomFile, $this->_requestMock->ddlCustomFile . '.testbackup');
        }
        
        // Create an empty test custom file
        mkdir_recursive(dirname($this->_requestMock->ddlCustomFile));
        sugar_file_put_contents($this->_requestMock->ddlCustomFile, '<?php' . "\n");
        
        // Force a mobile platform 
        $this->_restLogin($this->_user->user_name, $this->_user->user_name, 'mobile');
        
        // Lets clear the metadata cache to make sure we are start with fresh data
        $this->_clearMetadataCache();
    }
    
    public function tearDown()
    {
        // This should really only happen if the test suite doesn't pass completely
        foreach ($this->_teardowns as $teardown) {
            $this->$teardown();
        }
        
        // Set the request back to what it was originally
        $_REQUEST = $this->_request;
        
        // Clean up at the parent
        parent::tearDown();
        
        // Handle modListHeader
        if ($this->_modListHeaderSet) {
            unset($GLOBALS['modListHeader']);
        }
    }

    /**
     * Tests the process of clearing out metadata cache for each step along the 
     * way of creating and editing a field, then deleting that field
     * 
     * @group rest
     */
    public function testCustomFieldChangesClearMetadataCache()
    {
        // Start by calling the metadata api to set the cache and get the first result
        $reply = $this->_restCall('metadata');
        $this->assertNotEmpty($reply['reply'], "The reply from the initial metadata request is empty");
        $initialMetadata = $reply;
        
        // Add a custom field
        $_REQUEST = $this->_requestMock->createFieldRequestVars;
        $mb = new ModuleBuilderController();
        $mb->action_SaveField();
        
        // Add the teardown method to the teardown stack in case of failure
        $this->_teardowns['cf'] = '_teardownCustomField';
        
        // Test custom field shows in metadata request
        $reply = $this->_restCall('metadata');
        $this->assertNotEmpty($reply['reply']['modules']['Accounts']['fields']['unit_testy_c'], "The created custom field was not found in the metadata response");
        $this->assertFalse(isset($initialMetadata['reply']['modules']['Accounts']['fields']['unit_testy_c']), "The custom field was found in the initial request but should not have been");
        
        // Change the custom field by adding a formula
        $_REQUEST['name'] .= '_c';
        $_REQUEST['formula'] = 'add(1,3)';
        $mb = new ModuleBuilderController();
        $mb->action_SaveField();
        
        // Test custom field edit shows in metadata request
        $reply = $this->_restCall('metadata');
        $this->assertNotEmpty($reply['reply']['modules']['Accounts']['fields']['unit_testy_c']['formula'], "The custom field formula was not found in the metadata response");
        $this->assertEquals($_REQUEST['formula'], $reply['reply']['modules']['Accounts']['fields']['unit_testy_c']['formula'], "The formula that was saved was not the formula that was passed in");
        
        // Change a label
        $_REQUEST['labelValue'] = $this->_requestMock->deleteFieldRequestVars['labelValue'];
        $mb = new ModuleBuilderController();
        $mb->action_SaveField();
        
        // Test label change shows up in metadata request
        $reply = $this->_restCall('metadata');
        $this->assertNotEmpty($reply['reply']['modules']['Accounts']['fields']['unit_testy_c']['vname'], "The created custom field label id was not found in the metadata response");
        // Set the label for use in the next test
        $vname = $reply['reply']['modules']['Accounts']['fields']['unit_testy_c']['vname'];
        
        // Get the app strings from the label url
        $this->assertNotEmpty($reply['reply']['labels']['en_us'], "Label metadata entry is missing");
        $contents = json_decode(file_get_contents($reply['reply']['labels']['en_us']), true);
        $this->assertNotEmpty($contents['mod_strings']['Accounts'][$vname], "The label value for the custom field label was not found");
        $this->assertEquals($_REQUEST['labelValue'], $contents['mod_strings']['Accounts'][$vname], "The custom field label change did not reflect in the metadata response");
        
        // Delete the custom field and remove the teardown method from the 
        // teardown stack since at this point out testing would have cleaned up
        $this->_teardownCustomField();
        unset($this->_teardowns['cf']);
        
        // Test custom field no longer shows in metadata
        $reply = $this->_restCall('metadata');
        $this->assertFalse(isset($reply['reply']['modules']['Accounts']['fields']['unit_testy_c']), "The created custom field was found in the metadata response and it should not have been");
        $this->assertEquals(count($initialMetadata['reply']['modules']['Accounts']['fields']), count($reply['reply']['modules']['Accounts']['fields']), "Starting and ending field counts do not match");
    }

    /**
     * Tests relationship create, edit and delete reflect immediately in metadata
     * requests
     *
     * @group rest 
     */
    public function testRelationshipChangesClearMetadataCache()
    {
        $this->markTestSkipped("Skipping for now as this is just not working.");
        // Base private metadata manager
        $mm = MetaDataManager::getManager();
        $mm->rebuildCache();
        
        // Create a relationship
        $_REQUEST = $this->_requestMock->createRelationshipRequestVars;
        $relationships = new DeployedRelationships($_REQUEST ['view_module']);
        // This should return the new relationship object
        $new = $relationships->addFromPost();
        // Get the new relationship name since we will need that in assertions
        $relName = $new->getName();
        
        // We also need it in our delete process, so set it there now
        $this->_requestMock->createRelationshipRequestVars['relationship_name'] = $relName;
        
        // Finish the save now
        $relationships->save();
        $relationships->build();
        
        // Add to the teardown stack for catching failures
        $this->_teardowns['r'] = '_teardownRelationship';
        
        // Test relationship shows in metadata
        //$reply = $this->_restCall('metadata');
        
        $data = $mm->getMetadata();
        $this->assertNotEmpty($data['relationships'][$relName], "The created relationship was not found in the metadata response and it should have been");
        
        // Delete the relationship and remove the teardown method from the 
        // teardown stack since at this point it will have cleaned itself up
        $this->_teardownRelationship();
        unset($this->_teardowns['r']);
        
        // Test relationship no longer shows up 
        //$reply = $this->_restCall('metadata');
        $data = $mm->getMetadata();
        $this->assertFalse(isset($data['relationships'][$relName]), "The created relationship was found in the metadata response and it should not have been");
    }

    /**
     * Test the creation of a dropdown list immediately shows up in metadata
     * requests
     * 
     * @group rest
     */
    public function testDropdownListChangesClearMetadataCache()
    {        
        // Create a dropdown
        $_REQUEST = $this->_requestMock->ddlFieldRequestVars;
        $parser = new ParserDropDown();
        $parser->saveDropDown($_REQUEST);
        
        // Stack it
        $this->_teardowns['ddl'] = '_teardownDropdownList';
        
        // Test it 
        $reply = $this->_restCall('metadata');
        $this->assertNotEmpty($reply['reply']['labels']['en_us'], "Label metadata entry is missing");
        $contents = json_decode(file_get_contents($reply['reply']['labels']['en_us']), true);
        $this->assertNotEmpty($contents['app_list_strings'][$_REQUEST['dropdown_name']], "The custom dropdown list was not found");
        
        // Delete the dropdown - This could be delegated to the teardown method
        // Normally we would test removing the dropdown list, but we can't since
        // we don't allow deleting a dropdown list
        $this->_teardownDropdownList();
        unset($this->_teardowns['ddl']);
    }
    
    protected function _teardownCustomField()
    {
        // Set the request
        $_REQUEST = $this->_requestMock->deleteFieldRequestVars;
        
        // Delete
        $mb = new ModuleBuilderController();
        $mb->action_DeleteField();
    }
    
    protected function _teardownRelationship()
    {
        $_REQUEST = $this->_requestMock->createRelationshipRequestVars;
        $mb = new ModuleBuilderController();
        $mb->action_DeleteRelationship();
    }

    protected function _teardownDropdownList()
    {
        // Clean up our file
        unlink($this->_requestMock->ddlCustomFile);
        
        if (file_exists($this->_requestMock->ddlCustomFile . '.testbackup')) {
            rename($this->_requestMock->ddlCustomFile . '.testbackup', $this->_requestMock->ddlCustomFile);
        }
        
        // Clear the cache
        sugar_cache_clear('app_list_strings.en_us');
        $this->_clearMetadataCache();
    }
}

/**
 * Mock collection object of various requests used in changing metadata elements
 */
class RestCacheClearRequestMock
{
    /**
     * Mock request for creating a field
     * 
     * @var array
     */
    public $createFieldRequestVars = array(
        "action" => "saveField",
        "comments" => "",
        "default" => "",
        "dependency" => "",
        "dependency_display" => "",
        "duplicate_merge" => "0",
        "enforced" => "false",
        "formula" => "",
        "formula_display" => "",
        "help" => "",
        "importable" => "true",
        "is_update" => "true",
        "labelValue" => "Unit Testy",
        "label" => "LBL_UNIT_TESTY",
        "new_dropdown" => "",
        "reportableCheckbox" => "1",
        "reportable" => "1",
        "to_pdf" => "true",
        "type" => "varchar",
        "name" => "unit_testy",
        "module" => "ModuleBuilder",
        "view_module" => "Accounts",
    );

    /**
     * Mock request for deleting a field
     * 
     * @var array
     */
    public $deleteFieldRequestVars = array(
        "action" => "DeleteField",
        "labelValue" => "Unit Testosterone",
        "label" => "LBL_UNIT_TESTY",
        "to_pdf" => "true",
        "type" => "varchar",
        "name" => "unit_testy_c",
        "module" => "ModuleBuilder",
        "view_module" => "Accounts",
    );

    /**
     * Mock relationship request
     * 
     * @var array
     */
    public $createRelationshipRequestVars = array(
        'to_pdf' => '1',
        'module' => 'ModuleBuilder',
        'action' => 'SaveRelationship',
        'remove_tables' => 'true',
        'view_module' => 'Bugs',
        'relationship_name' => '',
        'lhs_module' => 'Bugs',
        'relationship_type' => 'one-to-one',
        'rhs_module' => 'Accounts'
    );

    /**
     * Mock dropdown list items
     * 
     * @var array
     */
    public $ddlItems = array(
        array('jimmy', 'Jimmy'),
        array('jerry', 'Jerry'),
        array('jenny', 'Jenny'),
    );

    /**
     * Mock dropdownlist request
     * 
     * @var array
     */
    public $ddlFieldRequestVars = array(
        'list_value' => '',
        'dropdown_lang' => 'en_us',
        'dropdown_name' => 'unit_test_dropdown',
        'view_package' => 'studio',
    );
    
    /**
     * Custom file created by the dropdownlist save
     * @var string
     */
    public $ddlCustomFile = 'custom/include/language/en_us.lang.php';

    /**
     * Setup the dropdown list elements
     */
    public function __construct() {
        // Prepare the dropdownlist items
        $this->ddlFieldRequestVars['list_value'] = json_encode($this->ddlItems);
    }
}
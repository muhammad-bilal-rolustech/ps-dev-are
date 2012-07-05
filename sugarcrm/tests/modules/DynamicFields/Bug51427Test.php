<?php
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
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


require_once("modules/ModuleBuilder/parsers/StandardField.php");
require_once("modules/DynamicFields/FieldCases.php");


/**
 *  Bug #51427: Setting a "name" field to calculated removes it from Global Search
 *
 *  StandardField class didn't have functionality for checking `unified_search`
 *  option. `unified_search` was always `false` for non-custom fields.
 */

class Bug51427Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $old_dictionary;
    private $old_bean_list;
    private $bean_name;
    private $test_field;
    private $test_standart_field;

    public function setUp()
    {
        global $dictionary, $bean_list;

        $this->old_dictionary=$dictionary;
        $this->old_bean_list=$bean_list;
        $this->test_standart_field = new TestStandardField();
        $this->test_standart_field->module='Accounts';
        loadBean($this->test_standart_field->module);
        $this->test_field=get_widget('varchar');
        $this->test_field->name='name';
        $this->bean_name = get_valid_bean_name($this->test_standart_field->module);
    }

    public function tearDown()
    {
        global $dictionary, $bean_list;

        $dictionary=$this->old_dictionary;
        $bean_list=$this->old_bean_list;
        VardefManager::clearVardef('Accounts', 'Account');
        VardefManager::refreshVardefs('Accounts', 'Account');
    }

    /**
     * Test different combinations of the options
     *
     * @dataProvider providerUnifiedSearchOptions
     */
    public function testIfUnifiedSearchEnabledByDefault(array $opt_common, array $opt_field, $assert)
    {
        $this->test_field->unified_search=false;
        $this->setOptions($opt_common, $opt_field);
        $this->test_standart_field->addFieldObject($this->test_field);
        $this->assertEquals($assert,(boolean)$this->test_field->unified_search,
                "\r\n"
                .'Assertion error: field->unified_search should be `'.($assert ? 'true' : 'false').'` with the following vardef options:'

                ."\r\nvardef[<module>][unified_search_default_enabled] "
                    .($opt_common['unified_search_default_enabled']===-1 ? 'is undefined' : ($opt_common['unified_search_default_enabled'] ? '= true' : '= false'))

                ."\r\nvardef[<module>][unified_search] "
                    .($opt_common['unified_search']===-1 ? 'is undefined' : ($opt_common['unified_search'] ? '= true' : '= false'))

                ."\r\nvardef[<module>][fields][<field_name>][unified_search] "
                    .($opt_field['unified_search']===-1 ? 'is undefined' : ($opt_field['unified_search'] ? '= true' : '= false'))
                ."\r\n"
        );
    }

    public function providerUnifiedSearchOptions()
    {
        return array(
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>1,
                    'unified_search'=>1,
                ),
                'options_field'=>array(
                    'unified_search'=>1,
                ),
                'assert'=>true,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>0,
                    'unified_search'=>1,
                ),
                'options_field'=>array(
                    'unified_search'=>1,
                ),
                'assert'=>false,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>0,
                    'unified_search'=>0,
                ),
                'options_field'=>array(
                    'unified_search'=>1,
                ),
                'assert'=>false,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>0,
                    'unified_search'=>0,
                ),
                'options_field'=>array(
                    'unified_search'=>0,
                ),
                'assert'=>false,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>0,
                    'unified_search'=>1,
                ),
                'options_field'=>array(
                    'unified_search'=>0,
                ),
                'assert'=>false,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>0,
                    'unified_search'=>0,
                ),
                'options_field'=>array(
                    'unified_search'=>1,
                ),
                'assert'=>false,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>1,
                    'unified_search'=>1,
                ),
                'options_field'=>array(
                     'unified_search'=>-1,
                ),
                'assert'=>true,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>1,
                    'unified_search'=>-1,
                ),
                'options_field'=>array(
                    'unified_search'=>1,
                ),
                'assert'=>false,
            ),
            array(
                'options_common'=>array(
                    'unified_search_default_enabled'=>-1,
                    'unified_search'=>1,
                ),
                'options_field'=>array(
                    'unified_search'=>1,
                ),
                'assert'=>false,
            ),
        );
    }

    private function setOptions(array $opt_common, array $opt_field)
    {
        global $dictionary;
        $dictionary=$this->old_dictionary;
        if(!empty($opt_common))
        {
            foreach($opt_common as $k=>$v)
            {
                if($v===-1)
                {
                    unset($dictionary[$this->bean_name][$k]);
                }
                else
                {
                    $dictionary[$this->bean_name][$k]=$v;
                }
            }
        }
        if(!empty($opt_field))
        {
            foreach($opt_field as $k=>$v)
            {
                if($v===-1)
                {
                    unset($dictionary[$this->bean_name]['fields'][$this->test_field->name][$k]);
                }
                else
                {
                    $dictionary[$this->bean_name]['fields'][$this->test_field->name][$k]=$v;
                }
            }
        }
    }
}


/*
 * inherits StandardField
 * some methods aren't used in the test
 */
class TestStandardField extends StandardField
{
    function writeVardefExtension()
    {
    }

    function loadCustomDef()
    {
        $this->custom_def=array();
    }
}
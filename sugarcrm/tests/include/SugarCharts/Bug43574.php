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
 
require_once('include/SugarCharts/SugarChartFactory.php');

class Bug43574 extends Sugar_PHPUnit_Framework_TestCase
{
    private $sugarChart;
	public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->sugarChart = SugarChartFactory::getInstance('Jit', 'Reports');
    }
    
    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    /**
     * @dataProvider _xmlChartNodes
     */
    public function testJitCharXMLFormat($dirty, $clean)
    {
        $this->assertEquals($clean, $this->sugarChart->tab($dirty,1) );
    }

    public function _xmlChartNodes()
    {
        return array(
          array('simple string',"\tsimple string\n"),
          array('12345',"\t12345\n"),
          array('<xml_node/>',"\t<xml_node/>\n"),
          array('<xml_node>No bad data</xml_node>',"\t<xml_node>No bad data</xml_node>\n"),
          array('<xml_node>5852 string</xml_node>',"\t<xml_node>5852 string</xml_node>\n"),
          array('<xml_node>with ampersand &</xml_node>',"\t<xml_node>with ampersand &amp;</xml_node>\n"),
          array('<xml_node>with less than <</xml_node>',"\t<xml_node>with less than &lt;</xml_node>\n"),
          array('<xml_node>with greater than ></xml_node>',"\t<xml_node>with greater than &gt;</xml_node>\n"),
          array('<xml_node>Multiple & < > \'</xml_node>',"\t<xml_node>Multiple &amp; &lt; &gt; '</xml_node>\n"),
        );
    }

}

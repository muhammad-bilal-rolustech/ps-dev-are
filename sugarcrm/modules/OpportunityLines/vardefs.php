<?php
//FILE SUGARCRM flav=pro ONLY
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
$dictionary['OpportunityLine'] = array('table' => 'opportunity_lines','audited'=>false,
		'comment' => 'The opportunity line item assoicated with the product',
'fields' => array (

'id' =>
array (
  'name' => 'id',
  'vname' => 'LBL_ID',
  'type' => 'id',
  'required' => true,
  'reportable'=>false,
  'comment' => 'Unique identifier'
),
'product_id' =>
array (
  'name' => 'product_id',
  'vname' => 'LBL_PRODUCT_ID',
  'type' => 'id',
  'required' => true,
  'reportable' => false,
),
  'products' =>
  array(
    'name' => 'products',
    'type' => 'link',
    'relationship' => 'opportunity_lines_products',
    'source'=>'non-db',
    'link_type'=>'one',
    'module'=>'Products',
    'bean_name'=>'Product',
    'vname'=>'LBL_PRODUCTS',
  ),
'expert_id' =>
    array (
    'name' => 'expert_id',
    'vname' => 'LBL_EXPERT_ID',
    'type' => 'enum',
    'function' => 'get_expert_array',
    'dbType' => 'varchar',
),
'opportunity_id' =>
array (
  'name' => 'opportunity_id',
  'type' => 'id',
  'vname' => 'LBL_OPPORTUNITY_ID',
  'required'=>false,
  'reportable' => false,
  'comment' => 'The opportunity id for the line item entry'
),
'opportunity_name' =>
  array (
    'name' => 'opportunity_name',
    'rname' => 'name',
    'id_name' => 'opportunity_id',
    'vname' => 'LBL_OPPORTUNITY_NAME',
    'type' => 'relate',
    'table' => 'opportunities',
    'join_name'=>'opportunities',
    'isnull' => 'true',
    'module' => 'Opportunities',
    'dbType' => 'varchar',
    'link'=>'opportunities',
    'len' => '255',
    'source'=>'non-db',
    'unified_search' => true,
    'required' => true,
    'importable' => 'required',
    'required' => true,
  ),
'opportunities' =>
  array(
    'name' => 'opportunities',
    'type' => 'link',
    'relationship' => 'opportunity_lines_opportunities',
    'source'=>'non-db',
    'link_type'=>'one',
    'module'=>'Opportunities',
    'bean_name'=>'Opportunity',
    'vname'=>'LBL_OPPORTUNITIES',
  ),
'price' =>
array (
    'name' => 'price',
    'vname' => 'LBL_PRICE',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
    'comment' => 'Price for product'
),
'discount_price' =>
array (
    'name' => 'discount_price',
    'vname' => 'LBL_DISCOUNT_PRICE',
    'type' => 'currency',
    'len' => '26,6',
    'audited'=>true,
    'comment' => 'Discounted price ("Unit Price" in Quote)'
),
'discount_usdollar' =>
array (
    'name' => 'discount_usdollar',
    'vname' => 'LBL_DISCOUNT_USDOLLAR',
    'dbType' => 'decimal',
    'group'=>'discount_price',
    'type' => 'currency',
    'len' => '26,6',
    'comment' => 'Discount price expressed in USD',
    'studio' => array('editview' => false),
),
'total_price' =>
array(
    'name' => 'total_price',
    'vname' => 'LBL_TOTAL_PRICE',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
    'comment' => 'Total price (discount price * quantity)',
),
'best_case' =>
array (
    'name' => 'best_case',
    'vname' => 'LBL_BEST_CASE',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
),
'likely_case' =>
array (
    'name' => 'likely_case',
    'vname' => 'LBL_LIKELY_CASE',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
),
'worst_case' =>
array (
    'name' => 'worst_case',
    'vname' => 'LBL_WORST_CASE',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
),
'profit_margin' =>
array (
    'name' => 'profit_margin',
    'vname' => 'LBL_PROFIT_MARGIN',
    'dbType' => 'decimal',
    'type' => 'currency',
    'len' => '26,6',
),
'currency_id' =>
array (
    'name' => 'currency_id',
    'dbType' => 'id',
    'vname'=>'LBL_CURRENCY',
    'type' => 'varchar',
    'function'=>array('name'=>'getCurrencyDropDown', 'returns'=>'html'),
    'required'=>false,
    'reportable'=>false,
    'comment' => 'Currency of the product'
),
'tax_class' =>
array (
    'name' => 'tax_class',
    'vname' => 'LBL_TAX_CLASS',
    'type' => 'enum',
    'options' => 'tax_class_dom',
    'len' => 100,
    'comment' => 'Tax classification (ex: Taxable, Non-taxable)'
),
'quantity' =>
array (
    'name' => 'quantity',
    'vname' => 'LBL_QUANTITY',
    'type' => 'int',
    'len'=>5,
    'comment' => 'Quantity in use'
),
'note' =>
array (
    'name' => 'note',
    'vname' => 'LBL_NOTE',
    'type' => 'text',
    'comment' => 'Full text of the note',
    'rows' => 6,
    'cols' => 80,
),

),
    'relationships' => array (
        'opportunity_lines_products' =>
            array('lhs_module'=> 'Products', 'lhs_table'=> 'products', 'lhs_key' => 'id',
                'rhs_module'=> 'OpportunityLines', 'rhs_table'=> 'opportunity_lines', 'rhs_key' => 'product_id',
                'relationship_type'=>'one-to-many'),
        'opportunity_lines_opportunities' =>
            array('lhs_module'=> 'Opportunities', 'lhs_table'=> 'opportunities', 'lhs_key' => 'id',
                'rhs_module'=> 'OpportunityLines', 'rhs_table'=> 'opportunity_lines', 'rhs_key' => 'opportunity_id',
                'relationship_type'=>'one-to-many'),
)

);

VardefManager::createVardef('OpportunityLines','OpportunityLine',
       array('default',
       //BEGIN SUGARCRM flav=pro ONLY
       'team_security'
       //END SUGARCRM flav=pro ONLY
       )
);
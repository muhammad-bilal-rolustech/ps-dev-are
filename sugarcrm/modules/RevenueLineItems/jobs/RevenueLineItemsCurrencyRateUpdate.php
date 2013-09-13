<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

require_once('include/SugarCurrency/CurrencyRateUpdateAbstract.php');

/**
 * OpportunitiesCurrencyRateUpdate
 *
 * A class for updating currency rates on specified database table columns
 * when a currency conversion rate is updated by the administrator.
 *
 */
class RevenueLineItemsCurrencyRateUpdate extends CurrencyRateUpdateAbstract
{
    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        // set rate field definitions
        $this->addRateColumnDefinition('revenue_line_items', 'base_rate');
        // set usdollar field definitions
        $this->addUsDollarColumnDefinition('revenue_line_items', 'discount_amount', 'discount_amount_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'discount_price', 'discount_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'list_price', 'list_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'deal_calc', 'deal_calc_usdollar');
        $this->addUsDollarColumnDefinition('revenue_line_items', 'book_value', 'book_value_usdollar');
    }

    /**
     * doCustomUpdateRate
     *
     * Return true to skip updates for this module.
     * Return false to do default update of base_rate column.
     * To custom processing, do here and return true.
     *
     * @access public
     * @param  string $table
     * @param  string $column
     * @param  string $currencyId
     * @return boolean true if custom processing was done
     */
    public function doCustomUpdateRate($table, $column, $currencyId)
    {
        // get the conversion rate
        $rate = $this->db->getOne(sprintf("SELECT conversion_rate FROM currencies WHERE id = '%s'", $currencyId));

        $stages = $this->getClosedStages();

        // setup SQL statement
        $query = sprintf("UPDATE %s SET %s = '%s'
        WHERE sales_stage NOT IN ('%s')
        AND currency_id = '%s'",
            $table,
            $column,
            $rate,
            implode("','", $stages),
            $currencyId
        );
        // execute
        $result = $this->db->query(
            $query,
            true,
            string_format(
                $GLOBALS['app_strings']['ERR_DB_QUERY'],
                array('RevenueLineItemsCurrencyRateUpdate',$query
                )
            )
        );
        return !empty($result);
    }

    /**
     * doCustomUpdateUsDollarRate
     *
     * Return true to skip updates for this module.
     * Return false to do default update of amount * base_rate = usdollar
     * To custom processing, do here and return true.
     *
     * @access public
     * @param  string    $tableName
     * @param  string    $usDollarColumn
     * @param  string    $amountColumn
     * @param  string    $currencyId
     * @return boolean true if custom processing was done
     */
    public function doCustomUpdateUsDollarRate($tableName, $usDollarColumn, $amountColumn, $currencyId)
    {

        $stages = $this->getClosedStages();

        // setup SQL statement
        $query = sprintf("UPDATE %s SET %s = %s / base_rate
            WHERE sales_stage NOT IN ('%s')
            AND currency_id = '%s'",
            $tableName,
            $usDollarColumn,
            $amountColumn,
            implode("','", $stages),
            $currencyId
        );
        // execute
        $result = $this->db->query(
            $query,
            true,
            string_format(
                $GLOBALS['app_strings']['ERR_DB_QUERY'],
                array('RevenueLineItemsCurrencyRateUpdate', $query)
            )
        );
        return !empty($result);
    }

    /**
     * do post update process to update the opportunity RLIs, ENT only
     */
    public function doPostUpdateAction()
    {
        //BEGIN SUGARCRM flav=ent ONLY
        $sql = "SELECT opportunity_id               AS opp_id,
                          Sum(likely_case)             AS likely,
                          Sum(worst_case)              AS worst,
                          Sum(best_case)               AS best
                   FROM   (SELECT rli.opportunity_id,
                                  (rli.likely_case/rli.base_rate) as likely_case,
                                  (rli.worst_case/rli.base_rate) as worst_case,
                                  (rli.best_case/rli.base_rate) as best_case
                           FROM   revenue_line_items AS rli
                           WHERE  rli.deleted = 0) AS T
                   GROUP  BY opp_id";
        $results = $this->db->query($sql);

        $queries = array();

        // skip closed opps
        $sql = "UPDATE opportunities SET amount = '%s', best_case = '%s', worst_case = '%s' WHERE id = '%s' AND sales_status NOT IN ('Closed Won', 'Closed Lost')";
        while ($row = $this->db->fetchRow($results)) {
            $queries[] = sprintf(
                $sql,
                $row['likely'],
                $row['best'],
                $row['worst'],
                $row['opp_id']
            );
        }
        $this->db->query(join(';', $queries));
        //END SUGARCRM flav=ent ONLY
        return true;
    }

    /**
     * getClosedStages
     *
     * Return an array of closed stage names from the opportunity bean.
     *
     * @access public
     * @return array array of closed stage values
     */
    public function getClosedStages()
    {
        static $rli;
        if (!isset($rli)) {
            $rli = BeanFactory::getBean('RevenueLineItems');
        }
        return $rli->getClosedStages();
    }


}

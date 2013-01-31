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
require_once('include/SugarQuery/SugarQuery.php');

class FilterApi extends SugarApi {
    public function registerApiRest() {
        return array(
            'filterModuleGet' => array(
                'reqType' => 'GET',
                'path' => array('<module>','filter'),
                'pathVars' => array('module',''),
                'method' => 'filterList',
                'jsonParams' => array('filter'),
                'shortHelp' => 'Filter records from a single module',
                'longHelp' => 'include/api/help/filterModule.html',
            ),
            'filterModulePost' => array(
                'reqType' => 'POST',
                'path' => array('<module>','filter'),
                'pathVars' => array('module',''),
                'method' => 'filterList',
                'shortHelp' => 'Filter records from a single module',
                'longHelp' => 'include/api/help/filterModulePost.html',
            ),
            'filterModuleById' => array(
                'reqType' => 'GET',
                'path' => array('<module>','filter', '?'),
                'pathVars' => array('module','', 'record'),
                'method' => 'filterById',
                'shortHelp' => 'Filter records from a single module by a predefined filter',
                'longHelp' => 'include/api/help/filterModulePost.html',
            ),

        );
    }

    protected $defaultLimit = 20; // How many records should we show if they don't pass up a limit

    protected $current_user;

    public function __construct() {
        global $current_user;
        $this->current_user = $current_user;
    }

    public function filterById(ServiceBase $api, array $args) {
        $filter = BeanFactory::getBean('Filters', $args['record']);
        $filter_definition = json_decode($filter->filter_definition, true);
        $args = array_merge($args, $filter_definition);
        unset($args['record']);
        return $this->filterList($api, $args);
    }

    function parseOptions(ServiceBase $api, array $args, SugarBean $seed)
    {
        $options = array();

        // Set up the defaults
        $options['limit'] = $this->defaultLimit;
        $options['offset'] = 0;
        $options['order_by'] = array(array('date_modified','DESC'));
        
        if ( !empty($args['max_num']) ) {
            $options['limit'] = (int)$args['max_num'];
        }
        if ( !empty($args['offset']) ) {
            if ( $args['offset'] == 'end' ) {
                $options['offset'] = 'end';
            } else {
                $options['offset'] = (int)$args['offset'];
            }
        }
        if ( !empty($args['order_by']) ) {
            $orderBys = explode(',',$args['order_by']);
            $orderByArray = array();
            foreach ( $orderBys as $order ) {
                $orderSplit = explode(':',$order);
                
                if ( !$seed->ACLFieldAccess($orderSplit[0],'list') || !isset($seed->field_defs[$orderSplit[0]]) ) {
                    throw new SugarApiExceptionNotAuthorized('No access to view field: '.$orderSplit[0].' in module: '.$args['module']);
                }

                if ( !isset($orderSplit[1]) || strtolower($orderSplit[1]) == 'desc' ) {
                    $orderSplit[1] = 'DESC';
                } else {
                    $orderSplit[1] = 'ASC';
                }
                $orderByArray[] = $orderSplit;
            }
            $options['order_by'] = $orderByArray;
        }

        return $options;
    }

    function filterList(ServiceBase $api, array $args)
    {
        $seed = BeanFactory::newBean($args['module']);
        
        if ( ! $seed->ACLAccess('list') ) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: '.$args['module']);
        }

        $options = $this->parseOptions($api,$args,$seed);

        $q = new SugarQuery();
        // Just need ID, we need to fetch beans so we can format them later.
        $q->select(array('id'));
        $q->from($seed);
        $q->distinct(true);

        // return $args['filter'];
        if ( !isset($args['filter']) || !is_array($args['filter']) ) {
            $args['filter'] = array();
        }
        $this->addFilters($args['filter'], $q->where(), $q);
        $q->where()->equals("deleted",0);


        foreach ( $options['order_by'] as $orderBy ) {
            $q->orderBy($orderBy[0],$orderBy[1]);
        }
        // Add an extra record to the limit so we can detect if there are more records to be found
        $q->limit($options['limit']+1);
        $q->offset($options['offset']);

        $GLOBALS['log']->info("Filter SQL: ".$q->compileSql());
        $idRows = $q->execute();
        // return $idRows;

        $data = array();
        $data['next_offset'] = -1;

        $beans = array();
        foreach ( $idRows as $i => $row ) {
            if ( $i == $options['limit'] ) {
                $data['next_offset'] = (int)($options['limit']+$options['offset']);
                continue;
            }
            $bean = BeanFactory::getBean($args['module'],$row['id']);
            if ( $bean ) {
                // Sometimes team security changes mid-query
                $beans[] = $bean;
            }
        }
        
        $data['records'] = $this->formatBeans($api,$args,$beans);

        return $data;
    }
    
    function addFilters(array $filterDefs, SugarQuery_Builder_Where $where, SugarQuery $q)
    {
        foreach ( $filterDefs as $filterDef ) {
            foreach ( $filterDef as $field => $filter ) {
                if ( $field == '$or' ) {
                    $this->addFilters($filter,$where->queryOr(),$q);
                } else if ( $field == '$and' ) {
                    $this->addFilters($filter,$where->queryAnd(),$q);
                } else if ( $field == '$favorite' ) {
                    $this->addFavoriteFilter($q,$where,$filter);
                } else if ( $field == '$owner' ) {
                    $this->addOwnerFilter($q,$where,$filter);
                } else {
                    // Looks like just a normal field, parse it's options
                    if (  strpos($field,'.') ) {
                        // It looks like it's a related field that it's searching by
                        list($relatedTable,$relatedField) = explode('.',$field);
                        $q->join($relatedTable, array('joinType'=>'LEFT'));
                    }

                    if ( !is_array($filter) ) {
                        // This is just simple match
                        $where->equals($field,$filter);
                        continue;
                    }
                    foreach ( $filter as $op => $value ) {
                        switch ( $op ) {
                            case '$equals':
                                $where->equals($field,$value);
                                break;
                            case '$not_equals':
                                $where->notEquals($field, $value);
                                break;
                            case '$starts':
                                $where->starts($field,$value);
                                break;
                            case '$ends':
                                $where->ends($field,$value);
                                break;
                            case '$contains':
                                $where->contains($field,$value);
                                break;
                            case '$in':
                                $where->in($field,$value);
                                break;
                            case '$not_in':
                                $where->notIn($field,$value);
                                break;
                            case '$between':
                                $where->between($field,$value);
                                break;
                            case '$is_null':
                                $where->isNull($field);
                                break;
                            case '$not_null':
                                $where->notNull($field);
                                break;
                            case '$lt':
                                $where->lt($field,$value);
                                break;
                            case '$lte':
                                $where->lte($field,$value);
                                break;
                            case '$gt':
                                $where->gt($field,$value);
                                break;
                            case '$gte':
                                $where->gte($field,$value);
                                break;
                            case '$fromDays':
                                // FIXME: FRM-226, logic for these needs to be moved to SugarQuery
                                $where->addRaw("{$field} >= DATE_ADD(NOW(), INTERVAL {$value} DAY)");
                                break;
                            case '$tracker':
                                // FIXME: FRM-226, logic for these needs to be moved to SugarQuery
                                $where->addRaw("{$q->from->getTableName()}.id in
                                (select item_id from tracker
                                    where module_name='{$q->from->module_name}'
                                    and user_id='{$GLOBALS['current_user']->id}'
                                    and DATE_ADD({$field}, INTERVAL {$value})
                                )");
                                break;
                            default:
                                throw new SugarApiExceptionInvalidParameter("Did not recognize the operand: ".$op);
                        }
                    }
                }
                
            }
        }
    }

    /**
     * This function adds an owner filter to the sugar query
     * @param SugarQuery $q The whole SugarQuery object
     * @param SugarQuery_Builder_Where $where The Where part of the SugarQuery object
     * @param string $link Which module are you adding the owner filter to.
     */
    protected function addOwnerFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $link)
    {
        if ( $link == '' || $link == '_this' ) {
            $linkPart = '';
        } else {
            $q->join($link, array('joinType'=>'LEFT'));
            $linkPart = $link.'.';
        }

        $where->equals($linkPart.'assigned_user_id',$this->current_user->id);
    }

    /**
     * This function adds a favorite filter to the sugar query
     * @param SugarQuery $q The whole SugarQuery object
     * @param SugarQuery_Builder_Where $where The Where part of the SugarQuery object
     * @param string $link Which module are you adding the owner filter to.
     */
    protected function addFavoriteFilter(SugarQuery $q, SugarQuery_Builder_Where $where, $link)
    {
        $sfOptions = array('joinType'=>'LEFT');
        if ( $link == '' || $link == '_this' ) {
        } else {
            $q->join($link,array('joinType'=>'LEFT'));
            $sfOptions['joinTo'] = $link;
        }

        $sf = new SugarFavorites();
        $sfAlias = $sf->addToSugarQuery($q,$sfOptions);
        
        $where->notNull($sfAlias . '.id');
    }
}
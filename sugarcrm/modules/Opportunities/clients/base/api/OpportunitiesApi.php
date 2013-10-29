<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once 'clients/base/api/ListApi.php';
require_once 'data/BeanFactory.php';

class OpportunitiesApi extends ListApi
{
    public function registerApiRest()
    {
        return array(
            'influencers' => array(
                'reqType' => 'GET',
                'path' => array('Opportunities','?', 'influencers'),
                'pathVars' => array('module', 'record'),
                'method' => 'influencers',
                'shortHelp' => '',
                'longHelp' => '',
            ),
            'experts' => array(
                'reqType' => 'GET',
                'path' => array('Opportunities','?', 'experts'),
                'pathVars' => array('module', 'record'),
                'method' => 'recommendExperts',
                'shortHelp' => 'Recommend users to help with a particular record',
                'longHelp' => 'Test',
            ),
            'expertsTypeahead' => array(
                'reqType' => 'GET',
                'path' => array('Opportunities','?', 'expertsTypeahead'),
                'pathVars' => array('module', 'record'),
                'method' => 'recommendExpertsTypeahead',
                'shortHelp' => 'Typeahead provider for recommended users',
                'longHelp' => '',
            ),
            'similar' => array(
                'reqType' => 'GET',
                'path' => array('Opportunities','?', 'similar'),
                'pathVars' => array('module', 'record'),
                'method' => 'similarDeals',
                'shortHelp' => 'Show deals similar to the current record',
                'longHelp' => '',
            ),
        );
    }


    public function recommendExperts($api, $args)
    {
        $data = $this->getInteractionsByUser($api, $args);
        $sortCallback = function($a, $b) {
            return $a['interaction_count'] - $b['interaction_count'];
        };
        $filtered = array();
        if(!empty($args['title'])) {
            foreach($data as $entry) {
                if($entry['title'] === $args['title']) {
                    $filtered[] = $entry;
                }
            }
        } else {
            $filtered = $data;
        }
        usort($filtered, $sortCallback);
        return array_slice($filtered, 0, 5);
    }

    public function recommendExpertsTypeahead($api, $args)
    {
        $data = $this->getInteractionsByUser($api, $args);
        $titles = array();
        foreach ($data as $bean) {
            if(!empty($bean['title'])) {
                if(!in_array($bean['title'], $titles)) {
                    $titles[] = $bean['title'];
                }
            }
        }
        return $titles;
    }


    public function influencers($api, $args)
    {
        $data = $this->getInteractionsByUser($api, $args);
        return $data;
    }

    protected function getInteractionsByUser($api, $args) {
        $record = $this->getBean($api, $args);
        $account = $this->getAccountBean($api, $args, $record);
        $relationships = array('calls' => 0, 'meetings' => 0);
        $data = array();
        foreach($relationships as $relationship => $ignore) {
            // Load up the relationship
            if (!$account->load_relationship($relationship)) {
                // The relationship did not load, I'm guessing it doesn't exist
                throw new SugarApiExceptionNotFound('Could not find a relationship name ' . $relationship);
            }
            // Figure out what is on the other side of this relationship, check permissions
            $linkModuleName = $account->$relationship->getRelatedModuleName();
            $linkSeed = BeanFactory::newBean($linkModuleName);
            if (!$linkSeed->ACLAccess('view')) {
                throw new SugarApiExceptionNotAuthorized('No access to view records for module: '.$linkModuleName);
            }

            $relationshipData = $account->$relationship->query(array());

            foreach ($relationshipData['rows'] as $id => $value) {
                $bean = BeanFactory::getBean(ucfirst($relationship), $id);
                $bean->load_relationship('users');
                $userModuleName = $bean->users->getRelatedModuleName();
                $userSeed = BeanFactory::newBean($userModuleName);
                $userData = $bean->users->query(array());

                foreach($userData['rows'] as $userId => $user) {
                    if(empty($data[$userId])) {
                        $userBean = BeanFactory::getBean('Users', $userId);
                        if($userBean) {
                            $data[$userId] = array_merge($this->formatBean($api, $args, $userBean), $relationships);
                            $data[$userId][$relationship]++;
                            $data[$userId]['interaction_count'] = 1;
                        }
                    } else {
                        $data[$userId][$relationship]++;
                        $data[$userId]['interaction_count']++;
                    }
                }
            }
        }
        return array_values($data);
    }

    public function similarDeals($api, $args) {
        $record = $this->getBean($api, $args);
        $account = $this->getAccountBean($api, $args, $record);
        $data = array();

        $scoreSort = function($a, $b) {
            return 1000*($b['score'] - $a['score']);
        };

        $moduleName = $record->accounts->getRelatedModuleName();
        $seed = BeanFactory::newBean($moduleName);
        $beanList = $seed->get_full_list('', "{$seed->table_name}.industry = '" . $seed->db->quote($account->industry) . "'");
        if(count($beanList) == 1) {
            // If the current record is the only one of the current industry, load *all* the accounts.
            $beanList = $seed->get_full_list();
        }
        foreach($beanList as $bean) {
            if($bean->id == $account->id) {
                continue;
            }
            if(!$bean->load_relationship('opportunities')) {
                continue;
            }
            $opportunities = $bean->opportunities->query(array());
            foreach($opportunities['rows'] as $id => $value) {
                $opportunity = BeanFactory::getBean($args['module'], $id);
                $array = $this->formatBean($api, $args, $opportunity);
                $array['score'] = abs($array['amount'] - $record->amount)/$record->amount;
                $data[] = $array;
            }
        }

        usort($data, $scoreSort);
        $result =  array_slice($data, 0, 3);
        $users = array();
        foreach($result as $k=>$row){
            if(isset($row['assigned_user_id'])){
                $users[$row['assigned_user_id']] = $row['assigned_user_id'];
            }
        }
        if(!empty($users)){
            $pics = $GLOBALS['db']->query('SELECT id, picture FROM users WHERE id IN (\'' . implode($users, "', '"). '\')');
            foreach($pics as $pic){
                $users[$pic['id']] = $pic['picture'];
            }
            foreach($result as $k=>$row){
                 if(isset($row['assigned_user_id'])){
                    $result[$k]['picture'] = $users[$row['assigned_user_id']];
                }
            }
        }
        return $result;
    }

    protected function getBean($api, $args)
    {
        // Load up the bean
        $record = BeanFactory::getBean($args['module'], $args['record']);

        if (empty($record)) {
            throw new SugarApiExceptionNotFound('Could not find parent record '.$args['record'].' in module '.$args['module']);
        }
        if (!$record->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: '.$args['module']);
        }
        return $record;
    }

    protected function getAccountBean($api, $args, $record)
    {
        // Load up the relationship
        if (!$record->load_relationship('accounts')) {
            throw new SugarApiExceptionNotFound('Could not find a relationship name accounts');
        }

        // Figure out what is on the other side of this relationship, check permissions
        $linkModuleName = $record->accounts->getRelatedModuleName();
        $linkSeed = BeanFactory::newBean($linkModuleName);
        if (!$linkSeed->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: '.$linkModuleName);
        }

        $accounts = $record->accounts->query(array());
        foreach ($accounts['rows'] as $accountId => $value) {
            $account = BeanFactory::getBean('Accounts', $accountId);
            if (empty($account)) {
                throw new SugarApiExceptionNotFound('Could not find parent record '.$accountId.' in module Accounts');
            }
            if (!$account->ACLAccess('view')) {
                throw new SugarApiExceptionNotAuthorized('No access to view records for module: Accounts');
            }

            // Only one account, so we can return inside the loop.
            return $account;
        }
    }

    protected function getAccountRelationship($api, $args, $account, $relationship, $limit = 5, $query = array())
    {
        // Load up the relationship
        if (!$account->load_relationship($relationship)) {
            // The relationship did not load, I'm guessing it doesn't exist
            throw new SugarApiExceptionNotFound('Could not find a relationship name ' . $relationship);
        }
        // Figure out what is on the other side of this relationship, check permissions
        $linkModuleName = $account->$relationship->getRelatedModuleName();
        $linkSeed = BeanFactory::newBean($linkModuleName);
        if (!$linkSeed->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: '.$linkModuleName);
        }

        $relationshipData = $account->$relationship->query($query);
        $rowCount = 1;

        $data = array();
        foreach ($relationshipData['rows'] as $id => $value) {
            $rowCount++;
            $bean = BeanFactory::getBean(ucfirst($relationship), $id);
            $data[] = $this->formatBean($api, $args, $bean);
            if (!is_null($limit) && $rowCount == $limit) {
                // We have hit our limit.
                break;
            }
        }
        return $data;
    }


}

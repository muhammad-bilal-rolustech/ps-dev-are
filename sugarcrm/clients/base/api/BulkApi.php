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
//BEGIN SUGARCRM flav=ent ONLY
use Sugarcrm\Sugarcrm\ProcessManager\Registry;
//END SUGARCRM flav=ent ONLY
/**
 * Bulk API calls
 *
 */
class BulkApi extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'bulkCall' => array(
                'reqType' => 'POST',
                'path' => array('bulk'),
                'pathVars' => array(''),
                'method' => 'bulkCall',
                'shortHelp' => 'Run several API call in a sequence',
                'longHelp' => 'include/api/help/bulk_post_help.html',
            ),
        );
    }

    /**
     * Bulk API call
     * @param ServiceBase $api
     * @param array $args
     * @throws SugarApiExceptionMissingParameter
     * @return array
     */
    public function bulkCall(ServiceBase $api, array $args)
    {
        $this->requireArgs($args,array('requests'));
        $restResp = new BulkRestResponse($_SERVER);
        // reset vars so they won't confuse the child service
        $_GET = array(); $_POST = array();
        foreach($args['requests'] as $name => $request) {
            if(empty($request['url'])) {
                $GLOBALS['log']->fatal("Bulk Api: URL missing for request $name");
                throw new SugarApiExceptionMissingParameter("Invalid request - URL is missing");
            }
        }
        // check all reqs first so that we don't execute any reqs if one of them is broken
        foreach($args['requests'] as $name => $request) {
            $restReq = new BulkRestRequest($request);
            $restResp->setRequest($name);
            /**
             * @var $rest RestService
             */
            $rest = new BulkRestService($api);
            $rest->setRequest($restReq);
            $rest->setResponse($restResp);
            //BEGIN SUGARCRM flav=ent ONLY
            // Because we want to trigger processes for each save
            Registry\Registry::getInstance()->drop('triggered_starts');
            //END SUGARCRM flav=ent ONLY
            $rest->execute();

        }
        return $restResp->getResponses();
    }
}

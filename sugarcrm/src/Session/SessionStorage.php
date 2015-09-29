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

namespace Sugarcrm\Sugarcrm\Session;

use Sugarcrm\Sugarcrm\Util\Arrays\TrackableArray\TrackableArray;
use Sugarcrm\Sugarcrm\Logger\LoggerTransition;


/**
 * Class SessionStorage
 *
 * Base SessionStorageImplementation.
 * Backed by php sessions but with the ability to force non-blocking writes.
 *
 * @package Sugarcrm\Sugarcrm\Session
 */

class SessionStorage extends TrackableArray implements SessionStorageInterface
{
    protected static $shutdownRegisterd = false;

    protected static $instance;

    protected $closed = false;

    /**
     * {@inheritdoc} Checks for custom SessionStorage classes or alternate SessionStorage classes set in config.
     */
    public static function getInstance() {
        $className = \SugarConfig::getInstance()->get(
            'SessionStorageClass',
            'Sugarcrm\Sugarcrm\Session\SessionStorage'
        );
        if (!static::$instance) {
            $class = \SugarAutoLoader::customClass($className);
            static::$instance = new $class();
        }

        return static::$instance;
    }

    /**
     * {@inheritdoc} starts a normal php session.
     */
    public function start($lock = false)
    {
        session_start();
        $this->populateFromArray($_SESSION);
        //keep session values
        $previousUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false;

        $_SESSION = $this;
        $this->enableTracking();

        if (!$lock) {
            $this->unlock();
        }
        $this->registerShutdownFunction($previousUserId);
    }

    /**
     * {@inheritdoc} destroy the current php session.
     */
    public function destroy() {
        foreach($this as $key => $val) {
            $this->offsetUnset($key);
        }
        $this->modifiedKeys = array();
        $this->unsetKeys = array();
        session_destroy();
    }

    /**
     * {@inheritdoc} Sets the php session id
     */
    public function setId($id)
    {
        return session_id($id);
    }

    /**
     * {@inheritdoc} Returns the current php session id (if it exists).
     */
    public function getId()
    {
        return session_id();
    }

    public function unlock()
    {
        if (!$this->getId()) {
            return;
        }
        if (function_exists('session_status') && session_status() != PHP_SESSION_ACTIVE) {
            return;
        }
        session_write_close();
        $_SESSION = $this;
        $this->closed = true;
    }

    public function sessionHasId()
    {
        //Grab the PHP session name
        $session_name = session_name();
        if (!empty($_GET[$session_name])) {
            return true;
        }

        $session_id = $this->getId();

        return empty($session_id) ? true : false;
    }


    protected static function registerShutdownFunction($previousUserId)
    {
        if (!static::$shutdownRegisterd) {
            register_shutdown_function(function () use ($previousUserId) {
                $logger = new LoggerTransition(\LoggerManager::getLogger());
                //Now write out the session data again during shutdown
                $sessionObject = $_SESSION;
                if ($sessionObject instanceof SessionStorage && !$sessionObject->closed) {
                    $_SESSION = $sessionObject->getArrayCopy();
                } else {
                    //Supress any warning before we start the session. If there was any output, the cookie won't be sent
                    //But that is OK here as we are only saving changes, not outputting anything to user. They should already
                    //Have the session cookie.
                    $errRep = error_reporting();
                    error_reporting($errRep & ~E_WARNING);
                    session_start();
                    //First verify that the sessions still match and we didn't somehow switch users.
                    if ((!isset($_SESSION['user_id']) && $previousUserId) ||
                        ($previousUserId && isset($_SESSION['user_id']) && $previousUserId != $_SESSION['user_id'])
                    ) {
                        $logger->warning(
                            'Unexpected change in user or logout during session write at shutdown'
                        );
                    } else {
                        if ($sessionObject instanceof TrackableArray) {
                            $sessionObject->applyTrackedChangesToArray($_SESSION);
                        } else {
                            $logger->alert(
                                '$_SESSION changed from TrackableArray obect to ' . get_class($_SESSION)
                            );
                        }
                    }
                    error_reporting($errRep);
                }
                session_write_close();
            }
            );
            static::$shutdownRegisterd = true;
        }
    }
}
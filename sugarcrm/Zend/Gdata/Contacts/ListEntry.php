<?php
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
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

/**
 * Implementation by SugarCRM, not shipped by ZF.
 *
 */

/**
 * @see Zend_Gdata_Entry
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Contacts_Extension_Name
 */
require_once 'Zend/Gdata/Contacts/Extension/Name.php';

/**
 * @see Zend_Gdata_Contacts_Extension_Birthday
 */
require_once 'Zend/Gdata/Contacts/Extension/Birthday.php';

/**
 * @see Zend_Gdata_Contacts_Extension_Birthday
 */
require_once 'Zend/Gdata/Contacts/Extension/PhoneNumber.php';

/**
 * @see Zend_Gdata_Contacts_Extension_Birthday
 */
require_once 'Zend/Gdata/Contacts/Extension/Email.php';


/**
 * @see Zend_Extension_Where
 */
require_once 'Zend/Gdata/Extension/Where.php';

/**
 * Represents a Contact entry in the Contact data API meta feed
 *
 */
class Zend_Gdata_Contacts_ListEntry extends Zend_Gdata_Entry
{

    protected $_names = null;
    protected $_birthday = null;
    protected $_phones = array();
    protected $_emails = array();

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Zend_Gdata_Contacts::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {

            case $this->lookupNamespace('gd') . ':' . 'name';
                $item = new Zend_Gdata_Contacts_Extension_Name();
                $item->transferFromDOM($child);
                $this->_names = $item;
            break;

            case $this->lookupNamespace('gContact') . ':' . 'birthday';
                $item = new Zend_Gdata_Contacts_Extension_Birthday();
                $item->transferFromDOM($child);
                $this->_birthday = $item;
            break;

            case $this->lookupNamespace('gd') . ':' . 'phoneNumber';
                $item = new Zend_Gdata_Contacts_Extension_PhoneNumber();
                $item->transferFromDOM($child);
                $this->_phones[] = $item;
            break;

            case $this->lookupNamespace('gd') . ':' . 'email';
                $item = new Zend_Gdata_Contacts_Extension_email();
                $item->transferFromDOM($child);
                $this->_emails[] = $item;
            break;

            default:
                parent::takeChildFromDOM($child);
            break;
        }
    }

    public function toArray()
    {
        $entry = array('first' => '', 'last' => '', 'full' => '', 'id' => '', 'birthday' => '', 'phones' => array(),
                        'emails' => array(), 'notes' => '');
        
        if($this->_names != null)
            $entry = array_merge($entry, $this->_names->toArray() );

        //Get the self link so we can query for the contact details at a later date
        foreach($this->_link as $linkEntry)
        {
            $linkRel = $linkEntry->getRel();
            if( $linkRel != null && $linkRel == "self" )
            {
                $entry['self_link'] = $linkEntry->getHref();
                continue;
            }
        }

        //Process phones
        foreach($this->_phones as $phoneEntry)
        {
            $entry['phones'][] = array('number' => $phoneEntry->getNumber() , 'primary' => $phoneEntry->isPrimary(),
                                       'type' => $phoneEntry->getPhoneType() );
        }

        //Process emails
        foreach($this->_emails as $emailEntry)
        {
            $entry['emails'][] = array('email' => $emailEntry->getEmail() , 'primary' => $emailEntry->isPrimary(),
                                       'type' => $emailEntry->getEmailType() );
        }

        //Get Notes
        if($this->_content != null)
            $entry['notes'] = $this->getContent()->getText();

        //ID
        $entry['id'] = $this->getId()->getText();

        //Birthday
        if($this->_birthday != null)
            $entry['birthday'] = $this->_birthday->getBirthday();
        
        return $entry;
    }


}

<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
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
require_once "modules/Mailer/MailerFactory.php";  // imports all but one of the Mailer classes that are needed
require_once "modules/Mailer/AttachmentPeer.php"; // AttachmentPeer is needed to factor Attachments and EmbeddedImages
require_once 'include/upload_file.php';

class Email extends SugarBean {
	/* SugarBean schema */
	var $id;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $assigned_user_name;
	var $modified_user_id;
	var $created_by;
	//BEGIN SUGARCRM flav=pro ONLY
	var $team_id;
	//END SUGARCRM flav=pro ONLY
	var $deleted;
	var $from_addr;
	var $reply_to_addr;
	var $to_addrs;
    var $cc_addrs;
    var $bcc_addrs;
	var $message_id;

	/* Bean Attributes */
	var $name;
    var $type = 'archived';
    var $date_sent;
	var $status;
	var $intent;
	var $mailbox_id;
	var $from_name;

	var $reply_to_status;
	var $reply_to_name;
	var $reply_to_email;
	var $description;
	var $description_html;
	var $raw_source;
	var $parent_id;
	var $parent_type;

	/* link attributes */
	var $parent_name;


	/* legacy */
	var $date_start; // legacy
	var $time_start; // legacy
	var $from_addr_name;
	var $to_addrs_arr;
    var $cc_addrs_arr;
    var $bcc_addrs_arr;
	var $to_addrs_ids;
	var $to_addrs_names;
	var $to_addrs_emails;
	var $cc_addrs_ids;
	var $cc_addrs_names;
	var $cc_addrs_emails;
	var $bcc_addrs_ids;
	var $bcc_addrs_names;
	var $bcc_addrs_emails;
	var $contact_id;
	var $contact_name;

	/* Archive Email attrs */
	var $duration_hours;



	var $new_schema = true;
	var $table_name = 'emails';
	var $module_dir = 'Emails';
    var $module_name = 'Emails';
	var $object_name = 'Email';
	var $db;

	/* private attributes */
	var $rolloverStyle		= "<style>div#rollover {position: relative;float: left;margin: none;text-decoration: none;}div#rollover a:hover {padding: 0;text-decoration: none;}div#rollover a span {display: none;}div#rollover a:hover span {text-decoration: none;display: block;width: 250px;margin-top: 5px;margin-left: 5px;position: absolute;padding: 10px;color: #333;	border: 1px solid #ccc;	background-color: #fff;	font-size: 12px;z-index: 1000;}</style>\n";
	var $cachePath;
	var $cacheFile			= 'robin.cache.php';
	var $replyDelimiter	= "> ";
	var $emailDescription;
	var $emailDescriptionHTML;
	var $emailRawSource;
	var $link_action;
	var $emailAddress;
	var $attachments = array();
    var $saved_attachments = array();

	/* to support Email 2.0 */
	var $isDuplicate;
	var $uid;
	var $to;
	var $flagged;
	var $answered;
	var $seen;
	var $draft;
	var $relationshipMap = array(
		'Contacts'	=> 'emails_contacts_rel',
		'Accounts'	=> 'emails_accounts_rel',
		'Leads'		=> 'emails_leads_rel',
		'Users'		=> 'emails_users_rel',
		'Prospects'	=> 'emails_prospects_rel',
	);

	/* public */
	var $et;		// EmailUI object
	// prefix to use when importing inlinge images in emails
	public $imagePrefix;

    private $MockMailerFactoryClass = 'MailerFactory';

    /**
     * Used for keeping track of field defs that have been modified
     *
     * @var array
     */
    public $modifiedFieldDefs = array();

    /**
     * Used for keeping track of field defs that have been added
     *
     * @var array
     */
    protected $addedFieldDefs = array();

    /**
     * This is a depreciated method, please start using __construct() as this method will be removed in a future version
     *
     * @see __construct
     * @deprecated
     */
    public function Email()
    {
        self::__construct();
    }

	/**
	 * sole constructor
	 */
	public function __construct()
	{
	    global $current_user;
	    $this->cachePath = sugar_cached('modules/Emails');
		parent::__construct();
		//BEGIN SUGARCRM flav=pro ONLY
		$this->team_id = 1; // make the item globally accessible
		//END SUGARCRM flav=pro ONLY

		$this->emailAddress = BeanFactory::getBean('EmailAddresses');

		$this->imagePrefix = rtrim($GLOBALS['sugar_config']['site_url'], "/")."/cache/images/";
	}

	function email2init() {
		require_once('modules/Emails/EmailUI.php');
		$this->et = new EmailUI();
	}

    /**
     * This method is here solely to allow for the MailerFactory Class to be mocked for testing
     * It should never be used outside of the PHP Unit Test Framework
     *
     * @param $className
     */
    public function _setMailerFactoryClassName($className) {
        $this->MockMailerFactoryClass = $className;
    }


    function bean_implements($interface){
		switch($interface){
			case 'ACL': return true;
			default: return false;
		}

	}

	/**
	 * Presaves one attachment for new email 2.0 spec
	 * DOES NOT CREATE A NOTE
	 * @return string ID of note associated with the attachment
	 */
	public function email2saveAttachment()
	{
        $email_uploads = "modules/Emails/{$GLOBALS['current_user']->id}";
	    $upload = new UploadFile('email_attachment');
		if(!$upload->confirm_upload()) {
		    $err = $upload->get_upload_error();
   		    if($err) {
   		        $GLOBALS['log']->error("Email Attachment could not be attached due to error: $err");
   		    }
   		    return array();
		}

		$guid = create_guid();
		$fileName = $upload->create_stored_filename();
        $GLOBALS['log']->debug("Email Attachment [$fileName]");
        if($upload->final_move($guid)) {
        	copy("upload://$guid", sugar_cached("$email_uploads/$guid"));
			return array(
					'guid' => $guid,
					'name' => $GLOBALS['db']->quote($fileName),
					'nameForDisplay' => $fileName
				);
        } else {
			$GLOBALS['log']->debug("Email Attachment [$fileName] could not be moved to upload dir");
			return array();
        }
	}

	function safeAttachmentName($filename) {
		global $sugar_config;
		$badExtension = false;
		//get position of last "." in file name
		$file_ext_beg = strrpos($filename, ".");
		$file_ext = "";

		//get file extension
		if($file_ext_beg !== false) {
			$file_ext = substr($filename, $file_ext_beg + 1);
		}

		//check to see if this is a file with extension located in "badext"
		foreach($sugar_config['upload_badext'] as $badExt) {
			if(strtolower($file_ext) == strtolower($badExt)) {
				//if found, then append with .txt and break out of lookup
				$filename = $filename . ".txt";
				$badExtension = true;
				break; // no need to look for more
			} // if
		} // foreach

		return $badExtension;
	} // fn

    /**
     * takes output from email 2.0 to/cc/bcc fields and returns appropriate arrays for usage by PHPMailer
     *
     * @param string addresses
     * @return array
     */
    public function email2ParseAddresses($addresses)
    {
        $ret = array();
        if (!empty($addresses)) {
            $addresses = from_html($addresses);
            $addresses = $this->et->unifyEmailString($addresses);
            $pattern   = '/@.*,/U';
            preg_match_all($pattern, $addresses, $matchs);
            if (!empty($matchs[0])) {
                $total = $matchs[0];
                foreach ($total as $match) {
                    $convertedPattern = str_replace(',', '::;::', $match);
                    $addresses        = str_replace($match, $convertedPattern, $addresses);
                } //foreach
            }
            $exAddr = explode("::;::", $addresses);
            $clean  = array("<", ">");
            $dirty  = array("&lt;", "&gt;");
            foreach ($exAddr as $addr) {
                $name = '';
                $addr = str_replace($dirty, $clean, $addr);
                if ((strpos($addr, "<") === false) && (strpos($addr, ">") === false)) {
                    $address = $addr;
                } else {
                    $address = substr($addr, strpos($addr, "<") + 1, strpos($addr, ">") - 1 - strpos($addr, "<"));
                    $name    = substr($addr, 0, strpos($addr, "<"));
                }
                $addrTemp            = array();
                $addrTemp['email']   = trim($address);
                $addrTemp['display'] = trim($name);
                $ret[]               = $addrTemp;
            }
        }
        return $ret;
    }

	/**
	 * takes output from email 2.0 to/cc/bcc fields and returns appropriate arrays for usage by PHPMailer
	 * @param string addresses
	 * @return array
	 */
	function email2ParseAddressesForAddressesOnly($addresses) {
		$addresses = from_html($addresses);
		$pattern = '/@.*,/U';
		preg_match_all($pattern, $addresses, $matchs);
		if (!empty($matchs[0])){
			$total = $matchs[0];
			foreach ($total as $match) {
				$convertedPattern = str_replace(',', '::;::', $match);
				$addresses = str_replace($match, $convertedPattern, $addresses);
			} //foreach
		}

		$exAddr = explode("::;::", $addresses);

		$ret = array();
		$clean = array("<", ">");
		$dirty = array("&lt;", "&gt;");

		foreach($exAddr as $addr) {
			$name = '';

			$addr = str_replace($dirty, $clean, $addr);

			if(strpos($addr, "<") && strpos($addr, ">")) {
				$address = substr($addr, strpos($addr, "<") + 1, strpos($addr, ">") - 1 - strpos($addr, "<"));
			} else {
				$address = $addr;
			}

			$ret[] = trim($address);
		}

		return $ret;
	}

	/**
	 * Determines MIME-type encoding as possible.
	 * @param string $fileLocation relative path to file
	 * @return string MIME-type
	 */
	function email2GetMime($fileLocation) {
	    return get_file_mime_type($fileLocation, 'application/octet-stream');
	}


	function sendEmailTest($mailserver_url, $port, $ssltls, $smtp_auth_req, $smtp_username, $smtppassword, $fromaddress,
        $toaddress, $mail_sendtype = 'smtp', $fromname = ''
    ) {
		global $current_user,
               $app_strings;

		$mod_strings = return_module_language($GLOBALS['current_language'], 'Emails'); //Called from EmailMan as well.

        $fromname = (!empty($fromname)) ? html_entity_decode($fromname, ENT_QUOTES) : $current_user->name;

        $configurations                 = array();
        $configurations["from_email"]   = $fromaddress;
        $configurations["from_name"]    = $fromname;
        $configurations["display_name"] = "{$fromname} ({$fromaddress})";
        $configurations["personal"]     = 0;

        $outboundEmail                    = new OutboundEmail();
        $outboundEmail->mail_sendtype     = $mail_sendtype;
        $outboundEmail->mail_smtpserver   = $mailserver_url;
        $outboundEmail->mail_smtpport     = $port;
        $outboundEmail->mail_smtpauth_req = $smtp_auth_req;
        $outboundEmail->mail_smtpuser     = $smtp_username;
        $outboundEmail->mail_smtppass     = $smtppassword;
        $outboundEmail->mail_smtpssl      = $ssltls;

        $return = array();

        try {
            $outboundEmailConfiguration = OutboundEmailConfigurationPeer::buildOutboundEmailConfiguration(
                $current_user,
                $configurations,
                $outboundEmail
            );

            $mailer = MailerFactory::getMailer($outboundEmailConfiguration);

            $mailer->setSubject($mod_strings['LBL_TEST_EMAIL_SUBJECT']);
            $mailer->addRecipientsTo(new EmailIdentity($toaddress));
            $mailer->setTextBody($mod_strings['LBL_TEST_EMAIL_BODY']);

            $mailer->send();
            $return['status'] = true;
        } catch (MailerException $me) {
            $GLOBALS["log"]->error($me->getLogMessage());
            ob_clean();
            $return['status']       = false;
            $return['errorMessage'] = $app_strings['LBL_EMAIL_ERROR_PREPEND'] . $me->getMessage();
        }

        return $return;
	} // fn

	function decodeDuringSend($htmlData) {
	    $htmlData = str_replace("sugarLessThan", "&lt;", $htmlData);
	    $htmlData = str_replace("sugarGreaterThan", "&gt;", $htmlData);
		return $htmlData;
	}

	/**
	 * Returns true or false if this email is a draft.
	 *
	 * @param array $request
	 * @return bool True indicates this email is a draft.
	 */
	function isDraftEmail($request)
	{
	    return ( isset($request['saveDraft']) || ($this->type == 'draft' && $this->status == 'draft') );
	}

	/**
	 * Sends Email for Email 2.0
	 */
	function email2Send($request) {
		global $current_user;
		global $timedate;

        $saveAsDraft = !empty($request['saveDraft']);
        if (!$saveAsDraft && !empty($request["MAIL_RECORD_STATUS"]) &&  $request["MAIL_RECORD_STATUS"]=='archived') {
            $archived = true;
            $this->type = 'archived';
        } else {
            $archived = false;
            $this->type = 'out';
        }

		/**********************************************************************
		 * Sugar Email PREP
		 */
		/* preset GUID */

		$orignialId = "";
		if(!empty($this->id)) {
			$orignialId = 	$this->id;
		} // if

		if(empty($this->id)) {
			$this->id = create_guid();
			$this->new_with_id = true;
		}

		/* satisfy basic HTML email requirements */
		$this->name = $request['sendSubject'];

        if(isset($_REQUEST['setEditor']) && $_REQUEST['setEditor'] == 1) {
            $_REQUEST['description_html'] = $_REQUEST['sendDescription'];
            $this->description_html = $_REQUEST['description_html'];
        } else {
            $this->description_html = '';
            $this->description = $_REQUEST['sendDescription'];
        }

		if ( $this->isDraftEmail($request) )
		{
			if($this->type != 'draft' && $this->status != 'draft') {
	        	$this->id = create_guid();
	        	$this->new_with_id = true;
	        	$this->date_entered = "";
			} // if
			$q1 = "update emails_email_addr_rel set deleted = 1 WHERE email_id = '{$this->id}'";
			$this->db->query($q1);
		} // if

		if ($saveAsDraft) {
			$this->type = 'draft';
			$this->status = 'draft';
		} else {

            if ($archived) {
                $this->type = 'archived';
                $this->status = 'archived';
            }

			/* Apply Email Templates */
			// do not parse email templates if the email is being saved as draft....
		    $toAddresses = $this->email2ParseAddresses($_REQUEST['sendTo']);
	        $sea = BeanFactory::getBean('EmailAddresses');
	        $object_arr = array();

			if( !empty($_REQUEST['parent_type']) && !empty($_REQUEST['parent_id']) &&
				($_REQUEST['parent_type'] == 'Accounts' ||
				$_REQUEST['parent_type'] == 'Contacts' ||
				$_REQUEST['parent_type'] == 'Leads' ||
				$_REQUEST['parent_type'] == 'Users' ||
				$_REQUEST['parent_type'] == 'Prospects')) {
			        $bean = BeanFactory::getBean($_REQUEST['parent_type'], $_REQUEST['parent_id']);
			        if(!empty($bean->id)) {
			            $object_arr[$bean->module_dir] = $bean->id;
			        }
			}
			foreach($toAddresses as $addrMeta) {
				$addr = $addrMeta['email'];
				$beans = $sea->getBeansByEmailAddress($addr);
				foreach($beans as $bean) {
					if (!isset($object_arr[$bean->module_dir])) {
						$object_arr[$bean->module_dir] = $bean->id;
					}
				}
			}

	        /* template parsing */
	        if (empty($object_arr)) {
	          $object_arr= array('Contacts' => '123');
	        }
	        $object_arr['Users'] = $current_user->id;
	        $this->description_html = EmailTemplate::parse_template($this->description_html, $object_arr);
	        $this->name = EmailTemplate::parse_template($this->name, $object_arr);
	        $this->description = EmailTemplate::parse_template($this->description, $object_arr);
	        $this->description = html_entity_decode($this->description,ENT_COMPAT,'UTF-8');
            
            if (empty($this->id) && $this->type != 'draft' && $this->status != 'draft' &&
                $this->type != 'archived' && $this->status != 'archived'
            ) {
                $this->id = create_guid();
                $this->date_entered = "";
                $this->new_with_id = true;
                $this->type = 'out';
                $this->status = 'sent';
            }
        }

        if(isset($_REQUEST['parent_type']) && empty($_REQUEST['parent_type']) &&
			isset($_REQUEST['parent_id']) && empty($_REQUEST['parent_id']) ) {
				$this->parent_id = "";
				$this->parent_type = "";
		} // if

        $forceSave = false;
        $subject   = $this->name;
        $textBody  = from_html($this->description);
        $htmlBody  = from_html($this->description_html);

        //------------------- HANDLEBODY() ---------------------------------------------
        if ((isset($_REQUEST['setEditor']) /* from Email EditView navigation */
             && $_REQUEST['setEditor'] == 1
             && trim($_REQUEST['description_html']) != '')
            || trim($this->description_html) != '' /* from email templates */
               && $current_user->getPreference('email_editor_option', 'global') !== 'plain' //user preference is not set to plain text
        ) {
            $textBody = strip_tags(br2nl($htmlBody));
        } else {
            // plain-text only
            $textBody = str_replace("&nbsp;", " ", $textBody);
            $textBody = str_replace("</p>", "</p><br />", $textBody);
            $textBody = strip_tags(br2nl($textBody));
            $textBody = html_entity_decode($textBody, ENT_QUOTES, 'UTF-8');

            $this->description_html = ""; // make sure it's blank to avoid any mishaps
            $htmlBody               = $this->description_html;
        }

        $textBody               = $this->decodeDuringSend($textBody);
        $htmlBody               = $this->decodeDuringSend($htmlBody);
        $this->description      = $textBody;
        $this->description_html = $htmlBody;

        $mailConfig = null;
        try {
            if (isset($request["fromAccount"]) && !empty($request["fromAccount"])) {
                $mailConfig = OutboundEmailConfigurationPeer::getMailConfigurationFromId($current_user, $request["fromAccount"]);
            } else {
                $mailConfig = OutboundEmailConfigurationPeer::getSystemMailConfiguration($current_user);
            }
        } catch(Exception $e) {
            if (!$saveAsDraft && !$archived) {
                throw $e;
            }
        }
        if (!$saveAsDraft && !$archived && is_null($mailConfig)) {
            throw new MailerException("No Valid Mail Configurations Found", MailerException::InvalidConfiguration);
        }

        try {
            $mailer = null;
            if (!$saveAsDraft && !$archived) {
                $mailerFactoryClass = $this->MockMailerFactoryClass;
                $mailer = $mailerFactoryClass::getMailer($mailConfig);
                $mailer->setSubject($subject);
                $mailer->setHtmlBody($htmlBody);
                $mailer->setTextBody($textBody);

                $replyTo = $mailConfig->getReplyTo();
                if (!empty($replyTo)) {
                    $replyToEmail = $replyTo->getEmail();
                    if (!empty($replyToEmail)) {
                        $mailer->setHeader(
                            EmailHeaders::ReplyTo,
                            new EmailIdentity($replyToEmail, $replyTo->getName())
                        );
                    }
                }
            }

            if (!is_null($mailer)) {
                // Any individual Email Address that is not valid will be logged and skipped
                // If all email addresses in the request are skipped, an error "No Recipients" is reported for the request
                foreach ($this->email2ParseAddresses($request['sendTo']) as $addr_arr) {
                    try {
                        $mailer->addRecipientsTo(new EmailIdentity($addr_arr['email'], $addr_arr['display']));
                    } catch (MailerException $me) {
                        // Invalid Email Address - Log it and Skip
                        $GLOBALS["log"]->warning($me->getLogMessage());
                    }
                }

                foreach ($this->email2ParseAddresses($request['sendCc']) as $addr_arr) {
                    try {
                        $mailer->addRecipientsCc(new EmailIdentity($addr_arr['email'], $addr_arr['display']));
                    } catch (MailerException $me) {
                        // Invalid Email Address - Log it and Skip
                        $GLOBALS["log"]->warning($me->getLogMessage());
                    }
                }

                foreach ($this->email2ParseAddresses($request['sendBcc']) as $addr_arr) {
                    try {
                        $mailer->addRecipientsBcc(new EmailIdentity($addr_arr['email'], $addr_arr['display']));
                    } catch (MailerException $me) {
                        // Invalid Email Address - Log it and Skip
                        $GLOBALS["log"]->warning($me->getLogMessage());
                    }
                }
            }

            /* handle attachments */
            if (!empty($request['attachments'])) {
                $exAttachments = explode("::", $request['attachments']);

                foreach ($exAttachments as $file) {
                    $file = trim(from_html($file));
                    $file = str_replace("\\", "", $file);
                    if (!empty($file)) {
                        $fileGUID = preg_replace('/[^a-z0-9\-]/', "", substr($file, 0, 36));
                        $fileLocation = $this->et->userCacheDir . "/{$fileGUID}";
                        $filename     = substr($file, 36, strlen($file)); // strip GUID	for PHPMailer class to name outbound file

                        // only save attachments if we're archiving or drafting
                        if ((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
                            $note                 = new Note();
                            $note->id             = create_guid();
                            $note->new_with_id    = true; // duplicating the note with files
                            $note->parent_id      = $this->id;
                            $note->parent_type    = $this->module_dir;
                            $note->name           = $filename;
                            $note->filename       = $filename;
                            $note->file_mime_type = $this->email2GetMime($fileLocation);
                            //BEGIN SUGARCRM flav=pro ONLY
                            $note->team_id     = (isset($_REQUEST['primaryteam']) ? $_REQUEST['primaryteam'] : $current_user->getPrivateTeamID());
                            $noteTeamSet       = new TeamSet();
                            $noteteamIdsArray  = (isset($_REQUEST['teamIds']) ? explode(",", $_REQUEST['teamIds']) : array($current_user->getPrivateTeamID()));
                            $note->team_set_id = $noteTeamSet->addTeams($noteteamIdsArray);
                            //END SUGARCRM flav=pro ONLY
                            $dest = "upload://{$note->id}";

                            if (!file_exists($fileLocation) || (!copy($fileLocation, $dest))) {
                                $GLOBALS['log']->debug("EMAIL 2.0: could not copy attachment file to $fileLocation => $dest");
                            } else {
                                $note->save();
                                $validNote = true;
                            }
                        } else {
                            $note      = new Note();
                            $validNote = (bool)$note->retrieve($fileGUID);
                        }

                        if (isset($validNote) && $validNote === true) {
                            $attachment = AttachmentPeer::attachmentFromSugarBean($note);
                            if (!is_null($mailer)) {
                                $mailer->addAttachment($attachment);
                            }
                        }
                    }
                }
            }

            /* handle sugar documents */
            if (!empty($request['documents'])) {
                $exDocs = explode("::", $request['documents']);

                foreach ($exDocs as $docId) {
                    $docId = trim($docId);
                    if (!empty($docId)) {
                        $doc = new Document();
                        $doc->retrieve($docId);

                        if (empty($doc->id) || $doc->id != $docId) {
                            throw new Exception("Document Not Found: Id='". $request['documents'] . "'");
                        }

                        $documentRevision                             = new DocumentRevision();
                        $documentRevision->retrieve($doc->document_revision_id);
                        //$documentRevision->x_file_name   = $documentRevision->filename;
                        //$documentRevision->x_file_path   = "upload/{$documentRevision->id}";
                        //$documentRevision->x_file_exists = (bool) file_exists($documentRevision->x_file_path);
                        //$documentRevision->x_mime_type   = $documentRevision->file_mime_type;

                        $filename     = $documentRevision->filename;
                        $docGUID = preg_replace('/[^a-z0-9\-]/', "", $documentRevision->id);
                        $fileLocation = "upload://{$docGUID}";

                        if (empty($documentRevision->id) || !file_exists($fileLocation)) {
                            throw new Exception("Document Revision Id Not Found");
                        }

                        // only save attachments if we're archiving or drafting
                        if ((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
                            $note                 = new Note();
                            $note->id             = create_guid();
                            $note->new_with_id    = true; // duplicating the note with files
                            $note->parent_id      = $this->id;
                            $note->parent_type    = $this->module_dir;
                            $note->name           = $filename;
                            $note->filename       = $filename;
                            $note->file_mime_type = $documentRevision->file_mime_type;
                            //BEGIN SUGARCRM flav=pro ONLY
                            $note->team_id     = $this->team_id;
                            $note->team_set_id = $this->team_set_id;
                            //END SUGARCRM flav=pro ONLY
                            $dest = "upload://{$note->id}";
                            if (!file_exists($fileLocation) || (!copy($fileLocation, $dest))) {
                                $GLOBALS['log']->debug("EMAIL 2.0: could not copy SugarDocument revision file $fileLocation => $dest");
                            }
                            $note->save();
                        }

                        $attachment = AttachmentPeer::attachmentFromSugarBean($documentRevision);
                        //print_r($attachment);
                        if (!is_null($mailer)) {
                            $mailer->addAttachment($attachment);
                        }
                    }
                }
            }

            /* handle template attachments */
            if (!empty($request['templateAttachments'])) {
                $exNotes = explode("::", $request['templateAttachments']);

                foreach ($exNotes as $noteId) {
                    $noteId = trim($noteId);

                    if (!empty($noteId)) {
                        $note = new Note();
                        $note->retrieve($noteId);

                        if (!empty($note->id)) {
                            $filename     = $note->filename;
                            $noteGUID = preg_replace('/[^a-z0-9\-]/', "", $note->id);
                            $fileLocation = "upload://{$noteGUID}";
                            $mime_type    = $note->file_mime_type;

                            if (!$note->embed_flag) {
                                $attachment = AttachmentPeer::attachmentFromSugarBean($note);
                                //print_r($attachment);
                                if (!is_null($mailer)) {
                                    $mailer->addAttachment($attachment);
                                }

                                // only save attachments if we're archiving or drafting
                                if ((($this->type == 'draft') && !empty($this->id)) || (isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {
                                    if ($note->parent_id != $this->id) {
                                        $this->saveTempNoteAttachments($filename, $fileLocation, $mime_type);
                                    }
                                } // if
                            } // if
                        } else {
                            $fileGUID = preg_replace('/[^a-z0-9\-]/', "", substr($noteId, 0, 36));
                            $fileLocation = $this->et->userCacheDir . "/{$fileGUID}";
                            $filename = substr($noteId, 36, strlen($noteId)); // strip GUID	for PHPMailer class to name outbound file

                            $mimeType = $this->email2GetMime($fileLocation);
                            $note = $this->saveTempNoteAttachments($filename, $fileLocation, $mimeType);

                            $attachment = AttachmentPeer::attachmentFromSugarBean($note);
                            //print_r($attachment);
                            if (!is_null($mailer)) {
                                $mailer->addAttachment($attachment);
                            }
                        }
                    }
                }
            }

            /**********************************************************************
             * Final Touches
             */
            if ($this->type == 'draft' && !$saveAsDraft) {
                // sending a draft email
                $this->type   = 'out';
                $this->status = 'sent';
                $forceSave    = true;
            } elseif ($saveAsDraft) {
                $this->type   = 'draft';
                $this->status = 'draft';
                $forceSave    = true;
            }

            if (!is_null($mailer)) {
                $mailer->send();
            }
        }
        catch (MailerException $me) {
            $GLOBALS["log"]->error($me->getLogMessage());
            throw($me);
        }
        catch (Exception $e) {
            // eat the phpmailerException but use it's message to provide context for the failure
            $me = new MailerException("Email2Send Failed: " . $e->getMessage(), MailerException::FailedToSend);
            $GLOBALS["log"]->error($me->getLogMessage());
            $GLOBALS["log"]->info($me->getTraceMessage());
            if (!empty($mailConfig)) {
                $GLOBALS["log"]->info($mailConfig->toArray(),true);
            }
            throw($me);
        }


		if ((!(empty($orignialId) || $saveAsDraft || ($this->type == 'draft' && $this->status == 'draft'))) &&
			(($_REQUEST['composeType'] == 'reply') || ($_REQUEST['composeType'] == 'replyAll') || ($_REQUEST['composeType'] == 'replyCase')) && ($orignialId != $this->id)) {
			$originalEmail = BeanFactory::getBean('Emails', $orignialId);
			$originalEmail->reply_to_status = 1;
			$originalEmail->save();
			$this->reply_to_status = 0;
		} // if

        if (isset($_REQUEST['composeType']) && ($_REQUEST['composeType'] == 'reply' || $_REQUEST['composeType'] == 'replyCase')) {
			if (isset($_REQUEST['ieId']) && isset($_REQUEST['mbox'])) {
				$emailFromIe = BeanFactory::getBean('InboundEmail', $_REQUEST['ieId']);
				$emailFromIe->mailbox = $_REQUEST['mbox'];
				if (isset($emailFromIe->id) && $emailFromIe->is_personal) {
					if ($emailFromIe->isPop3Protocol()) {
						$emailFromIe->mark_answered($this->uid, 'pop3');
					}
					elseif ($emailFromIe->connectMailserver() == 'true') {
						$emailFromIe->markEmails($this->uid, 'answered');
						$emailFromIe->mark_answered($this->uid);
					}
				}
			}
		}


		if(	$forceSave ||
			$this->type == 'draft' ||
            $this->type == 'archived' ||
			(isset($request['saveToSugar']) && $request['saveToSugar'] == 1)) {

            // Set Up From Name and Address Information
            if ($this->type == 'archived') {
                $this->from_addr = empty($request['archive_from_address']) ? '' : $request['archive_from_address'];
            } elseif (!empty($mailConfig)) {
                $sender = $mailConfig->getFrom();
                $decodedFromName = mb_decode_mimeheader($sender->getName());
                $this->from_addr = "{$decodedFromName} <" . $sender->getEmail() . ">";
            } else {
                $ret = $current_user->getUsersNameAndEmail();
                if (empty($ret['email'])) {
                    $systemReturn  = $current_user->getSystemDefaultNameAndEmail();
                    $ret['email']  = $systemReturn['email'];
                    $ret['name']   = $systemReturn['name'];
                }
                $decodedFromName = mb_decode_mimeheader($ret['name']);
                $this->from_addr = "{$decodedFromName} <" . $ret['email'] . ">";
            }

			$this->from_addr_name = $this->from_addr;
			$this->to_addrs = $_REQUEST['sendTo'];
			$this->to_addrs_names = $_REQUEST['sendTo'];
			$this->cc_addrs = $_REQUEST['sendCc'];
			$this->cc_addrs_names = $_REQUEST['sendCc'];
			$this->bcc_addrs = $_REQUEST['sendBcc'];
			$this->bcc_addrs_names = $_REQUEST['sendBcc'];
			//BEGIN SUGARCRM flav=pro ONLY
			$this->team_id = (isset($_REQUEST['primaryteam']) ?  $_REQUEST['primaryteam'] : $current_user->getPrivateTeamID());
			$teamSet = BeanFactory::getBean('TeamSets');
			$teamIdsArray = (isset($_REQUEST['teamIds']) ?  explode(",", $_REQUEST['teamIds']) : array($current_user->getPrivateTeamID()));
			$this->team_set_id = $teamSet->addTeams($teamIdsArray);
			//END SUGARCRM flav=pro ONLY
			$this->assigned_user_id = $current_user->id;

			$this->date_sent = $timedate->now();
			///////////////////////////////////////////////////////////////////
			////	LINK EMAIL TO SUGARBEANS BASED ON EMAIL ADDY

			if(!empty($_REQUEST['parent_type']) && !empty($_REQUEST['parent_id']) ) {
	                $this->parent_id = $this->db->quote($_REQUEST['parent_id']);
	                $this->parent_type = $this->db->quote($_REQUEST['parent_type']);
					$a = $this->db->fetchOne("SELECT count(*) c FROM emails_beans WHERE  email_id = '{$this->id}' AND bean_id = '{$this->parent_id}' AND bean_module = '{$this->parent_type}'");
					if($a['c'] == 0) {
					    $bean = BeanFactory::getBean($_REQUEST['parent_type'], $_REQUEST['parent_id']);
                        if (!empty($bean)) {
                            if (!empty($bean->field_defs['emails']['type']) && $bean->field_defs['emails']['type'] == 'link') {
                                $email_link = "emails";
                            } else {
                                $email_link = $this->findEmailsLink($bean);
                            }
                            if ($email_link && $bean->load_relationship($email_link) ){
                                $bean->$email_link->add($this);
                            }
                        }
					} // if

				} else {
                    $c = BeanFactory::getBean('Cases');
                    if($caseId = InboundEmail::getCaseIdFromCaseNumber($subject, $c)) {
                        $c->retrieve($caseId);
                        $c->load_relationship('emails');
                        $c->emails->add($this->id);
                        $this->parent_type = "Cases";
                        $this->parent_id = $caseId;
                    } // if
				} // else

			////	LINK EMAIL TO SUGARBEANS BASED ON EMAIL ADDY
			///////////////////////////////////////////////////////////////////
			$this->save();
		}


        /**** --------------------------------- ?????????
		if(!empty($request['fromAccount'])) {
            $ie = new InboundEmail();
            $ie->retrieve($request['fromAccount']);
			if (isset($ie->id) && !$ie->isPop3Protocol() && $mail->oe->mail_smtptype != 'gmail') {
				$sentFolder = $ie->get_stored_options("sentFolder");
				if (!empty($sentFolder)) {
					$data = $mail->CreateHeader() . "\r\n" . $mail->CreateBody() . "\r\n";
					$ie->mailbox = $sentFolder;
					if ($ie->connectMailserver() == 'true') {
						$connectString = $ie->getConnectString($ie->getServiceString(), $ie->mailbox);
						$returnData = imap_append($ie->conn,$connectString, $data, "\\Seen");
						if (!$returnData) {
							$GLOBALS['log']->debug("could not copy email to {$ie->mailbox} for {$ie->name}");
						} // if
					} else {
						$GLOBALS['log']->debug("could not connect to mail serve for folder {$ie->mailbox} for {$ie->name}");
					} // else
				} else {
					$GLOBALS['log']->debug("could not copy email to {$ie->mailbox} sent folder as its empty");
				} // else
			} // if
		} // if
        ------------------------------------- ****/

		return true;
	} // end email2Send

	/**
	 * Generates a config-specified separated name and addresses to be used in compose email screen for
	 * contacts or leads from listview
     * By default, use comma, but allow for non-standard delimeters as specified in email_address_separator
	 *
	 * @param $module string module name
	 * @param $idsArray array of record ids to get the email address for
	 * @return string (config-specified) delimited list of email addresses
	 */
	public function getNamePlusEmailAddressesForCompose($module, $idsArray)
	{
		global $locale;
        $result = array();
		global $db;
		$table = BeanFactory::getBean($module)->table_name;
		$returndata = array();
		$idsString = "";
		foreach($idsArray as $id) {
			if ($idsString != "") {
				$idsString = $idsString . ",";
			} // if
			$idsString = $idsString . "'" . $id . "'";
		} // foreach
		$where = "({$table}.deleted = 0 AND {$table}.id in ({$idsString}))";

        foreach ($idsArray as $id)
        {
            // Load bean
            $bean = BeanFactory::getBean($module, $id);
            // Got a bean
            if (!empty($bean))
            {
                // For CE, just get primary e-mail address
                $emailAddress = $bean->email1;

                //BEGIN SUGARCRM flav=pro ONLY
                $emailAddress = '';
                // If has access to primary mail, use it
                if ($bean->ACLFieldAccess('email1', 'read'))
                {
                    $emailAddress = $bean->email1;
                }
                // Otherwise, try to use secondary
                else if ($bean->ACLFieldAccess('email2', 'read'))
                {
                    $emailAddress = $bean->email2;
                }
                //END SUGARCRM flav=pro ONLY

                // If we have an e-mail address loaded
                if (!empty($emailAddress))
                {
                    $fullName = from_html($bean->get_summary_text());

                    // Make e-mail address in format "Name <@email>"
                    $result[$bean->id] = $fullName . " <" . from_html($emailAddress) . ">";
                }
            }
        }

        // Broken out of method to facilitate unit testing
        return $this->_arrayToDelimitedString($result);
    }

    /**
     * @param Array $arr - list of strings
     * @return string the list of strings delimited by email_address_separator
     */
    function _arrayToDelimitedString($arr)
    {
        // bug 51804: outlook does not respect the correct email address separator (',') , so let
        // clients override the default.
        $separator = (isset($GLOBALS['sugar_config']['email_address_separator']) &&
                        !empty($GLOBALS['sugar_config']['email_address_separator'])) ?
                     $GLOBALS['sugar_config']['email_address_separator'] :
                     ',';

		return join($separator, array_values($arr));
    }

	/**
	 * Overrides
	 */
	///////////////////////////////////////////////////////////////////////////
	////	SAVERS
	function save($check_notify = false) {
        global $current_user;

		if($this->isDuplicate) {
			$GLOBALS['log']->debug("EMAIL - tried to save a duplicate Email record");
		} else {

			if(empty($this->id)) {
				$this->id = create_guid();
				$this->new_with_id = true;
			}
			$this->from_addr_name = $this->cleanEmails($this->from_addr_name);
			$this->to_addrs_names = $this->cleanEmails($this->to_addrs_names);
			$this->cc_addrs_names = $this->cleanEmails($this->cc_addrs_names);
			$this->bcc_addrs_names = $this->cleanEmails($this->bcc_addrs_names);
			$this->reply_to_addr = $this->cleanEmails($this->reply_to_addr);
			$this->description = SugarCleaner::cleanHtml($this->description);
            $this->description_html = SugarCleaner::cleanHtml($this->description_html, true);
            $this->raw_source = SugarCleaner::cleanHtml($this->raw_source, true);
			$this->saveEmailText();
			$this->saveEmailAddresses();

			$GLOBALS['log']->debug('-------------------------------> Email called save()');

			// handle legacy concatenation of date and time fields
			//Bug 39503 - SugarBean is not setting date_sent when seconds missing
 			if(empty($this->date_sent)) {
				global $timedate;
				$date_sent_obj = $timedate->fromUser($timedate->merge_date_time($this->date_start, $this->time_start), $current_user);
                 if (!empty($date_sent_obj) && ($date_sent_obj instanceof SugarDateTime)) {
 				    $this->date_sent = $date_sent_obj->asDb();
                 }
			}

			$parentSaveResult = parent::save($check_notify);

			if(!empty($this->parent_type) && !empty($this->parent_id)) {
                if(!empty($this->fetched_row) && !empty($this->fetched_row['parent_id']) && !empty($this->fetched_row['parent_type'])) {
                    if($this->fetched_row['parent_id'] != $this->parent_id || $this->fetched_row['parent_type'] != $this->parent_type) {
                        $mod = strtolower($this->fetched_row['parent_type']);
                        $rel = array_key_exists($mod, $this->field_defs) ? $mod : $mod . "_activities_emails"; //Custom modules rel name
                        if($this->load_relationship($rel) ) {
                            $this->$rel->delete($this->id, $this->fetched_row['parent_id']);
                        }
                    }
                }
                $mod = strtolower($this->parent_type);
                $rel = array_key_exists($mod, $this->field_defs) ? $mod : $mod . "_activities_emails"; //Custom modules rel name
                if($this->load_relationship($rel) ) {
                    $this->$rel->add($this->parent_id);
                }
			}

            return $parentSaveResult;
		}
		$GLOBALS['log']->debug('-------------------------------> Email save() done');
	}

	/**
	 * Helper function to save temporary attachments assocaited to an email as note.
	 *
	 * @param string $filename
	 * @param string $fileLocation
	 * @param string $mimeType
	 */
	function saveTempNoteAttachments($filename,$fileLocation, $mimeType)
	{
	    $tmpNote = BeanFactory::getBean('Notes');
	    $tmpNote->id = create_guid();
	    $tmpNote->new_with_id = true;
	    $tmpNote->parent_id = $this->id;
	    $tmpNote->parent_type = $this->module_dir;
	    $tmpNote->name = $filename;
	    $tmpNote->filename = $filename;
	    $tmpNote->file_mime_type = $mimeType;
	    //BEGIN SUGARCRM flav=pro ONLY
	    $tmpNote->team_id = $this->team_id;
	    $tmpNote->team_set_id = $this->team_set_id;
	    //END SUGARCRM flav=pro ONLY
	    $noteFile = "upload://{$tmpNote->id}";
        if(!file_exists($fileLocation) || (!copy($fileLocation, $noteFile))) {
    	    $GLOBALS['log']->fatal("EMAIL 2.0: could not copy SugarDocument revision file $fileLocation => $noteFile");
	    }
	    $tmpNote->save();
        return $tmpNote;
	}
	/**
	 * Handles normalization of Email Addressess
	 */
	function saveEmailAddresses() {
		// from, single address
		$fromId = $this->emailAddress->getEmailGUID(from_html($this->from_addr));
        if(!empty($fromId)){
		  $this->linkEmailToAddress($fromId, 'from');
        }

		// to, multiple
		$replace = array(",",";");
		$toaddrs = str_replace($replace, "::", from_html($this->to_addrs));
		$exToAddrs = explode("::", $toaddrs);

		if(!empty($exToAddrs)) {
			foreach($exToAddrs as $toaddr) {
				$toaddr = trim($toaddr);
				if(!empty($toaddr)) {
					$toId = $this->emailAddress->getEmailGUID($toaddr);
					$this->linkEmailToAddress($toId, 'to');
				}
			}
		}

		// cc, multiple
		$ccAddrs = str_replace($replace, "::", from_html($this->cc_addrs));
		$exccAddrs = explode("::", $ccAddrs);

		if(!empty($exccAddrs)) {
			foreach($exccAddrs as $ccAddr) {
				$ccAddr = trim($ccAddr);
				if(!empty($ccAddr)) {
					$ccId = $this->emailAddress->getEmailGUID($ccAddr);
					$this->linkEmailToAddress($ccId, 'cc');
				}
			}
		}

		// bcc, multiple
		$bccAddrs = str_replace($replace, "::", from_html($this->bcc_addrs));
		$exbccAddrs = explode("::", $bccAddrs);
		if(!empty($exbccAddrs)) {
			foreach($exbccAddrs as $bccAddr) {
				$bccAddr = trim($bccAddr);
				if(!empty($bccAddr)) {
					$bccId = $this->emailAddress->getEmailGUID($bccAddr);
					$this->linkEmailToAddress($bccId, 'bcc');
				}
			}
		}
	}

	function linkEmailToAddress($id, $type) {
		// TODO: make this update?
		$q1 = "SELECT * FROM emails_email_addr_rel WHERE email_id = '{$this->id}' AND email_address_id = '{$id}' AND address_type = '{$type}' AND deleted = 0";
		$r1 = $this->db->query($q1);
		$a1 = $this->db->fetchByAssoc($r1);

		if(!empty($a1) && !empty($a1['id'])) {
			return $a1['id'];
		} else {
			$guid = create_guid();
			$q2 = "INSERT INTO emails_email_addr_rel VALUES('{$guid}', '{$this->id}', '{$type}', '{$id}', 0)";
			$r2 = $this->db->query($q2);
		}

		return $guid;
	}

    protected $email_to_text = array(
        "email_id" => "id",
        "description" => "description",
        "description_html" => "description_html",
        "raw_source" => "raw_source",
        "from_addr" => "from_addr_name",
        "reply_to_addr" => "reply_to_addr",
    	"to_addrs" => "to_addrs_names",
        "cc_addrs" => "cc_addrs_names",
        "bcc_addrs" => "bcc_addrs_names",
    );

	function cleanEmails($emails)
	{
	    if(empty($emails)) return '';
		$emails = str_replace(array(",",";"), "::", from_html($emails));
		$addrs = explode("::", $emails);
		$res = array();
		foreach($addrs as $addr) {
            $parts = $this->emailAddress->splitEmailAddress($addr);
            if(empty($parts["email"])) {
                continue;
            }
            if(!empty($parts["name"])) {
                $res[] = "{$parts['name']} <{$parts['email']}>";
            } else {
                $res[] .= $parts["email"];
            }
		}
		return join(", ", $res);
	}

	protected function saveEmailText()
	{
        $text = BeanFactory::getBean("EmailText");
        foreach($this->email_to_text as $textfield=>$mailfield) {
            $text->$textfield = $this->$mailfield;
        }
        $text->email_id = $this->id;
		if(!$this->new_with_id) {
            $this->db->update($text);
		} else {
		    $this->db->insert($text);
		}
	}

    ///////////////////////////////////////////////////////////////////////////
    ////	RETRIEVERS
    function retrieve($id, $encoded = true, $deleted = true)
    {
        // cn: bug 11915, return SugarBean's retrieve() call bean instead of $this
        $bean = parent::retrieve($id, $encoded, $deleted);

        if ($bean) {
            $bean->loadAdditionalEmailData($bean);
        }

        return $bean;
    }

    /**
     * Load any additional data and perform any additional postRetrieve processing
     */
    function loadAdditionalEmailData(SugarBean $emailBean = null)
    {
        if (is_null($emailBean)) {
            $bean = $this;
        } else {
            $bean = $emailBean;
        }
        $bean->retrieveEmailText();
        $bean->description = to_html($bean->description);
        $bean->retrieveEmailAddresses();

        $bean->date_start = '';
        $bean->time_start = '';
        $dateSent = explode(' ', $bean->date_sent);
        if (!empty($dateSent)) {
            $bean->date_start = $dateSent[0];
            if (isset($dateSent[1])) {
                $bean->time_start = $dateSent[1];
            }
        }
        if ($bean !== $this) {
           foreach ($bean as $k => $v) {
                $this->$k = $v;
            }
        }
    }

    /**
	 * Retrieves email addresses from GUIDs
	 */
	function retrieveEmailAddresses() {
		$return = array();

		$q = "SELECT email_address, address_type
				FROM emails_email_addr_rel eam
				JOIN email_addresses ea ON ea.id = eam.email_address_id
				WHERE eam.email_id = '{$this->id}' AND eam.deleted=0";
		$r = $this->db->query($q);

		while($a = $this->db->fetchByAssoc($r)) {
			if(!isset($return[$a['address_type']])) {
				$return[$a['address_type']] = array();
			}
			$return[$a['address_type']][] = $a['email_address'];
		}

		if(count($return) > 0) {
			if(isset($return['from'])) {
				$this->from_addr = implode(", ", $return['from']);
			}
			if(isset($return['to'])) {
				$this->to_addrs = implode(", ", $return['to']);
			}
			if(isset($return['cc'])) {
				$this->cc_addrs = implode(", ", $return['cc']);
			}
			if(isset($return['bcc'])) {
				$this->bcc_addrs = implode(", ", $return['bcc']);
			}
		}
	}

	/**
	 * Handles longtext fields
	 */
	function retrieveEmailText() {
		$q = "SELECT from_addr, reply_to_addr, to_addrs, cc_addrs, bcc_addrs, description, description_html, raw_source FROM emails_text WHERE email_id = '{$this->id}'";
		$r = $this->db->query($q);
		$a = $this->db->fetchByAssoc($r, false);

		$this->description = $a['description'];
		$this->description_html = $a['description_html'];
		$this->raw_source = $a['raw_source'];
		$this->from_addr_name = $a['from_addr'];
		$this->reply_to_addr  = $a['reply_to_addr'];
		$this->to_addrs_names = $a['to_addrs'];
		$this->cc_addrs_names = $a['cc_addrs'];
		$this->bcc_addrs_names = $a['bcc_addrs'];
	}

    /**
     * @see SugarBean::populateFromRow
     */
    public function populateFromRow($row, $convert = false)
    {
        $row = parent::populateFromRow($row, $convert);

        $this->loadAdditionalEmailData();

        return $row;
    }

	function delete($id='') {
		if(empty($id))
			$id = $this->id;

		$q  = "UPDATE emails SET deleted = 1 WHERE id = '{$id}'";
		$qt = "UPDATE emails_text SET deleted = 1 WHERE email_id = '{$id}'";
		$qf = "UPDATE folders_rel SET deleted = 1 WHERE polymorphic_id = '{$id}' AND polymorphic_module = 'Emails'";
      	$r  = $this->db->query($q);
		$rt = $this->db->query($qt);
		$rf = $this->db->query($qf);
	}

	/**
	 * creates the standard "Forward" info at the top of the forwarded message
	 * @return string
	 */
	function getForwardHeader() {
		global $mod_strings;
		global $current_user;

		//$from = str_replace(array("&gt;","&lt;"), array(")","("), $this->from_name);
		$from = to_html($this->from_name);
		$subject = to_html($this->name);
		$ret  = "<br /><br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_FROM']} {$from}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_DATE_SENT']} {$this->date_sent}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_TO']} {$this->to_addrs}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_CC']} {$this->cc_addrs}<br />";
		$ret .= $this->replyDelimiter."{$mod_strings['LBL_SUBJECT']} {$subject}<br />";
		$ret .= $this->replyDelimiter."<br />";

		return $ret;
		//return from_html($ret);
	}

    /**
     * retrieves Notes that belong to this Email and stuffs them into the "attachments" attribute
     */
    function getNotes($id, $duplicate=false) {
        if(!class_exists('Note')) {

        }

        $exRemoved = array();
		if(isset($_REQUEST['removeAttachment'])) {
			$exRemoved = explode('::', $_REQUEST['removeAttachment']);
		}

        $noteArray = array();
        $q = "SELECT id FROM notes WHERE parent_id = '".$id."'";
        $r = $this->db->query($q);

        while($a = $this->db->fetchByAssoc($r)) {
        	if(!in_array($a['id'], $exRemoved)) {
	            $note = BeanFactory::getBean('Notes', $a['id']);

	            // duplicate actual file when creating forwards
		        if($duplicate) {
		        	if(!class_exists('UploadFile')) {
		        		require_once('include/upload_file.php');
		        	}

                    /*--- ????
		        	// save a brand new Note
		        	$noteDupe->id = create_guid();
		        	$noteDupe->new_with_id = true;
					$noteDupe->parent_id = $this->id;
					$noteDupe->parent_type = $this->module_dir;
                    ---- */

                    $note->id = create_guid();

					$noteFile = new UploadFile();
					$noteFile->duplicate_file($a['id'], $note->id, $note->filename);

					$note->save();
		        }
		        // add Note to attachments array
	            $this->attachments[] = $note;
        	}
        }
    }

	/**
	 * creates the standard "Reply" info at the top of the forwarded message
	 * @return string
	 */
	function getReplyHeader() {
		global $mod_strings;
		global $current_user;

		$from = str_replace(array("&gt;","&lt;", ">","<"), array(")","(",")","("), $this->from_name);
		$ret  = "<br>{$mod_strings['LBL_REPLY_HEADER_1']} {$this->date_start}, {$this->time_start}, {$from} {$mod_strings['LBL_REPLY_HEADER_2']}";

		return from_html($ret);
	}

	/**
	 * Quotes plain-text email text
	 * @param string $text
	 * @return string
	 */
	function quotePlainTextEmail($text) {
		$quoted = "\n";

		// plain-text
		$desc = nl2br(trim($text));
		$exDesc = explode('<br />', $desc);

		foreach($exDesc as $k => $line) {
			$quoted .= '> '.trim($line)."\r";
		}

		return $quoted;
	}

	/**
	 * "quotes" (i.e., "> my text yadda" the HTML part of an email
	 * @param string $text HTML text to quote
	 * @return string
	 */
	function quoteHtmlEmail($text) {
		$text = trim(from_html($text));

		if(empty($text)) {
			return '';
		}
		$out = "<div style='border-left:1px solid #00c; padding:5px; margin-left:10px;'>{$text}</div>";

		return $out;
	}

	/**
	 * "quotes" (i.e., "> my text yadda" the HTML part of an email
	 * @param string $text HTML text to quote
	 * @return string
	 */
	function quoteHtmlEmailForNewEmailUI($text) {
		$text = trim($text);

		if(empty($text)) {
			return '';
		}
		$text = str_replace("\n", "<BR/>", $text);
		$out = "<div style='border-left:1px solid #00c; padding:5px; margin-left:10px;'>{$text}</div>";

		return $out;
	}

	/**
	 * Ensures that the user is able to send outbound emails
	 */
	function check_email_settings() {
		global $current_user;

		$mail_fromaddress = $current_user->emailAddress->getPrimaryAddress($current_user);
		$replyToName = $current_user->getPreference('mail_fromname');
		$mail_fromname = (!empty($replyToName)) ? $current_user->getPreference('mail_fromname') : $current_user->full_name;

		if(empty($mail_fromaddress)) {
			return false;
		}
		if(empty($mail_fromname)) {
	  		return false;
		}

    	$send_type = $current_user->getPreference('mail_sendtype') ;
		if (!empty($send_type) && $send_type == "SMTP") {
			$mail_smtpserver = $current_user->getPreference('mail_smtpserver');
			$mail_smtpport = $current_user->getPreference('mail_smtpport');
			$mail_smtpauth_req = $current_user->getPreference('mail_smtpauth_req');
			$mail_smtpuser = $current_user->getPreference('mail_smtpuser');
			$mail_smtppass = $current_user->getPreference('mail_smtppass');
			if (empty($mail_smtpserver) ||
				empty($mail_smtpport) ||
                (!empty($mail_smtpauth_req) && ( empty($mail_smtpuser) || empty($mail_smtppass)))
			) {
				return false;
			}
		}
		return true;
	}

	/**
	 * outputs JS to set fields in the MassUpdate form in the "My Inbox" view
	 */
	function js_set_archived() {
		global $mod_strings;
		$script = '
		<script type="text/javascript" language="JavaScript"><!-- Begin
			function setArchived() {
				var form = document.getElementById("MassUpdate");
				var status = document.getElementById("mass_status");
				var ok = false;

				for(var i=0; i < form.elements.length; i++) {
					if(form.elements[i].name == "mass[]") {
						if(form.elements[i].checked == true) {
							ok = true;
						}
					}
				}

				if(ok == true) {
					var user = document.getElementById("mass_assigned_user_name");
					var team = document.getElementById("team");

					user.value = "";
					for(var j=0; j<status.length; j++) {
						if(status.options[j].value == "archived") {
							status.options[j].selected = true;
							status.selectedIndex = j; // for IE
						}
					}

					form.submit();
				} else {
					alert("'.$mod_strings['ERR_ARCHIVE_EMAIL'].'");
				}

			}
		//  End --></script>';
		return $script;
	}

	/**
	 * replaces the javascript in utils.php - more specialized
	 */
	function u_get_clear_form_js($type='', $group='', $assigned_user_id='') {
		$uType				= '';
		$uGroup				= '';
		$uAssigned_user_id	= '';

		if(!empty($type)) { $uType = '&type='.$type; }
		if(!empty($group)) { $uGroup = '&group='.$group; }
		if(!empty($assigned_user_id)) { $uAssigned_user_id = '&assigned_user_id='.$assigned_user_id; }

		$the_script = '
		<script type="text/javascript" language="JavaScript"><!-- Begin
			function clear_form(form) {
				var newLoc = "index.php?action=" + form.action.value + "&module=" + form.module.value + "&query=true&clear_query=true'.$uType.$uGroup.$uAssigned_user_id.'";
				if(typeof(form.advanced) != "undefined"){
					newLoc += "&advanced=" + form.advanced.value;
				}
				document.location.href= newLoc;
			}
		//  End --></script>';
		return $the_script;
	}

	function pickOneButton() {
		global $theme;
		global $mod_strings;
		$out = '<div><input	title="'.$mod_strings['LBL_BUTTON_GRAB_TITLE'].'"
						class="button"
						type="button" name="button"
						onClick="window.location=\'index.php?module=Emails&action=Grab\';"
						style="margin-bottom:2px"
						value="  '.$mod_strings['LBL_BUTTON_GRAB'].'  "></div>';
		return $out;
	}

	/**
	 * Determines what Editor (HTML or Plain-text) the current_user uses;
	 * @return string Editor type
	 */
	function getUserEditorPreference() {
		global $sugar_config;
		global $current_user;

		$editor = '';

		if(!isset($sugar_config['email_default_editor'])) {
			$sugar_config = $current_user->setDefaultsInConfig();
		}

		$userEditor = $current_user->getPreference('email_editor_option');
		$systemEditor = $sugar_config['email_default_editor'];

		if($userEditor != '') {
			$editor = $userEditor;
		} else {
			$editor = $systemEditor;
		}

		return $editor;
	}

	/**
	 * takes the mess we pass from EditView and tries to create some kind of order
	 * @param array addrs
	 * @param array addrs_ids (from contacts)
	 * @param array addrs_names (from contacts);
	 * @param array addrs_emails (from contacts);
	 * @return array Parsed assoc array to feed to PHPMailer
	 */
	function parse_addrs($addrs, $addrs_ids, $addrs_names, $addrs_emails) {
		// cn: bug 9406 - enable commas to separate email addresses
		$addrs = str_replace(",", ";", $addrs);

		$ltgt = array('&lt;','&gt;');
		$gtlt = array('<','>');

		$return				= array();
		$addrs				= str_replace($ltgt, '', $addrs);
		$addrs_arr			= explode(";",$addrs);
		$addrs_arr			= $this->remove_empty_fields($addrs_arr);
		$addrs_ids_arr		= explode(";",$addrs_ids);
		$addrs_ids_arr		= $this->remove_empty_fields($addrs_ids_arr);
		$addrs_emails_arr	= explode(";",$addrs_emails);
		$addrs_emails_arr	= $this->remove_empty_fields($addrs_emails_arr);
		$addrs_names_arr	= explode(";",$addrs_names);
		$addrs_names_arr	= $this->remove_empty_fields($addrs_names_arr);

		///////////////////////////////////////////////////////////////////////
		////	HANDLE EMAILS HAND-WRITTEN
		$contactRecipients = array();
		$knownEmails = array();

		foreach($addrs_arr as $i => $v) {
			if(trim($v) == "")
				continue; // skip any "blanks" - will always have 1

			$recipient = array();

			//// get the email to see if we're dealing with a dupe
			//// what crappy coding
			preg_match("/[A-Z0-9._%-\']+@[A-Z0-9.-]+\.[A-Z]{2,}/i",$v, $match);


			if(!empty($match[0]) && !in_array(trim($match[0]), $knownEmails)) {
				$knownEmails[] = $match[0];
				$recipient['email'] = $match[0];

				//// handle the Display name
				$display = trim(str_replace($match[0], '', $v));

				//// only trigger a "displayName" <email@address> when necessary
				if(isset($addrs_names_arr[$i])){
						$recipient['display'] = $addrs_names_arr[$i];
				}
				else if(!empty($display)) {
					$recipient['display'] = $display;
				}
				if(isset($addrs_ids_arr[$i]) && $addrs_emails_arr[$i] == $match[0]){
					$recipient['contact_id'] = $addrs_ids_arr[$i];
				}
				$return[] = $recipient;
			}
		}

		return $return;
	}

	function remove_empty_fields(&$arr) {
		$newarr = array();

		foreach($arr as $field) {
			$field = trim($field);
			if(empty($field)) {
				continue;
			}
			array_push($newarr,$field);
		}
		return $newarr;
	}

    /**
     * Used to find a usable Emails relationship link
     * @param SugarBean $bean
     *
     * @return bool|string Name of an Emails relationship link or false
     */
    protected function findEmailsLink(SugarBean $bean)
    {
        foreach($bean->field_defs as $field => $def) {
            if (!empty($def['type']) && $def['type'] == 'link' && !empty($def['module']) && $def['module'] == 'Emails') {
                return $field;
            }
        }
        return false;
    }

	/**
	 * handles attachments of various kinds when sending email
	 */
	function handleAttachments() {

		global $mod_strings;

        ///////////////////////////////////////////////////////////////////////////
        ////    ATTACHMENTS FROM DRAFTS
        if(($this->type == 'out' || $this->type == 'draft') && $this->status == 'draft' && isset($_REQUEST['record'])) {
            $this->getNotes($_REQUEST['record']); // cn: get notes from OLD email for use in new email
        }
        ////    END ATTACHMENTS FROM DRAFTS
        ///////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////
        ////    ATTACHMENTS FROM FORWARDS
        // Bug 8034 Jenny - Need the check for type 'draft' here to handle cases where we want to save
        // forwarded messages as drafts.  We still need to save the original message's attachments.
        if(($this->type == 'out' || $this->type == 'draft') &&
        	isset($_REQUEST['origType']) && $_REQUEST['origType'] == 'forward' &&
        	isset($_REQUEST['return_id']) && !empty($_REQUEST['return_id'])
        ) {
            $this->getNotes($_REQUEST['return_id'], true);
        }

        // cn: bug 8034 - attachments from forward/replies lost when saving in draft
        if(isset($_REQUEST['prior_attachments']) && !empty($_REQUEST['prior_attachments']) && $this->new_with_id == true) {
        	$exIds = explode(",", $_REQUEST['prior_attachments']);
        	if(!isset($_REQUEST['template_attachment'])) {
        		$_REQUEST['template_attachment'] = array();
        	}
        	$_REQUEST['template_attachment'] = array_merge($_REQUEST['template_attachment'], $exIds);
        }
        ////    END ATTACHMENTS FROM FORWARDS
        ///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	ATTACHMENTS FROM TEMPLATES
		// to preserve individual email integrity, we must dupe Notes and associated files
		// for each outbound email - good for integrity, bad for filespace
		if(isset($_REQUEST['template_attachment']) && !empty($_REQUEST['template_attachment'])) {
			$removeArr = array();
			$noteArray = array();

			if(isset($_REQUEST['temp_remove_attachment']) && !empty($_REQUEST['temp_remove_attachment'])) {
				$removeArr = $_REQUEST['temp_remove_attachment'];
			}


			foreach($_REQUEST['template_attachment'] as $noteId) {
				if(in_array($noteId, $removeArr)) {
					continue;
				}
				$noteTemplate = BeanFactory::getBean('Notes', $noteId);
				$noteTemplate->id = create_guid();
				$noteTemplate->new_with_id = true; // duplicating the note with files
				$noteTemplate->parent_id = $this->id;
				$noteTemplate->parent_type = $this->module_dir;
				$noteTemplate->date_entered = '';
				$noteTemplate->save();
				//BEGIN SUGARCRM flav=pro ONLY
				$noteTemplate->team_id = $this->team_id;
				//END SUGARCRM flav=pro ONLY

				$noteFile = new UploadFile();
				$noteFile->duplicate_file($noteId, $noteTemplate->id, $noteTemplate->filename);
				$noteArray[] = $noteTemplate;
			}
			$this->attachments = array_merge($this->attachments, $noteArray);
		}
		////	END ATTACHMENTS FROM TEMPLATES
		///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	ADDING NEW ATTACHMENTS
		$max_files_upload = 10;
        // Jenny - Bug 8211 Since attachments for drafts have already been processed,
        // we don't need to re-process them.
        if($this->status != "draft") {
    		$notes_list = array();
    		if(!empty($this->id) && !$this->new_with_id) {
    			$note = BeanFactory::getBean('Notes');
    			$where = "notes.parent_id='{$this->id}'";
    			$notes_list = $note->get_full_list("", $where, true);
    		}
    		$this->attachments = array_merge($this->attachments, $notes_list);
        }
		// cn: Bug 5995 - rudimentary error checking
		$filesError = array(
			0 => 'UPLOAD_ERR_OK - There is no error, the file uploaded with success.',
			1 => 'UPLOAD_ERR_INI_SIZE - The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			2 => 'UPLOAD_ERR_FORM_SIZE - The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			3 => 'UPLOAD_ERR_PARTIAL - The uploaded file was only partially uploaded.',
			4 => 'UPLOAD_ERR_NO_FILE - No file was uploaded.',
			5 => 'UNKNOWN ERROR',
			6 => 'UPLOAD_ERR_NO_TMP_DIR - Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.',
			7 => 'UPLOAD_ERR_CANT_WRITE - Failed to write file to disk. Introduced in PHP 5.1.0.',
		);

		for($i = 0; $i < $max_files_upload; $i++) {
			// cn: Bug 5995 - rudimentary error checking
			if (!isset($_FILES["email_attachment{$i}"])) {
				$GLOBALS['log']->debug("Email Attachment {$i} does not exist.");
				continue;
			}
			if($_FILES['email_attachment'.$i]['error'] != 0 && $_FILES['email_attachment'.$i]['error'] != 4) {
				$GLOBALS['log']->debug('Email Attachment could not be attach due to error: '.$filesError[$_FILES['email_attachment'.$i]['error']]);
				continue;
			}

			$note = BeanFactory::getBean('Notes');
			$note->parent_id = $this->id;
			$note->parent_type = $this->module_dir;
			$upload_file = new UploadFile('email_attachment'.$i);

			if(empty($upload_file)) {
				continue;
			}

			if(isset($_FILES['email_attachment'.$i]) && $upload_file->confirm_upload()) {
				$note->filename = $upload_file->get_stored_file_name();
				$note->file = $upload_file;
				$note->name = $mod_strings['LBL_EMAIL_ATTACHMENT'].': '.$note->file->original_file_name;
				//BEGIN SUGARCRM flav=pro ONLY
				$note->team_id = $this->team_id;
				//END SUGARCRM flav=pro ONLY

				$this->attachments[] = $note;
			}
		}

		$this->saved_attachments = array();
		foreach($this->attachments as $note) {
			if(!empty($note->id)) {
				array_push($this->saved_attachments, $note);
				continue;
			}
			$note->parent_id = $this->id;
			$note->parent_type = 'Emails';
			$note->file_mime_type = $note->file->mime_type;
			$note_id = $note->save();

			$this->saved_attachments[] = $note;

			$note->id = $note_id;
			$note->file->final_move($note->id);
		}
		////	END NEW ATTACHMENTS
		///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	ATTACHMENTS FROM DOCUMENTS
		for($i=0; $i<10; $i++) {
			if(isset($_REQUEST['documentId'.$i]) && !empty($_REQUEST['documentId'.$i])) {
				$doc = BeanFactory::getBean('Documents');
				$docRev = BeanFactory::getBean('DocumentRevisions');
				$docNote = BeanFactory::getBean('Notes');
				$noteFile = new UploadFile();

				$doc->retrieve($_REQUEST['documentId'.$i]);
				$docRev->retrieve($doc->document_revision_id);

				$this->saved_attachments[] = $docRev;

				// cn: bug 9723 - Emails with documents send GUID instead of Doc name
				$docNote->name = $docRev->getDocumentRevisionNameForDisplay();
				$docNote->filename = $docRev->filename;
				$docNote->description = $doc->description;
				$docNote->parent_id = $this->id;
				$docNote->parent_type = 'Emails';
				$docNote->file_mime_type = $docRev->file_mime_type;
				$docId = $docNote = $docNote->save();

				$noteFile->duplicate_file($docRev->id, $docId, $docRev->filename);
			}
		}

		////	END ATTACHMENTS FROM DOCUMENTS
		///////////////////////////////////////////////////////////////////////////

		///////////////////////////////////////////////////////////////////////////
		////	REMOVE ATTACHMENTS
        if(isset($_REQUEST['remove_attachment']) && !empty($_REQUEST['remove_attachment'])) {
            foreach($_REQUEST['remove_attachment'] as $noteId) {
                $q = 'UPDATE notes SET deleted = 1 WHERE id = \''.$noteId.'\'';
                $this->db->query($q);
            }
        }

        //this will remove attachments that have been selected to be removed from drafts.
        if(isset($_REQUEST['removeAttachment']) && !empty($_REQUEST['removeAttachment'])) {
            $exRemoved = explode('::', $_REQUEST['removeAttachment']);
            foreach($exRemoved as $noteId) {
                $q = 'UPDATE notes SET deleted = 1 WHERE id = \''.$noteId.'\'';
                $this->db->query($q);
            }
        }
		////	END REMOVE ATTACHMENTS
		///////////////////////////////////////////////////////////////////////////
	}


	/**
	 * Determines if an email body (HTML or Plain) has a User signature already in the content
	 * @param array Array of signatures
	 * @return bool
	 */
	function hasSignatureInBody($sig) {
		// strpos can't handle line breaks - normalize
		$html = $this->removeAllNewlines($this->description_html);
		$htmlSig = $this->removeAllNewlines($sig['signature_html']);
		$plain = $this->removeAllNewlines($this->description);
		$plainSig = $this->removeAllNewlines($sig['signature']);

		// cn: bug 11621 - empty sig triggers notice error
		if(!empty($htmlSig) && false !== strpos($html, $htmlSig)) {
			return true;
		} elseif(!empty($plainSig) && false !== strpos($plain, $plainSig)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * internal helper
	 * @param string String to be normalized
	 * @return string
	 */
	function removeAllNewlines($str) {
		$bad = array("\r\n", "\n\r", "\n", "\r");
		$good = array('', '', '', '');

		return str_replace($bad, $good, strip_tags(br2nl(from_html($str))));
	}



	/**
	 * Set navigation anchors to aid DetailView record navigation (VCR buttons)
	 * @param string uri The URI from the referring page (always ListView)
	 * @return array start Array of the URI broken down with a special "current_view" for My Inbox Navs
	 */
	function getStartPage($uri) {
		if(strpos($uri, '&')) { // "&" to ensure that we can explode the GET vars - else we're gonna trigger a Notice error
			$serial = substr($uri, (strpos($uri, '?')+1), strlen($uri));
			$exUri = explode('&', $serial);
			$start = array('module' => '', 'action' => '', 'group' => '', 'record' => '', 'type' => '');

			foreach($exUri as $k => $pair) {
				$exPair = explode('=', $pair);
				$start[$exPair[0]] = $exPair[1];
			}

			// specific views for current_user
			if(isset($start['assigned_user_id'])) {
				$start['current_view'] = "{$start['action']}&module={$start['module']}&assigned_user_id={$start['assigned_user_id']}&type={$start['type']}";
			}

			return $start;
		} else {
			return array();
		}
	}


    /**
     * Sends Email
     * @return bool True on success
     */
    function send() {
        global $mod_strings,
               $app_strings,
               $current_user,
               $sugar_config;

        try {
            $mailConfig = OutboundEmailConfigurationPeer::getSystemMailConfiguration($current_user);
            $mailerFactoryClass = $this->MockMailerFactoryClass;
            $mailer = $mailerFactoryClass::getMailer($mailConfig);

            if (is_array($this->to_addrs_arr)) {
                foreach ($this->to_addrs_arr as $addr_arr) {
                    try {
                        $mailer->addRecipientsTo(new EmailIdentity($addr_arr['email'], $addr_arr['display']));
                    } catch (MailerException $me) {
                        // eat the exception
                    }
                }
            }
            if (is_array($this->cc_addrs_arr)) {
                foreach ($this->cc_addrs_arr as $addr_arr) {
                    try {
                        $mailer->addRecipientsCc(new EmailIdentity($addr_arr['email'], $addr_arr['display']));
                    } catch (MailerException $me) {
                        // eat the exception
                    }
                }
            }
            if (is_array($this->bcc_addrs_arr)) {
                foreach ($this->bcc_addrs_arr as $addr_arr) {
                    try {
                        $mailer->addRecipientsBcc(new EmailIdentity($addr_arr['email'], $addr_arr['display']));
                    } catch (MailerException $me) {
                        // eat the exception
                    }
                }
            }

            // SENDER Info
            if (empty($this->from_addr)) {
                $this->from_addr = $current_user->getPreference('mail_fromaddress');
            }

            if (empty($this->from_name)) {
                $this->from_name = $current_user->getPreference('mail_fromname');
            }

            // REPLY-TO Info
            if (empty($this->reply_to_addr)) {
                $this->reply_to_addr = $this->from_addr;
                $this->reply_to_name = $this->from_name;
            }

            $mailer->setHeader(EmailHeaders::From, new EmailIdentity($this->from_addr, $this->from_name));
            $mailer->setHeader(EmailHeaders::ReplyTo, new EmailIdentity($this->reply_to_addr, $this->reply_to_name));
            $mailer->setSubject($this->name);

            ///////////////////////////////////////////////////////////////////////
            ////	ATTACHMENTS
            if (is_array($this->saved_attachments)) {
                foreach ($this->saved_attachments as $note) {
                    $mime_type = 'text/plain';
                    if ($note->object_name == 'Note') {
                        if (!empty($note->file->temp_file_location) && is_file($note->file->temp_file_location)) { // brandy-new file upload/attachment
                            $file_location = "upload://$note->id";
                            $filename = $note->file->original_file_name;
                            $mime_type = $note->file->mime_type;
                        } else { // attachment coming from template/forward
                            $file_location = "upload://{$note->id}";
                            // cn: bug 9723 - documents from EmailTemplates sent with Doc Name, not file name.
                            $filename = !empty($note->filename) ? $note->filename : $note->name;
                            $mime_type = $note->file_mime_type;
                        }
                    } elseif ($note->object_name == 'DocumentRevision') { // from Documents
                        $filePathName = $note->id;
                        // cn: bug 9723 - Emails with documents send GUID instead of Doc name
                        $filename = $note->getDocumentRevisionNameForDisplay();
                        $file_location = "upload://$note->id";
                        $mime_type = $note->file_mime_type;
                    }

                    // strip out the "Email attachment label if exists
                    $filename = str_replace($mod_strings['LBL_EMAIL_ATTACHMENT'].': ', '', $filename);
                    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                    //is attachment in our list of bad files extensions?  If so, append .txt to file location
                    //check to see if this is a file with extension located in "badext"
                    foreach ($sugar_config['upload_badext'] as $badExt) {
                        if (strtolower($file_ext) == strtolower($badExt)) {
                            //if found, then append with .txt to filename and break out of lookup
                            //this will make sure that the file goes out with right extension, but is stored
                            //as a text in db.
                            $file_location = $file_location . ".txt";
                            break; // no need to look for more
                        }
                    }

                    $attachment = null;

                    if ($note->embed_flag == true) {
                        $cid = $filename;
                        $attachment = AttachmentPeer::embeddedImageFromSugarBean($note, $cid);
                    } else {
                        $attachment = AttachmentPeer::attachmentFromSugarBean($note);
                    }

                    $mailer->addAttachment($attachment);
                }
            }
            ////	END ATTACHMENTS
            ///////////////////////////////////////////////////////////////////////

            if (isset($_REQUEST['description_html'])) {
                $this->description_html = $_REQUEST['description_html'];
            }

            $htmlBody = $this->description_html;
            $textBody = $this->description;

            //------------------- HANDLEBODY() ---------------------------------------------
            if ((isset($_REQUEST['setEditor']) /* from Email EditView navigation */
                 && $_REQUEST['setEditor'] == 1
                 && trim($this->description_html) != '')
                && $current_user->getPreference('email_editor_option', 'global') !== 'plain' //user preference is not set to plain text
            ) {
                $htmlBody = $this->decodeDuringSend($htmlBody);
                $textBody = $this->decodeDuringSend($textBody);
            } else {
                $textBody = str_replace("&nbsp;", " ", $textBody);
                $textBody = str_replace("</p>", "</p><br />", $textBody);
                $textBody = strip_tags(br2nl($textBody));
                $textBody = str_replace("&amp;", "&", $textBody);
                $textBody = str_replace("&#39;", "'", $textBody);
                $textBody = $this->decodeDuringSend($textBody);
            }

            $mailer->setHtmlBody($htmlBody);
            $mailer->setTextBody($textBody);

            $mailer->send();

            ///////////////////////////////////////////////////////////////////
            ////	INBOUND EMAIL HANDLING
            // mark replied
            if(!empty($_REQUEST['inbound_email_id'])) {
                $ieMail = new Email();
                $ieMail->retrieve($_REQUEST['inbound_email_id']);
                $ieMail->status = 'replied';
                $ieMail->save();
            }

            return true;
        }
        catch (MailerException $me) {
            $GLOBALS["log"]->error($me->getLogMessage());
        }
        catch (Exception $e) {
            $GLOBALS['log']->error($app_strings['LBL_EMAIL_ERROR_PREPEND'] . $e->getMessage());
        }

        return false;
    }


    function listviewACLHelper(){
		$array_assign = parent::listviewACLHelper();
		$is_owner = false;
		if(!empty($this->parent_name)){

			if(!empty($this->parent_name_owner)){
				global $current_user;
				$is_owner = $current_user->id == $this->parent_name_owner;
			}
		}
		if(!ACLController::moduleSupportsACL($this->parent_type) || ACLController::checkAccess($this->parent_type, 'view', $is_owner)){
			$array_assign['PARENT'] = 'a';
		} else {
			$array_assign['PARENT'] = 'span';
		}
		$is_owner = false;
		if(!empty($this->contact_name)) {
			if(!empty($this->contact_name_owner)) {
				global $current_user;
				$is_owner = $current_user->id == $this->contact_name_owner;
			}
		}
		if(ACLController::checkAccess('Contacts', 'view', $is_owner)) {
			$array_assign['CONTACT'] = 'a';
		} else {
			$array_assign['CONTACT'] = 'span';
		}

		return $array_assign;
	}

	function getSystemDefaultEmail() {
		$email = array();

		$r1 = $this->db->query('SELECT config.value FROM config WHERE name=\'fromaddress\'');
		$r2 = $this->db->query('SELECT config.value FROM config WHERE name=\'fromname\'');
		$a1 = $this->db->fetchByAssoc($r1);
		$a2 = $this->db->fetchByAssoc($r2);

		$email['email'] = $a1['value'];
		$email['name']  = $a2['value'];

		return $email;
	}


    function create_new_list_query($order_by, $where,$filter=array(),$params=array(), $show_deleted = 0,$join_type='', $return_array = false,$parentbean=null, $singleSelect = false) {

		if ($return_array) {
			return parent::create_new_list_query($order_by, $where,$filter,$params, $show_deleted,$join_type, $return_array,$parentbean, $singleSelect);
		}
        $custom_join = $this->getCustomJoin();

		$query = "SELECT ".$this->table_name.".*, users.user_name as assigned_user_name\n";

        $query .= $custom_join['select'];
    	$query .= " FROM emails\n";
    	if ($where != "" && (strpos($where, "contacts.first_name") > 0))  {
			$query .= " LEFT JOIN emails_beans ON emails.id = emails_beans.email_id\n";
    	}
		//BEGIN SUGARCRM flav=pro ONLY
		// We need to confirm that the user is a member of the team of the item.
		$this->addVisibilityFrom($query, array('where_condition' => true));
    	$query .= " LEFT JOIN teams ON emails.team_id=teams.id";
    	//END SUGARCRM flav=pro ONLY

    	$query .= " LEFT JOIN users ON emails.assigned_user_id=users.id \n";
    	if ($where != "" && (strpos($where, "contacts.first_name") > 0))  {

        $query .= " JOIN contacts ON contacts.id= emails_beans.bean_id AND emails_beans.bean_module='Contacts' and contacts.deleted=0 \n";
    	}

        $query .= $custom_join['join'];

		if($show_deleted == 0) {
			$where_auto = " emails.deleted=0 \n";
		}else if($show_deleted == 1){
			$where_auto = " emails.deleted=1 \n";
		}

		$this->addVisibilityWhere($where_auto, array('where_condition' => true));

        if($where != "")
			$query .= "WHERE $where AND ".$where_auto;
		else
			$query .= "WHERE ".$where_auto;

		if($order_by != "")
			$query .= " ORDER BY $order_by";
		else
			$query .= " ORDER BY date_sent DESC";

		return $query;
    } // fn


	function fill_in_additional_list_fields() {
		global $timedate, $mod_strings;
		$this->fill_in_additional_detail_fields();

		$this->link_action = 'DetailView';
		///////////////////////////////////////////////////////////////////////
		//populate attachment_image, used to display attachment icon.
		$query =  "select 1 from notes where notes.parent_id = '$this->id' and notes.deleted = 0";
		$result =$this->db->query($query,true," Error filling in additional list fields: ");

		$row = $this->db->fetchByAssoc($result);
        $this->attachment_image = ($row !=null) ? SugarThemeRegistry::current()->getImage('attachment',"","","") : "";

		if ($row !=null) {
			$this->attachment_image = SugarThemeRegistry::current()->getImage('attachment',"","","",'.gif',translate('LBL_ATTACHMENT', 'Emails'));
		}

		///////////////////////////////////////////////////////////////////////
		if(empty($this->contact_id) && !empty($this->parent_id) && !empty($this->parent_type) && $this->parent_type === 'Contacts' && !empty($this->parent_name) ){
			$this->contact_id = $this->parent_id;
			$this->contact_name = $this->parent_name;
		}
	}

	function fill_in_additional_detail_fields()
	{
		global $app_list_strings,$mod_strings;

        $mod_strings = return_module_language($GLOBALS['current_language'], 'Emails');

		$query  = "SELECT contacts.first_name, contacts.last_name, contacts.phone_work, contacts.id, contacts.assigned_user_id contact_name_owner, 'Contacts' contact_name_mod FROM contacts, emails_beans
		           WHERE emails_beans.email_id='$this->id' AND emails_beans.bean_id=contacts.id AND emails_beans.bean_module = 'Contacts' AND emails_beans.deleted=0 AND contacts.deleted=0";

			if(!empty($this->parent_id)){
				$query .= " AND contacts.id= '".$this->parent_id."' ";
			}else if(!empty($_REQUEST['record'])){
				$query .= " AND contacts.id= '".$_REQUEST['record']."' ";
			}
			$result =$this->db->query($query,true," Error filling in additional detail fields: ");

			// Get the id and the name.
			$row = $this->db->fetchByAssoc($result);
			if($row != null)
			{

				$contact = BeanFactory::getBean('Contacts', $row['id']);
				$this->contact_name = $contact->full_name;
				$this->contact_phone = $row['phone_work'];
				$this->contact_id = $row['id'];
				$this->contact_email = $contact->emailAddress->getPrimaryAddress($contact);
				$this->contact_name_owner = $row['contact_name_owner'];
				$this->contact_name_mod = $row['contact_name_mod'];
				$GLOBALS['log']->debug("Call($this->id): contact_name = $this->contact_name");
				$GLOBALS['log']->debug("Call($this->id): contact_phone = $this->contact_phone");
				$GLOBALS['log']->debug("Call($this->id): contact_id = $this->contact_id");
				$GLOBALS['log']->debug("Call($this->id): contact_email1 = $this->contact_email");
			}
			else {
				$this->contact_name = '';
				$this->contact_phone = '';
				$this->contact_id = '';
				$this->contact_email = '';
				$this->contact_name_owner = '';
				$this->contact_name_mod = '';
				$GLOBALS['log']->debug("Call($this->id): contact_name = $this->contact_name");
				$GLOBALS['log']->debug("Call($this->id): contact_phone = $this->contact_phone");
				$GLOBALS['log']->debug("Call($this->id): contact_id = $this->contact_id");
				$GLOBALS['log']->debug("Call($this->id): contact_email1 = $this->contact_email");
			}
		//}


		$this->link_action = 'DetailView';

		if(!empty($this->type)) {
			if($this->type == 'out' && $this->status == 'send_error') {
				$this->type_name = $mod_strings['LBL_NOT_SENT'];
			} else {
                if (isset($app_list_strings['dom_email_types'][$this->type])) {
                    $this->type_name = $app_list_strings['dom_email_types'][$this->type];
                } else {
                    $this->type_name = $this->type;
                }
			}

			if(($this->type == 'out' && $this->status == 'send_error') || $this->type == 'draft') {
				$this->link_action = 'EditView';
			}
		}

		//todo this  isset( $app_list_strings['dom_email_status'][$this->status]) is hack for 3261.
		if(!empty($this->status) && isset( $app_list_strings['dom_email_status'][$this->status])) {
			$this->status_name = $app_list_strings['dom_email_status'][$this->status];
		}

		if ( empty($this->name ) &&  empty($_REQUEST['record'])) {
			$this->name = $mod_strings['LBL_NO_SUBJECT'];
		}

		parent::fill_in_additional_detail_fields();
	}

	function get_list_view_data() {
		global $app_list_strings;
		global $theme;
		global $current_user;
		global $timedate;
		global $mod_strings;

		$email_fields = $this->get_list_view_array();
		$this->retrieveEmailText();
		$email_fields['FROM_ADDR'] = $this->from_addr_name;
		$mod_strings = return_module_language($GLOBALS['current_language'], 'Emails'); // hard-coding for Home screen ListView

		if($this->status != 'replied') {
			$email_fields['QUICK_REPLY'] = '<a  href="index.php?module=Emails&action=Compose&replyForward=true&reply=reply&record='.$this->id.'&inbound_email_id='.$this->id.'">'.$mod_strings['LNK_QUICK_REPLY'].'</a>';
			$email_fields['STATUS'] = ($email_fields['REPLY_TO_STATUS'] == 1 ? $mod_strings['LBL_REPLIED'] : $email_fields['STATUS']);
		} else {
			$email_fields['QUICK_REPLY'] = $mod_strings['LBL_REPLIED'];
		}
		if(!empty($this->parent_type)) {
			$email_fields['PARENT_MODULE'] = $this->parent_type;
		} else {
			switch($this->intent) {
				case 'support':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Cases&action=EditView&inbound_email_id='.$this->id.'" >' . SugarThemeRegistry::current()->getImage('CreateCases', 'border="0"', null, null, ".gif", $mod_strings['LBL_CREATE_CASES']).$mod_strings['LBL_CREATE_CASE'].'</a>';
				break;

				case 'sales':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Leads&action=EditView&inbound_email_id='.$this->id.'" >'.SugarThemeRegistry::current()->getImage('CreateLeads', 'border="0"', null, null, ".gif", $mod_strings['LBL_CREATE_LEADS']).$mod_strings['LBL_CREATE_LEAD'].'</a>';
				break;

				case 'contact':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Contacts&action=EditView&inbound_email_id='.$this->id.'" >'.SugarThemeRegistry::current()->getImage('CreateContacts', 'border="0"', null, null, ".gif", $mod_strings['LBL_CREATE_CONTACTS']).$mod_strings['LBL_CREATE_CONTACT'].'</a>';
				break;

				case 'bug':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Bugs&action=EditView&inbound_email_id='.$this->id.'" >'.SugarThemeRegistry::current()->getImage('CreateBugs', 'border="0"', null, null, ".gif", $mod_strings['LBL_CREATE_BUGS']).$mod_strings['LBL_CREATE_BUG'].'</a>';
				break;

				case 'task':
					$email_fields['CREATE_RELATED'] = '<a href="index.php?module=Tasks&action=EditView&inbound_email_id='.$this->id.'" >'.SugarThemeRegistry::current()->getImage('CreateTasks', 'border="0"', null, null, ".gif", $mod_strings['LBL_CREATE_TASKS']).$mod_strings['LBL_CREATE_TASK'].'</a>';
				break;

				case 'bounce':
				break;

				case 'pick':
				// break;

				case 'info':
				//break;

				default:
					$email_fields['CREATE_RELATED'] = $this->quickCreateForm();
				break;
			}

		}

		//BUG 17098 - MFH changed $this->from_addr to $this->to_addrs
		$email_fields['CONTACT_NAME']		= empty($this->contact_name) ? '</a>'.$this->trimLongTo($this->to_addrs).'<a>' : $this->contact_name;
		$email_fields['CONTACT_ID']         = empty($this->contact_id) ? '' : $this->contact_id;
        $email_fields['ATTACHMENT_IMAGE']	= empty($this->attachment_image) ? '' : $this->attachment_image;
		$email_fields['LINK_ACTION']		= $this->link_action;

    	if(isset($this->type_name))
	      	$email_fields['TYPE_NAME'] = $this->type_name;

		return $email_fields;
	}

    function quickCreateForm() {
        global $mod_strings, $app_strings, $currentModule, $current_language;

        // Coming from the home page via Dashlets
        if($currentModule != 'Email')
        	$mod_strings = return_module_language($current_language, 'Emails');
        return $mod_strings['LBL_QUICK_CREATE']."&nbsp;<a id='$this->id' onclick='return quick_create_overlib(\"{$this->id}\", \"".SugarThemeRegistry::current()->__toString()."\", this);' href=\"#\" >".SugarThemeRegistry::current()->getImage("advanced_search","border='0' align='absmiddle'", null,null,'.gif',$mod_strings['LBL_QUICK_CREATE'])."</a>";
    }

    /**
     * Searches all imported emails and returns the result set as an array.
     *
     */
    function searchImportedEmails($sort = '', $direction='')
    {
       	require_once('include/TimeDate.php');
		global $timedate;
		global $current_user;
		global $beanList;
		global $sugar_config;
		global $app_strings;

		$emailSettings = $current_user->getPreference('emailSettings', 'Emails');
		// cn: default to a low number until user specifies otherwise
		if(empty($emailSettings['showNumInList']))
			$pageSize = 20;
        else
            $pageSize = $emailSettings['showNumInList'];

        if( isset($_REQUEST['start']) && isset($_REQUEST['limit']) )
	       $page = ceil($_REQUEST['start'] / $_REQUEST['limit']) + 1;
	    else
	       $page = 1;

	     //Determine sort ordering

	     //Sort ordering parameters in the request do not coincide with actual column names
	     //so we need to remap them.
	     $hrSortLocal = array(
            'flagged' => 'type',
            'status'  => 'reply_to_status',
            'from'    => 'emails_text.from_addr',
            'subject' => 'name',
            'date'    => 'date_sent',
            'AssignedTo' => 'assigned_user_id',
            'flagged' => 'flagged'
        );

	     $sort = !empty($_REQUEST['sort']) ? $this->db->getValidDBName($_REQUEST['sort']) : "";
         $direction = !empty($_REQUEST['dir'])  && in_array(strtolower($_REQUEST['dir']), array("asc", "desc")) ? $_REQUEST['dir'] : "";

         $order = ( !empty($sort) && !empty($direction) ) ? " ORDER BY {$hrSortLocal[$sort]} {$direction}" : "";

         //Get our main query.
		$fullQuery = $this->_genereateSearchImportedEmailsQuery();

		//Perform a count query needed for pagination.
		$countQuery = $this->create_list_count_query($fullQuery);

		$count_rs = $this->db->query($countQuery, false, 'Error executing count query for imported emails search');
		$count_row = $this->db->fetchByAssoc($count_rs);
		$total_count = ($count_row != null) ? $count_row['c'] : 0;

        $start = ($page - 1) * $pageSize;

        //Execute the query
		$rs = $this->db->limitQuery($fullQuery . $order, $start, $pageSize);

		$return = array();

		while($a = $this->db->fetchByAssoc($rs)) {
			$temp = array();
			$temp['flagged'] = (is_null($a['flagged']) || $a['flagged'] == '0') ? '' : 1;
			$temp['status'] = (is_null($a['reply_to_status']) || $a['reply_to_status'] == '0') ? '' : 1;
			$temp['subject'] = $a['name'];
			$temp['date']  = $timedate->to_display_date_time($this->db->fromConvert($a['date_sent'], 'datetime'));
			$temp['uid'] = $a['id'];
			$temp['ieId'] = $a['mailbox_id'];
			$temp['site_url'] = $sugar_config['site_url'];
			$temp['seen'] = ($a['status'] == 'unread') ? 0 : 1;
			$temp['type'] = $a['type'];
			$temp['mbox'] = 'sugar::Emails';
			$temp['hasAttach'] =  $this->doesImportedEmailHaveAttachment($a['id']);
			//To and from addresses may be stored in emails_text, if nothing is found, revert to
			//regular email addresses.
			$temp['to_addrs'] = preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $a['to_addrs']);
			$temp['from']	= preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $a['from_addr']);
			if( empty($temp['from']) || empty($temp['to_addrs']) )
			{
    			//Retrieve email addresses seperatly.
    			$tmpEmail = BeanFactory::getBean('Emails');
    			$tmpEmail->id = $a['id'];
    			$tmpEmail->retrieveEmailAddresses();
    			$temp['from'] = $tmpEmail->from_addr;
    			$temp['to_addrs'] = $tmpEmail->to_addrs;
			}

			$return[] = $temp;
		}

		$metadata = array();
		$metadata['totalCount'] = $total_count;
		$metadata['out'] = $return;

		return $metadata;
    }

    /**
     * Determine if an imported email has an attachment by examining the relationship to notes.
     *
     * @param string $id
     * @return boolean
     */
    function doesImportedEmailHaveAttachment($id)
	{
	   $hasAttachment = FALSE;
	   $query = "SELECT id FROM notes where parent_id='$id' AND parent_type='Emails' AND file_mime_type is not null AND deleted=0";
	   $rs = $this->db->limitQuery($query, 0, 1);
	   $row = $this->db->fetchByAssoc($rs);
	   if( !empty($row['id']) )
	       $hasAttachment = TRUE;

	   return (int) $hasAttachment;
	}

    /**
     * Generate the query used for searching imported emails.
     *
     * @return String Query to be executed.
     */
    function _genereateSearchImportedEmailsQuery()
    {
		global $timedate;

        $additionalWhereClause = $this->_generateSearchImportWhereClause();

        $query = array();
        $fullQuery = "";
        $query['select'] = "emails.id , emails.mailbox_id, emails.name, emails.date_sent, emails.status, emails.type, emails.flagged, emails.reply_to_status,
		                      emails_text.from_addr, emails_text.to_addrs  FROM emails ";

        $query['joins'] = " JOIN emails_text on emails.id = emails_text.email_id ";

        //Handle from and to addr joins
        if( !empty($_REQUEST['from_addr']) )
        {
            $from_addr = $this->db->quote(strtolower($_REQUEST['from_addr']));
            $query['joins'] .= "INNER JOIN emails_email_addr_rel er_from ON er_from.email_id = emails.id AND er_from.deleted = 0 INNER JOIN email_addresses ea_from ON ea_from.id = er_from.email_address_id
                                AND er_from.address_type='from' AND emails_text.from_addr LIKE '%" . $from_addr . "%'";
        }

        if( !empty($_REQUEST['to_addrs'])  )
        {
            $to_addrs = $this->db->quote(strtolower($_REQUEST['to_addrs']));
            $query['joins'] .= "INNER JOIN emails_email_addr_rel er_to ON er_to.email_id = emails.id AND er_to.deleted = 0 INNER JOIN email_addresses ea_to ON ea_to.id = er_to.email_address_id
                                    AND er_to.address_type='to' AND ea_to.email_address LIKE '%" . $to_addrs . "%'";
        }

        //BEGIN SUGARCRM flav=pro ONLY
		$this->add_team_security_where_clause($query['joins']);
		//END SUGARCRM flav=pro ONLY
        $query['where'] = " WHERE (emails.type= 'inbound' OR emails.type='archived' OR emails.type='out') AND emails.deleted = 0 ";
		if( !empty($additionalWhereClause) )
    	    $query['where'] .= "AND $additionalWhereClause";

    	//If we are explicitly looking for attachments.  Do not use a distinct query as the to_addr is defined
    	//as a text which equals clob in oracle and the distinct query can not be executed correctly.
    	$addDistinctKeyword = "";
        if( !empty($_REQUEST['attachmentsSearch']) &&  $_REQUEST['attachmentsSearch'] == 1) //1 indicates yes
            $query['where'] .= " AND EXISTS ( SELECT id FROM notes n WHERE n.parent_id = emails.id AND n.deleted = 0 AND n.filename is not null )";
        else if( !empty($_REQUEST['attachmentsSearch']) &&  $_REQUEST['attachmentsSearch'] == 2 )
             $query['where'] .= " AND NOT EXISTS ( SELECT id FROM notes n WHERE n.parent_id = emails.id AND n.deleted = 0 AND n.filename is not null )";

        $fullQuery = "SELECT " . $query['select'] . " " . $query['joins'] . " " . $query['where'];

        $GLOBALS['log']->debug("---- Email Search - FullQuery --------------------------------");
        $GLOBALS['log']->debug("FullQuery: ({$fullQuery})");
        $GLOBALS['log']->debug("--------------------------------------------------------------");

        return $fullQuery;
    }
        /**
     * Generate the where clause for searching imported emails.
     *
     */
    function _generateSearchImportWhereClause()
    {
        global $timedate;

        //The clear button was removed so if a user removes the asisgned user name, do not process the id.
        if( empty($_REQUEST['assigned_user_name']) && !empty($_REQUEST['assigned_user_id'])  )
            unset($_REQUEST['assigned_user_id']);

        $availableSearchParam = array('name' => array('table_name' =>'emails'),
                                      'data_parent_id_search' => array('table_name' =>'emails','db_key' => 'parent_id','opp' => '='),
                                      'assigned_user_id' => array('table_name' => 'emails', 'opp' => '=') );

		$additionalWhereClause = array();
		foreach ($availableSearchParam as $key => $properties)
		{
		      if( !empty($_REQUEST[$key]) )
		      {
		          $db_key =  isset($properties['db_key']) ? $properties['db_key'] : $key;
                  $searchValue = $this->db->quote($_REQUEST[$key]);

		          $opp = isset($properties['opp']) ? $properties['opp'] : 'like';
		          if($opp == 'like')
		              $searchValue = "%" . $searchValue . "%";

		          $additionalWhereClause[] = "{$properties['table_name']}.$db_key $opp '$searchValue' ";
		      }
        }



        $isDateFromSearchSet = !empty($_REQUEST['searchDateFrom']);
        $isdateToSearchSet = !empty($_REQUEST['searchDateTo']);
        $bothDateRangesSet = $isDateFromSearchSet & $isdateToSearchSet;

        //Handle date from and to separately
        $dbFormatDateFrom = '';
        $dbFormatDateTo = '';
        if($bothDateRangesSet)
        {
            $dbFormatDateFrom = $timedate->to_db_date($_REQUEST['searchDateFrom'], false) . " 00:00:00";
            $dbFormatDateFrom = $this->toDatabaseSearchDateTime($dbFormatDateFrom);
            $dbFormatDateFrom = $GLOBALS['db']->convert($GLOBALS['db']->quoted($dbFormatDateFrom), 'datetime');

            $dbFormatDateTo = $timedate->to_db_date($_REQUEST['searchDateTo'], false) . " 23:59:59";
            $dbFormatDateTo = $this->toDatabaseSearchDateTime($dbFormatDateTo);
            $dbFormatDateTo = $GLOBALS['db']->convert($GLOBALS['db']->quoted($dbFormatDateTo), 'datetime');

            $additionalWhereClause[] = "( emails.date_sent >= $dbFormatDateFrom AND emails.date_sent <= $dbFormatDateTo )";
        }
        elseif ($isdateToSearchSet)
        {
            $dbFormatDateTo = $timedate->to_db_date($_REQUEST['searchDateTo'], false) . " 23:59:59";
            $dbFormatDateTo = $this->toDatabaseSearchDateTime($dbFormatDateTo);
            $additionalWhereClause[] = "emails.date_sent <= " . $GLOBALS['db']->convert($GLOBALS['db']->quoted($dbFormatDateTo), 'datetime');
        }
        elseif ($isDateFromSearchSet)
        {
            $dbFormatDateFrom = $timedate->to_db_date($_REQUEST['searchDateFrom'], false) . " 00:00:00";
            $dbFormatDateFrom = $this->toDatabaseSearchDateTime($dbFormatDateFrom);
            $additionalWhereClause[] = "emails.date_sent >= " . $GLOBALS['db']->convert($GLOBALS['db']->quoted($dbFormatDateFrom), 'datetime');
        }

        $GLOBALS['log']->debug("------ EMAIL SEARCH DATETIME Values ---------------------------------------------");
        $GLOBALS['log']->debug("dbFormatDateFrom: {$dbFormatDateFrom}");
        $GLOBALS['log']->debug("dbFormatDateTo: {$dbFormatDateTo}");
        $GLOBALS['log']->debug("$additionalWhereClause: " . $additionalWhereClause[count($additionalWhereClause)-1]);
        $GLOBALS['log']->debug("---------------------------------------------------------------------------------");

        $additionalWhereClause = implode(" AND ", $additionalWhereClause);

        return $additionalWhereClause;
    }



	/**
	 * takes a long TO: string of emails and returns the first appended by an
	 * elipse
	 */
	function trimLongTo($str) {
		if(strpos($str, ',')) {
			$exStr = explode(',', $str);
			return $exStr[0].'...';
		} elseif(strpos($str, ';')) {
			$exStr = explode(';', $str);
			return $exStr[0].'...';
		} else {
			return $str;
		}
	}

	function get_summary_text() {
		return $this->name;
	}



	function distributionForm($where) {
		global $app_list_strings;
		global $app_strings;
		global $mod_strings;
		global $theme;
		global $current_user;

		$distribution	= get_select_options_with_id($app_list_strings['dom_email_distribution'], '');
		$_SESSION['distribute_where'] = $where;

		//BEGIN SUGARCRM flav=pro ONLY
		require_once('include/SugarFields/Fields/Teamset/EmailSugarFieldTeamsetCollection.php');
		$teamSetField = new EmailSugarFieldTeamsetCollection($this, $this->field_defs, '', 'Distribute');
		$code = $teamSetField->get_code();
		$sqs_objects = $teamSetField->createQuickSearchCode(true);
		$teamWidget = $code.$sqs_objects;
		//END SUGARCRM flav=pro ONLY

		$out = '<form name="Distribute" id="Distribute">';
		$out .= get_form_header($mod_strings['LBL_DIST_TITLE'], '', false);
		$out .=<<<eoq
		<script>
			enableQS(true);
		</script>
eoq;
		$out .= '
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td>
					<script type="text/javascript">


						function checkDeps(form) {
							return;
						}

						function mySubmit() {
							var assform = document.getElementById("Distribute");
							var select = document.getElementById("userSelect");
							var assign1 = assform.r1.checked;
							var assign2 = assform.r2.checked;
							var dist = assform.dm.value;
							var assign = false;
							var users = false;
							var rules = false;
							var warn1 = "'.$mod_strings['LBL_WARN_NO_USERS'].'";
							var warn2 = "";

							if(assign1 || assign2) {
								assign = true;

							}

							for(i=0; i<select.options.length; i++) {
								if(select.options[i].selected == true) {
									users = true;
									warn1 = "";
								}
							}

							if(dist != "") {
								rules = true;
							} else {
								warn2 = "'.$mod_strings['LBL_WARN_NO_DIST'].'";
							}

							if(assign && users && rules) {

								if(document.getElementById("r1").checked) {
									var mu = document.getElementById("MassUpdate");
									var grabbed = "";

									for(i=0; i<mu.elements.length; i++) {
										if(mu.elements[i].type == "checkbox" && mu.elements[i].checked && mu.elements[i].name.value != "massall") {
											if(grabbed != "") { grabbed += "::"; }
											grabbed += mu.elements[i].value;
										}
									}
									var formgrab = document.getElementById("grabbed");
									formgrab.value = grabbed;
								}
								assform.submit();
							} else {
								alert("'.$mod_strings['LBL_ASSIGN_WARN'].'" + "\n" + warn1 + "\n" + warn2);
							}
						}

						function submitDelete() {
							if(document.getElementById("r1").checked) {
								var mu = document.getElementById("MassUpdate");
								var grabbed = "";

								for(i=0; i<mu.elements.length; i++) {
									if(mu.elements[i].type == "checkbox" && mu.elements[i].checked && mu.elements[i].name != "massall") {
										if(grabbed != "") { grabbed += "::"; }
										grabbed += mu.elements[i].value;
									}
								}
								var formgrab = document.getElementById("grabbed");
								formgrab.value = grabbed;
							}
							if(grabbed == "") {
								alert("'.$mod_strings['LBL_MASS_DELETE_ERROR'].'");
							} else {
								document.getElementById("Distribute").submit();
							}
						}

					</script>
						<input type="hidden" name="module" value="Emails">
						<input type="hidden" name="action" id="action">
						<input type="hidden" name="grabbed" id="grabbed">

					<table cellpadding="1" cellspacing="0" width="100%" border="0" class="edit view">
						<tr height="20">
							<td scope="col" scope="row" NOWRAP align="center">
								&nbsp;'.$mod_strings['LBL_ASSIGN_SELECTED_RESULTS_TO'].'&nbsp;';
					$out .= $this->userSelectTable();
					$out .=	'</td>
							<td scope="col" scope="row" NOWRAP align="left">
								&nbsp;'.$mod_strings['LBL_USING_RULES'].'&nbsp;
								<select name="distribute_method" id="dm" onChange="checkDeps(this.form);">'.$distribution.'</select>
							</td>';


					$out .= '</td>
							</tr>';

					//BEGIN SUGARCRM flav=pro ONLY
					$out .= '<tr><td/>';
					$out .= '<td>'.translate('LBL_TEAMS', 'EmailTemplates');
					$out .= $teamWidget;
					$out .= '</td>
							</tr>';
					//END SUGARCRM flav=pro ONLY

					$out .= '<tr>
								<td scope="col" width="50%" scope="row" NOWRAP align="right" colspan="2">
								<input title="'.$mod_strings['LBL_BUTTON_DISTRIBUTE_TITLE'].'"
									id="dist_button"
									class="button" onClick="AjaxObject.detailView.handleAssignmentDialogAssignAction();"
									type="button" name="button"
									value="  '.$mod_strings['LBL_BUTTON_DISTRIBUTE'].'  ">';
					$out .= '</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>';
	return $out;
	}

	function userSelectTable() {
		global $theme;
		global $mod_strings;

		$colspan = 1;
		$setTeamUserFunction = '';

		//BEGIN SUGARCRM flav=pro ONLY
		$colspan++;
		$teams = array();

		$teamTable = '<table cellpadding="1" cellspacing="0" border="0">';
		$teamTable .= '<tr><td colspan="2"><b>'.$mod_strings['LBL_SELECT_TEAM'].'</b></td></tr>';

		$r = $this->db->query('SELECT teams.id, teams.name FROM teams WHERE deleted=0 AND private = 0');
		while($a = $this->db->fetchByAssoc($r)) {
			$teamTable .= '<tr>';
			$teamTable .= '<td><input type="checkbox" style="border:0px solid #000000" name="'.$a['id'].'" id="'.$a['id'].'" onclick="checkDeps(this.form); setTeamUsers();"></td>';
			$teamTable .= '<td NOWRAP>'.$a['name'].'</td></tr>';
			$teams[$a['id']] = $a['name'];
		}
		$teamTable .= '</table>';

		$q2 = "SELECT t.id, t.name, tm.user_id FROM teams t JOIN team_memberships tm ON t.id = tm.team_id JOIN users u on tm.user_id = u.id WHERE t.deleted = 0 AND u.deleted = 0 AND u.is_group = 0 AND t.private = 0 AND u.status = 'Active' ORDER BY t.id";
		$r2 = $this->db->query($q2);
		$teamIfOpen = array();
		$teamIfDoes = array();
		$teamIfClose = array();

		$ifs = '';
		while($a2 = $this->db->fetchByAssoc($r2)) {
			$ifs .= 'if((document.getElementById("'.$a2['id'].'") != null) && (document.getElementById("'.$a2['id'].'").checked == true)) {';
			$ifs .= 'document.getElementById("'.$a2['user_id'].'").selected=true;';
			$ifs .= '} else if((document.getElementById("'.$a2['id'].'") != null) && (document.getElementById("'.$a2['id'].'").checked == false)) {';
			$ifs .= '   if((document.getElementById("'.$a2['user_id'].'") != null) && (document.getElementById("'.$a2['user_id'].'").selected == true)) {';
			$ifs .= 'document.getElementById("'.$a2['user_id'].'").selected=false; }';
			$ifs .= '}';
		}


		$setTeamUserFunction  = 'function setTeamUsers() {';
		$setTeamUserFunction .= $ifs;
		$setTeamUserFunction .= 'setCheckMark();';
		$setTeamUserFunction .= 'return;';
		$setTeamUserFunction .= '}';

		//END SUGARCRM flav=pro ONLY

		// get users
		$r = $this->db->query("SELECT users.id, users.user_name, users.first_name, users.last_name FROM users WHERE deleted=0 AND status = 'Active' AND is_group=0 ORDER BY users.last_name, users.first_name");

		$userTable = '<table cellpadding="0" cellspacing="0" border="0">';
		$userTable .= '<tr><td colspan="2"><b>'.$mod_strings['LBL_USER_SELECT'].'</b></td></tr>';
		$userTable .= '<tr><td><input type="checkbox" style="border:0px solid #000000" onClick="toggleAll(this); setCheckMark(); checkDeps(this.form);"></td> <td>'.$mod_strings['LBL_TOGGLE_ALL'].'</td></tr>';
		$userTable .= '<tr><td colspan="2"><select style="visibility:hidden;" name="users[]" id="userSelect" multiple size="12">';

		while($a = $this->db->fetchByAssoc($r)) {
			$userTable .= '<option value="'.$a['id'].'" id="'.$a['id'].'">'.$a['first_name'].' '.$a['last_name'].'</option>';
		}
		$userTable .= '</select></td></tr>';
		$userTable .= '</table>';

		$out  = '<script type="text/javascript">';
		$out .= $setTeamUserFunction;
		$out .= '
					function setCheckMark() {
						var select = document.getElementById("userSelect");

						for(i=0 ; i<select.options.length; i++) {
							if(select.options[i].selected == true) {
								document.getElementById("checkMark").style.display="";
								return;
							}
						}

						document.getElementById("checkMark").style.display="none";
						return;
					}

					function showUserSelect() {
						var targetTable = document.getElementById("user_select");
						targetTable.style.visibility="visible";
						var userSelectTable = document.getElementById("userSelect");
						userSelectTable.style.visibility="visible";
						return;
					}
					function hideUserSelect() {
						var targetTable = document.getElementById("user_select");
						targetTable.style.visibility="hidden";
						var userSelectTable = document.getElementById("userSelect");
						userSelectTable.style.visibility="hidden";
						return;
					}
					function toggleAll(toggle) {
						if(toggle.checked) {
							var stat = true;
						} else {
							var stat = false;
						}
						var form = document.getElementById("userSelect");
						for(i=0; i<form.options.length; i++) {
							form.options[i].selected = stat;
						}
					}


				</script>
			<span id="showUsersDiv" style="position:relative;">
				<a href="#" id="showUsers" onClick="javascript:showUserSelect();">
					'.SugarThemeRegistry::current()->getImage('Users', '', null, null, ".gif", $mod_strings['LBL_USERS']).'</a>&nbsp;
				<a href="#" id="showUsers" onClick="javascript:showUserSelect();">
					<span style="display:none;" id="checkMark">'.SugarThemeRegistry::current()->getImage('check_inline', 'border="0"', null, null, ".gif", $mod_strings['LBL_CHECK_INLINE']).'</span>
				</a>


				<div id="user_select" style="width:200px;position:absolute;left:2;top:2;visibility:hidden;z-index:1000;">
				<table cellpadding="0" cellspacing="0" border="0" class="list view">
					<tr height="20">
						<td  colspan="'.$colspan.'" id="hiddenhead" onClick="hideUserSelect();" onMouseOver="this.style.border = \'outset red 1px\';" onMouseOut="this.style.border = \'inset white 0px\';this.style.borderBottom = \'inset red 1px\';">
							<a href="#" onClick="javascript:hideUserSelect();">'.SugarThemeRegistry::current()->getImage('close', 'border="0"', null, null, ".gif", $mod_strings['LBL_CLOSE']).'</a>
							'.$mod_strings['LBL_USER_SELECT'].'
						</td>
					</tr>
					<tr>';
//<td valign="middle" height="30"  colspan="'.$colspan.'" id="hiddenhead" onClick="hideUserSelect();" onMouseOver="this.style.border = \'outset red 1px\';" onMouseOut="this.style.border = \'inset white 0px\';this.style.borderBottom = \'inset red 1px\';">
//BEGIN SUGARCRM flav=pro ONLY
		$out .= '		<td style="padding:5px" class="oddListRowS1" bgcolor="#fdfdfd" valign="top" align="left" style="left:0;top:0;">';
		$out .= $teamTable;
		$out .= '		</td>';
//END SUGARCRM flav=pro ONLY
		$out .=	'		<td style="padding:5px" class="oddListRowS1" bgcolor="#fdfdfd" valign="top" align="left" style="left:0;top:0;">
							'.$userTable.'
						</td>
					</tr>
				</table></div>
			</span>';
		return $out;
	}

	function checkInbox($type) {
		global $theme;
		global $mod_strings;
		$out = '<div><input	title="'.$mod_strings['LBL_BUTTON_CHECK_TITLE'].'"
						class="button"
						type="button" name="button"
						onClick="window.location=\'index.php?module=Emails&action=Check&type='.$type.'\';"
						style="margin-bottom:2px"
						value="  '.$mod_strings['LBL_BUTTON_CHECK'].'  "></div>';
		return $out;
	}

    /**
     * Guesses primary parent id from "To" and "From" email addresses.
     * This will not affect the many-to-many relationships already constructed as this is, at best,
     * informational linking.
     */
    public function fillPrimaryParentFields($table)
    {

        $addrs = $this->email2ParseAddressesForAddressesOnly($this->to_addrs);
        $addrs[] = $this->from_addr;

        if (empty($addrs)) {
            return;
        }

        $table = strtolower($table);
        $uctable = ucfirst($table);

        $addrs = "'" . implode("','", $addrs) . "'";
        $q = "SELECT a.name, a.id FROM {$table} a";
        $q .= " INNER JOIN email_addresses ea";
        $q .= " INNER JOIN email_addr_bean_rel eabr ON ea.id = eabr.email_address_id";
        $q .= " WHERE eabr.bean_module = '{$uctable}' AND email_address IN ({$addrs})";
        $q .= " AND eabr.bean_id = a.id AND a.deleted = 0";

        $ret = array();
        // loop through types to get hits
        $r = $this->db->query($q);
        while ($a = $this->db->fetchByAssoc($r)) {
            if (!empty($a['name']) && !empty($a['id'])) {
                $this->parent_type      = $uctable;
                $this->parent_id        = $a['id'];
                $this->parent_name      = $a['name'];
                return;
            }
        }
    }

        /**
         * Convert reference to inline image (stored as Note) to URL link
         * Enter description here ...
         * @param string $note ID of the note
         * @param string $ext type of the note
         */
        public function cid2Link($noteId, $noteType)
        {
            if(empty($this->description_html)) return;
			list($type, $subtype) = explode('/', $noteType);
			if(strtolower($type) != 'image') {
			    return;
			}
            $upload = new UploadFile();
			$this->description_html = preg_replace("#class=\"image\" src=\"cid:$noteId\.(.+?)\"#", "class=\"image\" src=\"{$this->imagePrefix}{$noteId}.\\1\"", $this->description_html);
	        // ensure the image is in the cache
            sugar_mkdir(sugar_cached("images/"));
			$imgfilename = sugar_cached("images/")."$noteId.".strtolower($subtype);
			$src = "upload://$noteId";
			if(!file_exists($imgfilename) && file_exists($src)) {
				copy($src, $imgfilename);
			}
        }

        /**
         * Convert all cid: links in this email into URLs
         */
    	function cids2Links()
    	{
            if(empty($this->description_html)) return;
    	    $q = "SELECT id, file_mime_type FROM notes WHERE parent_id = '{$this->id}' AND deleted = 0";
    		$r = $this->db->query($q);
            while($a = $this->db->fetchByAssoc($r)) {
                $this->cid2Link($a['id'], $a['file_mime_type']);
            }
    	}

    /**
     * Bugs 50972, 50973
     * Sets the field def for a field to allow null values
     *
     * @todo Consider moving to SugarBean to allow other models to set fields to NULL
     * @param string $field The field name to modify
     * @return void
     */
    public function setFieldNullable($field)
    {
        if (isset($this->field_defs[$field]) && is_array($this->field_defs[$field]))
        {
            if (empty($this->modifiedFieldDefs[$field]))
            {
                if (
                    isset($this->field_defs[$field]['isnull']) &&
                    (strtolower($this->field_defs[$field]['isnull']) == 'false' || $this->field_defs[$field]['isnull'] === false)
                )
                {
                    $this->modifiedFieldDefs[$field]['isnull'] = $this->field_defs[$field]['isnull'];
                    unset($this->field_defs[$field]['isnull']);
                }

                if (isset($this->field_defs[$field]['dbType']) && $this->field_defs[$field]['dbType'] == 'id')
                {
                    $this->modifiedFieldDefs[$field]['dbType'] = $this->field_defs[$field]['dbType'];
                    unset($this->field_defs[$field]['dbType']);
                }

                if (!isset($this->field_defs[$field]['required'])) {
                    $this->addedFieldDefs[$field]['required'] = true;
                    $this->field_defs[$field]['required'] = false;
                } elseif (!empty($this->field_defs[$field]['required'])) {
                    $this->modifiedFieldDefs[$field]['required'] = $this->field_defs[$field]['required'];
                    $this->field_defs[$field]['required'] = false;
                }
            }
        }
    }

    /**
     * Bugs 50972, 50973
     * Set the field def back to the way it was prior to modification
     *
     * @param $field
     * @return void
     */
    public function revertFieldNullable($field)
    {
        if (!empty($this->modifiedFieldDefs[$field]) && is_array($this->modifiedFieldDefs[$field]))
        {
            foreach ($this->modifiedFieldDefs[$field] as $k => $v)
            {
                $this->field_defs[$field][$k] = $v;
            }

            unset($this->modifiedFieldDefs[$field]);
        }

        if (isset($this->addedFieldDefs[$field])) {
            foreach (array_keys($this->addedFieldDefs[$field]) as $param) {
                unset($this->field_defs[$field][$param]);
            }
            unset($this->addedFieldDefs[$field]);
        }
    }

    /**
     * Set the DateTime Search Data based on Current User TimeZone
     *
     * @param  string $userSearchDateTime  - user Search Datetime
     * @return string $dbSearchDateTime    - database Search Datetime
     */
    public function toDatabaseSearchDateTime($userSearchDateTime) {
        global $timedate;
        global $current_user;

        $usertimezone = $current_user->getPreference('timezone');
        if (empty($usertimezone)) {
           $usertimezone = "UTC";
        }
        $tz = new DateTimeZone($usertimezone);

        $sugarDateTime = new SugarDateTime($userSearchDateTime);
        $sugarDateTime->setTimezone($tz);
        $dbSearchDateTime = $timedate->asDb($sugarDateTime);
        return $dbSearchDateTime;
    }
} // end class def

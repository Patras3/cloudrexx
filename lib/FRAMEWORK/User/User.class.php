<?php

class UserException extends Exception {}

/**
 * User Object
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Daeppen <thomas.daeppen@comvation.com>
 * @version     2.1.1
 * @package     contrexx
 * @subpackage  lib_framework
 */

require_once(ASCMS_FRAMEWORK_PATH.'/User/User_Profile.class.php');
require_once(ASCMS_FRAMEWORK_PATH.'/User/User_Setting.class.php');

/**
 * User Object
 *
 * The User object is used for all user related operations.
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Thomas Daeppen <thomas.daeppen@comvation.com>
 * @version     2.0.0
 * @package     contrexx
 * @subpackage  lib_framework
 */
class User extends User_Profile
{
    /**
     * ID of loaded user
     * @var integer
     * @access private
     */
    protected $id;

    /**
     * Username of user
     * @var string
     * @access private
     */
    private $username;

    /**
     * E-Mail address of user
     * @var string
     * @access private
     */
    private $email;

    /**
     * Password of user
     * @var string
     * @access private
     */
    private $password;

    /**
     * Language ID of user
     * @var integer
     * @access private
     */
    private $lang_id;

    /**
     * Language ID used for the frontend
     * @var integer
     * @access private
     */
    private $frontend_language;

    /**
     * Language ID used for the backend
     * @var integer
     * @access private
     */
    private $backend_language;

    /**
     * Active status of user
     * @var boolean
     * @access private
     */
    private $is_active;

    /**
     * Administrator status
     * @var boolean
     * @access private
     */
    private $is_admin;

    /**
     * Determines who is allowed to see the user's e-mail address
     * @var boolean
     * @access private
     */
    private $email_access;

    /**
     * Determines who is allowed to see the user's profile data.
     * @var string
     * @access private
     */
    private $profile_access;

    /**
     * Registration date of user (timestamp)
     * @var integer
     * @access private
     */
    private $regdate;

    /**
     * Expiration date of the account (timestamp)
     * @var integer
     * @access private
     */
    private $expiration;

    /**
     * Validity time period
     *
     * This integer represents the expiration of the user. A user won't be able to authenticate again after his validity time period has exceeded.
     * A validity time period of zero mean that the account has no expiration date.
     * @var integer
     * @access private
     */
    private $validity;

    /**
     * Key which will be used to reset the password
     * @var string
     * @access private
     */
    private $restore_key;

    /**
     * Date as timestamp when a new password was requested
     * @var integer
     * @access private
     */
    private $restore_key_time;

    /**
     * The last time the user had logged in (timestamp)
     * @var integer
     * @access private
     */
    private $last_auth;

    /**
     * The last time that the user was active (timestamp)
     * @var integer
     * @access private
     */
    private $last_activity;

    /**
     * Contains the number of currently loaded users
     * @var integer
     * @access private
     */
    private $filtered_search_count = 0;

    /**
     * Contains an array of all group IDS which the user is associated to
     * @var array
     * @access private
     */
    private $arrGroups;

    /**
     * @access public
     */
    public $EOF;

    /**
     * Array which holds all loaded users for later usage
     * @var array
     * @access protected
     */
    protected $arrLoadedUsers = array();

    /**
     * Array that holds all users which were ever loaded
     * @var array
     * @access protected
     */
    protected  $arrCachedUsers = array();

    /**
     * @access private
     */
    private $arrAttributes = array(
        'id'                => 'int',
        'is_admin'          => 'int',
        'username'          => 'string',
        'regdate'           => 'int',
        'expiration'        => 'int',
        'validity'          => 'int',
        'last_auth'         => 'int',
        'last_activity'     => 'int',
        'email'             => 'string',
        'email_access'      => 'string',
        'frontend_lang_id'  => 'int',
        'backend_lang_id'   => 'int',
        'active'            => 'int',
        'profile_access'    => 'string',
        'restore_key'       => 'string',
        'restore_key_time'  => 'int',
    );

    /**
     * @access private
     */
    private $arrPrivacyAccessTypes = array(
        'everyone'      => array(
            'email'         => 'TXT_ACCESS_EVERYONE_ALLOWED_SEEING_EMAIL',
            'profile'       => 'TXT_ACCESS_EVERYONE_ALLOWED_SEEING_PROFILE',
        ),
        'members_only'  => array(
            'email'         => 'TXT_ACCESS_MEMBERS_ONLY_ALLOWED_SEEING_EMAIL',
            'profile'       => 'TXT_ACCESS_MEMBERS_ONLY_ALLOWED_SEEING_PROFILE',
        ),
        'nobody'        => array(
            'email'         => 'TXT_ACCESS_NOBODY_ALLOWED_SEEING_EMAIL',
            'profile'       => 'TXT_ACCESS_NOBODY_ALLOWED_SEEING_PROFILE',
        ),
    );

    /**
     * @access private
     */
    private $defaultProfileAccessTyp;

    /**
     * @access private
     */
    private $defaultEmailAccessType;

    /**
     * Contains the message if an error occurs
     * @var string
     */
    public $error_msg = array();


    /**
     * TRUE if user is authenticated
     *
     * If this is TRUE the methods {@link load()} and {@link loadUsers()}
     * will be looked for further usage.
     * @todo    Explain this method in plain english...
     * @var     boolean
     * @access  private
     */
    private $loggedIn;


    public function __construct()
    {
        parent::__construct();
        $arrSettings = FWUser::getSettings();
// TODO:  Provide default values here in case the settings are missing!
        $this->defaultProfileAccessTyp = $arrSettings['default_profile_access']['value'];
        $this->defaultEmailAccessType = $arrSettings['default_email_access']['value'];
        $this->clean();
    }


    /**
     * Authenticate user against username and password
     *
     * Verifies the password of a username within the database.
     * If the password matches the appropriate users gets loaded
     * and the users last authentication time gets updated.
     * Returns TRUE on success or FALSE on failure.
     * @param   string    $username   The username
     * @param   string    $password   The MD5 hash of the password
     * @param   boolean   $backend    Tries to authenticate for the backend
     *                                if true, false otherwise
     * @return  boolean               True on success, false otherwise
     */
    public function auth($username, $password, $backend=false)
    {
        global $objDatabase;

        $objResult = $objDatabase->SelectLimit("
            SELECT `id`
              FROM `".DBPREFIX."access_users`
             WHERE `username`='".addslashes($username)."'
               AND `password`='".addslashes($password)."'
               AND `active`=1
               AND (`expiration`=0 OR `expiration`>".time().")", 1);
        if (!$objResult) {
//DBG::log("User::auth($username, $password, $backend): Query error");
            return false;
        }
        if ($objResult->RecordCount() != 1) {
//DBG::log("User::auth($username, $password, $backend): Wrong User count: ".$objResult->RecordCount());
            return false;
        }
        if (!$this->load($objResult->fields['id'])) {
//DBG::log("User::auth($username, $password, $backend): Failed to load User ID ".$objResult->fields['id']);
            return false;
        }
        if (!$this->hasModeAccess($backend)) {
//DBG::log("User::auth($username, $password, $backend): No access");
            return false;
        }
        if (!$this->updateLastAuthTime()) {
//DBG::log("User::auth($username, $password, $backend): Failed to update auth time");
            return false;
        }
//DBG::log("User::auth($username, $password, $backend): Success!");
        return true;
    }


    /**
     * Returns TRUE if the given password matches the current user,
     * FALSE otherwise.
     * @param   string    $password
     * @return  boolean
     */
    public function checkPassword($password)
    {
        global $objDatabase;

        return (boolean)$objResult = $objDatabase->SelectLimit("
            SELECT 1
              FROM `".DBPREFIX."access_users`
             WHERE `id`=$this->id
               AND `password`='".md5($password)."'", 1)
            && $objResult->RecordCount() == 1;
    }


    /**
     * Clean user metadata
     *
     * Reset all user metadata for a new user.
     * The metadata includes the ID of the user, the username, e-mail address,
     * password, language ID, active and administration status, registration date,
     * restore key and restore key timeout and the ID's of the associated groups.
     */
    private function clean()
    {
        global $_LANGID;

        $this->id = 0;
        $this->username = '';
        $this->email = '';
        $this->email_access = $this->defaultEmailAccessType;
        $this->password = '';
        $this->frontend_language = $_LANGID;
        $this->backend_language = $_LANGID;
        $this->is_active = false;
        $this->is_admin = false;
        $this->profile_access = $this->defaultProfileAccessTyp;
        $this->regdate = 0;
        $this->expiration = 0;
        $this->validity = 0;
        $this->last_auth = 0;
        $this->last_activity = 0;
        $this->restore_key = '';
        $this->restore_key_time = 0;
        $this->arrGroups = null;
        $this->EOF = true;
        $this->loggedIn = false;
    }


    /**
     * Delete the current loaded user account
     *
     * In the case that the current loaded user is the last available administrator
     * in the system, then the request will be refused and FALSE will be returned instead.
     * A user isn't able to delete its own account with which he is actually authenticated
     * at the moment unless the parameter $deleteOwnAccount is set to TRUE.
     * Returns TRUE on success or FALSE on failure.
     * @param   boolean       $deleteOwnAccount
     * @see     isLastAdmin()
     * @return  boolean
     */
    public function delete($deleteOwnAccount=false)
    {
        global $objDatabase, $_CORELANG;

        $objFWUser = FWUser::getFWUserObject();
        if ($this->id == $objFWUser->objUser->getId() && !$deleteOwnAccount) {
            return Message::error($_CORELANG['TXT_ACCESS_UNABLE_DELETE_YOUR_USER']);
        }
        if ($this->isLastAdmin()) {
            return Message::error(sprintf(
                $_CORELANG['TXT_ACCESS_LAST_ADMIN_USER'], $this->username));
        }
        $objResult = $objDatabase->Execute('
            DELETE tblU, tblP, tblG, tblA
              FROM `'.DBPREFIX.'access_users` AS tblU
              JOIN `'.DBPREFIX.'access_user_profile` AS tblP ON tblP.`user_id`=tblU.`id`
              LEFT JOIN `'.DBPREFIX.'access_rel_user_group` AS tblG ON tblG.`user_id`=tblU.`id`
              LEFT JOIN `'.DBPREFIX.'access_user_attribute_value` AS tblA ON tblA.`user_id`=tblU.`id`
             WHERE tblU.`id`='.$this->id);
        if (!$objResult) {
            return Message::error(sprintf(
                $_CORELANG['TXT_ACCESS_USER_DELETE_FAILED'], $this->username));
        }
        return true;
    }


    public function finishSignUp()
    {
        $this->restore_key = '';
        $this->restore_key_time = 0;
        $this->setActiveStatus(true);
        return $this->store();
    }


    /**
     * Load first user
     */
    public function first()
    {
        $this->EOF =
               reset($this->arrLoadedUsers) === false
            || !$this->load(key($this->arrLoadedUsers));
    }


    public static function forceDefaultEmailAccess()
    {
        global $objDatabase;

        $arrSettings = FWUser::getSettings();
        return $objDatabase->Execute("
            UPDATE `".DBPREFIX."access_users`
               SET `email_access`='".$arrSettings['default_email_access']['value']."'");
    }


    public static function forceDefaultProfileAccess()
    {
        global $objDatabase;

        $arrSettings = FWUser::getSettings();
        return $objDatabase->Execute("
            UPDATE `".DBPREFIX."access_users`
               SET `profile_access`='".$arrSettings['default_profile_access']['value']."'");
    }


    public function getActiveStatus()
    {
        return $this->is_active;
    }


    public function getAdminStatus()
    {
        return $this->is_admin;
    }


    public function getAssociatedGroupIds()
    {
        if (!isset($this->arrGroups)) {
            $this->arrGroups = $this->loadGroups();
        }
        return $this->arrGroups;
    }


    public function getBackendLanguage()
    {
        return $this->backend_language;
    }


    public function getDynamicPermissionIds($reload=false)
    {
        if (!isset($this->arrCachedUsers[$this->id]['dynamic_access_ids']) || $reload) {
            $this->loadPermissionIds('dynamic');
        }
        return $this->arrCachedUsers[$this->id]['dynamic_access_ids'];
    }


    public function getEmail()
    {
        // START: WORKAROUND FOR ACCOUNTS SOLD IN THE SHOP
        $email = $this->getShopUserEmail();
        return (empty($email) ? $this->email : $email);
        // END: WORKAROUND FOR ACCOUNTS SOLD IN THE SHOP
    }


    public function getEmailAccess()
    {
        return $this->email_access;
    }


    public function getErrorMsg()
    {
        return $this->error_msg;
    }


    public function getExpirationDate()
    {
        return $this->expiration;
    }


    public function getFilteredSearchUserCount()
    {
        return $this->filtered_search_count;
    }


    private function getFilteredUserIdList($arrFilter = null, $search = null)
    {
        $arrConditions = array();
        $arrSearchConditions = array();
        $tblCoreAttributes = false;
        $tblCustomAttributes = false;
        $tblGroup = false;
        $groupTables = false;

        // parse filter
        if (isset($arrFilter) && is_array($arrFilter)) {
            if (count($arrAccountConditions = $this->parseAccountFilterConditions($arrFilter))) {
                $arrConditions[] = implode(' AND ', $arrAccountConditions);
            }
            if (count($arrCoreAttributeConditions = $this->parseCoreAttributeFilterConditions($arrFilter))) {
                $arrConditions[] = implode(' AND ', $arrCoreAttributeConditions);
                $tblCoreAttributes = true;
            }
            if (count($arrCustomAttributeConditions = $this->parseCustomAttributeFilterConditions($arrFilter))) {
                $groupTables = true;
                $arrConditions[] = implode(' AND ', $arrCustomAttributeConditions);
                $tblCustomAttributes = true;
            }

            if (in_array('group_id', array_keys($arrFilter)) && !empty($arrFilter['group_id'])) {
                if (is_array($arrFilter['group_id'])) {
                    foreach ($arrFilter['group_id'] as $groupId) {
                        $arrGroupConditions[] = 'tblG.`group_id` = '.intval($groupId);
                    }
                    $groupTables = true;
                } else {
                    $arrGroupConditions[] = 'tblG.`group_id` = '.intval($arrFilter['group_id']);
                }
                $arrConditions[] = '('.implode(' OR ', $arrGroupConditions).')';

                if (!FWUser::getFWUserObject()->isBackendMode()) {
                    $arrConditions[] = "tblGF.`is_active` = 1 AND tblGF.`type` = 'frontend'";
                }

                $tblGroup = true;
            }
        }

        // parse search
        if (!empty($search)) {
            if (count($arrAccountConditions = $this->parseAccountSearchConditions($search))) {
                $arrSearchConditions[] = implode(' OR ', $arrAccountConditions);
            }
            if (count($arrCoreAttributeConditions = $this->parseAttributeSearchConditions($search, true))) {
                $arrSearchConditions[] = implode(' OR ', $arrCoreAttributeConditions);
                $tblCoreAttributes = true;
            }
            if (count($arrCustomAttributeConditions = $this->parseAttributeSearchConditions($search, false))) {
                $groupTables = true;
                $arrSearchConditions[] = implode(' OR ', $arrCustomAttributeConditions);
                $tblCustomAttributes = true;
            }
            if (count($arrSearchConditions)) {
                $arrConditions[] = implode(' OR ', $arrSearchConditions);
            }
        }

        $arrTables = array();
        if (!empty($tblCoreAttributes)) {
            $arrTables[] = 'core';
        }
        if (!empty($tblCustomAttributes)) {
            $arrTables[] = 'custom';
        }
        if ($tblGroup) {
            $arrTables[] = 'group';
        }

        return array(
            'tables'        => $arrTables,
            'conditions'    => $arrConditions,
            'group_tables'  => $groupTables
        );
    }


    public function getFrontendLanguage()
    {
        return $this->frontend_language;
    }


    public function getId()
    {
        return $this->id;
    }


    public function getLastActivityTime()
    {
        return $this->last_activity;
    }


    public function getLastAuthenticationTime()
    {
        return $this->last_auth;
    }


    public function getPrivacyAccessMenu($attrs, $option)
    {
        global $_ARRAYLANG;

        $menu = '<select'.(!empty($attrs) ? ' '.$attrs : '').'>';
        foreach ($this->arrPrivacyAccessTypes as $type => $arrDesc) {
            $menu .= "<option value='".$type."'".($type == $this->{$option.'_access'} ? ' selected="selected"' : '').">".$_ARRAYLANG[$arrDesc[$option]]."</option>";
        }
        $menu .= '</select>';
        return $menu;
    }


    public function getProfileAccess()
    {
        return $this->profile_access;
    }


    public function getProfileAttribute($attributeId, $historyId = 0)
    {
        if (isset($this->arrLoadedUsers[$this->id]['profile'][$attributeId][$historyId])) {
            return $this->arrLoadedUsers[$this->id]['profile'][$attributeId][$historyId];
        }
        if (isset($this->arrCachedUsers[$this->id]['profile'][$attributeId][$historyId])) {
            return $this->arrCachedUsers[$this->id]['profile'][$attributeId][$historyId];
        }
        return false;
    }


    public function getRegistrationDate()
    {
        return $this->regdate;
    }


    public function getRestoreKey()
    {
        return $this->restore_key;
    }


    public function getRestoreKeyTime()
    {
        return $this->restore_key_time;
    }


    public function getStaticPermissionIds($reload=false)
    {
        if (!isset($this->arrCachedUsers[$this->id]['static_access_ids']) || $reload) {
            $this->loadPermissionIds('static');
        }
        return $this->arrCachedUsers[$this->id]['static_access_ids'];
    }

    public function getPagePermissionIds($frontend=true, $reload=false)
    {
        $frontendOrBackend = $frontend ? 'frontend' : 'backend';
        $key = 'page_' . $frontendOrBackend . '_access_ids';
        if (!isset($this->arrCachedUsers[$this->id][$key]) || $reload) {
            $this->loadPermissionIds('page_'.$frontendOrBackend);
        }

        return $this->arrCachedUsers[$this->id][$key];
    }

    public function getUser($id)
    {
        $objUser = clone $this;
        $objUser->arrCachedUsers = &$this->arrCachedUsers;
        if ($objUser->load($id)) {
            return $objUser;
        }
        return false;
    }


    public function getUsername()
    {
        return $this->username;
    }


    /**
     * Returns the MD5 hash of the current User's password
     *
     * Used internally by the Shop Customer class.  Note that you should
     * preferrably *NOT* use this if you can avoid it.  Use {@see auth()}
     * instead.
     * @return    string      The MD5 hash of the current User's password
     */
    function getMd5Password()
    {
        return $this->password;
    }


    /**
     * Returns a User object according to the given criteria
     * @param   array   $filter
     * @param   string  $search
     * @param   array   $arrSort
     * @param   array   $arrAttributes
     * @param   integer $limit
     * @param   integer $offset
     * @return  User
     */
    public function getUsers(
        $filter=null, $search=null, $arrSort=null, $arrAttributes=null,
        $limit=null, $offset=0
    ) {
        $objUser = clone $this;
        $objUser->arrCachedUsers = &$this->arrCachedUsers;
        if ($objUser->loadUsers($filter, $search, $arrSort, $arrAttributes, $limit, $offset)) {
            return $objUser;
        }
        return false;
    }


    public function getValidityTimePeriod()
    {
        return $this->validity;
    }


    /**
     * Load user data
     *
     * Get username, email, lang_id, is_active and is_admin states from database
     * and put them into the analogous class variables.
     * @param integer $id
     * @throws UserException
     * @return unknown
     */
    private function load($id)
    {
        global $_LANGID;

        if ($this->isLoggedIn()) {
            throw new UserException("User->load(): Illegal method call - try getUser()!");
        }
        if ($id) {
            if (!isset($this->arrCachedUsers[$id])) {
                return $this->loadUsers($id);
            }
            $this->id = $id;
            $this->username = isset($this->arrCachedUsers[$id]['username']) ? $this->arrCachedUsers[$id]['username'] : '';
            $this->email = isset($this->arrCachedUsers[$id]['email']) ? $this->arrCachedUsers[$id]['email'] : '';
            $this->email_access = isset($this->arrCachedUsers[$id]['email_access']) ? $this->arrCachedUsers[$id]['email_access'] : $this->defaultEmailAccessType;
            $this->frontend_language = isset($this->arrCachedUsers[$id]['frontend_lang_id']) ? $this->arrCachedUsers[$id]['frontend_lang_id'] : $_LANGID;
            $this->backend_language = isset($this->arrCachedUsers[$id]['backend_lang_id']) ? $this->arrCachedUsers[$id]['backend_lang_id'] : $_LANGID;
            $this->is_active = isset($this->arrCachedUsers[$id]['active']) ? (bool)$this->arrCachedUsers[$id]['active'] : false;
            $this->is_admin = isset($this->arrCachedUsers[$id]['is_admin']) ? (bool)$this->arrCachedUsers[$id]['is_admin'] : false;
            $this->regdate = isset($this->arrCachedUsers[$id]['regdate']) ? $this->arrCachedUsers[$id]['regdate'] : 0;
            $this->expiration = isset($this->arrCachedUsers[$id]['expiration']) ? $this->arrCachedUsers[$id]['expiration'] : 0;
            $this->validity = isset($this->arrCachedUsers[$id]['validity']) ? $this->arrCachedUsers[$id]['validity'] : 0;
            $this->last_auth = isset($this->arrCachedUsers[$id]['last_auth']) ? $this->arrCachedUsers[$id]['last_auth'] : 0;
            $this->last_activity = isset($this->arrCachedUsers[$id]['last_activity']) ? $this->arrCachedUsers[$id]['last_activity'] : 0;
            $this->profile_access = isset($this->arrCachedUsers[$id]['profile_access']) ? $this->arrCachedUsers[$id]['profile_access'] : $this->defaultProfileAccessTyp;
            $this->restore_key = isset($this->arrCachedUsers[$id]['restore_key']) ? $this->arrCachedUsers[$id]['restore_key'] : '';
            $this->restore_key_time = isset($this->arrCachedUsers[$id]['restore_key_time']) ? $this->arrCachedUsers[$id]['restore_key_time'] : 0;
            $this->password = '';
            $this->arrGroups = null;
            $this->EOF = false;
            $this->loggedIn = false;
            return true;
        }
        $this->clean();
// TODO:  I guess this is wrong, then.
        return false;
    }


    private function loadUsers(
        $filter=null, $search=null, $arrSort=null, $arrAttributes=null,
        $limit=null, $offset=0
    ) {
        global $objDatabase;

        if ($this->isLoggedIn()) {
            $arrDebugBackTrace =  debug_backtrace();
            die("User->loadUsers(): Illegal method call in {$arrDebugBackTrace[0]['file']} on line {$arrDebugBackTrace[0]['line']}!");
        }
        $this->arrLoadedUsers = array();
        $arrSelectMetaExpressions = array();
        $arrSelectCoreExpressions = array();
        $arrSelectCustomExpressions = null;
        $this->filtered_search_count = 0;
        $sqlCondition = '';

        // set filter
        if (isset($filter) && is_array($filter) && count($filter) || !empty($search)) {
            $sqlCondition = $this->getFilteredUserIdList($filter, $search);
        } elseif (!empty($filter)) {
            $sqlCondition['tables'] = array('core');
            $sqlCondition['conditions'] = array('tblU.`id` = '.intval($filter));
            $sqlCondition['group_tables'] = false;
            $limit = 1;
        }

        // set sort order
        if (!($arrQuery = $this->setSortedUserIdList($arrSort, $sqlCondition, $limit, $offset))) {
            $this->clean();
            return false;
        }

        // set field list
        if (is_array($arrAttributes)) {
            foreach ($arrAttributes as $attribute) {
                if (isset($this->arrAttributes[$attribute]) && !in_array($attribute, $arrSelectMetaExpressions)) {
                    $arrSelectMetaExpressions[] = $attribute;
                } elseif ($this->objAttribute->isCoreAttribute($attribute) && !in_array($attribute, $arrSelectCoreExpressions)) {
                    $arrSelectCoreExpressions[] = $attribute;
                } elseif ($this->objAttribute->isCustomAttribute($attribute) && (!isset($arrSelectCustomExpressions) || !in_array($attribute, $arrSelectCustomExpressions))) {
                    $arrSelectCustomExpressions[] = $attribute;
                }
            }

            if (!in_array('id', $arrSelectMetaExpressions)) {
                $arrSelectMetaExpressions[] = 'id';
            }
        } else {
            $arrSelectMetaExpressions = array_keys($this->arrAttributes);
            $arrSelectCoreExpressions = $this->objAttribute->getCoreAttributeIds();
            $arrSelectCustomExpressions = array();
        }

        $query = 'SELECT tblU.`'.implode('`, tblU.`', $arrSelectMetaExpressions).'`'
            .(count($arrSelectCoreExpressions) ? ', tblP.`'.implode('`, tblP.`', $arrSelectCoreExpressions).'`' : '')
            .'FROM `'.DBPREFIX.'access_users` AS tblU'
            .(count($arrSelectCoreExpressions) || $arrQuery['tables']['core'] ? ' INNER JOIN `'.DBPREFIX.'access_user_profile` AS tblP ON tblP.`user_id` = tblU.`id`' : '')
            .($arrQuery['tables']['custom'] ? ' INNER JOIN `'.DBPREFIX.'access_user_attribute_value` AS tblA ON tblA.`user_id` = tblU.`id`' : '')
            .($arrQuery['tables']['group'] ? ' INNER JOIN `'.DBPREFIX.'access_rel_user_group` AS tblG ON tblG.`user_id` = tblU.`id`' : '')
            .($arrQuery['tables']['group'] && !FWUser::getFWUserObject()->isBackendMode() ? ' INNER JOIN `'.DBPREFIX.'access_user_groups` AS tblGF ON tblGF.`group_id` = tblG.`group_id`' : '')
            .(count($arrQuery['joins']) ? ' '.implode(' ',$arrQuery['joins']) : '')
// TODO: some conditions are not well enclosed, so there might be a more proper solution than adding more brackes at this point
            .(count($arrQuery['conditions']) ? ' WHERE ('.implode(') AND (', $arrQuery['conditions']).')' : '')
            .($arrQuery['group_tables'] ? ' GROUP BY tblU.`id`' : '')
            .(count($arrQuery['sort']) ? ' ORDER BY '.implode(', ', $arrQuery['sort']) : '');

        if (empty($limit)) {
            $objUser = $objDatabase->Execute($query);
        } else {
            $objUser = $objDatabase->SelectLimit($query, $limit, $offset);
        };

        if ($objUser !== false && $objUser->RecordCount() > 0) {
            while (!$objUser->EOF) {
                foreach ($objUser->fields as $attributeId => $value) {
                    if ($this->objAttribute->isCoreAttribute($attributeId)) {
                        $this->arrCachedUsers[$objUser->fields['id']]['profile'][$attributeId][0] = $this->arrLoadedUsers[$objUser->fields['id']]['profile'][$attributeId][0] = $value;
                    } else {
                        $this->arrCachedUsers[$objUser->fields['id']][$attributeId] = $this->arrLoadedUsers[$objUser->fields['id']][$attributeId] = $value;
                    }
                }
                $objUser->MoveNext();
            }

            isset($arrSelectCustomExpressions) ? $this->loadCustomAttributeProfileData($arrSelectCustomExpressions) : false;
            $this->first();
            return true;
        }
        $this->clean();
        return false;
    }


    public function __clone()
    {
        $this->clean();
    }


    /*private function parseFilterConditions($filter)
    {
        $arrConditions = array();

        if (count($arrAccountConditions = $this->parseAccountFilterConditions($filter))) {
            $arrConditions[] = implode(' AND ', $arrAccountConditions);
        }
        if (count($arrCoreAttributeConditions = $this->parseCoreAttributeFilterConditions($filter))) {
            $arrConditions[] = implode(' AND ', $arrCoreAttributeConditions);
        }
        if (count($arrCustomAttributeConditions = $this->parseCustomAttributeFilterConditions($filter))) {
            $arrConditions[] = implode(' AND ', $arrCustomAttributeConditions);
        }

        return $arrConditions;
    }*/


    /*private function parseSearchConditions($search)
    {
        $arrConditions = array();

        if (count($arrAccountConditions = $this->parseAccountSearchConditions($search))) {
            $arrConditions[] = implode(' OR ', $arrAccountConditions);
        }
        if (count($arrCoreAttributeConditions = $this->parseAttributeSearchConditions($search, true))) {
            $arrConditions[] = implode(' OR ', $arrCoreAttributeConditions);
        }
        if (count($arrCustomAttributeConditions = $this->parseAttributeSearchConditions($search, false))) {
            $arrConditions[] = implode(' OR ', $arrCustomAttributeConditions);
        }

        return $arrConditions;
    }*/


    private function parseAccountSearchConditions($search)
    {
        $FWUser = FWUser::getFWUserObject();
        $arrConditions = array();
        $arrAttribute = array('username');
        if ($FWUser->isBackendMode()) {
            $arrAttribute[] = 'email';
        }
        foreach ($arrAttribute as $attribute) {
            $arrConditions[] = "(tblU.`".$attribute."` LIKE '%".(is_array($search) ? implode("%' OR tblU.`".$attribute."` LIKE '%", array_map('addslashes', $search)) : addslashes($search))."%')";
        }
        return $arrConditions;
    }


    private function setSortedUserIdList(
        $arrSort, $sqlCondition=null, $limit=null, $offset=null
    ) {
        global $objDatabase;

        $arrCustomJoins = array();
        $arrCustomSelection = array();
        $joinCoreTbl = false;
        $joinCustomTbl = false;
        $joinGroupTbl = false;
        $arrUserIds = array();
        $arrSortExpressions = array();
        $groupTables = false;
        $nr = 0;

        if (!empty($sqlCondition)) {
            if (isset($sqlCondition['tables'])) {
                if (in_array('core', $sqlCondition['tables'])) {
                    $joinCoreTbl = true;
                }
                if (in_array('custom', $sqlCondition['tables'])) {
                    $joinCustomTbl = true;
                }
                if (in_array('group', $sqlCondition['tables'])) {
                    $joinGroupTbl = true;
                }
            }
            if (isset($sqlCondition['conditions']) && count($sqlCondition['conditions'])) {
                $arrCustomSelection = $sqlCondition['conditions'];
            }
            if (!empty($sqlCondition['group_tables'])) {
                $groupTables = true;
            }
        }
        if (is_array($arrSort)) {
            foreach ($arrSort as $attribute => $direction) {
                if (in_array(strtolower($direction), array('asc', 'desc'))) {
                    if (isset($this->arrAttributes[$attribute])) {
                        $arrSortExpressions[] = 'tblU.`'.$attribute.'` '.$direction;
                    } elseif ($this->objAttribute->load($attribute)) {
                        if ($this->objAttribute->isCoreAttribute($attribute)) {
                            $arrSortExpressions[] = 'tblP.`'.$attribute.'` '.$direction;
                            $joinCoreTbl = true;
                        } else {
                            $arrSortExpressions[] = 'tblA'.$nr.'.`value` '.$direction;
                            $arrCustomJoins[] = 'INNER JOIN `'.DBPREFIX.'access_user_attribute_value` AS tblA'.$nr.' ON tblA'.$nr.'.`user_id` = tblU.`id`';
                            $arrCustomSelection[] = 'tblA'.$nr.'.`attribute_id` = '.$attribute;
                            ++$nr;
                        }
                    }
                } elseif ($attribute == 'special') {
                    $arrSortExpressions[] = $direction;
                }
            }
        }
        if (!is_array($arrSort) || !array_key_exists('id', $arrSort)) {
            $arrSortExpressions[] = 'tblU.`id` ASC';
        }
        $query = '
            SELECT SQL_CALC_FOUND_ROWS DISTINCT tblU.`id`
              FROM `'.DBPREFIX.'access_users` AS tblU'.
            ($joinCoreTbl ? ' INNER JOIN `'.DBPREFIX.'access_user_profile` AS tblP ON tblP.`user_id`=tblU.`id`' : '').
            ($joinCustomTbl ? ' INNER JOIN `'.DBPREFIX.'access_user_attribute_value` AS tblA ON tblA.`user_id`=tblU.`id`' : '').
            ($joinGroupTbl ? ' INNER JOIN `'.DBPREFIX.'access_rel_user_group` AS tblG ON tblG.`user_id`=tblU.`id`' : '').
            ($joinGroupTbl && !FWUser::getFWUserObject()->isBackendMode() ? ' INNER JOIN `'.DBPREFIX.'access_user_groups` AS tblGF ON tblGF.`group_id`=tblG.`group_id`' : '').
            (count($arrCustomJoins) ? ' '.implode(' ',$arrCustomJoins) : '').
            (count($arrCustomSelection) ? ' WHERE '.implode(' AND ', $arrCustomSelection) : '').
            (count($arrSortExpressions) ? ' ORDER BY '.implode(', ', $arrSortExpressions) : '');
        if (empty($limit)) {
            $objUserId = $objDatabase->Execute($query);
            $this->filtered_search_count = $objUserId->RecordCount();
        } else {
            $objUserId = $objDatabase->SelectLimit($query, $limit, intval($offset));
            $objUserCount = $objDatabase->Execute('SELECT FOUND_ROWS()');
            $this->filtered_search_count = $objUserCount->fields['FOUND_ROWS()'];
        }

        if ($objUserId !== false) {
            while (!$objUserId->EOF) {
                $arrUserIds[$objUserId->fields['id']] = '';
                $objUserId->MoveNext();
            }
        }

        $this->arrLoadedUsers = $arrUserIds;

        if (!count($arrUserIds)) {
            return false;
        }

        return array(
            'tables' => array(
                'core'      => $joinCoreTbl,
                'custom'    => $joinCustomTbl,
                'group'     => $joinGroupTbl
            ),
            'joins'         => $arrCustomJoins,
            'conditions'    => $arrCustomSelection,
            'sort'          => $arrSortExpressions,
            'group_tables'  => $groupTables,
        );
        /*$arrNotSortedUserIds = array_diff(array_keys($this->arrLoadedUsers), $arrUserIds);
        foreach ($arrNotSortedUserIds as $userId) {
            $arrUserIds[$userId] = '';
        }*/
    }


    public function setRestoreKey()
    {
        $this->restore_key = md5($this->username.$this->regdate.time());
        $this->restore_key_time = time() + 3600;
    }


    public function releaseRestoreKey()
    {
        $this->restore_key = '';
        $this->restore_key_time = 0;
        return true;
    }


    /**
     * Parse account filter conditions
     *
     * Generate conditions of the account attributes for the SQL WHERE statement.
     * The filter conditions are defined through the two dimensional array $arrFilter.
     * Each key-value pair represents an attribute and its associated condition to which it must fit to.
     * The condition could either be a integer or string depending on the attributes type, or it could be
     * a collection of integers or strings represented in an array.
     *
     * Examples of the filer array:
     *
     * array(
     *      'firstname' => '%nicole%',
     * )
     * // will return all users who's firstname include 'nicole'
     *
     *
     * array(
     *      'firstname' => array(
     *          'd%',
     *          'e%',
     *          'f%',
     *          'g%'
     *      )
     * )
     * // will return all users which have a firstname of which its first letter is and between 'd' to 'g' (case less)
     *
     *
     * array(
     *      'firstname' => array(
     *          array(
     *              '>' => 'd',
     *              '<' => 'g'
     *          ),
     *          'LIKE'  => 'g%'
     *      )
     * )
     * // same as the preview example but in an other way
     *
     *
     * array(
     *      'is_active' => 1,
     *      'last_auth' => array(
     *          '>' => time()-3600
     *      )
     * )
     * // will return all users that are active and have been logged in at least in the last one hour
     *
     *
     *
     * @param array $arrFilter
     * @return array
     */
    private function parseAccountFilterConditions($arrFilter)
    {
        $arrConditions = array();
        foreach ($arrFilter as $attribute => $condition) {
            /**
             * $attribute is the account attribute like 'firstname' or 'username'
             * $condition is either a simple condition (integer or string) or an condition matrix (array)
             */
            if (isset($this->arrAttributes[$attribute])) {
                $arrComparisonOperators = array(
                    'int'       => array('=','<','>'),
                    'string'    => array('!=','<','>', 'REGEXP')
                );
                $arrDefaultComparisonOperator = array(
                    'int'       => '=',
                    'string'    => 'LIKE'
                );
                $arrEscapeFunction = array(
                    'int'       => 'intval',
                    'string'    => 'addslashes'
                );

                if (is_array($condition)) {
                    $arrRestrictions = array();
                    foreach ($condition as $operator => $restriction) {
                        /**
                         * $operator is either a comparison operator ( =, LIKE, <, >) if $restriction is an array or if $restriction is just an integer or a string then its an index which would be useless
                         * $restriction is either a integer or a string or an array which represents a restriction matrix
                         */
                        if (is_array($restriction)) {
                            $arrConditionRestriction = array();
                            foreach ($restriction as $restrictionOperator => $restrictionValue) {
                                /**
                                 * $restrictionOperator is a comparison operator ( =, <, >)
                                 * $restrictionValue represents the condition
                                 */
                                $arrConditionRestriction[] = "tblU.`{$attribute}` ".(
                                    in_array($restrictionOperator, $arrComparisonOperators[$this->arrAttributes[$attribute]], true) ?
                                        $restrictionOperator
                                    :   $arrDefaultComparisonOperator[$this->arrAttributes[$attribute]]
                                )." '".$arrEscapeFunction[$this->arrAttributes[$attribute]]($restrictionValue)."'";
                            }
                            $arrRestrictions[] = implode(' AND ', $arrConditionRestriction);
                        } else {
                            $arrRestrictions[] = "tblU.`{$attribute}` ".(
                                in_array($operator, $arrComparisonOperators[$this->arrAttributes[$attribute]], true) ?
                                    $operator
                                :   $arrDefaultComparisonOperator[$this->arrAttributes[$attribute]]
                            )." '".$arrEscapeFunction[$this->arrAttributes[$attribute]]($restriction)."'";
                        }
                    }
                    $arrConditions[] = '(('.implode(') OR (', $arrRestrictions).'))';
                } else {
                    $arrConditions[] = "(tblU.`".$attribute."` ".$arrDefaultComparisonOperator[$this->arrAttributes[$attribute]]." '".$arrEscapeFunction[$this->arrAttributes[$attribute]]($condition)."')";
                }
            }
        }

        return $arrConditions;
    }


    /**
     * Load group ID's of user
     *
     * Returns an array with the ID's of all groups to which
     * the user is associated to.
     * @param integer $userId
     * @global ADONewConnection
     * @return mixed array on success, FALSE on failure
     */
    private function loadGroups()
    {
        global $objDatabase;

        $objResult = $objDatabase->Execute('
            SELECT tblRel.`group_id`
              FROM `'.DBPREFIX.'access_rel_user_group` AS tblRel
             INNER JOIN `'.DBPREFIX.'access_user_groups` AS tblGroup
             USING (`group_id`)
             WHERE tblRel.`user_id`='.$this->id);
        if (!$objResult) {
            return false;
        }
        $arrGroups = array();
        while (!$objResult->EOF) {
            array_push($arrGroups, $objResult->fields['group_id']);
            $objResult->MoveNext();
        }
        return $arrGroups;
    }


    public function reset()
    {
        $this->clean();
    }


    /**
     * Load next user
     */
    public function next()
    {
        if (next($this->arrLoadedUsers) === false || !$this->load(key($this->arrLoadedUsers))) {
            $this->EOF = true;
        }
    }


    public function signUp()
    {
        $arrSettings = User_Setting::getSettings();
        if ($arrSettings['user_activation']['status']) {
            $this->restore_key = md5($this->username.$this->password.time());
            $this->restore_key_time = $arrSettings['user_activation_timeout']['status'] ? time() + $arrSettings['user_activation_timeout']['value'] * 3600 : 0;
        }
        return $this->store();
    }


    /**
     * Store user account
     *
     * This stores the metadata of the user, which includes the username,
     * password, email, language ID, activ status and the administration status,
     * to the database.
     * If it is a new user, it also sets the registration time to the current time.
     * @global ADONewConnection
     * @global array
     * @return boolean
     */
    public function store()
    {
        global $objDatabase, $_CORELANG;

        if (!$this->validateUsername()) {
DBG::log("User::store(): Invalid username ($this->username)");
            return false;
        }
        if (!$this->validateEmail()) {
DBG::log("User::store(): Invalid e-mail ($this->email)");
            return false;
        }

        if ($this->id) {
            if (!$objDatabase->Execute("
                UPDATE `".DBPREFIX."access_users`
                SET
                    `username` = '".addslashes($this->username)."',
                    `is_admin` = ".intval($this->is_admin).",
                    ".(!empty($this->password) ? "`password` = '".$this->password."'," : '')."
                    `email` = '".addslashes($this->email)."',
                    `email_access` = '".$this->email_access."',
                    `frontend_lang_id` = ".$this->frontend_language.",
                    `backend_lang_id` = ".$this->backend_language.",
                    `expiration` = ".intval($this->expiration).",
                    `validity` = ".intval($this->validity).",
                    `active` = ".intval($this->is_active).",
                   `profile_access` = '".$this->profile_access."',
                    `restore_key` = '".$this->restore_key."',
                    `restore_key_time` = ".$this->restore_key_time."
                WHERE `id` = ".$this->id
            )) {
DBG::log("User::store(): Failed to update: ".var_export($this, true));
                return Message::error($_CORELANG['TXT_ACCESS_FAILED_TO_UPDATE_USER_ACCOUNT']);
            }
        } else {
            if ($objDatabase->Execute("
                INSERT INTO `".DBPREFIX."access_users` (
                    `username`,
                    `is_admin`,
                    `password`,
                    `email`,
                    `email_access`,
                    `frontend_lang_id`,
                    `backend_lang_id`,
                    `regdate`,
                    `expiration`,
                    `validity`,
                    `last_auth`,
                    `last_activity`,
                    `active`,
                    `profile_access`,
                    `restore_key`,
                    `restore_key_time`
                ) VALUES (
                    '".addslashes($this->username)."',
                    ".intval($this->is_admin).",
                    '".$this->password."',
                    '".addslashes($this->email)."',
                    '".$this->email_access."',
                    ".$this->frontend_language.",
                    ".$this->backend_language.",
                    ".time().",
                    ".intval($this->expiration).",
                    ".intval($this->validity).",
                    ".$this->last_auth.",
                    ".$this->last_activity.",
                    ".intval($this->is_active).",
                    '".$this->profile_access."',
                    '".$this->restore_key."',
                    '".$this->restore_key_time."'
                )")) {
                $this->id = $objDatabase->Insert_ID();
                if (!$this->createProfile()) {
                    $this->delete();
                    $this->error_msg[] = $_CORELANG['TXT_ACCESS_FAILED_TO_ADD_USER_ACCOUNT'];
                    return false;
                }
            } else {
                $this->error_msg[] = $_CORELANG['TXT_ACCESS_FAILED_TO_ADD_USER_ACCOUNT'];
                return false;
            }
        }
        if (!$this->storeGroupAssociations()) {
            $this->error_msg[] = $_CORELANG['TXT_ARRAY_COULD_NOT_SET_GROUP_ASSOCIATIONS'];
            return false;
        }
        if (!$this->storeProfile()) {
            $this->error_msg[] = $_CORELANG['TXT_ACCESS_FAILED_STORE_PROFILE'];
            return false;
        }
        return true;
    }


    /**
     * Store group associations
     *
     * Stores the group associations of the loaded user.
     * Returns TRUE on success, FALSE on failure.
     * @global ADONewConnection
     * @return boolean
     */
    private function storeGroupAssociations()
    {
        global $objDatabase;

        $status = true;
        $arrCurrentGroups = $this->loadGroups();
        $arrAddedGroups = array_diff($this->getAssociatedGroupIds(), $arrCurrentGroups);
        $arrRemovedGroups = array_diff($arrCurrentGroups, $this->getAssociatedGroupIds());
        foreach ($arrRemovedGroups as $groupId) {
            if (!$objDatabase->Execute('
                DELETE FROM `'.DBPREFIX.'access_rel_user_group`
                 WHERE `group_id`='.$groupId.'
                   AND `user_id`='.$this->id)) {
                $status = false;
            }
        }
        foreach ($arrAddedGroups as $groupId) {
            if (!$objDatabase->Execute('
                INSERT INTO `'.DBPREFIX.'access_rel_user_group` (
                    `user_id`, `group_id`
                ) VALUES (
                    '.$this->id.', '.$groupId.'
                )')) {
                $status = false;
            }
        }
        return $status;
    }


    private static function removeOutdatedAccounts()
    {
        global $objDatabase;
        static $userActivationTimeoutStatus = null;

        if (!isset($userActivationTimeoutStatus)) {
            $arrSettings = User_Setting::getSettings();
            $userActivationTimeoutStatus =
                !empty($arrSettings['user_activation_timeout']['status']);
        }
        if ($userActivationTimeoutStatus) {
            $objDatabase->Execute('
                DELETE tblU, tblP, tblG, tblA
                  FROM `'.DBPREFIX.'access_users` AS tblU
                 INNER JOIN `'.DBPREFIX.'access_user_profile` AS tblP ON tblP.`user_id`=tblU.`id`
                  LEFT JOIN `'.DBPREFIX.'access_rel_user_group` AS tblG ON tblG.`user_id`=tblU.`id`
                  LEFT JOIN `'.DBPREFIX.'access_user_attribute_value` AS tblA ON tblA.`user_id`=tblU.`id`
                 WHERE tblU.`active`=0
                   AND tblU.`restore_key`!=\'\'
                   AND tblU.`restore_key_time`<'.time());
        }
    }


    /**
     * Returns true if the User name is valid and unique
     * @return  boolean       True if the User name is valid and unique,
     *                        false otherwise
     */
    function validateUsername()
    {
        global $_CORELANG;

        if (!self::isValidUsername($this->username)) {
            return Message::error($_CORELANG['TXT_ACCESS_INVALID_USERNAME']);
        }
        if (!$this->isUniqueUsername($this->username, $this->id)) {
            return Message::error($_CORELANG['TXT_ACCESS_USERNAME_ALREADY_USED']);
        }
        return true;
    }


    private function isLoggedIn()
    {
        return $this->loggedIn;
    }


    public function login($backend=false)
    {
        global $sessionObj;

        if ($this->loggedIn) return true;
        if (   isset($sessionObj)
            && is_object($sessionObj)
            && $sessionObj->userId
            && $this->load($sessionObj->userId)
            && $this->getActiveStatus()
            && $this->hasModeAccess($backend)
            && $this->updateLastActivityTime()) {
            $this->loggedIn = true;
            return true;
        }
        return false;
    }


    private function validateEmail()
    {
        global $_CORELANG;

        if (FWValidator::isEmail($this->email)) {
            if ($this->isUniqueEmail($this->email, $this->id)) {
                return true;
            }
            return Message::error($_CORELANG['TXT_ACCESS_EMAIL_ALREADY_USED']);
        }
        return Message::error($_CORELANG['TXT_ACCESS_INVALID_EMAIL_ADDRESS']);
    }


    /**
     * Validate language id
     *
     * Checks if the language ids frontend_lang_id and backend_lang_id are valid language IDs.
     * In the case that the specified langauge isn't valid, the ID 0 is taken instead.
     * $scope could either be 'frontend' or 'backend'
     *
     * @param string $scope
     */
    private function validateLanguageId($scope)
    {
        $this->{$scope.'_language'} =
            (FWLanguage::getLanguageParameter(
                $this->{$scope.'_language'}, $scope)
                  ? $this->{$scope.'_language'} : 0);
    }


    private function loadPermissionIds($type)
    {
        global $objDatabase;
        
        if(substr($type, 0, 4) != 'page') {
            $query = '
                SELECT tblI.`access_id`
                FROM `'.DBPREFIX.'access_users` AS tblU
                INNER JOIN `'.DBPREFIX.'access_rel_user_group` AS tblR ON tblR.`user_id` = tblU.`id`
                INNER JOIN `'.DBPREFIX.'access_user_groups` AS tblG ON tblG.`group_id` = tblR.`group_id`
                INNER JOIN `'.DBPREFIX.'access_group_'.$type.'_ids` AS tblI ON tblI.`group_id` = tblG.`group_id`
                WHERE tblU.`id` = '.$this->id.'
                      AND tblG.`is_active`
                GROUP BY tblI.`access_id`
                ORDER BY tblI.`access_id`';
//TODO: is the above GROUP BY necessary?
//TODO: is the above ORDER BY necessary?
            $objAccessId = $objDatabase->Execute($query);
            if ($objAccessId !== false) {
                $this->arrCachedUsers[$this->id][$type.'_access_ids'] = array();
                while (!$objAccessId->EOF) {
                    $this->arrCachedUsers[$this->id][$type.'_access_ids'][] = $objAccessId->fields['access_id'];
                    $objAccessId->MoveNext();
                }
            }    
        }
        else { // page_backend or page_frontend
            $backOrFrontend = substr($type, 5);
            $backOrFrontendType = 0; //value for frontend
            if($backOrFrontend == 'backend')
                $backOrFrontend = 1;

            $query = '
                SELECT tblP.`page_id`
                FROM `'.DBPREFIX.'access_users` AS tblU
                INNER JOIN `'.DBPREFIX.'access_rel_user_group` AS tblR ON tblR.`user_id` = tblU.`id`
                INNER JOIN `'.DBPREFIX.'access_user_groups` AS tblG ON tblG.`group_id` = tblR.`group_id`
                INNER JOIN `'.DBPREFIX.'access_group_page` AS tblP ON tblP.`group_id` = tblG.`group_id`
                WHERE tblU.`id` = '.$this->id.'
                      AND tblP.`type` = '.$backOrFrontendType.'
                      AND tblG.`is_active`';
            $rs = $objDatabase->Execute($query);
            if ($rs !== false) {
                $key = 'page_'.$backOrFrontendType.'_access_ids';
                $this->arrCachedUsers[$this->id][$key] = array();
                while (!$rs->EOF) {
                    $this->arrCachedUsers[$this->id][$key][] = $rs->fields['page_id'];
                    $rs->MoveNext();
                }
            }
        }
    }


    public function hasModeAccess($backend=false)
    {
        global $objDatabase;

        if ($this->getAdminStatus()) {
//DBG::log("User::hasModeAccess($backend): Admin, okay");
            return true;
        }
        $query = "
            SELECT 1
              FROM `".DBPREFIX."access_user_groups` AS tblG
              JOIN `".DBPREFIX."access_rel_user_group` AS tblR ON tblR.`group_id`=tblG.`group_id`
              JOIN `".DBPREFIX."access_users` AS tblU ON tblU.`id`=tblR.`user_id`
             WHERE tblU.`id`=$this->id
               AND tblG.`is_active`=1
               AND (tblG.`type`='".($backend ? 'backend' : 'frontend')."'
                OR tblG.`type`='backend')";
        $objResult = $objDatabase->SelectLimit($query, 1);
        if (!$objResult) {
//DBG::log("User::hasModeAccess($backend): Query error: $query");
            return false;
        }
        if ($objResult->EOF) {
//DBG::log("User::hasModeAccess($backend): EOF, fail");
            return false;
        }
        return true;
    }


    private function updateLastActivityTime()
    {
        global $objDatabase;

        $arrSettings = User_Setting::getSettings();
        $intervalvalue = (isset($arrSettings['session_user_interval']['value'])
            ? $arrSettings['session_user_interval']['value'] : 500);
        if (time() > ($intervalvalue + $this->last_activity)) {
            return $objDatabase->Execute("
                UPDATE `".DBPREFIX."access_users`
                   SET `last_activity`='".time()."'
                 WHERE `id`=$this->id");
        }
        return true;
    }


    private function updateLastAuthTime()
    {
        global $objDatabase;

        return $objDatabase->Execute("
            UPDATE `".DBPREFIX."access_users`
               SET `last_auth`='".time()."'
             WHERE `id`=$this->id");
    }


    /**
     * Sets username of user
     *
     * This will set the attribute username of this object to $username
     * if the parameter $username is valid and isn't yet used by an other user.
     * @param string $username
     * @return boolean
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }


    /**
     * Sets the validity period of the account
     *
     * Administrators cannot be restricted in their validity.
     * Returns TRUE.
     * @param integer $timestamp
     * @return boolean
     */
    public function setValidityTimePeriod($validity)
    {
        if ($this->getAdminStatus()) {
            $this->validity = 0;
            $this->setExpirationDate(0);
        } else {
            $this->validity = $validity;
            $this->setExpirationDate(($validitySeconds = $validity*60*60*24) ? mktime(23, 59, 59, date('m', time() + $validitySeconds), date('d', time() + $validitySeconds), date('Y', time() + $validitySeconds)) : 0);
        }
        return true;
    }


    private function setExpirationDate($expiration)
    {
        $this->expiration = $expiration;
    }


    /**
     * Sets email address of user
     *
     * This will set the attribute email of this object to $email
     * if the parameter $email is valid and isn't yet used by an other user.
     * @param   string    $email
     * @return  boolean
     */
    public function setEmail($email)
    {
        // START: WORKAROUND FOR ACCOUNTS SOLD IN THE SHOP
        $emailPrefix = array();
        if (preg_match(
            '/^(shop_customer_[0-9]+_[0-9]+_[0-9]-).+$/',
            $this->email, $emailPrefix)) {
            $email = $emailPrefix[1].$email;
        }
        // END: WORKAROUND FOR ACCOUNTS SOLD IN THE SHOP
        $this->email = $email;
    }


    /**
     * Sets password of user
     *
     * This will set the attribute password of this object to the md5 hash
     * of $password if $password is a valid password and if it was confirmed
     * by the second parameter $confirmedPassword.
     * @param   string    $password           The new password
     * @param   string    $confirmedPassword  The new password, again
     * @return  boolean                       True on success, false otherwise
     * @global  array     $_CORELANG
     */
    public function setPassword($password, $confirmedPassword=null, $reset=false)
    {
        global $_CORELANG;

        if (empty($password) && empty($confirmedPassword) && $this->id && !$reset) {
            return true;
        }
        if ($this->isValidPassword($password)) {
            if (isset($confirmedPassword) && $password != $confirmedPassword) {
                $this->error_msg[] = $_CORELANG['TXT_ACCESS_PASSWORD_NOT_CONFIRMED'];
                return false;
            }

            $this->password = md5($password);
            return true;
        } else {
            $this->error_msg[] = $_CORELANG['TXT_ACCESS_INVALID_PASSWORD'];
        }
        return false;
    }


    /**
     * Set frontend language ID of user
     *
     * This will set the attribute frontend_lang_id of this object to $langId.
     * @param   integer   $langId
     * @return  void
     */
    public function setFrontendLanguage($langId)
    {
        $this->frontend_language = intval($langId);
        $this->validateLanguageId('frontend');
    }


    /**
     * Set backend language ID of user
     *
     * This will set the attribute backend_lang_id of this object to $langId.
     * @param   integer   $langId
     * @return  void
     */
    public function setBackendLanguage($langId)
    {
        $this->backend_language = intval($langId);
        $this->validateLanguageId('backend');
    }


    /**
     * Set active status of user
     *
     * This will set the attribute is_active of this object either
     * to TRUE or FALSE, depending of $status.
     * @param   boolean   $status
     * @return  void
     */
    public function setActiveStatus($status)
    {
        $this->is_active = (bool)$status;
    }


    /**
     * Set administration status of user
     *
     * This will set the attribute is_admin of this object to $status.
     * If $status is FALSE then it will only be accepted if this object
     * isn't the only administrator.
     * @param   boolean   $status
     * @global  array
     * @return  boolean
     */
    public function setAdminStatus($status)
    {
        global $_CORELANG;

        if ($status || !$this->isLastAdmin()) {
            $this->is_admin = (bool)$status;
            return true;
        }
        $this->error_msg[] = sprintf($_CORELANG['TXT_ACCESS_CHANGE_PERM_LAST_ADMIN_USER'], $this->getUsername());
        return false;
    }


    /**
     * Set ID's of groups to which this user should belong to
     * @see     UserGroup, UserGroup::getGroups(), UserGroup::getId()
     * @param   array   $arrGroups
     * @return  void
     */
    public function setGroups($arrGroups)
    {
        $objFWUser = FWUser::getFWUserObject();
        $objGroup = $objFWUser->objGroup->getGroups(null,null,array());
        $this->arrGroups = array();
        while (!$objGroup->EOF) {
            if (in_array($objGroup->getId(), $arrGroups)) {
                $this->arrGroups[] = $objGroup->getId();
            }
            $objGroup->next();
        }
    }


    public function setEmailAccess($emailAccess)
    {
        $this->email_access = in_array($emailAccess, array_keys($this->arrPrivacyAccessTypes))
            ? $emailAccess : $this->defaultEmailAccessType;
    }


    public function setProfileAccess($profileAccess)
    {
        $this->profile_access = in_array($profileAccess, array_keys($this->arrPrivacyAccessTypes))
            ? $profileAccess : $this->defaultProfileAccessTyp;
    }


    /**
     * Returns true if the current User has the only active admin account
     * present in the system.
     *
     * Returns false if either
     *  - the current User is not an admin, or
     *  - there are at least two active admins present
     * Note that true is returned if the database query fails, so the User
     * will not be allowed to be deleted.  You might have a whole different
     * kind of problem in that case anyway.
     * @global  ADONewConnection
     * @return  boolean
     */
    private function isLastAdmin()
    {
        global $objDatabase;

        if (!$this->is_admin) return false;
        $objResult = $objDatabase->Execute('
            SELECT COUNT(*) AS `numof_admin`
              FROM `'.DBPREFIX.'access_users`
             WHERE `is_admin`=1
               AND `active`=1');
        // If the query fails, assume that he's the last one.
        if (!$objResult) return true;
        return ($objResult->fields['numof_admin'] < 2);
    }


    /**
     * Returns true if $email is a unique e-mail address in the system
     * @param   string    $email
     * @param   integer   $id
     * @return  boolean
     */
    private function isUniqueEmail($email, $id=0)
    {
        global $objDatabase;

        self::removeOutdatedAccounts();
        $objResult = $objDatabase->SelectLimit("
            SELECT 1
              FROM ".DBPREFIX."access_users
             WHERE email='".addslashes($email)."'
               AND id!=$id", 1);
        return ($objResult && $objResult->RecordCount() == 0);
    }


    /**
     * Returns true if $username is a unique user name
     *
     * Returns false if the test for uniqueness fails, or if the $username
     * exists already.
     * If non-empty, the given User ID is excluded from the search, so the
     * User does not match herself.
     * @param   string    $username   The username to test
     * @param   integer   $id         The optional current User ID
     * @return  boolean               True if the username is available,
     *                                false otherwise
     */
    function isUniqueUsername($username, $id=0)
    {
        global $objDatabase;

        self::removeOutdatedAccounts();
        $objResult = $objDatabase->SelectLimit("
            SELECT 1
              FROM ".DBPREFIX."access_users
             WHERE username='".addslashes($username)."'
               AND id!=$id", 1);
        return ($objResult && $objResult->RecordCount() == 0);
    }


    /**
     * Returns true if the given $username is valid
     * @param   string    $username
     * @return  boolean
     * @static
     */
    static function isValidUsername($username)
    {
        if (preg_match('/^[a-zA-Z0-9-_]+$/', $username)) {
            return true;
        }
// For version 2.3, inspired by migrating Shop Customers to Users:
// In addition to the above, also accept usernames that look like valid
// e-mail addresses
// TODO: Maybe this should be restricted to MODULE_ID == 16 (Shop)?
        if (FWValidator::isEmail($username)) {
//DBG::log("User::isValidUsername($username): is an e-mail address");
            return true;
        }
//DBG::log("User::isValidUsername($username): is *NOT* an e-mail address");
        return false;
    }


    /**
     * Returns true if the given $password is valid
     * @param   string    $password
     * @return  boolean
     */
    static function isValidPassword($password)
    {
        return strlen($password) >= 6;
    }


    public function isAllowedToChangeEmailAccess()
    {
        if ($this->getAdminStatus()) {
            return true;
        }
        $arrSettings = User_Setting::getSettings();
        return $arrSettings['user_config_email_access']['status'];
    }


    public function isAllowedToChangeProfileAccess()
    {
        if ($this->getAdminStatus()) {
            return true;
        }
        $arrSettings = User_Setting::getSettings();
        return $arrSettings['user_config_profile_access']['status'];
    }


    public function isAllowedToDeleteAccount()
    {
        $arrSettings = User_Setting::getSettings();
        return $arrSettings['user_delete_account']['status'];
    }


    /**
     * Returns the e-mail address if the User accounts has been created
     * by the Shop.
     *
     * Such accounts have e-mail addresses that look like
     * "shop_customer_$orderId_$productId_$instance-$email"
     * Note that this is a temporary workaround and will be changed in
     * a future release.
     * @todo    All affected customers *MUST* be migrated properly from
     *          the Shop to the User administration
     * @return  string            The e-mail address if the account was
     *                            autocreated by the Shop, the empty string
     *                            otherwise.
     */
    private function getShopUserEmail()
    {
        $email = array();
        if (preg_match(
            '/^shop_customer_[0-9]+_[0-9]+_[0-9]-(.+)$/', $this->email, $email)) {
            return $email[1];
        }
        return '';
    }


    /**
     * Tries to form a valid and unique username from the words given
     *
     * Usually, you would use first and last names as parameters.
     * @return    string                The new user name on success,
     *                                  false otherwise
     */
    static function make_username($word1, $word2)
    {
//echo("User::makeUsername($word1, $word2): Entered<br />");
        // Just letters, please
        $word1 = preg_replace('/[^a-z]/i', '', $word1);
        $word2 = preg_replace('/[^a-z]/i', '', $word2);
        $usernames = array(
            $word2, "$word1$word2", "${word1}_$word2", "$word1.$word2", $word1,
        );
        $suffix = '';
        while (true) {
            foreach ($usernames as $username) {
//echo("Username /$username/$suffix/ ");
                if (!self::isValidUsername($username.$suffix)) {
//echo("not valid<br />");
                    continue;
                }
                if (!self::isUniqueUsername($username.$suffix)) {
//echo("not unique<br />");
                    continue;
                }
//echo("OK<br />");
                return $username.$suffix;
            }
            // Note that this method will run for a long time, or even
            // forever, for very common names.
            $suffix = intval(mt_rand(0, 99));
        }
        // Never reached
        return null;
    }


    /**
     * Returns a valid password
     *
     * Generated passwords consist of
     *  - at most 4 lower caps letters [qwertzupasdfghjkyxcvbnm],
     *  - at most 4 upper caps letters [QWERTZUPASDFGHJKLYXCVBNM],
     *  - at most 2 digits [23456789], and
     *  - at most 1 special character [-+_!?%&], if enabled.
     * If $length is less than 6, the length will be 6 characters.
     * If $length is bigger than 8, the length will be 8 characters.
     * @param     integer   $length       Desired password length,
     *                                    6 to 8 characters.  Defaults to 8
     * @param     boolean   $use_special  Use "special" characters [-+_!?%&]
     *                                    if true.  Defaults to false
     * @return    string                  The new password
     */
    static function make_password($length=8, $use_special=false)
    {
        static $lower = 'qwertzupasdfghjkyxcvbnm';
        static $upper = 'QWERTZUPASDFGHJKLYXCVBNM';
        static $digit = '23456789';
        static $special = '-+_!?%&';

        $length = min(max($length, 6), 8);
        while (true) {
            $password = '';
            $have_lower = 0;
            $have_upper = 0;
            $have_digit = 0;
            $have_other = 0;
            while (strlen($password) < $length) {
                if ($have_lower < 4 && mt_rand(0, 6) < 2) {
                    $password .= substr($lower, mt_rand(0, strlen($lower))-1, 1);
                    ++$have_lower;
                    continue;
                }
                if ($have_upper < 4 && mt_rand(0, 6) < 2) {
                    $password .= substr($upper, mt_rand(0, strlen($upper))-1, 1);
                    ++$have_upper;
                    continue;
                }
                if ($have_digit < 2 && mt_rand(0, 6) < 1) {
                    $password .= substr($digit, mt_rand(0, strlen($digit))-1, 1);
                    ++$have_digit;
                    continue;
                }
                if ($use_special && $have_other < 1 && mt_rand(0, 6) < 1) {
                    $password .= substr($special, mt_rand(0, strlen($special))-1, 1);
                    ++$have_other;
                }
            }
            if (self::isValidPassword($password)) return $password;
        }
        // Never reached
        return null;
    }


    /**
     * Set the active status of one or more Users
     *
     * The $mix_user_id parameter may either be a user ID or an array thereof.
     * Sets appropriate messages.
     * @param   mixed   $mix_user_id        The User ID or an array of those
     * @param   boolean $active             Activate (true) or deactivate
     *                                      (false) the User(s).
     * @return  void
     */
    static function set_active($mix_user_id, $active)
    {
        global $_CORELANG;

        if (empty($mix_user_id)) return;
        if (!is_array($mix_user_id)) {
            $mix_user_id = array($mix_user_id);
        }
        $count = 0;
        global $objFWUser;
        $objUser = $objFWUser->objUser;
        foreach ($mix_user_id as $user_id) {
            $objUser = $objUser->getUser($user_id);
            if (!$objUser) {
                Message::warning(sprintf(
                    $_CORELANG['TXT_ACCESS_NO_USER_WITH_ID'], $user_id));
                continue;
            }
//$objUser = new User();
            $objUser->setActiveStatus($active);
            if (!$objUser->store()) {
                Message::warning(sprintf(
                    $_CORELANG['TXT_SHOP_ERROR_CUSTOMER_UPDATING'], $user_id));
                continue;
            }
            ++$count;
        }
        if ($count) {
            Message::ok(
                $_CORELANG['TXT_ACCESS_USER_ACCOUNT'.
                ($count > 1 ? 'S' : '').'_'.
                ($active ? '' : 'DE').'ACTIVATED']);
        }
        return;
    }

}

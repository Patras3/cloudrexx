<?php
/**
 * Media  Directory Entry Class
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @package     contrexx
 * @subpackage  module_mediadir
 * @todo        Edit PHP DocBlocks!
 */

/**
 * Includes
 */
require_once ASCMS_MODULE_PATH . '/mediadir/lib/inputfield.class.php';
require_once ASCMS_MODULE_PATH . '/mediadir/lib/mail.class.php';
require_once ASCMS_MODULE_PATH . '/mediadir/lib/voting.class.php';
require_once ASCMS_MODULE_PATH . '/mediadir/lib/comment.class.php';
require_once ASCMS_MODULE_PATH . '/mediadir/lib/comment.class.php';
require_once ASCMS_LIBRARY_PATH. '/googleServices/googleMap.class.php';

class mediaDirectoryEntry extends mediaDirectoryInputfield
{
    private $intEntryId;
    private $intLevelId;
    private $intCatId;
    private $strSearchTerm;
    private $bolLatest;
    private $bolUnconfirmed;
    private $bolActive;
    private $intLimitStart;
    private $intLimitEnd;
    private $intUserId;
    private $bolPopular;

    private $strBlockName;

    public $arrEntries = array();
    public $recordCount = 0;

    /**
     * Constructor
     */
    function __construct()
    {
        /*if($bolGetEnties == 1) {
            $this->arrEntries = self::getEntries();
        }*/

        parent::getSettings();
        parent::getFrontendLanguages();
    }

    function getEntries($intEntryId=null, $intLevelId=null, $intCatId=null, $strSearchTerm=null, $bolLatest=null, $bolUnconfirmed=null, $bolActive=null, $intLimitStart=null, $intLimitEnd='n', $intUserId=null, $bolPopular=null)
    {
        global $_ARRAYLANG, $_CORELANG, $objDatabase, $_LANGID, $objInit;

        $this->intEntryId = intval($intEntryId);
        $this->intLevelId = intval($intLevelId);
        $this->intCatId = intval($intCatId);
        $this->bolLatest = intval($bolLatest);
        $this->bolUnconfirmed = intval($bolUnconfirmed);
        $this->bolActive = intval($bolActive);
        $this->strBlockName = null;
        $this->intLimitStart = intval($intLimitStart);
        $this->intLimitEnd = $intLimitEnd;
        $this->intUserId = intval($intUserId);
        $this->bolPopular = intval($bolPopular);

        if(($strSearchTerm != $_ARRAYLANG['TXT_MEDIADIR_ID_OR_SEARCH_TERM']) && (!empty($strSearchTerm))) {
            $this->strSearchTerm = contrexx_addslashes($strSearchTerm);
        } else {
            $this->strSearchTerm = null;
        }

        if(!empty($this->intEntryId)) {
            $strWhereEntryId = "AND (entry.`id` = ".$this->intEntryId.") ";
        }

        if(!empty($this->intUserId)) {
            $strWhereEntryId = "AND (entry.`added_by` = ".$this->intUserId.") ";
        }

        if(!empty($this->intLevelId)) {
            $strWhereLevel = "AND ((level.`level_id` = ".$this->intLevelId.") AND (level.`entry_id` = entry.`id`)) ";
            $strFromLevel = " ,".DBPREFIX."module_mediadir_rel_entry_levels AS level";
        }

        if(!empty($this->intCatId)) {
            $strWhereCategory = "AND ((category.`category_id` = ".$this->intCatId.") AND (category.`entry_id` = entry.`id`)) ";
            $strFromCategory = " ,".DBPREFIX."module_mediadir_rel_entry_categories AS category";
        }

        if(!empty($this->bolLatest)) {
            $strOrderLatest = "entry.`validate_date` DESC,";
            $this->strBlockName = "mediadirLatestList";
        }

        if(!empty($this->bolPopular)) {
            $strOrderPopular = "entry.`popular_hits` DESC,";
        }

        if(!empty($this->bolUnconfirmed)) {
            $strWhereUnconfirmed = "AND (entry.`confirmed` = 0) ";
            $this->strBlockName = "mediadirConfirmList";
        } else {
            $strWhereUnconfirmed = "AND (entry.`confirmed` = 1) ";
        }

        if(!empty($this->bolActive)) {
            $strWhereActive = "AND (entry.`active` = 1) ";
        } else {
            $strWhereActive = "";
        }

        if(empty($this->intLimitStart) && $this->intLimitStart == 0) {
            $strSelectLimit = "LIMIT ".$this->intLimitEnd;
        } else {
            $strSelectLimit = "LIMIT ".$this->intLimitStart.",".$this->intLimitEnd;
        }

        if($this->intLimitEnd === 'n') {
            $strSelectLimit = '';
        }

        if(empty($this->strSearchTerm)) {
            $query = "
                SELECT
                    inputfield.`id` AS `id`
                FROM
                    ".DBPREFIX."module_mediadir_inputfields AS inputfield
                WHERE
                    (inputfield.`type` != 16 AND inputfield.`type` != 17)
                AND
                    (inputfield.`form` = entry.`form_id`)
                ORDER BY
                    inputfield.`order` ASC
                LIMIT 1
            ";

            $strWhereFirstInputfield = "AND (rel_inputfield.`form_id` = entry.`form_id`) AND (rel_inputfield.`field_id` = (".$query.")) AND (rel_inputfield.`value` != '') AND (rel_inputfield.`lang_id` = '".$_LANGID."')";
        } else {
            $strWhereTerm = "AND ((rel_inputfield.`value` LIKE '%".$this->strSearchTerm."%') OR (entry.`id` = '".$this->strSearchTerm."')) ";
            $strWhereFirstInputfield = '';
            $this->strBlockName = "";
        }

        if(empty($this->strBlockName)) {
            $this->strBlockName = "mediadirEntryList";
        }
        
        if($objInit->mode == 'frontend') {
	        if(intval($this->arrSettings['settingsShowEntriesInAllLang']) == 0) {
	        	$strWhereLangId = "AND (entry.`lang_id` = ".$_LANGID.") ";
	        } else {
	            $strWhereLangId = "";
	        }
        }

        $query = "
            SELECT
                entry.`id` AS `id`,
                entry.`form_id` AS `form_id`,
                entry.`create_date` AS `create_date`,
                entry.`update_date` AS `update_date`,
                entry.`validate_date` AS `validate_date`,
                entry.`added_by` AS `added_by`,
                entry.`updated_by` AS `updated_by`,
                entry.`lang_id` AS `lang_id`,
                entry.`hits` AS `hits`,
                entry.`popular_hits` AS `popular_hits`,
                entry.`popular_date` AS `popular_date`,
                entry.`last_ip` AS `last_ip`,
                entry.`confirmed` AS `confirmed`,
                entry.`active` AS `active`,
                entry.`duration_type` AS `duration_type`,
                entry.`duration_start` AS `duration_start`,
                entry.`duration_end` AS `duration_end`,
                entry.`duration_notification` AS `duration_notification`,
                entry.`translation_status` AS `translation_status`,
                rel_inputfield.`value` AS `value`
            FROM
                ".DBPREFIX."module_mediadir_entries AS entry,
                ".DBPREFIX."module_mediadir_rel_entry_inputfields AS rel_inputfield
                ".$strFromCategory."
                ".$strFromLevel."
            WHERE
                (rel_inputfield.`entry_id` = entry.`id`)
                ".$strWhereFirstInputfield."
                ".$strWhereTerm."
                ".$strWhereUnconfirmed."
                ".$strWhereCategory."
                ".$strWhereLevel."
                ".$strWhereEntryId."
                ".$strWhereActive."
                ".$strWhereLangId."
            GROUP BY
                entry.`id`
            ORDER BY
                ".$strOrderLatest."
                ".$strOrderPopular."
                entry.`id` DESC
                ".$strSelectLimit."
        ";

        $objEntries = $objDatabase->Execute($query);
        
        $arrEntries = array();

        if ($objEntries !== false) {
            while (!$objEntries->EOF) {
                $arrEntry = array();
                $arrEntryFields = array();

                if(array_key_exists($objEntries->fields['id'], $arrEntries)) {
                    $arrEntries[intval($objEntries->fields['id'])]['entryFields'][] = $objEntries->fields['value'];
                } else {
                    $arrEntryFields[] = $objEntries->fields['value'];

                    $arrEntry['entryId'] = intval($objEntries->fields['id']);
                    $arrEntry['entryFormId'] = intval($objEntries->fields['form_id']);
                    $arrEntry['entryFields'] = $arrEntryFields;
                    $arrEntry['entryCreateDate'] = intval($objEntries->fields['create_date']);
                    $arrEntry['entryValdateDate'] = intval($objEntries->fields['validate_date']);
                    $arrEntry['entryAddedBy'] = intval($objEntries->fields['added_by']);
                    $arrEntry['entryHits'] = intval($objEntries->fields['hits']);
                    $arrEntry['entryPopularHits'] = intval($objEntries->fields['popular_hits']);
                    $arrEntry['entryPopularDate'] = intval($objEntries->fields['popular_date']);
                    $arrEntry['entryLastIp'] = htmlspecialchars($objEntries->fields['last_ip'], ENT_QUOTES, CONTREXX_CHARSET);
                    $arrEntry['entryConfirmed'] = intval($objEntries->fields['confirmed']);
                    $arrEntry['entryActive'] = intval($objEntries->fields['active']);
                    $arrEntry['entryDurationType'] = intval($objEntries->fields['duration_type']);
                    $arrEntry['entryDurationStart'] = intval($objEntries->fields['duration_start']);
                    $arrEntry['entryDurationEnd'] = intval($objEntries->fields['duration_end']);
                    $arrEntry['entryDurationNotification'] = intval($objEntries->fields['duration_notification']);
                    $arrEntry['entryTranslationStatus'] = explode(",",$objEntries->fields['translation_status']);
                    
                    $this->arrEntries[$objEntries->fields['id']] = $arrEntry;
                }

                $objEntries->MoveNext();
            }
            
            $this->recordCount = $objEntries->RecordCount();
        }
    }



    function listEntries($objTpl, $intView)
    {
        global $_ARRAYLANG, $_CORELANG, $objDatabase;

        $objFWUser = FWUser::getFWUserObject();
        $intToday = mktime();

        switch ($intView) {
            case 1:
                //Backend View
                if(!empty($this->arrEntries)){
                    foreach ($this->arrEntries as $key => $arrEntry) {
                        if(intval($arrEntry['entryAddedBy']) != 0) {
                            if ($objUser = $objFWUser->objUser->getUser(intval($arrEntry['entryAddedBy']))) {
                                $strAddedBy = $objUser->getUsername();
                            } else {
                                $strAddedBy = "unknown";
                            }
                        } else {
                            $strAddedBy = "unknown";
                        }

                        if($arrEntry['entryActive'] == 1) {
                		    $strStatus = 'images/icons/status_green.gif';
                		    $intStatus = 0;
                		    
                		    if(($arrEntry['entryDurationStart'] > $intToday || $arrEntry['entryDurationEnd'] < $intToday) && $arrEntry['entryDurationType'] == 2) {
                		    	$strStatus = 'images/icons/status_yellow.gif';
                		    }
                		} else {
                		    $strStatus = 'images/icons/status_red.gif';
                		    $intStatus = 1;
                		}
                		
                        $objTpl->setVariable(array(
                            'MEDIADIR_ROW_CLASS' =>  $i%2==0 ? 'row1' : 'row2',
                            'MEDIADIR_ENTRY_ID' =>  $arrEntry['entryId'],
                            'MEDIADIR_ENTRY_STATUS' => $strStatus,
                            'MEDIADIR_ENTRY_SWITCH_STATUS' => $intStatus,
                            'MEDIADIR_ENTRY_VALIDATE_DATE' =>  date("H:i:s - d.m.Y",$arrEntry['entryValdateDate']),
                            'MEDIADIR_ENTRY_CREATE_DATE' =>  date("H:i:s - d.m.Y",$arrEntry['entryCreateDate']),
                            'MEDIADIR_ENTRY_AUTHOR' =>  htmlspecialchars($strAddedBy, ENT_QUOTES, CONTREXX_CHARSET),
                            'MEDIADIR_ENTRY_HITS' =>  $arrEntry['entryHits'],
                        ));

                        foreach ($arrEntry['entryFields'] as $key => $strFieldValue) {
                            $intPos = $key+1;

                            $objTpl->setVariable(array(
                                'MEDIADIR_ENTRY_FIELD_'.$intPos.'_POS' => substr($strFieldValue, 0, 255),
                            ));
                        }

                        //get votes
                        if($this->arrSettings['settingsAllowVotes'] == 1) {
                            $objVoting = new mediaDirectoryVoting();
                            $objVoting->getVotes($objTpl, $arrEntry['entryId']);
                        } else {
                            $objTpl->setVariable(array(
                                'MEDIADIR_ENTRY_VOTES' => $_CORELANG['TXT_DEACTIVATED'],
                            ));
                        }

                        //get comments
                        if($this->arrSettings['settingsAllowComments'] == 1) {
                            $objComment = new mediaDirectoryComment();
                            $objComment->getComments($objTpl, $arrEntry['entryId']);
                        } else {
                            $objTpl->setVariable(array(
                                'MEDIADIR_ENTRY_COMMENTS' => $_CORELANG['TXT_DEACTIVATED'],
                            ));
                        }

                        $i++;
                        $objTpl->parse($this->strBlockName);
                        $objTpl->hideBlock('noEntriesFound');
                        $objTpl->clearVariables();
                    }
                } else {
                    $objTpl->setGlobalVariable(array(
                        'TXT_MEDIADIR_NO_ENTRIES_FOUND' => $_ARRAYLANG['TXT_MEDIADIR_NO_ENTRIES_FOUND'],
                    ));

                    $objTpl->touchBlock('noEntriesFound');
                    $objTpl->clearVariables();
                }
                break;
            case 2:
                //Frontend View
                if(!empty($this->arrEntries)) {
                    foreach ($this->arrEntries as $key => $arrEntry) {
	                    if(($arrEntry['entryDurationStart'] < $intToday && $arrEntry['entryDurationEnd'] > $intToday) || $arrEntry['entryDurationType'] == 1) {
	                        $objInputfields = new mediaDirectoryInputfield(intval($arrEntry['entryFormId']),false,$arrEntry['entryTranslationStatus']);
	                        $objInputfields->listInputfields($objTpl, 3, intval($arrEntry['entryId']));
	
	                        if(intval($arrEntry['entryAddedBy']) != 0) {
		                        if ($objUser = $objFWUser->objUser->getUser(intval($arrEntry['entryAddedBy']))) {
								    $strAddedBy = $objUser->getUsername();
								} else {
	                                $strAddedBy = "unknown";
								}
	                        } else {
	                            $strAddedBy = "unknown";
	                        }
	
	                        $strCategoryLink = $this->intCatId != 0 ? '&amp;cid='.$this->intCatId : null;
	                        $strLevelLink = $this->intLevelId != 0 ? '&amp;lid='.$this->intLevelId : null;
	
	                        if($this->checkPageCmd('detail'.intval($arrEntry['entryFormId']))) {
	                            $strDetailCmd = 'detail'.intval($arrEntry['entryFormId']);
	                        } else {
	                            $strDetailCmd = 'detail';
	                        }
	
	                        $objTpl->setVariable(array(
	                            'MEDIADIR_ROW_CLASS' =>  $i%2==0 ? 'row1' : 'row2',
	                            'MEDIADIR_ENTRY_ID' =>  $arrEntry['entryId'],
	                            'MEDIADIR_ENTRY_STATUS' => $strStatus,
	                            'MEDIADIR_ENTRY_VALIDATE_DATE' =>  date("H:i:s - d.m.Y",$arrEntry['entryValdateDate']),
	                            'MEDIADIR_ENTRY_CREATE_DATE' =>  date("H:i:s - d.m.Y",$arrEntry['entryCreateDate']),
	                            'MEDIADIR_ENTRY_AUTHOR' =>  htmlspecialchars($strAddedBy, ENT_QUOTES, CONTREXX_CHARSET),
	                            'MEDIADIR_ENTRY_CATEGORIES' =>  $this->getCategoriesLevels(1, $arrEntry['entryId']),
                                'MEDIADIR_ENTRY_LEVELS' =>  $this->getCategoriesLevels(2, $arrEntry['entryId']),
                                'MEDIADIR_ENTRY_HITS' =>  $arrEntry['entryHits'],
	                            'MEDIADIR_ENTRY_POPULAR_HITS' =>  $arrEntry['entryPopularHits'],
	                            'MEDIADIR_ENTRY_DETAIL_URL' =>  'index.php?section=mediadir&amp;cmd='.$strDetailCmd.$strLevelLink.$strCategoryLink.'&amp;eid='.$arrEntry['entryId'],
	                            'MEDIADIR_ENTRY_EDIT_URL' =>  'index.php?section=mediadir&amp;cmd=edit&amp;eid='.$arrEntry['entryId'],
	                            'MEDIADIR_ENTRY_DELETE_URL' =>  'index.php?section=mediadir&amp;cmd=delete&amp;eid='.$arrEntry['entryId'],
	                            'TXT_MEDIADIR_ENTRY_DELETE' =>  $_ARRAYLANG['TXT_MEDIADIR_DELETE'],
	                            'TXT_MEDIADIR_ENTRY_EDIT' =>  $_ARRAYLANG['TXT_MEDIADIR_EDIT'],
	                            'TXT_MEDIADIR_ENTRY_DETAIL' =>  $_ARRAYLANG['TXT_MEDIADIR_DETAIL'],
	                            'TXT_MEDIADIR_ENTRY_CATEGORIES' =>  $_ARRAYLANG['TXT_MEDIADIR_CATEGORIES'],
	                            'TXT_MEDIADIR_ENTRY_LEVELS' =>  $_ARRAYLANG['TXT_MEDIADIR_LEVELS'],
	                        ));
	
	                        if($this->arrSettings['settingsAllowVotes']) {
	                            $objVoting = new mediaDirectoryVoting();
	
	                            if(intval($objTpl->blockExists('mediadirEntryVoteForm')) != 0) {
	                                $objVoting->getVoteForm($objTpl, $arrEntry['entryId']);
	                            }
	                            if(intval($objTpl->blockExists('mediadirEntryVotes')) != 0) {
	                                $objVoting->getVotes($objTpl, $arrEntry['entryId']);
	                            }
	                        }
	
	                        /*print_r("<pre>");
	                        print_r($this->arrSettings);
	                        print_r("</pre>");*/
	
	                        if($this->arrSettings['settingsAllowComments']) {
	                            $objComment = new mediaDirectoryComment();
	
	                            if(intval($objTpl->blockExists('mediadirEntryComments')) != 0) {
	                                $objComment->getComments($objTpl, $arrEntry['entryId']);
	                            }
	
	                            if(intval($objTpl->blockExists('mediadirEntryCommentForm')) != 0) {
	                                $objComment->getCommentForm($objTpl, $arrEntry['entryId']);
	                            }
	                        }
	
	                        if(!$this->arrSettings['settingsAllowEditEntries'] && intval($objTpl->blockExists('mediadirEntryEditLink')) != 0) {
	                            $objTpl->hideBlock('mediadirEntryEditLink');
	                        }
	
	                        if(!$this->arrSettings['settingsAllowDelEntries'] && intval($objTpl->blockExists('mediadirEntryDeleteLink')) != 0) {
	                            $objTpl->hideBlock('mediadirEntryDeleteLink');
	                        }
	
	                        $i++;
	                        $objTpl->parse('mediadirEntryList');
	                        $objTpl->clearVariables();
	                    }
                    }
                } /*else {
                    $objTpl->setVariable(array(
                        'TXT_MEDIADIR_SEARCH_MESSAGE' => $_ARRAYLANG['TXT_MEDIADIR_NO_ENTRIES_FOUND'],
                    ));

                    $objTpl->parse('mediadirNoEntriesFound');
                    $objTpl->clearVariables();
                }*/
                break;
            case 4:
                //Google Map
                $objGoogleMap = new googleMap();
                $objGoogleMap->setMapId('mediadirGoogleMap');
                $objGoogleMap->setMapStyleClass('mapLarge');
                $objGoogleMap->setMapType($this->arrSettings['settingsGoogleMapType']);

                $arrValues = explode(',', $this->arrSettings['settingsGoogleMapStartposition']);
                $objGoogleMap->setMapZoom($arrValues[2]);
                $objGoogleMap->setMapCenter($arrValues[0], $arrValues[1]);

                foreach ($this->arrEntries as $key => $arrEntry) {
                	if(($arrEntry['entryDurationStart'] < $intToday && $arrEntry['entryDurationEnd'] > $intToday) || $arrEntry['entryDurationType'] == 1) {
	                    $arrValues = array();
	
	                    if($this->checkPageCmd('detail'.intval($arrEntry['entryFormId']))) {
	                        $strDetailCmd = 'detail'.intval($arrEntry['entryFormId']);
	                    } else {
	                        $strDetailCmd = 'detail';
	                    }
	
	                    $strEntryLink = '<a href="index.php?section=mediadir&amp;cmd='.$strDetailCmd.'&amp;eid='.$arrEntry['entryId'].'">'.$_ARRAYLANG['TXT_MEDIADIR_DETAIL'].'</a>';
	                    $strEntryTitle = '<b>'.$arrEntry['entryFields']['0'].'</b>';
	                    $intEntryId = intval($arrEntry['entryId']);
	                    $intEntryFormId = intval($arrEntry['entryFormId']);
	
	                    $query = "
	                        SELECT
	                            inputfield.`id` AS `id`,
	                            rel_inputfield.`value` AS `value`
	                        FROM
	                            ".DBPREFIX."module_mediadir_inputfields AS inputfield,
	                            ".DBPREFIX."module_mediadir_rel_entry_inputfields AS rel_inputfield
	                        WHERE
	                            inputfield.`form` = '".$intEntryFormId."'
	                        AND
	                            inputfield.`type`= '15'
	                        AND
	                            rel_inputfield.`field_id` = inputfield.`id`
	                        AND
	                            rel_inputfield.`entry_id` = '".$intEntryId."'
	                        LIMIT 1
	                    ";
	
	                    $objRSMapKoordinates = $objDatabase->Execute($query);
	
	                    if($objRSMapKoordinates !== false) {
	                        $arrValues = explode(',', $objRSMapKoordinates->fields['value']);
	                    }
	
	                    $strValueLon = $arrValues[0];
	                    $strValueLat = $arrValues[1];
	                    $strValueGeoXml = $arrValues[3];
	                    $strValueClick = 'marker'.$intEntryId.'.openInfoWindowHtml(info'.$intEntryId.');';
	
	                    if(!empty($strValueGeoXml) || $strValueGeoXml != 0){
	                        $strServerProtocol = ASCMS_PROTOCOL."://";
	                        $strServerName = $_SERVER['SERVER_NAME'];
	                        $strServerKmlWebPath = ASCMS_MEDIADIR_IMAGES_WEB_PATH.'/uploads';
	
	                        $strValueGeoXmlPath = $strServerProtocol.$strServerName.$strServerKmlWebPath.$strValueGeoXml;
	                        //test only
	                        //$strValueGeoXmlPath = 'http://mapgadgets.googlepages.com/cta.kml';
	                        $strValueMouseover = 'loadGeoXml(kml'.$intEntryId.');';
	                        $strValueMouseout = 'hideGeoXml(kml'.$intEntryId.');';
	                    } else {
	                        $strValueGeoXmlPath = null;
	                        $strValueMouseover = null;
	                        $strValueMouseout = null;
	                    }
	
	                    $objGoogleMap->addMapMarker($intEntryId, $strValueLon, $strValueLat, $strEntryTitle."<br />".$strEntryLink, true, $strValueGeoXmlPath, true, $strValueClick, $strValueMouseover, $strValueMouseout);
                    }
                }

                $objTpl->setVariable(array(
                    'MEDIADIR_GOOGLE_MAP' => $objGoogleMap->getMap()
                ));

                break;
        }
    }



    function checkPageCmd($strPageCmd)
    {
        global $objDatabase, $_LANGID;

        $query = "
            SELECT
                content.`catid` AS `catid`,
                module.`id` AS `id`
            FROM
                ".DBPREFIX."content_navigation AS content,
                ".DBPREFIX."modules AS module
            WHERE
                content.`cmd` = '".contrexx_addslashes($strPageCmd)."'
            AND
                content.`lang`= '".intval($_LANGID)."'
            AND
                module.`name` = 'mediadir'
            AND
                content.`module` = module.`id`
        ";

        $objCheckPageCmd = $objDatabase->Execute($query);

        if($objCheckPageCmd !== false) {
            if($objCheckPageCmd->RecordCount() > 0){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    function saveEntry($arrData, $intEntryId=null)
    {
        global $_ARRAYLANG, $_CORELANG, $objDatabase, $_LANGID, $objInit;

        $objFWUser = FWUser::getFWUserObject();

        //get data
        $intId = intval($intEntryId);
        $intFormId = intval($arrData['formId']);
        $strCreateDate = mktime();
        $strUpdateDate = mktime();
        $intUserId = intval($objFWUser->objUser->getId());
        $strLastIp = contrexx_addslashes($_SERVER['REMOTE_ADDR']);
        
        switch($this->arrSettings['settingsEntryDisplaydurationValueType']) {
        	case 1:
        		$intDiffDay = $this->arrSettings['settingsEntryDisplaydurationValue'];
                $intDiffMonth = 0;
                $intDiffYear = 0;
        		break;
            case 2:
                $intDiffDay = 0;
                $intDiffMonth = $this->arrSettings['settingsEntryDisplaydurationValue'];
                $intDiffYear = 0;
                break;
            case 3:
                $intDiffDay = 0;
                $intDiffMonth = 0;
                $intDiffYear = $this->arrSettings['settingsEntryDisplaydurationValue'];
                break;
        }
        
        if(empty($intId)) {
            if($objInit->mode == 'backend') {
                $intConfirmed = 1;
                $intActive = 0;
                $intShowIn = 3;
                $intDurationType =  intval($arrData['durationType']);
                $arrDurationStart = explode("-",$arrData['durationStart']);
                $arrDurationEnd = explode("-",$arrData['durationEnd']);
                $intDurationStart = intval(mktime(0,0,0,$arrDurationStart[1],$arrDurationStart[2],$arrDurationStart[0]));
                $intDurationEnd =  intval(mktime(0,0,0,$arrDurationEnd[1],$arrDurationEnd[2],$arrDurationEnd[0]));
            } else {
                $intConfirmed = $this->arrSettings['settingsConfirmNewEntries'] == 1 ? 0 : 1;
                $intActive = 1;
                $intShowIn = 2;
                $intDurationType = $this->arrSettings['settingsEntryDisplaydurationType'];
                $intDurationStart = mktime();
                $intDurationEnd = mktime(0,0,0,date("m")+$intDiffMonth,date("d")+$intDiffDay,date("Y")+$intDiffYear);
            }

            $strValidateDate = $intConfirmed == 1 ? mktime() : 0;

            //insert new entry
            $objInsertEntry = $objDatabase->Execute("
                INSERT INTO
                    ".DBPREFIX."module_mediadir_entries
                SET
                    `form_id`='".$intFormId."',
                    `create_date`='".$strCreateDate."',
                    `validate_date`='".$strValidateDate."',
                    `added_by`='".$intUserId."',
                    `lang_id`='".$_LANGID."',
                    `hits`='0',
                    `last_ip`='".$strLastIp."',
                    `confirmed`='".$intConfirmed."',
                    `active`='".$intActive."',
                    `duration_type`='".$intDurationType."',
                    `duration_start`='".$intDurationStart."',
                    `duration_end`='".$intDurationEnd."',
                    `duration_notification`='0'
            ");

            if($objInsertEntry !== false) {
                $intId = $objDatabase->Insert_ID();
            } else {
                return false;
            }
        } else {
            if($objInit->mode == 'backend') {
                $intConfirmed = 1;
                $intShowIn = 3; 
                
                $arrDurationStart = explode("-",$arrData['durationStart']);
                $arrDurationEnd = explode("-",$arrData['durationEnd']);
                $intDurationStart = intval(mktime(0,0,0,$arrDurationStart[1],$arrDurationStart[2],$arrDurationStart[0]));
                $intDurationEnd =  intval(mktime(0,0,0,$arrDurationEnd[1],$arrDurationEnd[2],$arrDurationEnd[0]));
                
                $arrAdditionalQuery[] = "`duration_type`='". intval($arrData['durationType'])."', `duration_start`='". intval($intDurationStart)."',  `duration_end`='". intval($intDurationEnd)."'";
            } else {
                $intConfirmed = $this->arrSettings['settingsConfirmUpdatedEntries'] == 1 ? 0 : 1;
                $intShowIn = 2;
                $arrAdditionalQuery = null;
            }
            
            $arrAdditionalQuery[] = " `updated_by`='".$intUserId."'";
            
            if(intval($arrData['userId']) != 0) {
                $arrAdditionalQuery[] = "`added_by`='".intval($arrData['userId'])."'";
            } 
            
            if(intval($arrData['durationResetNotification']) == 1) {
                $arrAdditionalQuery[] = "`duration_notification`='0'";
            } 
            
            $strAdditionalQuery = join(",", $arrAdditionalQuery);
            $strValidateDate = $intConfirmed == 1 ? mktime() : 0;
            
            $objUpdateEntry = $objDatabase->Execute("
                UPDATE
                    ".DBPREFIX."module_mediadir_entries
                SET
                    `update_date`='".$strUpdateDate."',
                    `validate_date`='".$strValidateDate."',
                    ".$strAdditionalQuery."
                WHERE
                    `id`='".$intId."'
            ");
            
            if($objUpdateEntry !== false) {
            	$objShowIn = $objDatabase->Execute("SELECT inputfield.`id` AS `id` FROM ".DBPREFIX."module_mediadir_inputfields AS inputfield WHERE (inputfield.`type` != 16 AND inputfield.`type` != 17) AND (inputfield.`show_in` = '".$intShowIn."' OR inputfield.`show_in` = '1')");
            	if($objShowIn !== false) {
                    while (!$objShowIn->EOF) {
                    	$arrDeletableIds[] = $objShowIn->fields['id'];
                    	$objShowIn->MoveNext();
                    }
            	}
            	
                $objDeleteNames = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_mediadir_rel_entry_inputfields WHERE entry_id='".$intId."' AND field_id IN (".join(",",$arrDeletableIds).")");
                if($objDeleteNames !== false) {
                    $objDeleteCategories = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_mediadir_rel_entry_categories WHERE entry_id='".$intId."'");
                    if($objDeleteCategories !== false) {
                        $objDeleteLevels = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_mediadir_rel_entry_levels WHERE entry_id='".$intId."'");
                        if($objDeleteLevels === false) {
                            return false;
                        }
                    } else {
                       return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        foreach ($this->getInputfields() as $key => $arrInputfield) {
            if($arrInputfield['id'] != 1 && $arrInputfield['id'] != 2 && $arrInputfield['type'] != 16 && $arrInputfield['type'] != 18 && ($arrInputfield['show_in'] == $intShowIn || $arrInputfield['show_in'] == 1)) {
                if(!empty($arrData['mediadirInputfield'][$arrInputfield['id']]) && $arrData['mediadirInputfield'][$arrInputfield['id']] != $arrInputfield['default_value'][$_LANGID]) {
                    $strType = $arrInputfield['type_name'];
                    $strInputfieldClass = "mediaDirectoryInputfield".ucfirst($strType);

                    try {
                        $objInputfield = safeNew($strInputfieldClass);
                        if($arrInputfield['type_multi_lang'] == 1) {
                            foreach ($this->arrFrontendLanguages as $key => $arrLang) {
                                $intLangId = $arrLang['id'];
                                
                                if((empty($arrData['mediadirInputfield'][$arrInputfield['id']][$intLangId])) || $intLangId == $_LANGID) {
									$strMaster = $arrData['mediadirInputfield'][$arrInputfield['id']][0];
									$strOldDefault = $arrData['mediadirInputfield'][$arrInputfield['id']]['old'];
									$strNewDefault = $arrData['mediadirInputfield'][$arrInputfield['id']][$_LANGID];
									
									if($strNewDefault != $strMaster) {
										if($strMaster != $strOldDefault && $strNewDefault == $strOldDefault) {
											$strDefault = $strMaster;
										} else {
											$strDefault = $strNewDefault;
										}
									} else {
										$strDefault = $arrData['mediadirInputfield'][$arrInputfield['id']][$_LANGID];
									}
									
									$strInputfieldValue = $objInputfield->saveInputfield($arrInputfield['id'], $strDefault);
                                } else {
                                    $strInputfieldValue = $objInputfield->saveInputfield($arrInputfield['id'], $arrData['mediadirInputfield'][$arrInputfield['id']][$intLangId]);
                                }

                                $objInsertInputfieldData = $objDatabase->Execute("
                                    INSERT INTO
                                        ".DBPREFIX."module_mediadir_rel_entry_inputfields
                                    SET
                                        `entry_id`='".intval($intId)."',
                                        `lang_id`='".intval($intLangId)."',
                                        `form_id`='".intval($intFormId)."',
                                        `field_id`='".intval($arrInputfield['id'])."',
                                        `value`='".$strInputfieldValue."'
                                    ");

                                if($objInsertInputfieldData === false) {
                                    return false;
                                }
                            }
                        } else {
                            $strInputfieldValue = $objInputfield->saveInputfield($arrInputfield['id'], $arrData['mediadirInputfield'][$arrInputfield['id']]);

                            $objInsertInputfieldData = $objDatabase->Execute("
                                INSERT INTO
                                    ".DBPREFIX."module_mediadir_rel_entry_inputfields
                                SET
                                    `entry_id`='".intval($intId)."',
                                    `lang_id`='".intval($_LANGID)."',
                                    `form_id`='".intval($intFormId)."',
                                    `field_id`='".intval($arrInputfield['id'])."',
                                    `value`='".$strInputfieldValue."'
                                ");

                            if($objInsertInputfieldData === false) {
                                return false;
                            }
                        }
                    } catch (Exception $e) {
                        echo "Error: ".$e->getMessage();
                    }
                }
            } else {
                if($arrInputfield['id'] == 2) {
                    if($this->arrSettings['settingsShowLevels'] == 1) {
                        foreach ($arrData['selectedLevels'] as $key => $intLevelId) {
                            $objInsertLevel = $objDatabase->Execute("
                            INSERT INTO
                                ".DBPREFIX."module_mediadir_rel_entry_levels
                            SET
                                `entry_id`='".intval($intId)."',
                                `level_id`='".intval($intLevelId)."'
                            ");

                            if($objInsertLevel === false) {
                                return false;
                            }
                        }
                    }
                } else if ($arrInputfield['id'] == 1) {
                    foreach ($arrData['selectedCategories'] as $key => $intCategoryId) {
                        $objInsertCategory = $objDatabase->Execute("
                        INSERT INTO
                            ".DBPREFIX."module_mediadir_rel_entry_categories
                        SET
                            `entry_id`='".intval($intId)."',
                            `category_id`='".intval($intCategoryId)."'
                        ");

                        if($objInsertCategory === false) {
                            return false;
                        }
                    }
                }
            }
        }
        
        if(empty($intEntryId)) {
            $objMail = new mediaDirectoryMail(1, $intId);
            $objMail = new mediaDirectoryMail(2, $intId);
        } else {
            $objMail = new mediaDirectoryMail(6, $intId);
        }
        
        return true;
    }



    function deleteEntry($intEntryId)
    {
        global $_ARRAYLANG, $_CORELANG, $objDatabase;

        $objMail = new mediaDirectoryMail(5, $intEntryId);

        //delete entry
        $objDeleteEntry = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_mediadir_entries WHERE `id`='".intval($intEntryId)."'");

        if($objDeleteEntry !== false) {
            //delete inputfields
            foreach ($this->getInputfields() as $key => $arrInputfield) {
                if($arrInputfield['id'] != 1 && $arrInputfield['id'] != 2) {

                    $strType = $arrInputfield['type_name'];
                    $strInputfieldClass = "mediaDirectoryInputfield".ucfirst($strType);

                    try {
                        $objInputfield = safeNew($strInputfieldClass);

                        if(!$objInputfield->deleteContent(intval($intEntryId), intval($arrInputfield['id']))) {
                            return false;
                        }
                    } catch (Exception $e) {
                        echo "Error: ".$e->getMessage();
                    }
                }
            }

            //delete categories
            $objDeleteCategories = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_mediadir_rel_entry_categories WHERE `entry_id`='".intval($intEntryId)."'");

            //delete levels
            $objDeleteLevels = $objDatabase->Execute("DELETE FROM ".DBPREFIX."module_mediadir_rel_entry_levels WHERE `entry_id`='".intval($intEntryId)."'");
        } else {
            return false;
        }

        return true;
    }



    function confirmEntry($intEntryId)
    {
        global $_ARRAYLANG, $_CORELANG, $objDatabase;

        $objConfirmEntry = $objDatabase->Execute("
            UPDATE
                ".DBPREFIX."module_mediadir_entries
            SET
                `confirmed`='1',
                `active`='1',
                `validate_date`='".mktime()."'
            WHERE
                `id`='".intval($intEntryId)."'
        ");

        if($objConfirmEntry !== false) {
            $objMail = new mediaDirectoryMail(3, $intEntryId);
            return true;
        } else {
           return false;
        }
    }



    function updateHits($intEntryId)
    {
        global $_ARRAYLANG, $_CORELANG, $objDatabase;

        $intHits        = intval($this->arrEntries[intval($intEntryId)]['entryHits']);
        $intPopularHits = intval($this->arrEntries[intval($intEntryId)]['entryPopularHits']);
        $strPopularDate = $this->arrEntries[intval($intEntryId)]['entryPopularDate'];
        $intPopularDays = intval($this->arrSettings['settingsPopularNumRestore']);
        $strLastIp      = $this->arrEntries[intval($intEntryId)]['entryLastIp'];
        $strNewIp       = contrexx_addslashes($_SERVER['REMOTE_ADDR']);

        $strToday  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
        $tempDays  = date("d",$strPopularDate);
        $tempMonth = date("m",$strPopularDate);
        $tempYear  = date("Y",$strPopularDate);

        $strPopularEndDate  = mktime(0, 0, 0, $tempMonth, $tempDays+$intPopularDays,  $tempYear);

        if ($strLastIp != $strNewIp) {
            if ($strToday >= $strPopularEndDate) {
                $strNewPopularDate  = $strToday;
                $intPopularHits     = 1;
            } else {
                $strNewPopularDate  = $strPopularDate;
                $intPopularHits++;
            }

            $intHits++;

            $objResult = $objDatabase->Execute("UPDATE
                                                    ".DBPREFIX."module_mediadir_entries
                                                SET
                                                    hits='".$intHits."',
                                                    popular_hits='".$intPopularHits."',
                                                    popular_date='".$strNewPopularDate."',
                                                    last_ip='".$strNewIp."'
                                                WHERE
                                                    id='".intval($intEntryId)."'
                                               ");
        }
    }



    function countEntries($intCategoryId, $intLevelId)
    {
        global $objDatabase;

        if(!empty($intLevelId)) {
            $strWhereLevel = "AND ((level.`level_id` = ".$intLevelId.") AND (level.`entry_id` = entry.`id`)) ";
            $strFromLevel = " ,".DBPREFIX."module_mediadir_rel_entry_levels AS level";
        }

        if(!empty($intCategoryId)) {
            $strWhereCategory = "AND ((category.`category_id` = ".$intCategoryId.") AND (category.`entry_id` = entry.`id`)) ";
            $strFromCategory = " ,".DBPREFIX."module_mediadir_rel_entry_categories AS category";
        }

        $query = "SELECT
                    entry.`id` AS `id`
                  FROM
                    ".DBPREFIX."module_mediadir_entries AS entry
                    ".$strFromCategory."
                    ".$strFromLevel."
                  WHERE
                    (entry.`id` != 0)
                  AND 
                    (entry.`active` = 1)
                    ".$strWhereCategory."
                    ".$strWhereLevel."
                  GROUP BY
                    entry.`id`";

        $objNumEntries = $objDatabase->Execute($query);
        $intNumEntries = $objNumEntries->RecordCount();

        return intval($intNumEntries);
    }
    
    
    
    function getUsers($intEntryId=null)
    {
        global $objDatabase;
        
        $strDropdownUsers = '<select name="userId"style="width: 302px">';
        $objFWUser = FWUser::getFWUserObject();
        
		if ($objUser = $objFWUser->objUser->getUsers()) {
	        while (!$objUser->EOF) {
	        	if(intval($objUser->getID()) == intval($this->arrEntries[$intEntryId]['entryAddedBy'])) {
                    $strSelected = 'selected="selected"';
                } else {
                    $strSelected = '';
                }
                
	        	$strDropdownUsers .= '<option value="'.intval($objUser->getID()).'" '.$strSelected.' >'.$objUser->getUsername().'</option>';
                $objUser->next();
	        }
		}
        
        $strDropdownUsers .= '</select>';

        return $strDropdownUsers;
    }
    
    
    
    function getCategoriesLevels($intType, $intEntryId=null)
    {
        global $objDatabase, $_LANGID;
        
        if($intType == 1) {
        	//categories
        	$query = "SELECT
                    cat_rel.`category_id` AS `elm_id`,
                    cat_name.`category_name` AS `elm_name`
                  FROM
                    ".DBPREFIX."module_mediadir_rel_entry_categories AS cat_rel,
                    ".DBPREFIX."module_mediadir_categories_names AS cat_name
                  WHERE
                    cat_rel.`category_id` = cat_name.`category_id`
                  AND
                    cat_rel.`entry_id` = '".intval($intEntryId)."'
                  AND
                    cat_name.`lang_id` = '".intval($_LANGID)."'
                  ORDER BY
                    cat_name.`category_name` ASC
                  ";
        } else {
        	//levels
            $query = "SELECT
                    level_rel.`level_id` AS `elm_id`,
                    level_name.`level_name` AS `elm_name`
                  FROM
                    ".DBPREFIX."module_mediadir_rel_entry_levels AS level_rel,
                    ".DBPREFIX."module_mediadir_level_names AS level_name
                  WHERE
                    level_rel.`level_id` = level_name.`level_id`
                  AND
                    level_rel.`entry_id` = '".intval($intEntryId)."'
                  AND
                    level_name.`lang_id` = '".intval($_LANGID)."'
                  ORDER BY
                    level_name.`level_name` ASC
                  ";
        }
        
        $objEntryCategoriesLevels = $objDatabase->Execute($query);
        
        if ($objEntryCategoriesLevels !== false) {
        	$strList = '<ul>';
            while (!$objEntryCategoriesLevels->EOF) {
            	$strList .= '<li>'.htmlspecialchars($objEntryCategoriesLevels->fields['elm_name'], ENT_QUOTES, CONTREXX_CHARSET).'</li>';
                $objEntryCategoriesLevels->MoveNext();
            }
            $strList .= '</ul>';
        }
        
        return $strList;
    }
    
    
    function setDisplaydurationNotificationStatus($intEntryId, $bolStatus)
    {
        global $objDatabase;

        $objResult = $objDatabase->Execute("UPDATE ".DBPREFIX."module_mediadir_entries SET duration_notification='".intval($bolStatus)."' WHERE id='".intval($intEntryId)."'");
    }
}
?>
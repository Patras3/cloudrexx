<?php

/**
 * Shop Country class
 * @version     2.1.0
 * @since       2.1.0
 * @package     contrexx
 * @subpackage  module_shop
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @todo        Test!
 * @todo        To be unified with the core Country class
 */

/**
 * Country helper methods
 * @version     2.1.0
 * @since       2.1.0
 * @package     contrexx
 * @subpackage  module_shop
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @todo        Test!
 * @todo        To be unified with the core Country class
 */
class Country
{
    /**
     * Array of all countries
     * @var     array
     * @access  private
     * @see     initCountries()
     */
    private static $arrCountries = false;

    /**
     * Array of all country-zone relations
     * @var     array
     * @access  private
     * @see     initCountryRelations()
     */
    private static $arrCountryRelations = false;


    /**
     * Initialise the static array with all countries from the database
     *
     * Note that the Countries are always shown in the selected
     * frontend language.
     * @global  ADONewConnection  $objDatabase
     * @return  boolean                     True on success, false otherwise
     */
    function initCountries()
    {
        global $objDatabase;

        $arrSqlName = Text::getSqlSnippets(
            '`country`.`text_name_id`', FRONTEND_LANG_ID,
            MODULE_ID, TEXT_SHOP_COUNTRY_NAME
        );
        $query = "
            SELECT `country`.`id`, `country`.`status`,
                   `country`.`iso_code_2`, `country`.`iso_code_3`".
                   $arrSqlName['field']."
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_countries AS `country`".
                   $arrSqlName['join']."
             ORDER BY `country`.`id` ASC
        ";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        while (!$objResult->EOF) {
            $id = $objResult->fields['id'];
            $text_name_id = $objResult->fields[$arrSqlName['name']];
            $strName = $objResult->fields[$arrSqlName['text']];
            if ($strName === null) {
                $objText = Text::getById($text_name_id, 0);
                if ($objText) $strName = $objText->getText();
            }
            self::$arrCountries[$id] = array(
                'id' => $id,
                'name' => $strName,
                'text_name_id' => $text_name_id,
                'iso_code_2' => $objResult->fields['iso_code_2'],
                'iso_code_3' => $objResult->fields['iso_code_3'],
                'status' => $objResult->fields['status']
            );
            $objResult->MoveNext();
        }
        return true;
    }


    /**
     * Initialise the static array with all country relations from the database
     * @global  ADONewConnection  $objDatabase
     * @return  boolean                 True on success, false otherwise
     */
    function initCountryRelations()
    {
        global $objDatabase;

        $query = "
            SELECT zone_id, country_id
              FROM ".DBPREFIX."module_shop".MODULE_INDEX."_rel_countries
             ORDER BY id ASC
        ";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        while (!$objResult->EOF) {
            self::$arrCountryRelations[] = array(
                'zone_id'    => $objResult->fields['zone_id'],
                'country_id' => $objResult->fields['country_id']);
            $objResult->MoveNext();
        }
        return true;
    }


    /**
     * Returns the name of the country selected by its ID
     * @param   integer   $country_id     The country ID
     * @return  string                    The country name
     * @static
     */
    static function getNameById($country_id)
    {
        if (empty(self::$arrCountries)) self::initCountries();
        return self::$arrCountries[$country_id]['name'];
    }


    /**
     * Returns the HTML dropdown menu code for the active countries.
     * @param   string  $menuName   Optional name of the menu,
     *                              defaults to "countryId"
     * @param   string  $selectedId Optional preselected country ID
     * @param   string  $onchange   Optional onchange callback function
     * @return  string              The HTML dropdown menu code
     * @static
     */
    static function getMenu($menuName='countryId', $selectedId='', $onchange='')
    {
        $strMenu =
            '<select name="'.$menuName.'" '.
            ($onchange ? ' onchange="'.$onchange.'"' : '').">\n".
            self::getCountryMenuoptions($selectedId).
            "</select>\n";
        return $strMenu;
    }


    /**
     * Returns the HTML code for the countries dropdown menu options
     * @param   string  $selectedId   Optional preselected country ID
     * @param   boolean $flagActiveonly   If true, only active countries
     *                                are added to the options, all otherwise.
     * @return  string                The HTML dropdown menu options code
     * @static
     */
    static function getMenuoptions($selected_id=0, $flagActiveonly=true)
    {
        static $strMenuoptions = '';
        static $last_selected_id = 0;

        if (empty(self::$arrCountries)) self::initCountries();
        if ($strMenuoptions && $last_selected_id == $selected_id)
            return $strMenuoptions;
        if (empty(self::$arrCountries)) self::initCountries();
        foreach (self::$arrCountries as $id => $arrCountry) {
            if (   $flagActiveonly
                && empty($arrCountry['status'])) continue;
            $strMenuoptions .=
                '<option value="'.$id.'"'.
                ($selected_id == $id ? ' selected="selected"' : '').'>'.
                $arrCountry['name']."</option>\n";
        }
        $last_selected_id = $selected_id;
        return $strMenuoptions;
    }


    /**
     * Returns an array of two arrays; one with countries in the given zone,
     * the other with the remaining countries.
     *
     * The array looks like this:
     *  array(
     *    'in' => array(    // Countries in the zone
     *      country ID => array(
     *        'id' => country ID,
     *        'name' => country name,
     *        'text_name_id' => country name Text ID,
     *      ),
     *      ... more ...
     *    ),
     *    'out' => array(   // Countries not in the zone
     *      country ID => array(
     *        'id' => country ID,
     *        'name' => country name,
     *        'text_name_id' => country name Text ID,
     *      ),
     *      ... more ...
     *    ),
     *  );
     * @param   integer     $zone_id        The zone ID
     * @return  array                       Countries array, as described above
     */
    static function getArraysByZoneId($zone_id)
    {
        global $objDatabase;

        // Query relations between zones and countries:
        // Get all country IDs and names
        // associated with that zone ID
        $arrSqlName = Text::getSqlSnippets(
            '`country`.`text_name_id`', FRONTEND_LANG_ID,
            MODULE_ID, TEXT_SHOP_COUNTRY_NAME
        );
        $query = "
            SELECT `country`.`id`, `relation`.`country_id`".
                   $arrSqlName['field']."
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_countries` AS `country`".
                   $arrSqlName['join']."
              LEFT JOIN `".DBPREFIX."module_shop".MODULE_INDEX."_rel_countries` AS `relation`
                ON `country`.`id`=`relation`.`country_id`
               AND `relation`.`zone_id`=$zone_id
             WHERE `country`.`status`=1
             ORDER BY ".$arrSqlName['text']." ASC
        ";
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return false;
        $arrCountries = array();
        while (!$objResult->EOF) {
            $id = $objResult->fields['id'];
            $strName = $objResult->fields[$arrSqlName['text']];
            $text_name_id = $objResult->fields[$arrSqlName['name']];
            if ($strName === null) {
                $objText = Text::getById($text_name_id, 0);
                if ($objText) $strName = $objText->getText();
            }
            $flagInZone =
                ($objResult->fields['country_id'] === null
                    ? 'out' : 'in'
                );
echo("Country::getArraysByZoneId($zone_id): Country ID $id, name /$strName/, relation is $flagInZone<br />");
            $arrCountries[$flagInZone][$id] = array(
                'id' => $id,
                'name' => $strName,
                'text_name_id' => $text_name_id,
            );
            $objResult->MoveNext();
        }
        return $arrCountries;
    }

}

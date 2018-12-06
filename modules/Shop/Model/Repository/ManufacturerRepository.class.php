<?php

namespace Cx\Modules\Shop\Model\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ManufacturerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ManufacturerRepository extends EntityRepository
{
    /**
     * Text keys
     */
    const TEXT_NAME = 'manufacturer_name';
    const TEXT_URI  = 'manufacturer_uri';

    /**
     * Static class data with the manufacturers
     * @var   array
     */
    private $arrManufacturer = null;

    /**
     * Initialise the Manufacturer array
     *
     * Uses the FRONTEND_LANG_ID constant to determine the language.
     * The array has the form
     *  array(
     *    'id' => Manufacturer ID,
     *    'name' => Manufacturer name,
     *    'url' => Manufacturer URI,
     *  )
     * @static
     * @param   string            $order      The optional sorting order.
     *                                        Defaults to null (unsorted)
     * @return  boolean                       True on success, false otherwise
     * @global  ADONewConnection  $objDatabase
     * @global  array             $_ARRAYLANG
     * @todo    Order the Manufacturers by their name
     */
    protected function init($order=null)
    {
        global $objDatabase;

        $arrSql = \Text::getSqlSnippets('`manufacturer`.`id`',
            FRONTEND_LANG_ID, 'Shop',
            array('name' => self::TEXT_NAME, 'url' => self::TEXT_URI));
        $query = "
            SELECT `manufacturer`.`id`, ".
            $arrSql['field']."
              FROM `".DBPREFIX."module_shop".MODULE_INDEX."_manufacturer` AS `manufacturer`".
            $arrSql['join'].
            ($order ? " ORDER BY $order" : '');
        $objResult = $objDatabase->Execute($query);
        if (!$objResult) return $this->errorHandler();
        $this->arrManufacturer = array();
        while (!$objResult->EOF) {
            $id = $objResult->fields['id'];
            $strName = $objResult->fields['name'];
            // Replace Text in a missing language by another, if available
            if ($strName === null) {
                $strName = \Text::getById(
                    $id, 'Shop', self::TEXT_NAME)->content();
            }
            $strUrl = $objResult->fields['url'];
            if ($strUrl === null) {
                $strUrl = \Text::getById(
                    $id, 'Shop', self::TEXT_URI)->content();
            }
            $this->arrManufacturer[$id] = array(
                'id' => $id,
                'name' => $strName,
                'url' => $strUrl,
            );
            $objResult->MoveNext();
        }
        return true;
    }

    /**
     * Get the Manufacturer dropdown menu HTML code string.
     *
     * Used in the Product search form, see {@link products()}.
     * @static
     * @param   string  $menu_name      The optional menu name.  Defaults to
     *                                  manufacturer_id
     * @param   integer $selected_id    The optional preselected Manufacturer ID
     * @param   boolean $include_none   If true, a dummy option for "none" is
     *                                  included at the top
     * @return  string                  The Manufacturer dropdown menu HTML code
     * @global  ADONewConnection
     * @global  array
     */
    public function getMenu(
        $menu_name='manufacturerId', $selected_id=0, $include_none=false
    ) {
//DBG::log("Manufacturer::getMenu($selected_id): Manufacturers: ".var_export(self::$arrManufacturer, true));
        return \Html::getSelectCustom(
            $menu_name, self::getMenuoptions($selected_id, $include_none));
    }

    /**
     * Returns the Manufacturer HTML dropdown menu options code
     *
     * Used in the Product search form, see {@link products()}.
     * @static
     * @param   integer $selected_id    The optional preselected Manufacturer ID
     * @param   boolean $include_none   If true, a dummy option for "none" is
     *                                  included at the top
     * @return  string                  The Manufacturer dropdown menu options
     * @global  ADONewConnection  $objDatabase
     */
    public function getMenuoptions($selected_id=0, $include_none=false)
    {
        global $_ARRAYLANG;

        return
            ($include_none
                ? '<option value="0">'.
                $_ARRAYLANG['TXT_SHOP_MANUFACTURER_ALL'].
                '</option>'
                : '').
            \Html::getOptions($this->getNameArray(), $selected_id);
    }

    /**
     * Returns the array of Manufacturer names
     *
     * Call this only *after* updating the database, or the static
     * array in here will be outdated.
     * database table.
     * @return  array               The Manufacturer name array
     */
    protected function getNameArray()
    {
        static $arrManufacturerName = null;
        if (is_null($arrManufacturerName)) {
            $arrManufacturerName = array();
            $count = 0;
            foreach ($this->getArray($count, '`name` ASC', 0, 1000)
                     as $id => $arrManufacturer) {
                $arrManufacturerName[$id] = $arrManufacturer['name'];
            }
        }
        return $arrManufacturerName;
    }

    /**
     * Returns an array of Manufacturers
     *
     * The $filter parameter is unused, as this functionality is not
     * implemented yet.
     * Note that you *SHOULD* re-init() the array after changing the
     * database table.
     * See {@link init()} for details on the array.
     * @param   integer   $count    The count, by reference
     * @param   string    $order    The optional sorting order.
     *                              Defaults to null
     * @param   integer   $offset   The optional record offset.
     *                              Defaults to 0 (zero)
     * @param   integer   $limit    The optional record count limit
     *                              Defaults to null (all records)
     * @return  array               The Manufacturer array on success,
     *                              null otherwise
     * //@param   array     $filter   NOT IMPLEMENTED: The optional filter array.
     * //                             Defaults to null
     * @todo    Implement the filter
     */
    protected function getArray(&$count, $order=null, $offset=0, $limit=null)//, $filter=null)
    {
//        $filter; // Shut up the code analyzer
        if (is_null($this->arrManufacturer)) self::init($order);
        $count = count($this->arrManufacturer);
        return array_slice($this->arrManufacturer, $offset, $limit, true);
    }

    /**
     * Returns the name of the Manufacturer with the given ID
     * @static
     * @param   integer $id             The Manufacturer ID
     * @return  string                  The Manufacturer name on success,
     *                                  or the empty string on failure
     * @global  ADONewConnection
     * @todo    Move this to the Manufacturer class!
     */
    public function getNameById($id)
    {
        if (is_null($this->arrManufacturer)) $this->init();
        if (isset($this->arrManufacturer[$id]))
            return $this->arrManufacturer[$id]['name'];
        return '';
    }

    /**
     * Returns the URL of the Manufacturers for the given ID
     * @static
     * @param   integer $id             The Manufacturer ID
     * @return  string                  The Manufacturer URL on success,
     *                                  or the empty string on failure
     * @global  ADONewConnection
     * @todo    Move this to the Manufacturer class!
     */
    public function getUrlById($id)
    {
        if (is_null($this->arrManufacturer)) $this->init();
        if (isset($this->arrManufacturer[$id]))
            return $this->arrManufacturer[$id]['url'];
        return '';
    }

    /**
     * Handles database errors
     *
     * Also migrates the old Manufacturers to the new structure
     * @return  boolean             False.  Always.
     * @throws  Cx\Lib\Update_DatabaseException
     */
    protected function errorHandler()
    {
// Manufacturer
        // Fix the Text table first
        \Text::errorHandler();

        $table_name = DBPREFIX.'module_shop_manufacturer';
        // Note:  As this table uses a single column, the primary key will
        // have to be added separately below.  Otherwise, UpdateUtil::table()
        // will drop the id column first, then try to drop all the others,
        // which obviously won't work.
        // In that context, the "AUTO_INCREMENT" has to be dropped as well,
        // for that only applies to a primary key column.
        $table_structure = array(
            'id' => array('type' => 'INT(10)', 'unsigned' => true),
        );
        $table_index = array();
        $default_lang_id = \FWLanguage::getDefaultLangId();
        if (   \Cx\Lib\UpdateUtil::table_exist($table_name)
            && \Cx\Lib\UpdateUtil::column_exist($table_name, 'name')) {
            // Get rid of bodies
            \Text::deleteByKey('Shop', self::TEXT_NAME);
            \Text::deleteByKey('Shop', self::TEXT_URI);
            // Migrate all Manufacturer text fields to the Text table
            $query = "
                SELECT `id`, `name`, `url`
                  FROM `".DBPREFIX."module_shop_manufacturer`";
            $objResult = \Cx\Lib\UpdateUtil::sql($query);
            while (!$objResult->EOF) {
                $id = $objResult->fields['id'];
                $name = $objResult->fields['name'];
                $uri = $objResult->fields['url'];
                if (!\Text::replace($id, $default_lang_id, 'Shop',
                    self::TEXT_NAME, $name)) {
                    throw new \Cx\Lib\Update_DatabaseException(
                        "Failed to migrate Manufacturer name '$name'");
                }
                if (!\Text::replace($id, $default_lang_id, 'Shop',
                    self::TEXT_URI, $uri)) {
                    throw new \Cx\Lib\Update_DatabaseException(
                        "Failed to migrate Manufacturer URI '$uri'");
                }
                $objResult->MoveNext();
            }
        }
        \Cx\Lib\UpdateUtil::table($table_name, $table_structure, $table_index);
        \Cx\Lib\UpdateUtil::sql("
            ALTER TABLE `$table_name`
              ADD PRIMARY KEY (`id`)");
        \Cx\Lib\UpdateUtil::sql("
            ALTER TABLE `$table_name`
           CHANGE `id` `id` int(10) unsigned NOT NULL AUTO_INCREMENT");

        // Always
        return false;
    }
}

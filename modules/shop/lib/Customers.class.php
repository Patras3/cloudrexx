<?php

/**
 * Shop Customer
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     2.1.0
 * @package     contrexx
 * @subpackage  module_shop
 * @todo        Test!
 */

/**
 * Customer as used in the Shop.
 *
 * Includes access methods and data layer.
 * Do not, I repeat, do not access private fields, or even try
 * to access the database directly!
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Reto Kohli <reto.kohli@comvation.com>
 * @version     2.1.0
 * @package     contrexx
 * @subpackage  module_shop
 */
class Customers
{
    /**
     * Create a Customers helper object (PHP5)
     */
    function __construct()
    {
    }


    /**
     * Returns a string with HTML code for the Customer type
     * dropdown menu options
     * @param   integer     $selected   The optional preselected type
     * @return  string                  The Menuoptions HTML code
     * @static
     */
    static function getCustomerTypeMenuoptions($selected=-1)
    {
        global $_ARRAYLANG;

        $arrType = array(
            -1 => '--&nbsp;'.$_ARRAYLANG['TXT_CUSTOMER_TYP'].'&nbsp;--',
             0 => $_ARRAYLANG['TXT_CUSTOMER'],
             1 => $_ARRAYLANG['TXT_RESELLER'],
        );
        return Html::getOptions($arrType, $selected);
    }


    /**
     * Returns a string with HTML code for the Customer status
     * dropdown menu options
     * @param   integer     $selected   The optional preselected status
     * @return  string                  The Menuoptions HTML code
     * @static
     */
    static function getCustomerStatusMenuoptions($selected)
    {
        global $_ARRAYLANG;

        $arrStatus = array(
            -1 => '--&nbsp;'.$_ARRAYLANG['TXT_STATUS'].'&nbsp;--',
             0 => $_ARRAYLANG['TXT_INACTIVE'],
             1 => $_ARRAYLANG['TXT_ACTIVE'],
        );
        return Html::getOptions($arrStatus, $selected);
    }


    /**
     * Returns a string with HTML code for the Customer sorting
     * dropdown menu options
     * @param   integer     $selected   The optional preselected order
     * @return  string                  The Menuoptions HTML code
     */
    function getCustomerSortMenuoptions($selected='customerid')
    {
        global $_ARRAYLANG;

        $arrField = array(
// TODO: The customer ID cannot be used for the time being, as there may be
// a name conflict in the resulting query.
// See the customer overview code in the backend.
//            'customerid' => $_ARRAYLANG['TXT_SHOP_ID'],
            'lastname'   => $_ARRAYLANG['TXT_LAST_NAME'],
            'firstname'  => $_ARRAYLANG['TXT_FIRST_NAME'],
            'company'    => $_ARRAYLANG['TXT_COMPANY'],
        );
        return Html::getOptions($arrField, $selected);
    }

}

?>

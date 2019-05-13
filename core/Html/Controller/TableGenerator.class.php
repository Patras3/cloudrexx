<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2015
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Cloudrexx" is a registered trademark of Cloudrexx AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_html
 */

namespace Cx\Core\Html\Controller;

/**
 * Creates a table view
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      CLOUDREXX Development Team <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  core_html
 */
class TableGenerator extends \BackendTable
{
    /**
     * Overwrites the constructor to convert the $attr array into a dataset to
     * flip it and show the attributes besides the values.
     *
     * @param $attrs   array attributes and values
     * @param $options array options for view generator
     * @param boolean $readOnly if view is only readable
     */
    public function __construct($attrs = array(), $options = array(), $readOnly = false)
    {
        global $_ARRAYLANG;

        // Rename Key Fields
        foreach ($attrs as $rowname=>$row) {
            $newRowName = $rowname;
            if (isset($_ARRAYLANG[$rowname])) {
                $newRowName = $_ARRAYLANG[$rowname];
            }

            if (
                isset($options['fields'][$rowname]['show']['show']) &&
                !$options['fields'][$rowname]['show']['show']
            ) {
                continue;
            }

            if (
                $readOnly &&
                isset($options['fields']) &&
                isset($options['fields'][$rowname]) &&
                isset($options['fields'][$rowname]['show']) &&
                isset($options['fields'][$rowname]['show']['parse'])
            ) {
                $callback = $options['fields'][$rowname]['show']['parse'];
                if (
                    is_array($callback) &&
                    isset($callback['adapter']) &&
                    isset($callback['method'])
                ) {
                    $json = new \Cx\Core\Json\JsonData();
                    $jsonResult = $json->data(
                        $callback['adapter'],
                        $callback['method'],
                        array(
                            'value' => $row,
                            'entity' => $attrs,
                            'options' => $options['fields'][$rowname],
                        )
                    );
                    if ($jsonResult['status'] == 'success') {
                        $data = $jsonResult["data"];
                    }
                } else if (is_callable($callback)) {
                    $data = $callback(
                        $row,
                        $attrs,
                        $options['fields'][$rowname]
                    );
                }
            } else {
                $data = $row;
            }
            if (!empty($options['fields'][$rowname]['show']['encode'])) {
                // 1->n & n->n relations
                $displayedRelationsLimit = 3;
                if (
                    is_object($data) &&
                    $data instanceof \Doctrine\ORM\PersistentCollection
                ) {
                    // EXTRA_LAZY fetched can be sliced (results in a LIMIT)
                    $data = $data->slice(0, $displayedRelationsLimit + 1);
                }
                if (is_array($data)) {
                    if (count($data) > $displayedRelationsLimit) {
                        $data = array_slice($data, 0, $displayedRelationsLimit);
                        $data[] = '...';
                    }
                    $data = implode(', ', $data);
                }
                //replaces curly brackets, so they get not parsed with the sigma
                // engine
                $data = preg_replace(
                    array("/{/","/}/"),
                    array("&#123;","&#125;"),
                    contrexx_raw2xhtml($data),
                    -1
                );
            }

            $rows[$newRowName] = $data;
        }

        $data = new \Cx\Core_Modules\Listing\Model\Entity\DataSet(
            array('key' => array_keys($rows), 'value' => array_values($rows))
        );
        $options['fields']['key']['sorting'] = false;
        $options['fields']['value']['sorting'] = false;
        $options['functions']['add'] = false;
        $options['functions']['edit'] = false;
        $options['functions']['delete'] = false;
        $options['functions']['show'] = false;
        unset($options['multiActions']);
        unset($options['tabs']);

        $data = $data->flip();

        parent::__construct($data, $options, true, null, $readOnly);
    }
}

<?php
/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2018
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
 * PricelistController to handle pricelists
 *
 * @copyright   Cloudrexx AG
 * @author      Sam Hawkes <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_shop
 */


namespace Cx\Modules\Shop\Controller;


class PricelistController extends \Cx\Core\Core\Model\Entity\Controller
{
    /**
     * @return \Cx\Core\Html\Model\Entity\HtmlElement
     * @throws \Doctrine\ORM\ORMException
     */
    public function getCategoryCheckboxesForPricelist()
    {
        // Until we know how to get the editId without the $_GET param
        if ($this->cx->getRequest()->hasParam('editid')) {
            $pricelistId = explode(
                '}',
                explode(
                    ',',
                    $this->cx->getRequest()->getParam('editid')
                )[1]
            )[0];
        }
        $repo = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Pricelist'
        );
        $categories = $this->cx->getDb()->getEntityManager()->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Category'
        )->findBy(array('active' => 1));
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');
        $index = count($categories)-1;

        foreach ($categories as $category) {
            $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
            $label->setAttributes(
                array(
                    'class' => 'category',
                    'for' => 'category-'. $category->getId()
                )
            );
            $text = new \Cx\Core\Html\Model\Entity\TextElement(
                $category->getName()
            );
            $checkbox = new \Cx\Core\Html\Model\Entity\DataElement(
                'categories[' . $index-- . ']',
                $category->getId()

            );

            $isActive = (boolean)$repo->getPricelistByCategoryAndId(
                $category,
                $pricelistId
            );
            $checkbox->setAttributes(
                array(
                    'type' => 'checkbox',
                    'id' => 'category-' . $category->getId(),
                    empty($isActive) ? '' : 'checked' => 'checked'
                )
            );

            $label->addChild($checkbox);
            $label->addChild($text);
            $wrapper->addChild($label);
        }
        return $wrapper;
    }

    public function getAllCategoriesCheckbox($isActive)
    {
        global $_ARRAYLANG;

        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

        $label = new \Cx\Core\Html\Model\Entity\HtmlElement('label');
        $label->setAttributes(
            array(
                'class' => 'category',
                'for' => 'category-all'
            )
        );
        $text = new \Cx\Core\Html\Model\Entity\TextElement(
            $_ARRAYLANG['TXT_SHOP_ALL_CATEGORIES']
        );
        $checkbox = new \Cx\Core\Html\Model\Entity\DataElement(
            'category-all',
            1
        );
        $checkbox->setAttributes(
            array(
                'type' => 'checkbox',
                'id' => 'category-all',
                empty($isActive) ? '' : 'checked' => 'checked'
            )
        );

        $label->addChild($checkbox);
        $label->addChild($text);
        $wrapper->addChild($label);

        return $wrapper;
    }
}
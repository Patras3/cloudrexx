<?php

namespace Cx\Modules\Shop\Model\Repository;

/**
 * PricelistsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PricelistRepository extends \Doctrine\ORM\EntityRepository
{
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
        $text = new \Cx\Core\Html\Model\Entity\TextElement($_ARRAYLANG['TXT_SHOP_ALL_CATEGORIES']);
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

    public function getCategoryCheckboxesForPricelist()
    {
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();

        // Until we know how to get the editId without the $_GET param
        if ($cx->getRequest()->hasParam('editid')) {
            $pricelistId = explode(
                '}',
                explode(
                    ',',
                    $cx->getRequest()->getParam('editid')
                )[1]
            )[0];
        }

        $categories = $this->_em->getRepository(
            '\Cx\Modules\Shop\Model\Entity\Category'
        )->findAll();
        $wrapper = new \Cx\Core\Html\Model\Entity\HtmlElement('div');

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
                'category-'. $category->getId(),
                1

            );

            $isActive = (boolean)$this->getPricelistByCategoryAndId(
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

    protected function getPricelistByCategoryAndId($category, $pricelistId)
    {
        $pricelists = $category->getPricelists();
        foreach ($pricelists as $pricelist) {
            if ($pricelist->getId() == $pricelistId) {
                return $pricelist;
            }
        }
    }
}

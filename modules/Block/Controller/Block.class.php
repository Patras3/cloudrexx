<?php

/**
 * Cloudrexx
 *
 * @link      https://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2017
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
 * Cx\Modules\Block
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Controller;

/**
 * Cx\Modules\Block\Controller\Block
 *
 * block module class
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Cloudrexx Development Team <info@cloudrexx.com>
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @access      public
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 * @todo        Edit PHP DocBlocks!
 */
class Block extends \Cx\Modules\Block\Controller\BlockLibrary
{
    public static function setBlocks(&$content, $page)
    {
        $config = \Env::get('config');

        $objBlock = new self();

        if (!is_array($content)) {
            $arrTemplates = array(&$content);
        } else {
            $arrTemplates = &$content;
        }

        foreach ($arrTemplates as &$template) {
            // Set blocks [[BLOCK_<ID>]]
            if (preg_match_all('/{' . $objBlock->blockNamePrefix . '([0-9]+)}/', $template, $arrMatches)) {
                $objBlock->setBlock($arrMatches[1], $template, $page);
            }

            // Set global block [[BLOCK_GLOBAL]]
            if (preg_match('/{' . $objBlock->blockNamePrefix . 'GLOBAL}/', $template)) {
                $objBlock->setBlockGlobal($template, $page);
            }

            // Set category blocks [[BLOCK_CAT_<ID>]]
            if (preg_match_all('/{' . $objBlock->blockNamePrefix . 'CAT_([0-9]+)}/', $template, $arrMatches)) {
                $objBlock->setCategoryBlock($arrMatches[1], $template, $page);
            }

            /* Set random blocks [[BLOCK_RANDOMIZER]], [[BLOCK_RANDOMIZER_2]],
                                 [[BLOCK_RANDOMIZER_3]], [[BLOCK_RANDOMIZER_4]] */
            if ($config['blockRandom'] == '1') {
                $placeholderSuffix = '';

                $randomBlockIdx = 1;
                while ($randomBlockIdx <= 4) {
                    if (preg_match('/{' . $objBlock->blockNamePrefix . 'RANDOMIZER' . $placeholderSuffix . '}/', $template)) {
                        $objBlock->setBlockRandom($template, $randomBlockIdx, $page);
                    }

                    $randomBlockIdx++;
                    $placeholderSuffix = '_' . $randomBlockIdx;
                }
            }
        }
    }


    /**
     * Set block
     *
     * Parse a block
     *
     * @access public
     * @param array $arrBlocks
     * @param string &$code
     * @param object $page
     * @see blockLibrary::_setBlock()
     */
    function setBlock($arrBlocks, &$code, $page)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $blockRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Block');

        foreach ($arrBlocks as $blockId) {
            $block = $blockRepo->findOneBy(array('id' => $blockId));
            $this->_setBlock($block, $code, $page);
        }
    }


    /**
     * Set category block
     *
     * Parse a category block
     *
     * @access public
     * @param array $arrCategoryBlocks
     * @param string &$code
     * @param object $page
     * @see blockLibrary::_setBlock()
     */
    function setCategoryBlock($arrCategoryBlocks, &$code, $page)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $categoryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\Category');

        foreach ($arrCategoryBlocks as $categoryId) {
            $category = $categoryRepo->findOneBy(array('id' => $categoryId));
            $this->_setCategoryBlock($category, $code, $page);
        }
    }

    /**
     * Set block Random
     *
     * Parse a block Random
     *
     * @access public
     * @param string &$code
     * @param int $id
     * @param object $page
     * @see blockLibrary::_setBlock()
     */
    function setBlockRandom(&$code, $id, $page)
    {
        $this->_setBlockRandom($code, $id, $page);
    }

    function setBlockGlobal(&$code, $page)
    {
        $this->_setBlockGlobal($code, $page);
    }
}

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

namespace Cx\Modules\Block\Model\Repository;

use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Cx\Modules\Block\Model\Repository\BlockLogRepository
 *
 * @copyright   CLOUDREXX CMS - CLOUDREXX AG
 * @author      Manuel Schenk <manuel.schenk@comvation.com>
 * @version     1.0.0
 * @package     cloudrexx
 * @subpackage  module_block
 */
class BlockLogRepository extends LogEntryRepository
{
    /**
     * Returns logs
     *
     * @param $entityClass string class of logged entity
     * @param $entityId integer id of logged entity
     * @param $limit integer limitation of returned results
     * @param $offset integer $offset of returned results
     * @return $logs array containing \Cx\Modules\Block\Model\Entity\LogEntry entities
     */
    public function getLogs($entityClass, $entityId, $limit = null, $offset = null)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $logEntryRepo = $em->getRepository('\Cx\Modules\Block\Model\Entity\LogEntry');
        // sets findBy criteria
        $criteria = array(
            'objectClass' => $entityClass,
            'objectId' => $entityId,
        );
        // finds logs by given parameters
        $logs = $logEntryRepo->findBy(
            $criteria,
            array(
                'version' => 'DESC'
            ),
            $limit,
            $offset
        );
        // returns found logs
        return $logs;
    }

    /**
     * Returns logs count for given entity
     *
     * @param $entity \Cx\Model\Base\EntityBase
     * @return $count integer count of all found entries
     */
    public function getLogCount($entity)
    {
        // gets logs count for given entity
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('count(le.id)')
            ->from('\Cx\Modules\Block\Model\Entity\LogEntry', 'le')
            ->where('le.objectClass = \'' . get_class($entity) . '\'')
            ->andWhere('le.objectId = :eId')
            ->setParameter('eId', $entity->getId());

        // gets result
        $count = $query->getQuery()->getSingleScalarResult();

        // returns logs count
        return intval($count);
    }

    /**
     * Reverts provided entity
     *
     * @param $entity \Cx\Model\Base\EntityBase entity to revert
     * @param $version integer wanted entity version
     * @return $revertedEntity \Cx\Model\Base\EntityBase reverted entity
     */
    public function revertEntity($entity, $version)
    {
        $em = \Cx\Core\Core\Controller\Cx::instanciate()->getDb()->getEntityManager();
        // reverts entity by version
        $this->revert($entity, $version);
        $revertedEntity = $entity;
        // clears the identity map of the EntityManager to enforce entity reloading
        $em->clear($entity);
        // returns reverted entity
        return $revertedEntity;
    }
}
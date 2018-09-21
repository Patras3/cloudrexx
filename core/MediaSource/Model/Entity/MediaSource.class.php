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
 * @copyright   Cloudrexx AG
 * @author      Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 * @subpackage  coremodule_mediabrowser
 */

namespace Cx\Core\MediaSource\Model\Entity;

use Cx\Core\DataSource\Model\Entity\DataSource;

/**
 * Class MediaSource
 *
 * @copyright   Cloudrexx AG
 * @author      Robin Glauser <robin.glauser@comvation.com>
 * @package     cloudrexx
 * @subpackage  coremodule_mediabrowser
 */
class MediaSource extends DataSource {

    /**
     * Name of the mediatype e.g. files, shop, media1
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $position;

    /**
     * Human readable name
     * @var string
     */
    protected $humanName;

    /**
     * Array with the web and normal path to the directory.
     *
     * e.g:
     * array(
     *      $this->cx->getWebsiteImagesContentPath(),
     *      $this->cx->getWebsiteImagesContentWebPath(),
     * )
     *
     * @var array
     */
    protected $directory = array();

    /**
     * Array with access ids to use with \Permission::checkAccess($id, 'static', true)
     * @var array
     */
    protected $accessIds = array();

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var bool if indexer is activated
     */
    protected $isIndexingActivated;

    /**
     * @var \Cx\Core\Core\Model\Entity\SystemComponentController $systemComponentController
     */
    protected $systemComponentController;


    public function __construct($name,$humanName, $directory, $accessIds = array(), $position = '',FileSystem $fileSystem = null, \Cx\Core\Core\Model\Entity\SystemComponentController $systemComponentController = null, $isIndexingActivated = true) {
        $this->fileSystem = $fileSystem ? $fileSystem : LocalFileSystem::createFromPath($directory[0]);
        $this->name      = $name;
        $this->position  = $position;
        $this->humanName = $humanName;
        $this->directory = $directory;
        $this->accessIds = $accessIds;
        $this->setIndexingActivated($isIndexingActivated);

        // Sets provided SystemComponentController
        $this->systemComponentController = $systemComponentController;
        if (!$this->systemComponentController) {
            // Searches a SystemComponentController intelligently by RegEx on backtrace stack frame
            $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $trace = end($traces);
            if (empty($trace['class'])) {
                throw new MediaBrowserException('No SystemComponentController for ' . __CLASS__ . ' can be found');
            }
            $matches = array();
            preg_match(
                '/Cx\\\\(?:Core|Core_Modules|Modules)\\\\([^\\\\]*)\\\\/',
                $trace['class'],
                $matches
            );
            $this->systemComponentController = $this->getComponent($matches[1]);
        }
    }

    /**
     * Define if indexer is activated
     *
     * @param $activated
     *
     * @return void
     */
    public function setIndexingActivated($activated)
    {
        $this->isIndexingActivated = $activated;
    }

    /**
     * Get information if indexer is activated
     *
     * @return bool
     */
    public function isIndexingActivated()
    {
        return $this->isIndexingActivated;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getDirectory()
    {
        return $this->directory;
    }


    /**
     * @return array
     */
    public function getAccessIds()
    {
        return $this->accessIds;
    }

    /**
     * @param array $accessIds
     */
    public function setAccessIds($accessIds)
    {
        $this->accessIds = $accessIds;
    }

    /**
     * @return bool
     */
    public function checkAccess(){
        foreach ($this->accessIds as $id){
            if (!\Permission::checkAccess($id, 'static', true)){
                return false;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getHumanName()
    {
        return $this->humanName;
    }

    /**
     * @param string $humanName
     */
    public function setHumanName($humanName)
    {
        $this->humanName = $humanName;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return FileSystem
     */
    public function getFileSystem() {
        return $this->fileSystem;
    }

    /**
     * @return \Cx\Core\Core\Model\Entity\SystemComponentController
     */
    public function getSystemComponentController() {
        return $this->systemComponentController;
    }

    /**
     * Gets one or more entries from this DataSource
     *
     * If an argument is not provided, no restriction is made for this argument.
     * So if this is called without any arguments, all entries of this
     * DataSource are returned.
     * If no entry is found, an empty array is returned.
     * @param array $elementId (optional) field=>value-type condition array identifying an entry
     * @param array $filter (optional) field=>value-type condition array, only supports = for now
     * @param array $order (optional) field=>order-type array, order is either "ASC" or "DESC"
     * @param int $limit (optional) If set, no more than $limit results are returned
     * @param int $offset (optional) Entry to start with
     * @param array $fieldList (optional) Limits the result to the values for the fields in this list
     * @throws \Exception If something did not go as planned
     * @return array Two dimensional array (/table) of results (array($row=>array($fieldName=>$value)))
     */
    public function get(
        $elementId = array(),
        $filter = array(),
        $order = array(),
        $limit = 0,
        $offset = 0,
        $fieldList = array()
    ) {
        throw new \Exception('Not yet implemented');
    }

    /**
     * Adds a new entry to this DataSource
     * @param array $data Field=>value-type array. Not all fields may be required.
     * @throws \Exception If something did not go as planned
     */
    public function add($data) {
        throw new \Exception('Not yet implemented');
    }

    /**
     * Updates an existing entry of this DataSource
     * @param array $elementId field=>value-type condition array identifying an entry
     * @param array $data Field=>value-type array. Not all fields are required.
     * @throws \Exception If something did not go as planned
     */
    public function update($elementId, $data) {
        throw new \Exception('Not yet implemented');
    }

    /**
     * Drops an entry from this DataSource
     * @param array $elementId field=>value-type condition array identifying an entry
     * @throws \Exception If something did not go as planned
     */
    public function remove($elementId) {
        throw new \Exception('Not yet implemented');
    }

    /**
     * Get all matches from search term.
     *
     * @param $searchterm string term to search
     * @param $path       string path to search in
     *
     * @throws \Cx\Core\Core\Model\Entity\SystemComponentException
     * @return array all search results
     */
    public function getFileSystemMatches($searchterm, $path)
    {
        $searchLength = \Cx\Core\Setting\Controller\Setting::getValue(
            'searchDescriptionLength'
        );
        $fullPath = $this->getDirectory()[0] . $path;
        $fileList = array();
        $searchResult = array();

        $orgFile = new \Cx\Core\MediaSource\Model\Entity\LocalFile(
            $path,
            $this->getFileSystem()
        );

        if ($this->getFileSystem()->isDirectory($orgFile)) {
            $fileList = $this->getFileSystem()->getFileList($path);
        } else {
            $fileEntry = $this->getFileSystem()->getFileFromPath($fullPath);
            array_push($fileList, $fileEntry);
        }
        // ToDo: Implement glob algorithm for matching file names
        $files = $this->getAllFilesAsObjects($fileList, $orgFile, array());
        foreach ($files as $file) {
            $fileInformation = array();
            $filePath = $file->getFileSystem()->getRootPath() . $file->__toString();
            $content = '';
            if ($this->isIndexingActivated()) {
                $indexer = $this->getComponentController()->getIndexer(
                    $file->getExtension()
                );
                if (!empty($indexer)) {
                    $match = $indexer->getMatch($searchterm, $filePath);
                    if (!empty($match)) {
                        $content = substr(
                            $match->getContent(), 0, $searchLength
                        ).'...';
                    }
                }
            }

            if (strpos(strtolower($file->getName()), strtolower($searchterm))
                === false && empty($content)
            ) {
                continue;
            }

            $componentName = '';
            if (!empty($this->getSystemComponentController())) {
                $componentName = $this->getSystemComponentController()->getName();
            }

            $fileInformation['Score'] = 100;
            $fileInformation['Title'] = ucfirst($file->getName());
            $fileInformation['Content'] = $content;
            $link = explode($this->cx->getWebsiteDocumentRootPath(), $filePath);
            $fileInformation['Link'] = $link[1];
            $fileInformation['Component'] = $componentName;
            array_push($searchResult, $fileInformation);
        }
        return $searchResult;
    }

    /**
     * Returns an array with all file paths of all files in this directory,
     * including files located in subdirectories.
     *
     * @param $fileList array  all files and directories
     * @param $file    \Cx\Core\MediaSource\Model\Entity\LocalFile file to check
     * @param $result   array  existing result
     *
     * @return array with all files as
     *               \Cx\Core\MediaSource\Model\Entity\LocalFile
     */
    protected function getAllFilesAsObjects($fileList, $file, $result)
    {
        foreach ($fileList as $fileEntryKey => $fileListEntry) {
            $newFile = new \Cx\Core\MediaSource\Model\Entity\LocalFile(
                $file->__toString() . $fileEntryKey,
                $this->getFileSystem()
            );
            if ($this->getFileSystem()->isDirectory($newFile)) {
                $result = $this->getAllFilesAsObjects(
                    $fileListEntry, $newFile, $result
                );
            } else if ($this->getFileSystem()->isFile($newFile)) {
                array_push($result, $newFile);
            }
        }
        return $result;
    }
}

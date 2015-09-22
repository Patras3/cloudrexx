<?php
/**
 * Event-listener for Routing/PageNotFound-event
 *
 * @copyright CLOUDREXX *MS - CLOUDREXX AG
 * @author Cloudrexx Development Team <info@cloudrexx.com>
 * @access public
 * @version 5
 * @package cloudrexx
 * @subpackage core_modules_error
 */

namespace Cx\Core_Modules\Error\Controller;

/**
 * Exception to skip the resolving of the current page/component
 *
 * @copyright CLOUDREXX *MS - CLOUDREXX AG
 * @author Cloudrexx Development Team <info@cloudrexx.com>
 * @access public
 * @version 5
 * @package cloudrexx
 * @subpackage core_modules_error
 */
class SkipResolverException extends \Exception {}

/**
 * Event-listener for Routing/PageNotFound-event
 *
 * @copyright CLOUDREXX *MS - CLOUDREXX AG
 * @author Cloudrexx Development Team <info@cloudrexx.com>
 * @access public
 * @version 5
 * @package cloudrexx
 * @subpackage core_modules_error
 */

class FrontendController extends \Cx\Core\Core\Model\Entity\SystemComponentFrontendController implements \Cx\Core\Event\Model\Entity\EventListener
{
    /**
     * @var \Cx\Core\Routing\Resolver Resolver which triggered the event
     */
    protected $resolver;

    /**
     * @var string The section of the page if it is a component-page
     */
    protected $section = '';

    /**
     * @var string The cmd if one is set
     */
    protected $cmd = '';

    /**
     * @var \Cx\Core\ContentManager\Model\Entity\Page The page which might only be inactive
     */
    protected $inactivePage = null;

    /**
     * @var string reason why the event was triggered
     */
    protected $reason = '';

    /**
     * @param $eventName String The name of the Event
     * @param array $eventArgs
     * @throws \Exception
     */
    public function onEvent($eventName, array $eventArgs) {
        if (preg_match("#/#", $eventName)) {
            $eventName = explode("/", $eventName)[1];
        }
        $eventName = lcfirst($eventName);
        if (!method_exists($this, $eventName)) {
            return;
        }

        $this->$eventName($eventArgs);
    }

    /**
     * @TODO: Check what happens when this is called in load or preContentLoad $eventArgs might need a stage-value
     * @param array $eventArgs
     * @throws \Cx\Core_Modules\Error\Controller\SkipResolverException
     */
    private function pageNotFound(array $eventArgs) {
        global $page, $plainSection;

        // display a sexy message in the stacktrace
        \DBG::stack();

        // get the information about missing or deactivated component / page
        $this->section = $eventArgs['section'];
        $this->cmd = $eventArgs['cmd'];
        $this->inactivePage = $eventArgs['page'];
        $history = $eventArgs['history'];
        $this->resolver = $eventArgs['resolver'];
        $this->reason = $eventArgs['reason'];

        // Load the content of the error-page
        $pageRepo = $this->cx->getDb()->getEntityManager()->getRepository('Cx\Core\ContentManager\Model\Entity\Page');
        $errorPage = $pageRepo->findOneByModuleCmdLang('Error', '', FRONTEND_LANG_ID);

        // @TODO: Remove this as soon as global $page is no longer in use! Update global $page to the error-page
        $page = $errorPage;
        // @TODO: Remove this as soon as global $plainSection is no longer in use! Update global $plainSectino to Error
        $plainSection = $errorPage->getModule();

        // setup the corresponding template
        $this->resolver->setTemplateBasedOnPage($errorPage);
        // check if the error-page has restricted access
        $this->resolver->checkFrontendAccess($errorPage, $history);

        // update the page-object
        $this->cx->setPage($errorPage);

        // set 404 page not found http-header
        header("HTTP/1.0 404 Not Found");
        // throw exception so that the resolver stops resolving gets caught in \Cx\Core\Core\Controller\Cx.class.php
        throw new \Cx\Core_Modules\Error\Controller\SkipResolverException();
    }

    /**
     * Use this to parse your frontend page
     *
     * You will get a template based on the content of the resolved page
     * You can access Cx class using $this->cx
     * To show messages, use \Message class
     * @param \Cx\Core\Html\Sigma $template Template containing content of resolved page
     * @return null
     */
    public function parsePage(\Cx\Core\Html\Sigma $template, $cmd)
    {
        global $_ARRAYLANG, $_CONFIG;
        // is a component-page
        if (!isset($this->section)) {
            $template->hideBlock('error_module_information');
            $template->hideBlock('error_module_description');
            $template->hideBlock('error_module_name');
            $template->hideBlock('error_module_installation_instructions');
            return;
        }

        // load language data of given component
        $coreLang = \Env::get('init')->getComponentSpecificLanguageData('Core', false, FRONTEND_LANG_ID);

        // check if a description is available
        $noDescription = !isset($coreLang['TXT_' . strtoupper($this->section) . '_MODULE_DESCRIPTION']) ? true : false;
        // check if user has permission to install a module
        $noAccess = !(\FWUser::getFWUserObject()->objUser->login(true) && \Permission::checkAccess(52, 'static', true));

        // prepare the template
        $template->touchBlock('error_module_information');
        $template->touchBlock('error_module_name');

        // only parse the description-block if a description is available
        if ($noDescription) {
            $template->hideBlock('error_module_description');
        } else {
            $template->touchBlock('error_module_description');
        }

        if ($noAccess) {
            $template->hideBlock('error_module_installation_instructions');
        } else {
            $template->touchBlock('error_module_installation_instructions');
        }

        // only replace the description if one exists
        if(!$noDescription) {
            $template->setVariable(array(
                'ERROR_MODULE_DESCRIPTION' => $coreLang['TXT_' . strtoupper($this->section) . '_MODULE_DESCRIPTION']
            ));
        }

        // only show installation instructions when the user has the permissions to
        if(!$noAccess && $this->reason == 'page not found') {
            // prepare the instructions
            $instructions = nl2br($_ARRAYLANG['TXT_ERROR_MODULE_INSTALLATION_GUIDE']);
            $backendLink = '<a href="' . ASCMS_PROTOCOL . $_CONFIG['domainUrl'] . '/' . ASCMS_PATH_OFFSET . '/cadmin" title="' .
                $_ARRAYLANG['TXT_ERROR_BACKEND_NAME'] . '" target="_blank">' . $_ARRAYLANG['TXT_ERROR_BACKEND_NAME'] . '</a>';
            $componentManagerLink = '<a href="' . ASCMS_PROTOCOL . $_CONFIG['domainUrl'] . '/' . ASCMS_PATH_OFFSET . '/cadmin/ComponentManager/edit" title="' .
                $_ARRAYLANG['TXT_ERROR_COMPONENT_MANAGER_NAME'] . '" target="_blank">' .
                $_ARRAYLANG['TXT_ERROR_COMPONENT_MANAGER_NAME'] . '</a>';
            $instructions = sprintf($instructions, $backendLink, $componentManagerLink, $this->section);
            $template->setVariable(array(
                'ERROR_MODULE_INSTALLATION_INSTRUCTION_TITLE' => $_ARRAYLANG['TXT_ERROR_MODULE_INSTALLATION_INSTRUCTION_TITLE'],
                'ERROR_MODULE_INSTALLATION_GUIDE' => $instructions
            ));
        }

        // replace the variables
        $template->setVariable(array(
            'ERROR_MODULE_NAME' => $this->section
        ));

    }
}
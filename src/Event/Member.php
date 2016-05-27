<?php
/**
 * Phire Members Module
 *
 * @link       https://github.com/phirecms/phire-members
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Phire\Members\Event;

use Phire\Members\Table;
use Phire\Controller\AbstractController;
use Pop\Application;
use Pop\Http\Response;

/**
 * Member Event class
 *
 * @category   Phire\Members
 * @package    Phire\Members
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 * @version    1.0.0
 */
class Member
{

    /**
     * Bootstrap the module
     *
     * @param  Application $application
     * @return void
     */
    public static function bootstrap(Application $application)
    {
        $members = Table\Members::findAll();
        if ($members->hasRows()) {
            foreach ($members->rows() as $member) {
                $controllerParams = [
                    'memberName' => $member->name,
                    'memberUri'  => $member->uri,
                    'roleId'     => $member->role_id,
                    'redirect'   => $member->redirect,
                    'append'     => true
                ];

                $routes = [
                    $member->uri . '[/]' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'index',
                        'controllerParams' => $controllerParams
                    ],
                    $member->uri . '/login[/]' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'login',
                        'controllerParams' => $controllerParams
                    ],
                    $member->uri . '/register/:id' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'register',
                        'controllerParams' => $controllerParams,
                        'acl'              => [
                            'resource'     => 'member-register'
                        ]
                    ],
                    $member->uri . '/profile[/]' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'profile',
                        'controllerParams' => $controllerParams,
                        'acl'              => [
                            'resource'     => 'member-profile'
                        ]
                    ],
                    $member->uri . '/verify/:id/:hash' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'verify',
                        'controllerParams' => $controllerParams
                    ],
                    $member->uri . '/forgot[/]' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'forgot',
                        'controllerParams' => $controllerParams
                    ],
                    $member->uri . '/unsubscribe[/]' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'unsubscribe',
                        'controllerParams' => $controllerParams,
                        'acl'              => [
                            'resource'     => 'member-unsubscribe'
                        ]
                    ],
                    $member->uri . '/logout[/]' => [
                        'controller'       => 'Phire\Members\Controller\IndexController',
                        'action'           => 'logout',
                        'controllerParams' => $controllerParams
                    ],
                ];
                $application->router()->addRoutes($routes);
            }
        }
    }


    /**
     * Check for the member session
     *
     * @param  Application $application
     * @return void
     */
    public static function sessionCheck(Application $application)
    {
        if ((null !== $application->router()->getController()) &&
            ($application->router()->getController() instanceof \Phire\Members\Controller\IndexController)) {
            $sess      = $application->getService('session');
            $action    = $application->router()->getRouteMatch()->getAction();
            $route     = $application->router()->getRouteMatch()->getRoute();
            $memberUri = $application->router()->getController()->getMemberUri();

            // If logged in, and a member URL, redirect to dashboard
            if (isset($sess->member) && (($action == 'login') || ($action == 'register') ||
                    ($action == 'verify') || ($action == 'forgot'))) {
                Response::redirect(BASE_PATH . $memberUri);
                exit();
            // Else, if NOT logged in and NOT a system URL, redirect to login
            } else if (!isset($sess->member) && (($action != 'login') && ($action != 'register') &&
                ($action != 'unsubscribe') && ($action != 'verify') && ($action != 'forgot') && (null !== $action)) &&
                (substr($route, 0, strlen($memberUri)) == $memberUri)) {
                Response::redirect(BASE_PATH . $memberUri . '/login');
                exit();
            }
        }
    }


    /**
     * Init category nav and categories
     *
     * @param  AbstractController $controller
     * @param  Application        $application
     * @return void
     */
    public static function setTemplate(AbstractController $controller, Application $application)
    {
        if (($controller->hasView()) && ($controller instanceof \Phire\Members\Controller\IndexController)) {
            $template     = basename($controller->view()->getTemplate()->getTemplate());
            $memberName   = $controller->view()->memberName;
            $memberUri    = $controller->view()->memberUri;
            $templateName = null;
            if ($application->isRegistered('phire-templates')) {
                switch ($template) {
                    case 'login.phtml':
                        $templateName = $memberName . ' Login';
                        break;
                    case 'forgot.phtml':
                        $templateName = $memberName . ' Forgot';
                        break;
                    case 'index.phtml':
                        $templateName = $memberName . ' Index';
                        break;
                    case 'profile.phtml':
                        $templateName = $memberName . ' Profile';
                        break;
                    case 'register.phtml':
                        $templateName = $memberName . ' Register';
                        break;
                    case 'unsubscribe.phtml':
                        $templateName = $memberName . ' Unsubscribe';
                        break;
                    case 'verify.phtml':
                        $templateName = $memberName . ' Verify';
                        break;
                }

                if (null !== $templateName) {
                    $tmpl = \Phire\Templates\Table\Templates::findBy(['name' => $templateName]);
                    if (isset($tmpl->id)) {
                        $controller->view()->setTemplate($tmpl->template);
                    }
                }
            } else if ($application->isRegistered('phire-themes')) {
                switch ($template) {
                    case 'login.phtml':
                        $templateName = $memberUri . '/login';
                        break;
                    case 'forgot.phtml':
                        $templateName = $memberUri . '/forgot';
                        break;
                    case 'index.phtml':
                        $templateName = $memberUri . '/index';
                        break;
                    case 'profile.phtml':
                        $templateName = $memberUri . '/profile';
                        break;
                    case 'register.phtml':
                        $templateName = $memberUri . '/register';
                        break;
                    case 'unsubscribe.phtml':
                        $templateName = $memberUri . '/unsubscribe';
                        break;
                    case 'verify.phtml':
                        $templateName = $memberUri . '/verify';
                        break;
                }

                if (null !== $templateName) {
                    $theme = \Phire\Themes\Table\Themes::findBy(['active' => 1]);
                    if (isset($theme->id)) {
                        $templateName = $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . CONTENT_PATH . '/themes/' . $theme->folder . $templateName;
                        $tmpl = null;
                        if (file_exists($templateName . '.phtml')) {
                            $tmpl = $templateName . '.phtml';
                        } else if (file_exists($templateName . '.php')) {
                            $tmpl = $templateName . '.php';
                        }
                        if (null !== $tmpl) {
                            $controller->view()->setTemplate($tmpl);
                        }
                    }
                }
            }
        }
    }

}

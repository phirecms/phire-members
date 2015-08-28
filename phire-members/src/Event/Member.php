<?php

namespace Phire\Members\Event;

use Phire\Members\Table;
use Pop\Application;
use Pop\Http\Response;

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

}

<?php
/**
 * Module Name: phire-members
 * Author: Nick Sagona
 * Description: This is the members module for Phire CMS 2
 * Version: 1.0
 */
return [
    'phire-members' => [
        'prefix'     => 'Phire\Members\\',
        'src'        => __DIR__ . '/../src',
        'routes'     => include 'routes.php',
        'resources'  => include 'resources.php',
        'forms'      => include 'forms.php',
        'nav.module' => [
            'name' => 'Members Admin',
            'href' => '/members-admin',
            'acl' => [
                'resource'   => 'members',
                'permission' => 'index'
            ]
        ],
        'models' => [
            'Phire\Members\Model\Member' => []
        ],
        'events' => [
            [
                'name'     => 'app.route.pre',
                'action'   => 'Phire\Members\Event\Member::bootstrap',
                'priority' => 1000
            ],
            [
                'name'     => 'app.dispatch.pre',
                'action'   => 'Phire\Members\Event\Member::sessionCheck',
                'priority' => 1001
            ],
            [
                'name'     => 'app.send.pre',
                'action'   => 'Phire\Members\Event\Member::setTemplate',
                'priority' => 1000
            ]
        ]
    ]
];

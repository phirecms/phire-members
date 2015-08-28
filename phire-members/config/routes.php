<?php

return [
    APP_URI => [
        '/members-admin[/]' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'index',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'index'
            ]
        ],
        '/members-admin/add[/]' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'add',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'add'
            ]
        ],
        '/members-admin/edit/:id' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'edit',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'edit'
            ]
        ],
        '/members-admin/remove[/]' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'remove',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'remove'
            ]
        ]
    ]
];

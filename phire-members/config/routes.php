<?php

return [
    APP_URI => [
        '/members[/]' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'index',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'index'
            ]
        ],
        '/members/add[/]' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'add',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'add'
            ]
        ],
        '/members/edit/:id' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'edit',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'edit'
            ]
        ],
        '/members/remove[/]' => [
            'controller' => 'Phire\Members\Controller\AdminController',
            'action'     => 'remove',
            'acl'        => [
                'resource'   => 'members',
                'permission' => 'remove'
            ]
        ]
    ]
];

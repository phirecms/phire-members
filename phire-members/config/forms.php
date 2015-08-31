<?php

return [
    'Phire\Members\Form\MembersAdmin' => [
        [
            'submit' => [
                'type'       => 'submit',
                'value'      => 'Save',
                'attributes' => [
                    'class'  => 'save-btn wide'
                ]
            ],
            'role_id' => [
                'type'       => 'select',
                'label'      => 'Role',
                'value'      => [
                    '----' => '----'
                ],
                'validators' => new \Pop\Validator\NotEqual('----', 'You must select a role.')
            ],
            'id' => [
                'type'  => 'hidden',
                'value' => 0
            ]
        ],
        [
            'name' => [
                'type'       => 'text',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => [
                    'size'   => 60,
                    'style'  => 'width: 99.5%'
                ]
            ],
            'uri' => [
                'type'       => 'text',
                'label'      => 'URI',
                'required'   => true,
                'attributes' => [
                    'size'   => 60,
                    'style'  => 'width: 99.5%'
                ]
            ],
            'redirect' => [
                'type'       => 'text',
                'label'      => 'Redirect',
                'attributes' => [
                    'size'   => 60,
                    'style'  => 'width: 99.5%'
                ]
            ]
        ]
    ],
    'Phire\Members\Form\Login' =>     [
        'username' => [
            'type'       => 'text',
            'required'   => 'true',
            'validators' => new \Pop\Validator\NotEmpty(),
            'attributes' => [
                'placeholder' => 'Username'
            ]
        ],
        'password' => [
            'type'       => 'password',
            'required'   => 'true',
            'validators' => new \Pop\Validator\NotEmpty(),
            'attributes' => [
                'placeholder' => 'Password'
            ]
        ],
        'submit' => [
            'type'  => 'submit',
            'value' => 'Login',
            'attributes' => [
                'class'  => 'save-btn'
            ]
        ],
        'role_id' => [
            'type'  => 'hidden',
            'value' => 0
        ]
    ]
];

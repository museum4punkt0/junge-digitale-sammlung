<?php

use Kirby\Cms\App;
use Kirby\Cms\Find;
use Kirby\Cms\UserRules;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Panel\Field;
use Kirby\Panel\Panel;
use Kirby\Toolkit\Escape;
use Kirby\Toolkit\I18n;

/**
 * This plugin overrides the original Users Area of Kirby.
 * Please refer to the original files to see the differences
 * 
 * https://github.com/getkirby/kirby/blob/main/config/areas/users.php
 * https://github.com/getkirby/kirby/blob/main/panel/src/components/Views/UsersView.vue
 * 
 */
Kirby::plugin('2av/users', [
    'areas' => [
        'users' => function () {
            return [
                // we adjust the label
                'label' => 'Konten',
                'views' => [
                    'users' => [
                        'action' => function () {
                            $kirby = App::instance();
                            $role  = $kirby->request()->get('role');
                            $roles = $kirby->roles()->toArray(fn ($role) => [
                                'id'    => $role->id(),
                                'title' => $role->title(),
                            ]);
                            return [
                                'component' => 'k-users-view',
                                'props'     => [
                                    'role' => function () use ($kirby, $roles, $role) {
                                        if ($role) {
                                            return $roles[$role] ?? null;
                                        }
                                    },
                                    'roles' => array_values($roles),
                                    'users' => function () use ($kirby, $role) {
                                        $users = $kirby->users();
                                        // we sort the users by role
                                        $users = $users->sortBy('role');
                                        if (empty($role) === false) {
                                            $users = $users->role($role);
                                        }

                                        $users = $users->paginate([
                                            'limit' => 20,
                                            'page'  => $kirby->request()->get('page')
                                        ]);

                                        return [
                                            'data' => $users->values(fn ($user) => [
                                                'id'    => $user->id(),
                                                'image' => $user->panel()->image(),
                                                'info'  => Escape::html($user->role()->title()),
                                                'link'  => $user->panel()->url(true),
                                                'text'  => Escape::html($user->username())
                                            ]),
                                            'pagination' => $users->pagination()->toArray()
                                        ];
                                    },
                                ]
                            ];
                        }
                    ]
                ],
                'dialogs' => [
                    // create admin
                    'user.create' => [
                        'pattern' => 'users/create',
                        'load' => function () {
                            $kirby = App::instance();
                            return [
                                'component' => 'k-form-dialog',
                                'props' => [
                                    'fields' => [
                                        'name'  => Field::username(),
                                        'email' => Field::email([
                                            'link'     => false,
                                            'required' => true
                                        ]),
                                        'password'     => Field::password(),
                                        'translation'  => Field::translation([
                                            'required' => true
                                        ])
                                        // we removed the original extra field for 'role', we are only creating admins here
                                    ],
                                    'submitButton' => I18n::translate('create'),
                                    'value' => [
                                        'name'        => '',
                                        'email'       => '',
                                        'password'    => '',
                                        'translation' => $kirby->panelLanguage(),
                                        'role'        => 'admin' // we are only creating admins here
                                    ]
                                ]
                            ];
                        },
                        'submit' => function () {
                            $kirby = App::instance();

                            $kirby->users()->create([
                                'name'     => $kirby->request()->get('name'),
                                'email'    => $kirby->request()->get('email'),
                                'password' => $kirby->request()->get('password'),
                                'language' => $kirby->request()->get('translation'),
                                'role'     => $kirby->request()->get('role')
                            ]);

                            return [
                                'event' => 'user.create'
                            ];
                        }
                    ],
                    // create tempuser
                    /**
                     * Here we create a shorter form with autogenerated password
                     * and a random email address that is only used in the system.
                     * The tempusers will loing only with usernames ,so they dont need
                     * to know their email address.
                     */
                    'users/createTempUser' => [
                        // dialog callback functions
                        'load' => function () {
                            $kirby = App::instance();
                            $uniqueId = randomPassword();
                            return [
                                'component' => 'k-form-dialog',
                                'props' => [
                                    'fields' => [
                                        'name'  => Field::username([
                                            'placeholder' => 'z.B. Name der Schule/Institution',
                                            'label' => 'Benutzername'
                                        ]),
                                        'password' => array_merge([
                                            'label' => I18n::translate('password'),
                                            'type'  => 'text'
                                        ], ['icon'     => 'key',]),
                                    ],
                                    'submitButton' => I18n::translate('create'),
                                    /**
                                     * here we ill use the domain from the config for the
                                     * auto generated addresses
                                     */
                                    'value' => [
                                        'name'        => '',
                                        'email'       => $uniqueId . '@' . option('jds.tempusers.defaultdomain', '2av.de'),
                                        'password'    => password_generate(8),
                                        'translation' => $kirby->panelLanguage(),
                                        'role'        => 'frontenduser'
                                    ]
                                ]
                            ];
                        },
                        'submit' => function () {
                            $kirby = App::instance();
                            $em = $kirby->request()->get('email');
                            $user = explode("@", $em);
                            $kirby->users()->create([
                                'name'     => $kirby->request()->get('name'),
                                'email'    => $em,
                                'password' => $kirby->request()->get('password'),
                                'language' => $kirby->request()->get('translation'),
                                'role'     => $kirby->request()->get('role')
                            ]);

                            return [
                                'event' => 'user.create'
                            ];
                        }
                    ]
                ]
            ];
        }
    ]
]);


function randomPassword()
{
    $currentmonth = date('n') - 1;
    $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
    $random_hash = substr(md5(uniqid(rand(), true)), 18, 9);
    return $random_hash . substr($alphabet, $currentmonth, 1);
}

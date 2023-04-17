<?php

use Kirby\Cms\App;
use Kirby\Cms\Find;
use Kirby\Cms\UserRules;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Panel\Field;
use Kirby\Panel\Panel;
use Kirby\Toolkit\Escape;
use Kirby\Toolkit\I18n;

Kirby::plugin('2av/temporaryusers', [
    'areas' => [
        'temporaryusers' => function ($kirby) {
            return [
                'label' => 'Temp users',
                'icon' => 'check',
                'menu' => true,
                'link'  => 'temporaryusers',
                //'views'     => require __DIR__ . '/tempusers/views.php'
                'views' => [
                    'temporaryusers' => [
                        // the Panel patterns must not start with 'panel/',
                        // the `panel` slug is automatically prepended.
                        'pattern' => 'temporaryusers',
                        'action'  => function () {
                            $kirby = App::instance();

                            /*$role  = $kirby->request()->get('role');                            

                             $roles = $kirby->roles()->toArray(fn ($role) => [
                                'id'    => $role->id(),
                                'title' => $role->title(),
                            ]); 

                          $roles = $kirby->roles()->filterBy('name','frontenduser')->toArray(fn ($role) => [
                                'id'    => $role->id(),
                                'title' => $role->title(),
                            ]); */



                            return [
                                'component' => 'temporaryusers',
                                'props'     => [
                                    /* 'role' => function () use ($kirby, $roles, $role) {
                                        if ($role) {
                                            return $roles[$role] ?? null;
                                        }
                                    },
                                    'roles' => array_values($roles), */
                                    //'users' => function () use ($kirby, $role) {
                                    'users' => function () use ($kirby) {
                                        $users = $kirby->users();

                                        /* if (empty($role) === false) {
                                            $users = $users->role($role);
                                        } */

                                        $users = $users->filterBy('role', 'frontenduser')->paginate([
                                            'limit' => 20,
                                            'page'  => $kirby->request()->get('page')
                                        ]);

                                        return [
                                            'data' => $users->values(fn ($user) => [
                                                'id'    => $user->id(),
                                                'image' => $user->panel()->image(),
                                                'info'  => Escape::html($user->role()->title()),
                                                //'link'  => 'temporaryusers/'.$user->id(),
                                                'link'  => $user->panel()->url(true),
                                                'text'  => Escape::html($user->username())
                                            ]),
                                            'pagination' => $users->pagination()->toArray()
                                        ];
                                    },
                                ]
                            ];
                        }
                    ],
                    'temporaryuser' => [
                        'pattern' => 'temporaryusers/(:any)',
                        'action'  => function (string $id) {
                            return Find::user($id)->panel()->view();
                        }
                    ],
                ],

                'dialogs' => [

                    // the key of the dialog defines its routing pattern
                    'temporaryusers/create' => [
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
                                        /* 'email' => Field::email([
                                            'link'     => false,
                                            'required' => true,
                                            'disabled' => true,
                                        ]), */
                                        'password' => array_merge([
                                            'label' => I18n::translate('password'),
                                            'type'  => 'text'
                                        ], ['icon'     => 'key',]),
                                        /* 'password'     => Field::password([
                                            'required' => true,
                                            'disabled' => true,
                                        ]), */

                                        
                                        /* 'translation'  => Field::translation([
                                            'required' => true
                                        ]), */
                                        /* 'role' => Field::role([
                                            'required' => true
                                        ]) */
                                    ],
                                    'submitButton' => I18n::translate('create'),
                                    'value' => [
                                        'name'        => '',
                                        'email'       => $uniqueId . '@2av.de',
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
                                'name'     => $kirby->request()->get('name')/* .'_'.$user[0] */,
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

<?php

return function ($page, $kirby) {

  # Grab the data from the default controller for authentification
  $site_vars = $kirby->controller('site', compact('page', 'kirby'));

  // forward to workshop if user already signed in
  if ($site_vars['authenticated']) {
    $ws = $site_vars['linkedUser']->linked_workshop()->toPageOrDraft();

    if($ws){
      $ws->go();
    }
    else{
      go($kirby->url() . '/logout');
    }
  }

  // handle the form submission
  if ($kirby->request()->is('POST') && get('login')) {

    // try to log the user in with the provided credentials
    try {
      $user = $kirby->users()->findBy('name', get('username'));

      if ($user) {
        if ($user->active()->toBool()) {
          $logeduser = $kirby->auth()->login($user->email(), get('password'));
          if ($logeduser) {
          } else {
            $alert[] = 'Benutzername oder Passwort nicht korrekt.';
          }
          // $kirby->auth()->login(get('username'), get('password'));   
          // redirect to the homepage if the login was successful
          if ($userlobby = $user->linked_workshop()->toPageOrDraft()) {
            go($userlobby->url());
          } else {
            kirby()->session()->clear();
            $user->logout();

            $alert[] = 'Der Benutzer ist mit keinem Workshop verbunden';
          }
        } else {
          $user->logout();
          $alert[] = 'Der Benutzer ist inaktiv';
        }
      } else {
        $alert[] = 'Benutzername oder Passwort nicht korrekt.';
      }
    } catch (Exception $e) {
      if ($e->getKey() == 'error.access.login') {
        $alert[] = 'Benutzername oder Passwort nicht korrekt.';
      } else {
        $alert[] = $e->getKey() . ': Es ist ein Fehler aufgetretten. Versuchen Sie es später noch mal.';
      }
    }
  }

  return [
    'alert' => $alert ?? null,
    'authenticated' => $site_vars['authenticated'] ?? false,
  ];
};

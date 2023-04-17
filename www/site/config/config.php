<?php

//@include __DIR__ . DS . 'credentials.php';

return [
  'debug' => true,
  'languages' => false,
  'panel' => [
    'css' => 'assets/css/custom-panel.css'
  ],

  'session' => [
    /* 'durationNormal' => 1500,            // default: 2 hours
    'timeout'        => 1500,            // default: half an hour */
    'gcInterval'     => 10              // default: cleanup every ~100 requests
  ],

  'mailsettings' => [
    'un' => getenv('CLOUDRON_MAIL_SMTP_USERNAME'),
    'pw' => getenv('CLOUDRON_MAIL_SMTP_PASSWORD'),
    'host' => getenv('CLOUDRON_MAIL_SMTP_SERVER'),
    'port' => getenv('CLOUDRON_MAIL_SMTP_PORT'),
  ],

  // API
  'api' => require_once 'api.php',

  // ROUTES
  'routes' => require_once 'routes.php',

  // HOOKS
  'hooks' => require_once 'hooks.php',

  // JANITORS
  'bnomei.janitor.secret' => 'ef1aebc3c119b6ddba70dd8b368f1d99',
  'bnomei.janitor.jobs' => require_once 'janitor_jobs.php',

  // srcset thumbs
  'thumbs' => [
    'srcsets' => [
      'default' => [
        '500w' => ['width' => 500, 'quality' => 79, 'format' => 'webp'],
        '800w' => ['width' => 800, 'quality' => 79, 'format' => 'webp'],
        '1024w' => ['width' => 1024, 'quality' => 79, 'format' => 'webp'],
        '1600w' => ['width' => 1600, 'quality' => 79, 'format' => 'webp']
      ],
      'galleryThumb' => [
        '500w' => ['width' => 200, 'quality' => 79, 'format' => 'webp'],
        '900w' => ['width' => 600, 'quality' => 79, 'format' => 'webp']
      ],
    ]
  ],

  // cookie consent plugin
  'michnhokn.cookie-banner' => [
    'features' => [
      'embeds' => 'Embeds',
    ],
  ],

  // MetaKnight
  'diesdasdigital.meta-knight' => [
    'separator' => ' | ',
  ],

  // robots plugin
  'bnomei.robots-txt.groups' => null,
  'bnomei.robots-txt.sitemap' => 'sitemap.xml', // string or callback
  'bnomei.robots-txt.content' => '
      user-agent: *
      disallow: /kirby/
      disallow: /site/
      allow: /media/
      ###
      user-agent: WebReaper
      user-agent: WebCopier
      user-agent: Offline Explorer
      user-agent: HTTrack
      user-agent: Microsoft.URL.Control
      user-agent: EmailCollector
      user-agent: penthesilea
      disallow: /',

];

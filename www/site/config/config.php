<?php

/**
 * Kirby Configuration file :
 * Here you can find default values for the
 * system and plugins. To know more about the
 * config file please read the Kirby documentation:
 * 
 * https://getkirby.com/docs/guide/configuration
 */

return [
  'debug' => true,

  'languages' => false,

  'panel' => [
    'language' => 'de',
    /**
     * custom styles for the admin-area. it is
     * important to note, that some areas have
     * been hidden via CSS to prevent unwanted
     * changes
     */
    'css' => 'assets/css/custom-panel.css'
  ],

  'session' => [
    // https://getkirby.com/docs/reference/system/options/session
    'gcInterval'     => 10              // cleanup every ~10 requests
  ],

  /**
   * JDS specific
   * configuration parameters
   */
  'jds' => [
    'tempusers' => [
      /**
       * Default domain for the automatically
       * generated email addresses for the temp-users. 
       * These emails have no real practical use and are
       * only needed by the system for authentification.
       * The temp-users will login with usernam and password,
       * not with the email.
       */
      'defaultdomain' => '2av.de'
    ],

    /**
     * If your server needs special configuration for the SMTP
     * service, please enter it here (for instance Virtual Server
     * running on Cloudron). Otherwise on regular hosting providers
     * or localhost you can leave it empty.
     */
    'mailsettings' => [
      'un' => getenv('CLOUDRON_MAIL_SMTP_USERNAME'),
      'pw' => getenv('CLOUDRON_MAIL_SMTP_PASSWORD'),
      'host' => getenv('CLOUDRON_MAIL_SMTP_SERVER'),
      'port' => getenv('CLOUDRON_MAIL_SMTP_PORT'),
    ],

    /**
     * Defaults for amount of items to be loaded
     * in 'Load more' templates.
     */
    'loadmoresettings' => [
      'blog' => 12,
      'home' => 12,
    ],
  ],

  /**
   * JANITOR PLUGIN ( System functions)
   */
  'bnomei.janitor.secret' => 'ef1aebc3c119b6ddba70dd8b368f1d99',
  'bnomei.janitor.jobs' => require_once 'system_functions.php',

  /**
   * Cookie consent plugin
   */
  'michnhokn.cookie-banner' => [
    'features' => [
      'embeds' => 'Embeds',
    ],
    'content' => [
      'title' => 'Cookie Einstellungen',
      'text' => 'Wir nutzen Cookies um Dir die bestmögliche Erfahrung zu bieten. Um die komplette Sammlung sehen zu können benötigst du die "Embeds" Cookies.',
      'essentialText' => 'Essentiell',
      'denyAll' => 'Alle ablehnen',
      'acceptAll' => 'Alle annehmen',
      'save' => 'Speichern',
    ],
  ],

  /**
   * MetaKnight Plugin (Open Graph)
   */
  'diesdasdigital.meta-knight' => [
    'separator' => ' | ',
  ],

  /**
   * Robots plugin
   */
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

  /**
   * Srcset sets
   */
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

  /**
   * API
   */
  'api' => require_once 'api.php',

  /**
   * ROUTES
   */
  'routes' => require_once 'routes.php',

  /**
   * HOOKS
   */
  'hooks' => require_once 'hooks.php',

];

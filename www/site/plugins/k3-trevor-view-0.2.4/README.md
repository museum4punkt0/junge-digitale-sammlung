# Trevor trenslates your voriables.

Edit your language translation variables from the Panel.

> This plugin is completely free and published under the MIT license. However, if you are using it in a commercial project and want to help me keep up with maintenance, please consider [buying me caffeine](https://buymeacoff.ee/zLFxgCHlG) or purchasing your license(s) through my [affiliate link](https://a.paddle.com/v2/click/1129/36164?link=1170).

![Screencast of the plugin](screenshot.gif)

## Requirements

This version requires at least Kirby 3.6.0. For previous versions, please use version 0.1.

## Installation

### Download

Download and copy a release (from the release tab) to `/site/plugins/k3-trevor-view`.

### Git submodule

```
git submodule add https://github.com/rasteiner-dist/k3-trevor-view.git site/plugins/k3-trevor-view
```

### Composer

```
composer require rasteiner/k3-trevor-view
```

## Setup

Languages must be enabled in your installation and some languages must be created.

See the Kirby documentation.
https://getkirby.com/docs/guide/languages/introduction#enabling-the-multi-lang-feature-in-the-panel

## Config
By default users are allowed to add new translation keys. This enables you, as a developer, to add keys while you are developing your project, or it allows users to add keys for dynamically generated content.
You can disable this feature in the config by setting `trevor.allow_add_keys` to `false`.

```php
<?php
return [
    'trevor' => [
        'allow_add_keys' => false,
    ],
];

```

## Use it
After installation and setup, you'll find a "Trevor" menu entry in your panel.
On the left you will see all your language variable keys, click on one to edit the translations.

Keys with missing translations will have a red dot next to them.

While editing a translation, you can navigate from one key to the next by pressing Cmd/Ctrl + Down. Cmd/Ctrl + Up gets you to the previous.

## License

MIT

## Credits

- [Roman Steiner](https://github.com/rasteiner)
- [Bastian Allgeier](https://github.com/bastianallgeier) (port for Kirby 3.6)

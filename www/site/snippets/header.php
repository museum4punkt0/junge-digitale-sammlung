<!DOCTYPE html>
<html lang="<?= $kirby->language() ?? 'de' ?>">

<head>
  <!-- Meta -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- MetaKnight -->
  <?= snippet('meta_information'); ?>
  <?= snippet('robots'); ?>

  <!-- Favicon -->
  <?php // snippet('header_elements/header_favicon') ?>

  <?php snippet('favicon') ?>

  <?= css('assets/css/index.css', 'screen') ?>
  <?= css('@auto') ?>
</head>

<body class="<?= $page->intendedTemplate() ?> <?= $page->title()->slug() ?>">
  <span id="cross-data" timeout-time="<?= $site->usertimeouttime()->value() ?? 20 ?>" page-slug="<?= $page->slug() ?>"></span><!-- we pass some PHP values to JS via a span with attributes -->
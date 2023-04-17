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
  <?= snippet('header_elements/header_favicon') ?>

  <?= css('assets/css/index.css', 'screen') ?>
  <?= css('@auto') ?>
</head>

<body class="<?= $page->intendedTemplate() ?> <?= $page->title()->slug() ?>">
<span id="cross-data" timeout-time="<?= $site->usertimeouttime() ?>" page-slug="<?= $page->slug() ?>"></span>


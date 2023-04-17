<?php
$twitter_image_thumb = [
    'width'   => 1200,
    'height'  => 675,
    'quality' => 80,
    'crop'    => true
];
$og_image_thumb = [
    'width'   => 1200,
    'height'  => 630,
    'quality' => 80,
    'crop'    => true
];

$full_title =  $page->meta_title()->or($page->title()) . ' | '. $site->meta_title()->or($site->title());
?>

<?php // Page Title 
?>
<title><?= $full_title ?></title>

<?php // Description 
?>
<meta name="description" content="<?= $page->meta_description()->or($site->meta_description()) ?>">

<?php // Canonical URL 
?>
<link rel="canonical" href="<?= $page->canonicalUrl() ?>" />


<?php // Alternate languages 
?>
<?php if ($kirby->languages()->count() > 0) : ?>
    <?php foreach ($kirby->languages() as $language) : ?>
        <link rel="alternate" hreflang="<?= strtolower(html($language->code())) ?>" href="<?= $page->url($language->code()) ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= $page->url($kirby->defaultLanguage()->code()) ?>">
<?php endif ?>

<?php // Image 
?>
<?php if ($meta_image = $page->meta_image()->toFile() ?? $site->meta_image()->toFile()) : ?>
    <meta id="schema_image" itemprop="image" content="<?= $meta_image->url() ?>">
<?php endif; ?>

<?php // Author 
?>
<meta name="author" content="<?= $page->meta_author()->or($site->meta_author()) ?>">


<?php // Open Graph 
?>
<meta property="og:title" content="<?= $page->meta_title()->or($page->title()) ?>">
<meta property="og:description" content="<?= $page->meta_description()->or($site->meta_description()) ?>">

<?php if ($og_image = $page->meta_image()->toFile() ?? $site->meta_image()->toFile()) : ?>
    <meta property="og:image" content="<?= $og_image->thumb($og_image_thumb)->url() ?>">
    <meta property="og:image:width" content="<?= $og_image->thumb($og_image_thumb)->width() ?>">
    <meta property="og:image:height" content="<?= $og_image->thumb($og_image_thumb)->height() ?>">
<?php endif; ?>

<meta property="og:site_name" content="<?= $site->meta_title()->or($site->title()) ?>">
<meta property="og:url" content="<?= $page->url() ?>">


<?php if ($kirby->language() !== null) : ?>
    <meta property="og:locale" content="<?= $kirby->language()->locale(LC_ALL) ?>">
    <?php foreach ($kirby->languages() as $language) : ?>
        <?php if ($language !== $kirby->language()) : ?>
            <meta property="og:locale:alternate" content="<?= $language->locale(LC_ALL) ?>">
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

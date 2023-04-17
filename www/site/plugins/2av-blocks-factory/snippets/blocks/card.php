<?php
$link     = $block->link()->value();
$image    = $block->image()->toFile();
$heading  = $block->heading();
$text     = $block->text();

$srcsetargs = [
  '400w'  => ['height' => 225, 'width' => 400, 'quality' => 79, 'format' => 'webp'],
  '800w'  => ['height' => 450, 'width' => 800, 'quality' => 79, 'format' => 'webp'],
  '1200w' => ['height' => 562, 'width' => 1000, 'quality' => 79, 'format' => 'webp']
];

?>

<?php if ($block->isNotEmpty()) : ?>
  <div class="card">
    <?php if (!empty($link)) : ?>
      <a href="<?= $link ?>">
      <?php endif; ?>
      <?php if ($image) : ?>
        <div class="card-image framed" style="--w:16;--h:9">
          <img class="img-responsive" src="<?= $image->placeholderUri() ?>" data-srcset="<?= $image->srcset($srcsetargs) ?>" data-lazyload alt="<?= $image->alt() ?>">
        </div>
      <?php endif ?>
      <div class="card-header">
        <h3>
          <?= $heading ?>
        </h3>
      </div>
      <div class="card-body">
        <?= $text ?>
      </div>
      <?php if (!empty($link)) : ?>
      </a>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php snippet('header') ?>
<?php snippet('section-header', ['isFrontEnd' => true]) ?>

<main>
  <div id="wrapper">
    <div class="container">
      <h1>
        <?= $page->title()->html() ?>
      </h1>
    </div>
    <?php snippet('renderers/layouts-renderer', ['layouts' => $page->layout()]) ?>
    <div class="container grid-xl">
      <ul class="load-more-container" data-limit="<?= $limit ?>">
        <?php foreach ($loadmoreContent as $loadmoreElement) : ?>
          <?php snippet('factories/loadmore', ['element' => $loadmoreElement]) ?>
        <?php endforeach ?>
      </ul>
      <button class="btn btn-primary load-more-btn">
        <?= $page->loadmore_btn_lbl() ?>
      </button>
    </div>
  </div>
</main>

<?php snippet('footer', ['isFrontEnd' => true]) ?>
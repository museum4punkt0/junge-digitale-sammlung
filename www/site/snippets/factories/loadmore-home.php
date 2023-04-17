<?php if ($type == 'c_exhibit') : ?>
  <?php
  $exhibit_type_class = '';
  switch ($collectionItem->type()->value()) {
    case 0:
      $exhibit_type_class = "physical";
      break;
    case 1:
      $exhibit_type_class = "embed";
      break;
    case 2:
      $exhibit_type_class = "born-digital";
      break;
  }
  ?>
  <?php $compact_exhibit_class = $collectionItem->threed_model_size()->toBool() ? 'size-compact' : ''; ?>
  <div class="col-exhibit loadmore-element ">

    <?php if ($exhibit_type_class == "embed") : ?>
      <?php if ($url = $collectionItem->embed_url()->toEmbed()) : ?>

        <?php if ($url->providerName()->lower() == 'twitter') : ?>
          <div class="twitter-container single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" tabindex="-1">
            <?= $url->code() ?>
            <a title="<?= $collectionItem->title() ?>" class="exhibit-link cover-link" href="<?= $collectionItem->id() ?>">
              <?= $collectionItem->title()->value() ?>
            </a>
          </div>
        <?php else : ?>
          <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
            <?php if ($url->image()) : ?>
              <img src="<?= $url->image() ?>" alt="">
            <?php else : ?>
              <p class="single-exhibit">
                <span class="empty"><i icon-name="help-circle" class="icon-only"></i></span>
              </p>
            <?php endif ?>
          </a>
        <?php endif ?>

      <?php else : ?>
        <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
          <p class="single-exhibit">
            <span class="empty"><i icon-name="help-circle" class="icon-only"></i></span>
          </p>
        </a>
      <?php endif ?>
    <?php else : ?>
      <a title="<?= $collectionItem->title() ?>" class="exhibit-link single-exhibit <?= $compact_exhibit_class ?> <?= $exhibit_type_class ?>" href="<?= $collectionItem->id() ?>">
        <?php if ($img = $collectionItem->exhibit_preview()->toFile()) : ?>
          <?= $img->responsiveImg() ?>
        <?php else : ?>
          <p class="single-exhibit">
            <span class="empty"><i icon-name="help-circle" class="icon-only"></i></span>
          </p>
        <?php endif ?>
      </a>
    <?php endif ?>

    <div class="exhibit-podest"> </div>
  </div>

<?php elseif ($type == 'c_exhibition') : ?>
  <div class="col-exhibition loadmore-element">
    <a title="<?= $collectionItem->title() ?>" class="exhibition-link single-exhibit" href="<?= $collectionItem->id() ?>">
      <div class="flex-container">
        <div class="img-container">
          <?php $exhibits = $collectionItem->getLinkedExhibits(); ?>
          <?php foreach ($exhibits as $exhibit) : ?>

            <?php
            $exhibit_type_class = '';
            switch ($exhibit->type()->value()) {
              case 0:
                $exhibit_type_class = "physical";
                break;
              case 1:
                $exhibit_type_class = "embed";
                break;
              case 2:
                $exhibit_type_class = "born-digital";
                break;
            }
            $compact_exhibit_class = $exhibit->threed_model_size()->toBool() ? 'size-compact' : ''; ?>

            <?php if ($exhibit_type_class == "embed") : ?>
              <?php if ($url = $exhibit->embed_url()->toEmbed()) : ?>
                <img src="<?= $url->image() ?>" alt="">
              <?php else : ?>
                <div class="empty"><i icon-name="help-circle" class="icon-only"></i></div>
              <?php endif ?>
            <?php else : ?>
              <?php if ($img = $exhibit->exhibit_preview()->toFile()) : ?>
                <img src="<?= $img->resize(100)->url(); ?>" alt="" class="<?= $compact_exhibit_class ?>">
              <?php else : ?>
                <div class="empty"><i icon-name="help-circle" class="icon-only"></i></div>
              <?php endif ?>
            <?php endif ?>

          <?php endforeach ?>
        </div>
      </div>
      <div class="flex-container">
        <h2>
          <?= $collectionItem->title() ?>
        </h2>
      </div>
    </a>
  </div>
<?php endif ?>
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
    <?= snippet('renderers/frontend/podest-exhibit', ['collectionItem' => $collectionItem, 'isPosterCollage' => false, 'exhibit_type_class' => $exhibit_type_class, 'compact_exhibit_class' => $compact_exhibit_class]) ?>
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
            <?= snippet('renderers/frontend/podest-exhibit', ['collectionItem' => $exhibit, 'isPosterCollage' => true, 'exhibit_type_class' => $exhibit_type_class, 'compact_exhibit_class' => $compact_exhibit_class]) ?>
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
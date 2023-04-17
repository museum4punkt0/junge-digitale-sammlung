<?php
$_index = $index >= 0 ? $index : str_replace('-', '', $block->id());
?>
<?php if ($block->summary()->isNotEmpty()) : ?>
  <div class="accordion-item">
    <h2 class="accordion-header" id="accordion-heading_<?= $_index ?>">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-collapse_<?= $_index ?>" aria-expanded="false" aria-controls="accordion-collapse_<?= $_index ?>">
        <?= $block->summary() ?>
      </button>
    </h2>
    <div id="accordion-collapse_<?= $_index ?>" class="accordion-collapse collapse" data-bs-parent="<?= $parentid ?>" aria-labelledby="accordion-heading_<?= $_index ?>">
      <div class="accordion-body">
        <?= $block->details() ?>
      </div>
    </div>
  </div>
<?php endif; ?>
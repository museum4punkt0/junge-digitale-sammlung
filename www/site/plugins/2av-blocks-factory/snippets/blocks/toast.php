<?php if($block->text()->isNotEmpty()): ?>
  <div class="toast toast-<?= $block->toastType() ?>">
    <?= $block->text() ?>
  </div>
<?php endif; ?>
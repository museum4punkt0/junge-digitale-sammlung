<?php $accordionItems = $block->elements()->toBlocks(); ?>
<div class="accordion" id="a<?= str_replace('-', '', $block->id()); ?>">
  <?php if ($accordionItems->isNotEmpty()) : ?>
    <?php foreach ($accordionItems as $key => $accordionItem) : ?>
      <?php snippet('blocks/' . $accordionItem->type(), [
        'block' => $accordionItem,
        'index' => $accordionItem->indexOf(),
        'parentid' => 'a'.str_replace('-', '', $block->id())
      ]) ?>
    <?php endforeach ?>
  <?php endif; ?>
</div>
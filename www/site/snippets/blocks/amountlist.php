<div class="amount__list">
    <?php
    $items = $block->rows()->toStructure();
    // we can then loop through the entries and render the individual fields
    foreach ($items as $item) : ?>
        <div class="row">
            <div class="col li"><?= $item->amount() ?></div>
            <div class="col li"><?= $item->text() ?></div>
        </div>
    <?php endforeach ?>
</div>
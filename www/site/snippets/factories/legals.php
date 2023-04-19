<div class="container meta-text legals pt-4">
    <div class="vstack gap-2">
        <?php $legalpages = $site->children()->filterBy('intendedTemplate', 'legal');
        foreach ($legalpages as $lp) : ?>
            <div class="text-end">
                <a href="<?= $lp->url() ?>" title="<?= $lp->title() ?>" class="link-info" target="_blank">
                    <?= $lp->title() ?> ›
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>
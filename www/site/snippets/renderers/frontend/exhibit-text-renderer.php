<?php if ($user) : ?>
    <?php if ($exhibit = $user->linked_exhibit()->toPageOrDraft()) : ?>
        <div class="col col-lg-6 group-podest">
            <h2>
                <?= $exhibit->title() ?>
            </h2>
            <?php if ($exhibittext) : ?>                
                    <?= $exhibittext ?>                
            <?php endif ?>            
            <a href="<?= $exhibit->url()  ?>">Objekt im Detail ›</a>
        </div>
    <?php endif ?>
<?php endif ?>
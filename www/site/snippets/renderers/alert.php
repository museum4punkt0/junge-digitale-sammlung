<?php
// if the form input is not valid, show a list of alerts
if ($alert) : ?>
    <div class="alert auto <?= isset($local) ? 'position-relative' : 'position-fixed' ?>">
        <?php if (count($alert) > 1) : ?>
            <ul>
                <?php foreach ($alert as $message) : ?>
                    <li><?= kirbytext($message) ?></li>
                <?php endforeach ?>
            </ul>
        <?php else : ?>
            <?php foreach ($alert as $message) : ?>
                <?= kirbytext($message) ?>
            <?php endforeach ?>
        <?php endif ?>
    </div>
<?php endif ?>
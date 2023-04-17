<?= snippet('footer_elements/back-to-top') ?>
<?= snippet('footer_elements/cookie-area') ?>

<?= js('/assets/js/ajax-functions.js') ?>
<?= js('/assets/js/index.js') ?>

<?php if ($authenticated && !$isFrontEnd) : ?>
    <?= js('/assets/js/admin.js') ?>
<?php endif ?>

<?php if ($authenticated) : ?>
    <script>
        initLogoutTimer();
    </script>
<?php endif ?>

<?= js('@auto') ?>
<!DOCTYPE html>
<html>

<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= css('assets/css/A4.css') ?>
    <?= css('@auto') ?>
</head>

<body class="<?= $page->intendedTemplate() ?> <?= $page->title()->slug() ?>">
    <div class="info__area">
        <a href="#" class="btn btn-download" id="print-button" onclick="window.print();return false;">Drucken/PDF speichern</a>
        <p>PDF kann im Druckfenster gespeichert werden.</p>
    </div>


    <?php if ($tg = $page->target_page()->toPageOrDraft()) : ?>
        <?php $participants = $tg->childrenAndDrafts()->filterBy('intendedTemplate', 'c_curator')->sortBy('title'); ?>
        <?php foreach ($participants as $participant) : ?>
            <div class="page">
                <h1>
                    <?= $tg->institution() ?>
                </h1>
                <div class="user-form">
                    <p class="p-title">
                        <strong>Name: </strong><?= $participant->title() ?>
                    </p>
                    <p class="p-id">
                        <strong>ID: </strong><?= $participant->slug() ?>
                    </p>
                    <p class="login">
                        <strong>Login: </strong>
                        <?= $kirby->url() . '/login' ?>
                    </p>
                    <div class="qr">
                        <?= $site->find('login')->qrcode()->html($page->slug() . '.png'); ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else : ?>
        <p>Noch keine Teilnehmer</p>
    <?php endif ?>


</body>

</html>
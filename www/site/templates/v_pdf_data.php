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

    <script>
        if (navigator.vendor.startsWith('Apple'))
            document.documentElement.classList.add('on-apple');
    </script>
    <div class="info__area">
        <a href="#" class="btn btn-download" id="print-button" onclick="window.print();return false;">Drucken/PDF speichern</a>
        <p>PDF kann im Druckfenster gespeichert werden.</p>
    </div>

    <?php if ($tg = $page->target_page()->toPageOrDraft()) : ?>
        <?php $participants = $tg->childrenAndDrafts()->filterBy('intendedTemplate', 'c_curator')->sortBy('title'); ?>
        <?php foreach ($participants as $participant) : ?>
            <div class="page">
                <div class="logos">
                    <?php if ($image1 = $site->institutionlogo()->toFile()) : ?>
                        <div class="logo__header logo__institution">
                            <img src="<?= $image1->url() ?>" data-lazyload alt="<?= $image1->alt() ?>">
                        </div>
                    <?php endif ?>
                    <?php if ($image2 = $site->projectlogo()->toFile()) : ?>
                        <div class="logo__header logo__project">
                            <img src="<?= $image2->url() ?>" data-lazyload alt="<?= $image2->alt() ?>">
                        </div>
                    <?php endif ?>
                </div>
                <div class="wrapper">
                    <div class="txt_center">
                        <h1>
                            Login Workshop-Bereich
                        </h1>
                        <h1 class="italic">
                            <?= $tg->institution() ?>
                        </h1>
                        <div class="qr">
                            <?= $site->find('login')->qrcode()->html($page->slug() . '.png'); ?>
                        </div>
                        <p class="login">
                            <?= $kirby->url() . '/login' ?>
                        </p>
                    </div>

                    <div class="user-form">
                        <h2>
                            1. Login Gruppenkonto
                        </h2>
                        <p class="p-account">
                            Gruppenkonto: <?= $tg->mainuser()->toUser() ? $tg->mainuser()->toUser()->name() : 'Kein Benutzer' ?>
                        </p>
                        <p class="p-pw closer">
                            Passwort: Dieses erhältst du von der Kursleitung/Lehrkraft.
                        </p>
                        <h2>
                            2. Login individuelles Profil
                        </h2>
                        <p class="p-id">
                            Deine ID: <?= $participant->slug() ?>
                        </p>
                        <p class="p-pin closer">
                            PIN: Diese vergibst du selbst beim ersten Log-in.
                        </p>
                        <p>
                            Viel Freude beim Workshop!
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php else : ?>
        <p>Noch keine Teilnehmer</p>
    <?php endif ?>


</body>

</html>
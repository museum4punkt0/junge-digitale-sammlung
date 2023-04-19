<?php
$warningStyle = 'color:#ff560f';
$hrSoftStyle = 'border: none; border-bottom: 1px solid #aaa';

$participants = $data['ws-participants'];
$exhibits = $data['ws-exhibits'];
$exhibitions = $data['ws-exhibitions'];
?>
<h1>
    Neuer Workshop wurde als fertig markiert.
</h1>
<h3>
    <?= $data['ws-name'] ?>
</h3>
<hr style="<?= $hrSoftStyle ?>">
<h2>
    Angereicht von Gruppen-Leiter:in
</h2>
<p>
    <?= $data['ws-leader'] ?>
</p>
<?php if ($data['ws-comment'] > 0) : ?>
    <h3>Kommentar</h3>
    <p><?= $data['ws-comment'] ?></p>
<?php endif ?>
<h3>Information</h3>
<p><?= $participants->count() ?> Teilnehmer.</p>
<p><?= $exhibits->count() ?> Objekte.</p>
<p><?= $exhibitions->count() ?> Ausstellungen.</p>
<?php if ($data['ws-notDoneParticipants'] > 0) : ?>
    <p><span style=<?= $warningStyle ?>><?= $data['ws-notDoneParticipants'] ?> Teilnehmer haben unvollständige Daten.</span></p>
<?php endif ?>
<?php if ($data['ws-notDoneExhibits'] > 0) : ?>
    <p><span style=<?= $warningStyle ?>><?= $data['ws-notDoneExhibits'] ?> Objekte haben unvollständige Daten.</span></p>
<?php endif ?>
<?php if ($data['ws-notDoneExhibits'] > 0) : ?>
    <p><span style=<?= $warningStyle ?>><?= $data['ws-notDoneExhibitions'] ?> Ausstellungen haben unvollständige Daten.</span></p>
<?php endif ?>
<hr style="<?= $hrSoftStyle ?>">

<!-- Teilnehmer -->
<h2>
    Teilnehmer
</h2>
<h3>
    <?= '(0) Benutzername / Name / ID ' ?>
</h3>

<?php
$index = 1;
foreach ($participants as $item) : ?>
    <p>
        <strong>
            <?= '(' . $index . ') ' . $item->username()->or(' kein Benutzername ') . ' / ' . $item->title() . ' / ' . $item->slug() ?>
        </strong>
    </p>

    <?php if ($item->missingInfo()->isNotEmpty()) : ?>
        <p>Fehlende Information:</p>
        <p><i><?= $item->missingInfo() ?></i></p>
    <?php endif ?>

    <?php if (!$item->missingInfo()->exists()) : ?>
        <p><i><span style=<?= $warningStyle ?>>Datensätze wurden nie bearbeitet.</span></i></p>
    <?php endif ?>

    <?php if ($item->pin()->isEmpty()) : ?>
        <p><i><span style=<?= $warningStyle ?>>PIN wurde nicht angelegt!</span></i></p>
    <?php endif ?>
    ------
    <?php $index++; ?>
<?php endforeach ?>

<hr style="<?= $hrSoftStyle ?>">

<!-- Objekte -->
<h2>
    Objekte
</h2>
<h3>
    <?= '(0) Objektname / Typ ' ?>
</h3>

<?php
$index = 1;
foreach ($exhibits as $exhibit) : ?>
    <p>
        <strong>
            <?= '(' . $index . ') ' . $exhibit->title()->or(' kein Objektname ') . ' / ' . $exhibit->type()->mapValueToLabel() ?>
        </strong>
    </p>
    <p>(<?= $exhibit->linked_user()->toPage()->username()->or($exhibit->linked_user()->toPage()->title())?>)</p>

    <?php if ($exhibit->missingInfo()->isNotEmpty()) : ?>
        <p>Fehlende Information:</p>
        <p><i><?= $exhibit->missingInfo() ?></i></p>
    <?php endif ?>

    <?php if (!$exhibit->missingInfo()->exists()) : ?>
        <p><i><span style=<?= $warningStyle ?>>Datensätze wurden nie bearbeitet.</span></i></p>
    <?php endif ?>

    ------
    <?php $index++; ?>
<?php endforeach ?>

<hr style="<?= $hrSoftStyle ?>">

<!-- Ausstellungen -->
<h2>
    Ausstellungen
</h2>
<h3>
    <?= '(0) Name / Anzahl Objekte ' ?>
</h3>

<?php
$index = 1;
foreach ($exhibitions as $exhibition) : ?>
    <p>
        <strong>
            <?= '(' . $index . ') ' . $exhibition->title()->or(' kein Ausstellungsname ') . ' / ' . $exhibition->getLinkedTotalExhibitsCount() . " Objekte" ?>
        </strong>
    </p>

    <?php if ($exhibition->missingInfo()->isNotEmpty()) : ?>
        <p>Fehlende Information:</p>
        <p><i><?= $exhibition->missingInfo() ?></i></p>
    <?php endif ?>

    <?php if (!$exhibition->missingInfo()->exists()) : ?>
        <p><i><span style=<?= $warningStyle ?>>Datensätze wurden nie bearbeitet.</span></i></p>
    <?php endif ?>

    <?php
    $exhibits = $exhibition->getLinkedExhibits();
    foreach ($exhibits as $exhibit) : ?>
    <h4><?= $exhibit->title()->value() ?></h4>
    <?php $exhibit_user = $exhibit->linked_user()->toPageOrDraft(); ?>
    <p>(<?= $exhibit_user->username()->or(' kein Benutzername ') . ' / ' . $exhibit_user->title() . ' / ' . $exhibit_user->slug() ?>)</p>
    <?php endforeach ?>

    ------
    <?php $index++; ?>
<?php endforeach ?>
<?php

$participants = $data['ws-participants'];
$exhibits = $data['ws-exhibits'];
$exhibitions = $data['ws-exhibitions'];
?>

Neues Workshop wurde als fertig markiert.

<?= $data['ws-name'] ?>

---------------------

Angereicht von Gruppen-Leiter:in


<?= $data['ws-leader'] ?>

<?php if ($data['ws-comment'] > 0) : ?>
    Kommentar
    <?= $data['ws-comment'] ?>
<?php endif ?>
Information
<?= $participants->count() ?> Teilnehmer.
<?= $exhibits->count() ?> Objekte.
<?= $exhibitions->count() ?> Ausstellungen.
<?php if ($data['ws-notDoneParticipants'] > 0) : ?>
    <?= $data['ws-notDoneParticipants'] ?> Teilnehmer haben unvollständige Daten.
<?php endif ?>
<?php if ($data['ws-notDoneExhibits'] > 0) : ?>
    <?= $data['ws-notDoneExhibits'] ?> Objekte haben unvollständige Daten.
<?php endif ?>
<?php if ($data['ws-notDoneExhibits'] > 0) : ?>
    <?= $data['ws-notDoneExhibitions'] ?> Ausstellungen haben unvollständige Daten.
<?php endif ?>
---------------------
<!-- Teilnehmer -->

Teilnehmer
<?= '(0) Benutzername / Name / ID ' ?>

<?php
$index = 1;
foreach ($participants as $item) : ?>


    <?= '(' . $index . ') ' . $item->username()->or(' kein Benutzername ') . ' / ' . $item->title() . ' / ' . $item->slug() ?>



    <?php if ($item->missingInfo()->isNotEmpty()) : ?>
        Fehlende Information:
        <?= $item->missingInfo() ?>
    <?php endif ?>

    <?php if (!$item->missingInfo()->exists()) : ?>
        Datensätze wurden nie bearbeitet.
    <?php endif ?>

    <?php if ($item->pin()->isEmpty()) : ?>
        PIN wurde nicht angelegt!
    <?php endif ?>
    ------
    <?php $index++; ?>
<?php endforeach ?>

---------------------
<!-- Objekte -->


Objekte
<?= '(0) Objektname / Typ ' ?>

<?php
$index = 1;
foreach ($exhibits as $exhibit) : ?>


    <?= '(' . $index . ') ' . $exhibit->title()->or(' kein Objektname ') . ' / ' . $exhibit->type()->mapValueToLabel() ?>
    (<?= $exhibit->linked_user()->toPage()->username()->or($exhibit->linked_user()->toPage()->title())?>)



    <?php if ($exhibit->missingInfo()->isNotEmpty()) : ?>
        Fehlende Information:
        <?= $exhibit->missingInfo() ?>
    <?php endif ?>

    <?php if (!$exhibit->missingInfo()->exists()) : ?>
        Datensätze wurden nie bearbeitet.
    <?php endif ?>

    ------
    <?php $index++; ?>
<?php endforeach ?>

---------------------
<!-- Ausstellungen -->


Ausstellungen
<?= '(0) Name / Anzahl Objekte ' ?>

<?php
$index = 1;
foreach ($exhibitions as $exhibition) : ?>

    <?= '(' . $index . ') ' . $exhibition->title()->or(' kein Ausstellungsname ') . ' / ' . $exhibition->getLinkedTotalExhibitsCount() . " Objekte" ?>

    <?php if ($exhibition->missingInfo()->isNotEmpty()) : ?>
        Fehlende Information:
        <?= $exhibition->missingInfo() ?>
    <?php endif ?>

    <?php if (!$exhibition->missingInfo()->exists()) : ?>
        Datensätze wurden nie bearbeitet.
    <?php endif ?>

    <?php
    $exhibits = $exhibition->getLinkedExhibits();
    foreach ($exhibits as $exhibit) : ?>
    - <?= $exhibit->title()->value() ?>
    <?php $exhibit_user = $exhibit->linked_user()->toPageOrDraft(); ?>
    (<?= $exhibit_user->username()->or(' kein Benutzername ') . ' / ' . $exhibit_user->title() . ' / ' . $exhibit_user->slug() ?>)
    <?php endforeach ?>

    ------
    <?php $index++; ?>
<?php endforeach ?>
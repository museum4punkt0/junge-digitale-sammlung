<?php

$confirmText = '<p>' .
    snippet('renderers/labeler', ['field' => 'ws_send_main_txt', 'fallback' => 'Sind Sie sicher, dass Sie die Daten dieses Workshops abschicken wollen?'], true) .
    '</p>';

if ($notDoneParticipants > 0) {
    $confirmText .= '<p>' . $notDoneParticipants . ' Teilnehmer haben unvollständige Daten.</p>';
}
if ($notDoneExhibits > 0) {
    $confirmText .= '<p>' . $notDoneExhibits . ' Objekte haben unvollständige Daten.</p>';
}
if ($notDoneExhibitions > 0) {
    $confirmText .= '<p>' . $notDoneExhibitions . ' Ausstellungen haben unvollständige Daten.</p>';
}
?>

<div class="modal fade" id="sendWorkshopModal" tabindex="-1" aria-labelledby="sendWorkshopModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="sendWorkshopModalLabel">Workshop abschicken</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="workshop-form position-relative" action="<?= $page->url() ?>" method="POST">
                <div class="modal-body">
                    <?= $confirmText ?>
                    <input type="hidden" id="notDoneParticipants" name="notDoneParticipants" value="<?= $notDoneParticipants ?>">
                    <input type="hidden" id="notDoneExhibits" name="notDoneExhibits" value="<?= $notDoneExhibits ?>">
                    <input type="hidden" id="notDoneExhibitions" name="notDoneExhibitions" value="<?= $notDoneExhibitions ?>">
                    <label for="ws-comment">Kommentar</label>
                    <textarea id="ws-comment" name="ws-comment" class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" value="Workshop abschicken" name="send-ws" class="btn btn-primary">
                        Workshop abschicken
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
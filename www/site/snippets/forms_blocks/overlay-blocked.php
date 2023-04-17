<?php if (isset($lock)) {
    $lockInfo = $lock->getInfos();
    $time = $lockInfo['time'] ?? '';
}
$locktime = site()->lockedpagestime()->value() ?? 10;

date_default_timezone_set('Europe/Berlin');
?>
<div class="overlay__blocked container">
    <div class="overlay"></div>
    <div class="row h-100 position-relative align-items-center text-center">
        <div class="col">
            <h3>Wird zurzeit von einem anderen Teilnehmer bearbeitet. Sie können nach ca. <?= $locktime ?> Minuten diese Seite trotzdem entsperren.</h3>
            <p>
                <?= isset($lockInfo['curator']) ? $lockInfo['curatorUN'] : $lockInfo['curator'] ?>
            </p>

            <?php if (($time + 60 * $locktime) <= time()) : ?>
                <p>
                    Entsperren: Achtung! es könnte zu überschreibungen führen!
                </p>
                <form action="" method="POST" action="<?= $page->url() ?>">
                    <?php if (isset($lockMe)) : ?>
                        <input type="hidden" id="targetpage" name="targetpage" value="<?= $lockMe ?>">
                        <button class="btn btn-primary" type="submit" name="force-unlock" value="force-unlock">
                            Entsperren
                        </button>
                    <?php else : ?>
                        <p>
                            Fehler: Seite nicht gefunden
                        </p>
                    <?php endif ?>
                </form>
            <?php else : ?>
                <p>
                    Bitte versuchen Sie es später noch mal:
                </p>
                <p>
                    <strong><?= date('H:i:s, d.m.Y', ($time + 60 * $locktime)) ?></strong>
                </p>
            <?php endif ?>
        </div>
    </div>
</div>
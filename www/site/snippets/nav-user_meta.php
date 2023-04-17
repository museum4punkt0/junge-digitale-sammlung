<nav class="user__meta p-4">
    <?php
    $currPage = $page->parent() ?? $page;
    ?>
    <div>
        <b><?= $currPage->institution() ?></b>
    </div>
    <?php
    $classes = array_values($currPage->getParticipantClasses());
    $classesString = [];
    foreach ($classes as $class) {
        if ($class != '')
            array_push($classesString, $class);
    }
    $classesString = implode(', ', $classesString);

    $states = array_values($currPage->getParticipantStates());
    $statesString = [];
    foreach ($states as $state) {
        if ($state != '')
            array_push($statesString, $state);
    }
    $statesString = implode(', ', $statesString);
    ?>
    <div>
        <?= $classesString ?>
    </div>
    <div>
        <i><?= $statesString ?></i>
    </div>
    <?php if ($kirby->user()->linked_workshop()->isNotEmpty()) : ?>
        <div>
            <a href="<?= $kirby->user()->linked_workshop()->toPage()->url() ?>">Lobby ›</a>
        </div>
    <?php endif ?>

    <div>
        <a href="<?= $page->url() ?>?logout=true">Abmelden ›</a>
    </div>
    <p id="logout__counter">00:00</p>
</nav>
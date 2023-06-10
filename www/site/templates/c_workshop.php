<?php snippet('header') ?>
<?php snippet('section-header') ?>

<main>
    <div id="wrapper">
        <div class="container text-center mb-5">
            <h1>
                <?= $page->title()->html() ?>
            </h1>
        </div>
        <?php snippet('renderers/alert'); ?>
        <?php snippet('renderers/modal_confirmation'); ?>
        <?php snippet('renderers/layouts-renderer', ['layouts' => $page->layout()]) ?>
        <!-- USER -->
        <?php if ($authenticated) : ?>
            <?php if ($page->isDraft()) : ?>
                <div class="container">
                    <h2>Das Workshop ist zurzeit deaktiviert. Bitte versuchen Sie es später noch mal.</h2>
                </div>
            <?php else : ?>
                <?php if ($kirby->session()->get('participantLogged')) : ?>

                    <div class="container text-center">
                        <p>Willkommen zurück <?= $kirby->session()->get('participantID') ?>!</p>
                        <a href="<?= $page->url() . '/' . $kirby->session()->get('participantID') ?>">Zu meinem Profil »</a>
                        <p class="mt-5 meta-text text-muted ">Bist du nicht <?= $kirby->session()->get('participantID') ?>? Klicke dann bitte <a href="?changeuser=true">hier</a>.</p>
                    </div>
                <?php else : ?>
                    <!-- PIN FORMS -->
                    <div class="container text-align-center">
                        <div class="row text-center">
                            <h2>Bitte ID eingeben:</h2>
                        </div>
                        <!-- check for page -->
                        <form class="ws-redirect-form pin__form autofocus" autocomplete="off" action="<?= $page->url() ?>" method="POST">
                            <div class="justify-content-center hstack gap-3">
                                <input type="text" autocorrect="off" spellcheck="false" autocapitalize="off" aria-autocomplete="none" autocomplete="off" id="url_first_part" name="url_first_part" class="first field__pin" pattern="[A-Za-z0-9]+" maxlength="1" value="<?= $data['url_first_part'] ?? '' ?>">
                                <input type="text" autocorrect="off" spellcheck="false" autocapitalize="off" aria-autocomplete="none" autocomplete="off" id="url_second_part" name="url_second_part" class="second field__pin" pattern="[A-Za-z0-9]+" maxlength="1" value="<?= $data['url_second_part'] ?? '' ?>">
                                <input type="text" autocorrect="off" spellcheck="false" autocapitalize="off" aria-autocomplete="none" autocomplete="off" id="url_third_part" name="url_third_part" class="third field__pin" pattern="[A-Za-z0-9]+" maxlength="1" value="<?= $data['url_third_part'] ?? '' ?>">
                                <input type="text" autocorrect="off" spellcheck="false" autocapitalize="off" aria-autocomplete="none" autocomplete="off" id="url_fourth_part" name="url_fourth_part" class="fourth field__pin" pattern="[A-Za-z0-9]+" maxlength="1" value="<?= $data['url_fourth_part'] ?? '' ?>">
                            </div>
                            <div class="row py-4">
                                <div class="col d-flex justify-content-center">
                                    <input class="authenticate__user-button btn btn-primary" type="submit" name="search-participant" value="Profil-ID senden" />
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php
                    // if the form input is not valid, show a list of alerts
                    if ($pageexists) : ?>
                        <!-- CHECK IF PIN EXISTS -->
                        <?php
                        $_pinSet = $page->find($urlparticipant)->pin()->isNotEmpty();
                        if ($_pinSet) : ?>
                            <div class="container mt-5">
                                <div class="row text-center">
                                    <h2>Bitte PIN eingeben:</h2>
                                </div>
                                <!-- authenticate for page -->
                                <form class="ws-redirect-auth-form pin__form autofocus" autocomplete="off" action="<?= $page->url() ?>" method="POST">
                                    <div class="justify-content-center hstack gap-3">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="first_pin" name="first_pin" class="first field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1" value="<?= $data['first_pin'] ?? '' ?>">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="second_pin" name="second_pin" class="second field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1" value="<?= $data['second_pin'] ?? '' ?>">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="third_pin" name="third_pin" class="third field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1" value="<?= $data['third_pin'] ?? '' ?>">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="fourth_pin" name="fourth_pin" class="fourth field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1" value="<?= $data['fourth_pin'] ?? '' ?>">
                                        <input type="hidden" id="participant_url" name="participant_url" value="<?= $urlparticipant ?? '' ?>" />
                                    </div>
                                    <div class="row py-4">
                                        <div class="col d-flex justify-content-center">
                                            <input class="authenticate__user-button btn btn-primary" type="submit" name="authenticate-participant" value="PIN validieren" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php else : ?>
                            <div class="container mt-5">
                                <div class="row text-center">
                                    <h2>Bitte eine PIN anlegen:</h2>
                                </div>
                                <form class="ws-set-pin-form pin__form autofocus" autocomplete="off" action="<?= $page->url() ?>" method="POST">
                                    <div class="justify-content-center hstack gap-3">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="first_pin" name="first_pin" class="first field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="second_pin" name="second_pin" class="second field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="third_pin" name="third_pin" class="third field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="fourth_pin" name="fourth_pin" class="fourth field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="hidden" id="participant_url" name="participant_url" value="<?= $urlparticipant ?? '' ?>" />
                                    </div>
                                    <div class="row text-center">
                                        <h2>PIN wiederholen:</h2>
                                    </div>
                                    <div class="justify-content-center hstack gap-3">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="first_pin_confirm" name="first_pin_confirm" class="first field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="second_pin_confirm" name="second_pin_confirm" class="second field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="third_pin_confirm" name="third_pin_confirm" class="third field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                        <input type="password" aria-autocomplete="none" autocomplete="off" id="fourth_pin_confirm" name="fourth_pin_confirm" class="fourth field__pin" pattern="[0-9]+" inputmode="numeric" maxlength="1">
                                    </div>
                                    <div class="row py-4">
                                        <div class="col d-flex justify-content-center">
                                            <input class="set__pin-button btn btn-primary" type="submit" name="set-pin" value="PIN speichern" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif ?>
                    <?php endif ?>
                <?php endif ?>
            <?php endif ?>
        <?php else : ?>
            <!-- user not authenticated -->
            <?= go($kirby->url()) ?>
        <?php endif ?>
    </div>
</main>

<?php snippet('footer') ?>
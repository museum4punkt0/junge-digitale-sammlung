<?php if (isFeatureAllowed('embeds')) : ?>
    <?php if ($embed = $page->embed_url()->toEmbed()) : ?>
        <section class="embed__container row justify-content-center text-center <?= $embed->providerName()->lower() ?>">
            <div>
                <?php if ($embed->providerName()->lower() == 'instagram') : ?>
                    <?= $embed->code() ?>
                <?php else : ?>
                    <div class="exhibit-embed">
                        <?= $embed->code() ?>
                    </div>
                <?php endif ?>
            </div>
        </section>
    <?php else : ?>
        <section class="embed__container row justify-content-center text-center">
            <div class="">
                <div class="exhibit-embed h-100">
                    <span class="empty w-75 text-primary"><i icon-name="help-circle" class="icon-only"></i></span>
                </div>
            </div>
        </section>
    <?php endif ?>
<?php else : ?>
    <section class="embed__container no-cookies row justify-content-center text-center">
        <div class="">
            <div class="exhibit-embed d-block h-100">
                <p>
                    <?= snippet('renderers/labeler', ['field' => 'cookies_infotext', 'fallback' => 'Cookies für externe Inhalte sind deaktiviert. Bitte passe die Einstellungen an, wenn du diese Inhalte sehen willst.']) ?>
                <div id="edit-embed-cookie"><strong role="button">Anpassen</strong></div>
                <script>
                    document.querySelector('#edit-embed-cookie').addEventListener('click', function() {
                        const event = document.createEvent('HTMLEvents');
                        event.initEvent('cookies:update', true, false);
                        document.querySelector('body').dispatchEvent(event);
                    });
                </script>
                </p>
                <p>
                    <i icon-name="cookie" class="icon-only"></i>
                </p>
            </div>
        </div>
    </section>
<?php endif; ?>
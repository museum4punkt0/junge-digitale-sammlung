<?php if (isFeatureAllowed('embeds')) : ?>
    <div id="embed__preview" class=""></div>
<?php else : ?>
    <section class="embed__container row justify-content-center text-center">
        <div class="">
            <div class="exhibit-embed h-100">
                <p class="me-4">
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
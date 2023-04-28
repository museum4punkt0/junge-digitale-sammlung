<?php snippet('cookie-modal', ['assets' => true]) ?>
<button class="btn" id="edit-cookies" title="Cookies" aria-label="Cookies"><span class="d-none d-lg-block">Cookies</span><i icon-name="cookie" class="icon-only d-lg-none"></i></button>
<script>
    document.querySelector('#edit-cookies').addEventListener('click', function(e) {
        e.preventDefault();
        const event = document.createEvent('HTMLEvents');
        event.initEvent('cookies:update', true, false);
        document.querySelector('body').dispatchEvent(event);
    });
</script>
<?php
$imgurl = "";
$imgalt = "";
$src = "";
$exposure = 1;

if ($img = $page->exhibit_preview()->toFile()) {
    $imgurl = $img->url();
    $imgalt = $img->alt();
}

if ($video = $page->digital_asset()->toFile())
    $src = $video->url();
?>

<section class="digital__container row justify-content-center">
    <div class="col align-self-center text-center exhibit-digital">
        <?php if ($src && $imgurl) : ?>
            <video crossorigin id="<?= $video->id() ?>" class="vlite-js modal__video" src="<?= $src ?>" poster="<?= $img->thumb([
                                                                                                                    'width'   => 800,
                                                                                                                    'height'  => 450,
                                                                                                                    'quality' => 50,
                                                                                                                    'format'  => 'webp',
                                                                                                                ])->url() ?>">
            </video>
        <?php elseif ($src) : ?>
            <video crossorigin id="<?= $video->id() ?>" class="vlite-js modal__video" src="<?= $src ?>">
            </video>
        <?php elseif ($imgurl) : ?>
            <div id="gallery" class="pswp-gallery">
                <a class="pswp-gallery__item" href="<?= $imgurl ?>" target="_blank" data-pswp-width="<?= $img->width() ?>" data-pswp-height="<?= $img->height() ?>">
                    <?= $img->responsiveImg() ?>
                </a>
            </div>
        <?php else : ?>
            <div>Keine Medien gefunden</div>
        <?php endif ?>
    </div>
</section>
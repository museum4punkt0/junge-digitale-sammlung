<script>
    self.ModelViewerElement = self.ModelViewerElement || {};
    self.ModelViewerElement.dracoDecoderLocation = '<?= kirby()->url() ?>/assets/js/vendor/decoder/';
</script>
<?= js('/assets/js/vendor/model-viewer.min.js', ['type' => 'module']) ?>
<?php
$imgurl = "";
$imgalt = "";
$modelurl = "";
$exposure = 1;

if ($img = $page->exhibit_preview()->toFile()) {

    $imgurl =  $img->thumb([
        'width'   => 250,
        'quality' => 5,
    ])->url();
    $imgalt = $img->alt();
}

if ($model = $page->threed_model()->toFile())
    $modelurl = $model->url();

$exposure = $page->threed_model_light()->value();
$model_size = $page->threed_model_size()->toBool() ? 'size-compact' : 'size-regular';
?>

<?php if ($modelurl && $imgurl) : ?>
    <section class="object__viewer">
        <model-viewer class="exhibit-3d <?= $model_size ?>" id="reveal" loading="eager" exposure="<?= $exposure ?>" disable-tap disable-pan camera-controls touch-action="pan-y" auto-rotate poster="<?= $imgurl ?>" src="<?= $modelurl ?>" shadow-intensity="0" alt="<?= $imgalt ?>" style="background-color: unset;">
            <div class="viewer-progress" slot="progress-bar"></div>
        </model-viewer>
    </section>
<?php elseif (!$modelurl && $imgurl) : ?>
    <section class="digital__container row justify-content-center">
        <div class="col align-self-center text-center exhibit-digital">
            <div id="gallery" class="pswp-gallery">
                <a class="pswp-gallery__item" href="<?= $img->url() ?>" target="_blank" data-pswp-width="<?= $img->width() ?>" data-pswp-height="<?= $img->height() ?>">
                    <?= $img->responsiveImg() ?>
                </a>
            </div>
        </div>
    </section>
<?php else : ?>
    <section class="object__viewer">
        <div class="exhibit-3d no-object row justify-content-center text-center">
            <!--<section class="exhibit-3d no-object row justify-content-center text-center"></section>-->
            <span class="empty w-50 text-primary"><i icon-name="help-circle" class="icon-only"></i></span>
        </div>
    </section>
<?php endif ?>
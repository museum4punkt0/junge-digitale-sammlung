<?php snippet('header') ?>
<?php snippet('section-header') ?>

<main>
  <div id="wrapper" class="d-flex">
    <div class="container overflow-hidden align-self-center">
      <div class="row pb-2">
        <h1>
          <?= $page->title()->html() ?>
        </h1>
      </div>
      <?php snippet('renderers/alert'); ?>
      <form method="post" action="<?= $page->url() ?>">
        <div class="row pb-2">
          <div class="col-12 col-md-3">
            <label for="username"><?= $page->username()->html() ?></label>
          </div>
          <div class="col">
            <input autocapitalize="off" class="form-control" type="username" id="username" name="username" value="<?= get('username') ? esc(get('username'), 'attr') : '' ?>">
          </div>
        </div>
        <div class="row pb-2">
          <div class="col-12 col-md-3">
            <label for="password"><?= $page->password()->html() ?></label>
          </div>
          <div class="col">
            <input class="form-control" type="password" id="password" name="password" value="<?= get('password') ? esc(get('password'), 'attr') : '' ?>">
          </div>
        </div>
        <div class="row pb-2">
          <div class="col d-md-flex justify-content-end">
            <input type="submit" class="btn btn-primary" name="login" value="<?= $page->button()->html() ?>">
          </div>
        </div>
      </form>
    </div>
  </div>
</main>

<?php snippet('footer') ?>
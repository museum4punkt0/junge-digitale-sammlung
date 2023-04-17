<li class="column" style="--columns: 3">
    <a href="<?= $project->id() ?>">
      <figure>
        <span class="img" style="--w:4;--h:5">
          <?= ($cover = $project->cover()) ? $cover->crop(400, 500) : null ?>
        </span>
        <figcaption class="img-caption">
          <?= $project->title()->html() ?>
        </figcaption>
      </figure>
    </a>
</li>
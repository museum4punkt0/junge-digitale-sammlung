<?php
class CCuratorLeaderPage extends JDSPage
{
  public function updateAtCreation($data = null) // override
  {
    $creationData = [
      option('2av.jds-pagemodel.dateCreated') => time(),
      option('2av.jds-pagemodel.createdByUser') => kirby()->user()->name(),
      'title' => $this->title() . '-' . $data,
    ];

    $this->update($creationData);
  }

  public static function hookPageCreate($page)
  {
    $page = $page->changeStatus('unlisted');

    $existingUsers = $page->parent()->childrenAndDrafts()->filterBy('intendedTemplate', 'in', ['c_curator', 'c_curator_leader']);
    $existingIds = [];

    if ($existingUsers->isNotEmpty()) {
      foreach ($existingUsers as $eu) {
        array_push($existingIds, $eu->slug());
      }
    }

    $pagestamp = random_strings(1, 4);

    while (in_array($pagestamp, $existingIds)) {
      $pagestamp = random_strings(1, 4);
    }

    $page = $page->changeSlugOnly($pagestamp);
    //$page = $page->changeTitleAtCreation($pagestamp);
    $page->updateAtCreation($pagestamp);
  }
}

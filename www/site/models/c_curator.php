<?php
class CCuratorPage extends JDSPage
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

  public static function hookPageCreate($page) // plugin
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

  public function update(array $input = NULL, string $languageCode = NULL, bool $validate = false)
  {
    //$input['title'] = $this->title();

    // repopulate from existing data in case the fields were not updated
    $input['fullname'] = $input['fullname'] ?? $this->fullname()->value();
    $input['username'] = $input['username'] ?? $this->username()->value();
    $input['dse'] = $input['dse'] ?? $this->dse()->value();

    $dataRules = [
      'fullname'              => ['required'],
      'username'              => ['required'],
      'dse'                   => ['required'],
      /* 'gender'                => [''],
      'age'                   => [''],
      'birthcountry'          => [''],
      'birthcountry_comment'  => [''],
      'stations'              => [''],
      'stations_comment'      => [''],
      'personaldrive'         => [''], */
    ];
    $messages = [
      'fullname'  => 'Name fehlt.',
      'username'  => 'Benutzername fehlt.',
      'dse'       => 'Datenschuzterklärung nicht angekreuzt.',
    ];

    if ($isComplete = invalid($input, $dataRules, $messages)) {
      $input['complete'] = false;
      $input['missingInfo'] = implode('<br>', array_values($isComplete));
    } else {
      $input['complete'] = true;
      $input['missingInfo'] = '';
    }

    return parent::update($input);
  }

  public static function hookPageUpdateAfter($newPage, $oldPage)
  {

    /*** REBUILD USER and EXHIBIT REFERENCES ****/
    $linked_exhibitNew = $newPage->linked_exhibit()->toPageOrDraft();
    $linked_exhibitOld = $oldPage->linked_exhibit()->toPageOrDraft();

    if ($linked_exhibitNew != $linked_exhibitOld) {

      if ($linked_exhibitNew && !$linked_exhibitOld) {
        // from none to selected

        if ($oldUserNewExhibit = $linked_exhibitNew->linked_user()->toPageOrDraft()) { // reset olduser from new exhibit                        
          $result = $oldUserNewExhibit->update([
            'linked_exhibit' => []
          ]);
        }
        $linked_exhibitNew->update([
          'linked_user' => [$newPage]
        ]);
      } else if (!$linked_exhibitNew && $linked_exhibitOld) {
        // from selected to none

        $linked_exhibitOld->update([
          'linked_user' => []
        ]);
      } else {
        // changed exhibits

        if ($oldUserNewExhibit = $linked_exhibitNew->linked_user()->toPageOrDraft()) { // reset olduser from new exhibit                        
          $result = $oldUserNewExhibit->update([
            'linked_exhibit' => []
          ]);
        }

        $linked_exhibitNew->update([ // set newuser in new exhibit
          'linked_user' => [$newPage]
        ]);

        $linked_exhibitOld->update([ // reset me from old exhibit
          'linked_user' => []
        ]);
      }
    }
  }

  public static function hookDeleteBefore($page, $force)
  {
    if ($exhibit = $page->linked_exhibit()->toPageOrDraft()) {
      $updateexhibit = $exhibit->update([
        'linked_user' => []
      ]);
    }

    if ($exhibition = $page->linked_exhibition()->toPageOrDraft()) {
      $exhibition->removeUser($page);
    }

    usernameRemove($page->username());
  }
}

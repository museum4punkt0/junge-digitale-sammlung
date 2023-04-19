<?php
class CExhibitionPage extends JDSPage
{

  protected $exhibitLimit = 5;

  public function update(array $input = NULL, string $languageCode = NULL, bool $validate = false)
  {
    $input['exhibitiontitle']    = $input['exhibitiontitle'] ?? $this->title();
    $input['impulse']            = $input['impulse'] ?? $this->impulse()->value();
    $input['exhibitionintro']       = $input['exhibitionintro'] ?? $this->exhibitionintro()->value();

    $dataRules = [
      'exhibitiontitle'    => ['required'],
      'impulse'            => ['required'],
      'exhibitionintro'       => ['required'],
    ];

    $messages = [
      'exhibitiontitle '  => 'Bitte einen Name eingeben.',
      'impulse'           => 'Bitte ein Thema eingeben.',
      'exhibitionintro'      => 'Einführungstext fehlt.',
    ];

    if ($isComplete = invalid($input, $dataRules, $messages)) {
      $input['complete'] = false;
      $input['missingInfo'] = implode('<br>', array_values($isComplete));
    } else {
      $input['complete'] = true;
      $input['missingInfo'] = '';
    }


    // refresh message infos in exhibition, if it is linked
    $usersArray = [];
    $usersArray['impulse'] = $input['impulse'] ?? $this->impulse()->value(); // we also need to pass the impulse
    for ($x = 1; $x <= $this->exhibitLimit; $x++) {
      if (isset($input['user' . $x])) {
        $usersArray['user' . $x]   = $input['user' . $x];
      } else if ($this->content()->get('user' . $x)->isNotEmpty()) { 
        $usersArray['user' . $x]   = $this->content()->get('user' . $x)->toPageOrDraft()->id();
      }
    }

    $exhibition_msgs = handleExhibitionMessages($usersArray, $this);
    $input = array_merge($input, $exhibition_msgs);

    return parent::update($input);
  }

  public static function hookPageCreate($page) // is in plugin
  {
    $linked_usersNew = $page->getLinkedUsers($page);

    foreach ($linked_usersNew as $key => $lu) {
      if ($lu->linked_exhibition() && $lu->linked_exhibition()->isNotEmpty()) {
        $old_exhibition = $lu->linked_exhibition()->toPageOrDraft();

        if ($old_exhibition != $page) {
          $old_exhibition->removeUser($lu);
        }
      }

      $lu->update([
        'linked_exhibition' => [$page],
      ]);
    }

    $indexedSlug = $page->checkSlugIndex($page->tempslug());
    $page->changeSlugOnly($indexedSlug);
  }

  public static function hookPageUpdateAfter($newPage, $oldPage)
  {

    /*** REBUILD EXHIBIT and EXHIBITION REFERENCES ****/
    $linked_usersNew = $newPage->getLinkedUsers($newPage);
    $linked_usersOld = $oldPage->getLinkedUsers($oldPage);

    if ($linked_usersNew != $linked_usersOld) {

      if (is_array($linked_usersNew) && count($linked_usersNew) > 0) {
        foreach ($linked_usersNew as $key => $lu) {
          if (is_array($linked_usersOld) && count($linked_usersOld) > 0) {
            if (!in_array($lu, $linked_usersOld)) { // was not already in content
              if ($lu->linked_exhibition() && $lu->linked_exhibition()->isNotEmpty()) {
                $old_exhibition = $lu->linked_exhibition()->toPageOrDraft();

                if ($old_exhibition != $newPage) {
                  $old_exhibition->removeUser($lu);
                }
              }
            }
          }
          $lu->update([
            'linked_exhibition' => [$newPage],
          ]);
        }
      }

      if (is_array($linked_usersOld) && count($linked_usersOld) > 0) {
        foreach ($linked_usersOld as $key => $lu) {
          if (!$linked_usersNew || !in_array($lu, $linked_usersNew)) { // if the new set of users is empty || if it is not in the new set anymore
            $lu->update([
              'linked_exhibition' => [],
            ]);
          }
        }
      }
    }
  }

  public function getLinkedUsers($_page)
  {
    $result = array();

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $_page->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty())
        if ($_field->toPageOrDraft()) {
          array_push($result, $_field->toPageOrDraft());
        }
    }

    return $result;
  }

  public function getLinkedExhibits()
  {
    $result = array();

    $users = $this->getLinkedUsers($this);

    foreach ($users as $u) {
      if ($exhibit = $u->linked_exhibit()->toPageOrDraft()) {
        array_push($result, $exhibit);
      }
    }

    return $result;
  }


  public function checkUser($userpage)
  {
    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty()) {
        $fieldpage = $_field->toPageOrDraft();

        if ($userpage == $fieldpage) {
          return true;
        }
      }
    }

    return false;
  }

  public function removeUser($userpage)
  {
    $updateData = [];

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty()) {
        $fieldpage = $_field->toPageOrDraft();

        if ($userpage == $fieldpage) {
          $updateData['user' . $i] = [];
        }
      }
    }

    $this->update($updateData);
  }

  public function getLinkedUsersStates()
  {
    $result = [];

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty()) {
        if ($f = $_field->toPageOrDraft()) {

          if (!in_array($f->curator_state()->value(), $result)) {
            array_push($result, $f->curator_state()->value());
          }
        }
      }
    }

    return $result;
  }

  public function getLinkedUsersClasses()
  {
    $result = [];

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty()) {
        if ($f = $_field->toPageOrDraft()) {

          if (!in_array($f->schoolclass()->value(), $result)) {
            array_push($result, $f->schoolclass()->value());
          }
        }
      }
    }

    return $result;
  }

  public function getLinkedUsersCount()
  {
    $amount = 0;

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty())
        $amount++;
    }

    return $amount;
  }

  public function getLinkedTotalExhibitsCount()
  {
    $amount = 0;

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty()) {
        if ($_field->toPageOrDraft()->linked_exhibit() && $_field->toPageOrDraft()->linked_exhibit()->isNotEmpty()) {
          $amount++;
        }
      }
    }

    return $amount;
  }

  public function getLinkedActiveExhibitsCount()
  {
    $amount = 0;

    for ($i = 1; $i <= $this->exhibitLimit; $i++) {
      $_field = $this->content()->get('user' . $i);

      if ($_field && $_field->isNotEmpty()) {
        if ($_field->toPageOrDraft()->linked_exhibit() && $_field->toPageOrDraft()->linked_exhibit()->isNotEmpty() && !$_field->toPageOrDraft()->linked_exhibit()->toPageOrDraft()->isDraft()) {
          $amount++;
        }
      }
    }

    return $amount;
  }

  public static function hookDeleteBefore($page, $force)
  {
    $allLinkedUsers = $page->getLinkedUsers($page);

    foreach ($allLinkedUsers as $u) {
      $u->update([
        'linked_exhibition' => []
      ]);
    }
  }
}

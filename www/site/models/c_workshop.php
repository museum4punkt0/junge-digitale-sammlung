<?php
// extends JDSPage for save-history logging and other functions
/**
 * Workshop Model
 * Mainly handles some relinking when deleted or updated.
 * Also handles the logic for checking if the username already
 * exists inside the workshop. The system will loop through all
 * workshops and call the function in each workshop.
 * 
 * Has some handy function to get information about exhibits and exhibitions
 */


class CWorkshopPage extends JDSPage
{

  public function updateAtCreation($data = null) // override
  {
    $materials = site()->children()->filterBy('intendedTemplate', 'materials')->children()->filterBy('intendedTemplate', 'material');

    $creationData = [
      option('2av.jds-pagemodel.dateCreated') => time(),
      option('2av.jds-pagemodel.createdByUser') => kirby()->user()->name(),
      'materials' => $materials->toArray(),
    ];

    $userDB = $this->createChild([
      'content'  => [
        'title' => $this->title()->value() . '-userDB'
      ],
      'template' => 'd_usernames',
    ]);

    $dbLink = [];
    if ($userDB) {
      $dbLink = [
        'data_usernames_page' => [$userDB->uuid()]
      ];
    }

    $this->update(array_merge($creationData, $dbLink));
  }

  public static function hookPageUpdateAfter($newPage, $oldPage)
  {
    $linked_userNew = $newPage->mainuser()->toUser();
    $linked_userOld = $oldPage->mainuser()->toUser();

    if ($linked_userNew != $linked_userOld) {

      if ($linked_userNew && !$linked_userOld) {
        // from none to selected

        if ($oldPageNewWorkshop = $linked_userNew->linked_workshop()->toPageOrDraft()) { // reset oldPage from new workshop  

          if ($oldPageNewWorkshop->id() != $newPage->id()) {
            $result = $oldPageNewWorkshop->update([
              'mainuser' => []
            ]);
          }
        }
        $linked_userNew->update([
          'linked_workshop' => [$newPage]
        ]);
      } else if (!$linked_userNew && $linked_userOld) {
        // from selected to none

        $linked_userOld->update([
          'linked_workshop' => []
        ]);
      } else {
        // changed users

        if ($oldPageNewWorkshop = $linked_userNew->linked_workshop()->toPageOrDraft()) { // reset oldPage from new workshop                        
          $result = $oldPageNewWorkshop->update([
            'mainuser' => []
          ]);
        }

        $linked_userNew->update([ // set newPage in new workshop
          'linked_workshop' => [$newPage]
        ]);

        $linked_userOld->update([ // reset me from old workshop
          'linked_workshop' => []
        ]);
      }
    }
  }

  function getParticipantClasses()
  {
    $result = [];
    $curators = $this->children()->filterBy('intendedTemplate', 'c_curator');

    foreach ($curators as $curator) {
      if (!in_array($curator->schoolclass()->mapValueToLabel(), $result)) {
        array_push($result, $curator->schoolclass()->mapValueToLabel());
      }
    }
    sort($result, SORT_NATURAL | SORT_FLAG_CASE);
    return $result;
  }

  function getParticipantStates()
  {
    $result = [];
    $curators = $this->children()->filterBy('intendedTemplate', 'c_curator');

    foreach ($curators as $curator) {
      if (!in_array($curator->curator_state()->mapValueToLabel(), $result)) {
        array_push($result, $curator->curator_state()->mapValueToLabel());
      }
    }
    sort($result, SORT_NATURAL | SORT_FLAG_CASE);
    return $result;
  }

  public function getPreviewImages()
  {
    $workshopImages = $this->images()->filterBy('template', 'previewimage');
    return $workshopImages;
  }

  public function getModels()
  {
    $workshopModels = $this->files()->filterBy('template', 'gltf');
    return $workshopModels;
  }


  ////////////////////////////////////////////////

  function usernameExists($username)
  {
    if (!$dbpage = $this->data_usernames_page()->toPage()) {

      $dbpage = $this->createChild([
        'content'  => [
          'title' => $this->title()->value() . '-userDB'
        ],
        'template' => 'd_usernames',
      ]);

      if ($dbpage) {
        $this->update([
          'data_usernames_page' => [$dbpage->uuid()],
        ]);
      }
    }

    $db = $dbpage->username_db()->yaml();
    $indexUsername = substr($username, 0, 1);

    foreach ($db as $db_index) {
      if ($indexUsername == $db_index['index']) {
        $entries = $db_index['index_structure'];

        foreach ($entries as $entry) {
          if (in_array($username, $entry))
            return true;
        }
      }
    }

    return false;
  }

  function usernameWrite($username, $oldUsername, $user)
  {
    $isChanging = $username != $oldUsername;

    if ($isChanging) {
      if (!$dbpage = $this->data_usernames_page()->toPage())
        return;

      $db = $dbpage->username_db()->yaml();

      $indexUsername = substr($username, 0, 1);
      $targetArray = [];
      $targetKey = '';

      $indexOldUsername = substr($oldUsername, 0, 1);
      $targetOldArray = [];
      $targetOldKey = '';

      foreach ($db as $key => $db_index) {
        // find arrays and keys for new entry
        if ($indexUsername == $db_index['index']) {
          $targetArray = $db_index;
          $targetKey = $key;
        }

        // find arrays and keys for old entry. also old data in case we need to remove data
        if ($indexOldUsername == $db_index['index']) {
          $targetOldArray = $db_index;
          $targetOldKey = $key;

          //if ($isChanging) {
          $entries = $db_index['index_structure'];

          foreach ($entries as $entrykey => $entry) {
            if (in_array($oldUsername, $entry)) {
              // remove element if it was in old array data ...
              unset($entries[$entrykey]);

              // ... and update targeted array
              if ($targetKey == $targetOldKey) {
                $targetArray['index_structure'] = $entries;
              } else {
                $targetOldArray['index_structure'] = $entries;
              }
            }
          }
          // }
        }
      }

      // if index didnt exist already we create the index entry ...
      if (!$targetArray) {
        $usrnmData = [];
        $usrnmData['username'] = $username;
        $usrnmData['user'] = [$user];

        // prepare new entry data
        $targetArray['index'] = $indexUsername;
        $targetArray['index_structure'][] = $usrnmData;

        // create the new entry
        $db[] = $targetArray;
      }
      // ... or we update the old index
      else {
        $usrnmData = [];
        $usrnmData['username'] = $username;
        $usrnmData['user'] = [$user];

        // prepare new entry data
        $targetArray['index_structure'][] = $usrnmData;

        // update the entry of whole index
        $db[$targetKey] = $targetArray;
      }

      if ($targetKey != $targetOldKey) {
        $db[$targetOldKey] = $targetOldArray;
      }

      try {
        $this->data_usernames_page()->toPage()->update([
          'username_db' => Yaml::encode($db)
        ]);
      } catch (Exception $error) {
        $errors .= 'Submission logging failed: ' . $error->getMessage() . '. ';
      }
    }
  }

  function usernameRemove($username)
  {
    if (!$dbpage = $this->data_usernames_page()->toPage())
      return;

    $db = $dbpage->username_db()->yaml();
    $indexUsername = substr($username, 0, 1);

    foreach ($db as $indexkey => $db_index) {

      // find arrays and keys for old entry. also old data in case we need to remove data
      if ($indexUsername == $db_index['index']) {
        $targetArray = $db_index;
        $targetKey = $indexkey;

        $entries = $db_index['index_structure'];

        foreach ($entries as $entrykey => $entry) {
          if (in_array($username, $entry)) {
            // remove element if it was in array data ...
            unset($entries[$entrykey]);

            // ... and update targeted array
            $targetArray['index_structure'] = $entries;

            $db[$targetKey] = $targetArray;

            try {
              $this->data_usernames_page()->toPage()->update([
                'username_db' => Yaml::encode($db)
              ]);

              $msg = 'Removed from DB';
            } catch (Exception $error) {
              $msg = 'Submission logging failed: ' . $error->getMessage() . '. ';
            }

            return true;
          }
        }
      }
    }

    return false;
  }
}

<?php
class CExhibitPage extends JDSPage
{

  public function update(array $input = NULL, string $languageCode = NULL, bool $validate = false)
  {
    if (!$input)
      $input = array();

    $input['exhibitname'] = $input['exhibitname'] ?? $this->title(); // originally 'exhibitname'
    $input['museum_preview']   = $input['museum_preview'] ?? (($this->museum_preview()->isNotEmpty() && $this->museum_preview()->toFile()) ? $this->museum_preview()->toFile()->filename() : null);

    $input['impulse']             = $input['impulse'] ?? $this->impulse()->value();
    $input['type']                = $input['type'] ?? $this->type()->value();
    $input['classification']      = $input['classification'] ?? $this->classification()->value();
    $input['description']         = $input['description'] ?? $this->description()->value();
    $input['personal_relation']   = $input['personal_relation'] ?? $this->personal_relation()->value();


    $dataRules = [
      'exhibitname'          => ['required'],
      'impulse'              => ['required'],
      'type'                 => ['required'],
      'classification'       => ['required'],
      'description'          => ['required'],
      'personal_relation'    => ['required'],
    ];

    $messages = [
      'exhibitname'       => 'Objektname fehlt.',
      'impulse'           => 'Thema fehlt.',
      'type'              => 'Objekttyp fehlt.',
      'classification'    => 'Klassifikation fehlt.',
      'description'       => 'Objektbeschreibung fehlt.',
      'personal_relation' => 'Objektstory fehlt.',

      //physisch
      'scan_device'       => 'Digitalisierungsgerät fehlt.',
      'museum_preview'    => 'Vorschaubild für Museum fehlt.',
      'exhibit_preview'   => 'Vorschaubild fehlt.',
      'threed_model'      => '3D Model fehlt.',

      //digital embed
      'embed_url'         => 'Embed URL fehlt.',

      //digital born
      //'exhibit_preview'   => 'Bild des Objekts fehlt.', same as physical
      'digital_asset'     => 'Mediendatei fehlt.',
    ];

    $oldExhibitType = $this->type()->value();
    $exhibitType = $input['type'] ?? $this->type()->value();

    if ((int)$exhibitType == '0') { // physical exhibit
      $physicalRules = [
        //'scan_date' => ['required'],
        'scan_device' => ['required'],
        'museum_preview' => ['required'],
        'exhibit_preview' => ['required'],
        'threed_model' => ['required'],
      ];

      //$input['scan_date']  = $input['scan_date'] ?? $this->scan_date();
      $input['scan_device']   = $input['scan_device'] ?? $this->scan_device()->value();

      $modelValues = bindImageTo3DModel($this, $input['exhibit_preview'] ?? null);
      if ($modelValues) {
        $input = array_merge($input, $modelValues);
        kirbylog($this->title()->value() . ": 3D file updated (in model update)");
      } else {
        $input['exhibit_preview'] = $input['exhibit_preview'] ?? ($this->exhibit_preview()->isNotEmpty() ? [$this->exhibit_preview()->toFile()->uuid()] : null);
        $input['threed_model'] = $input['threed_model'] ?? ($this->threed_model()->isNotEmpty() ? [$this->threed_model()->toFile()->uuid()] : null);
      }

      $dataRules = array_merge($dataRules, $physicalRules);
    } else if ((int)$exhibitType == '1') { // digital embed exhibit
      $digitalEmbedRules = [
        'embed_url'            => ['required'],
      ];

      $input['embed_url']   = $input['embed_url'] ?? $this->embed_url()->value();

      $dataRules = array_merge($dataRules, $digitalEmbedRules);
    } else if ((int)$exhibitType == '2') { // born digital exhibit
      $digitalBornRules = [
        'exhibit_preview'          => ['required'],
        'digital_asset'            => ['required'],
      ];

      $input['exhibit_preview'] = $input['exhibit_preview'] ?? ($this->exhibit_preview()->isNotEmpty() ? $this->exhibit_preview()->toFile()->filename() : null);
      $input['digital_asset']   = $input['digital_asset'] ?? ($this->digital_asset()->isNotEmpty() ? $this->digital_asset()->toFile()->filename() : null);

      $dataRules = array_merge($dataRules, $digitalBornRules);
    }

    //if changing types, reset the preview image anyway
    if ($oldExhibitType != $exhibitType) {
      $input['exhibit_preview']  = [];
      $input['digital_asset']  = [];

      // remove all images with image and asset template, so we have no large data garbage on the server
      $files = $this->files()->filterBy('template', 'in', ['image', 'asset']);
      foreach ($files as $f) {
        $status = $f->delete();

        if (!$status)
          kirbylog($this->title()->value() . ": Fehler beim Löschen einer alten Datei (in model update)");
      }
    }

    if ($isComplete = invalid($input, $dataRules, $messages)) {
      $input['complete'] = false;
      $input['missingInfo'] = implode('<br>', array_values($isComplete));
    } else {
      $input['complete'] = true;
      $input['missingInfo'] = '';
    }

    unset($input['exhibitname']);

    return parent::update($input);
  }

  public static function hookPageCreate($page) // plugin
  {
    $tempslug = $page->checkSlugIndex($page->tempslug());
    $page->changeSlugOnly($tempslug);
  }

  public static function hookChangeSlugAfter($newPage, $oldPage)
  {
    if ($newPage->from_frontend()->toBool()) {
      $linked_userNew =  $newPage->linked_user()->toPageOrDraft();
      $linked_userNew->update([
        'linked_exhibit' => [$newPage]
      ]);

      // refresh message infos in exhibition, if it is linked
      if ($exhibition = $linked_userNew->linked_exhibition()->toPageOrDraft()) {
        $exhibition_msgs = handleExhibitionMessages($exhibition->content()->data(), $exhibition);
        $exhibition->update($exhibition_msgs);
      }
    }
  }

  public static function hookPageUpdateAfter($newPage, $oldPage)
  {
    /*** REBUILD EXHIBIT and USER REFERENCES ****/
    $linked_userNew = $newPage->linked_user()->toPageOrDraft();
    $linked_userOld = $oldPage->linked_user()->toPageOrDraft();

    if ($linked_userNew != $linked_userOld) {

      if ($linked_userNew && !$linked_userOld) {
        // from none to selected
        if ($oldExhibitNewUser = $linked_userNew->linked_exhibit()->toPageOrDraft()) { // reset oldexhibit from new user                        
          $result = $oldExhibitNewUser->update([
            'linked_user' => []
          ]);
        }
        $linked_userNew = $linked_userNew->update([
          'linked_exhibit' => [$newPage]
        ]);
      } else if (!$linked_userNew && $linked_userOld) {
        // from selected to none
        $linked_userOld = $linked_userOld->update([
          'linked_exhibit' => []
        ]);
      } else {
        // changed exhibits
        if ($oldExhibitNewUser = $linked_userNew->linked_exhibit()->toPageOrDraft()) { // reset oldexhibit from new user                        
          $result = $oldExhibitNewUser->update([
            'linked_user' => []
          ]);
        }

        $linked_userNew = $linked_userNew->update([ // set exhibit in new user
          'linked_exhibit' => [$newPage]
        ]);

        $linked_userOld = $linked_userOld->update([ // reset me from old user
          'linked_exhibit' => []
        ]);
      }
    }


    // refresh messages of exhibition. we check if exhibit exists, since update gets called before hookChangeSlugAfter
    if ($linked_userNew && $linked_userNew->linked_exhibit()->isNotEmpty()) {
      // refresh message infos in exhibition, if it is linked
      if ($exhibition = $linked_userNew->linked_exhibition()->toPageOrDraft()) {
        $exhibition->update(); // we trigger an empty update of the exhibition so the messages can be updated in the exhibition model
      }
    }
  }

  public static function hookDeleteBefore($page, $force)
  {
    if ($curator = $page->linked_user()->toPageOrDraft()) {
      $updatecurator = $curator->update([
        'linked_exhibit' => []
      ]);
    }
  }

  public static function hookDeleteAfter(bool $status, Kirby\Cms\Page $page)
  {
    // refresh message infos in exhibition, if it is linked
    $linked_user = $page->linked_user()->toPageOrDraft();
    if ($exhibition = $linked_user->linked_exhibition()->toPageOrDraft()) {
      $exhibition_msgs = handleExhibitionMessages($exhibition->content()->data(), $exhibition);
      $exhibition->update($exhibition_msgs);
    }
  }

  public function getPreviewImages()
  {
    $workshopImages = $this->parent()->images()->filterBy('template', 'previewimage');

    //$workshopImages = $workshopImages->add($this->images()->filterBy('template', 'image'));

    return $workshopImages;
  }

  public function getModels()
  {
    $workshopModels = $this->parent()->files()->filterBy('template', 'gltf');

    //$workshopModels = $workshopModels->add($this->files()->filterBy('template', 'gltf'));

    return $workshopModels;
  }
}

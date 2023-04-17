<?php

use Kirby\Cms\CustomContentLock;

class JDSPage extends Page
{
    /**
     * Gets called once after page was created
     *
     * @param  Array $data
     * @return void
     */
    public function updateAtCreation($data = null)
    {
        $creationData = [
            option('2av.jds-pagemodel.dateCreated') => time(),
            option('2av.jds-pagemodel.createdByUser') => kirby()->user()->name()
        ];

        $this->update($creationData);
    }

    public function update(array $input = NULL, string $languageCode = NULL, bool $validate = false)
    {
        date_default_timezone_set('Europe/Berlin');

        $timestamp = time();
        $username = kirby()->user()->name() == "" ? 'System Task' : kirby()->user()->name();

        $fmt = datefmt_create(
            'de_DE',
            IntlDateFormatter::LONG,
            IntlDateFormatter::LONG,
            'Europe/Berlin',
            IntlDateFormatter::GREGORIAN,
            option('2av.jds-pagemodel.dateFormatIntl')
        );
        $ldString = datefmt_format($fmt, $timestamp);


        $pagelogs = $this->updateLogs()->yaml() ?? [];
        $pagelogs[] = [
            'user' => $username,
            'time' => date(option('2av.jds-pagemodel.dateFormat'), $timestamp),
            'time' => $ldString,
        ];

        $logsLength = 30;
        if (count($pagelogs) > $logsLength) {
            $offset = count($pagelogs) - $logsLength;
            $pagelogs = array_slice($pagelogs, $offset, $logsLength);
        }

        $input[option('2av.jds-pagemodel.dateModified')] = $timestamp;
        $input[option('2av.jds-pagemodel.modifiedByUser')] = $username;
        $input[option('2av.jds-pagemodel.updateLogs')] = $pagelogs;

        if (isset($input['title']) && $input['title'] != $this->title()) {
            $input['delayedSlugChange'] = true;
        }

        if ($this->parent() && $this->parent()->intendedTemplate() == 'c_workshop') {
            if ($this->parent()->clean()->toBool()) {
                $wsupdate = $this->parent()->update([
                    'clean' => false
                ]);
            }
        }

        return parent::update($input);
    }

    public function changeSlugOnly(string $slug)
    {
        return parent::changeSlug($slug);
    }

    public function changeSlug(string $slug, string $languageCode = null)
    {
        $this->update();
        return parent::changeSlug($slug, $languageCode);
    }

    public function checkSlugIndex($_slug)
    {
        $existingUrls = $this->parent()->childrenAndDrafts();
        $existingIds = [];

        if ($existingUrls->isNotEmpty()) {
            foreach ($existingUrls as $eu) {
                array_push($existingIds, $eu->slug());
            }
        }

        $tempslugOri = $_slug;
        $tempslug = $_slug;
        $index = 1;
        while (in_array($tempslug, $existingIds)) {
            $tempslug = $tempslugOri . '-' . $index;
            $index++;
        }

        return $tempslug;
    }

    /* public function changeTitleAtCreation(string $suffixSlug)
    {
        return parent::changeTitle($this->title() . '-' . $suffixSlug);
    } */

    public function changeTitle(string $title, string $languageCode = null)
    {
        $this->update();
        return parent::changeTitle($title, $languageCode);
    }




    /**
     * 
     * PIN and sub-auth functions
     * 
     */
    public function pinMe($workshopPage, $data)
    {
        if ($workshopPage && $data) {
            try {
                $target_page = $workshopPage->find($data['participant_url']);
                $entered_pin = $data['first_pin'] . $data['second_pin'] . $data['third_pin'] . $data['fourth_pin'];

                //if ($target_page->pin() == $entered_pin) {
                if (password_verify($entered_pin, $target_page->pin())) {

                    kirby()->session()->set([
                        'participantID' => $data['participant_url'],
                        'participantLogged'  => true
                    ]);
                    $alert[] = "PIN Richtig!";
                    $target_page->go();
                } else {
                    $alert[] = "Falsche PIN eingegeben.";
                }
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetreten ' . $e->getMessage();
            }
        } else {
            $alert[] = 'PIN Auth Fehler:: Keine Seite oder Daten übergeben.';
        }

        return $alert;
    }

    public function unpinMe($workshopPage)
    {
        if ($workshopPage) {
            try {
                kirby()->session()->remove('participantID');
                kirby()->session()->remove('participantLogged');
                $alert[] = 'Teilnehmer wurde ausgeloggt';
            } catch (Exception $e) {
                $alert[] = 'Es ist ein Fehler aufgetreten ' . $e->getMessage();
            }
        } else {
            $alert[] = 'PIN Auth Fehler:: Keine Seite oder Daten übergeben.';
        }

        return $alert;
    }

    public function checkPin()
    {
        return kirby()->session()->get('participantID') == $this->slug();
    }

    public function getPinStatus()
    {
        return kirby()->session()->get('participantLogged');
    }

    /**
     * 
     * Lock and blocking functions
     * 
     */
    public function lock()
    {
        $dir = $this->contentFileDirectory();

        if (
            $this->kirby()->option('content.locking', true) &&
            is_string($dir) === true &&
            file_exists($dir) === true
        ) {
            return new CustomContentLock($this);
        }
    }

    public function lockMe($curatorPage)
    {
        $username = $curatorPage->intendedTemplate() == 'c_curator' ? $curatorPage->username()->toString() : 'Leiterkonto';
        $this->lock()->createWithCurator($curatorPage->slug(), $username);
    }

    public function unlockMe()
    {
        $this->lock()->remove();
        $this->lock()->resolve();
    }

    public function checkBlock($curator): bool
    {
        $lock = $this->lock();
        return $lock && $lock->isBlocked($curator) === true;
    }



    /**
     * 
     * Populators and helper functions
     * 
     */
    public function populateAge(int $start = 1, int $end = 99, $invert = false)
    {
        $_structure = new Kirby\Cms\Structure();

        $dataPage = site()->data_populators_pick()->toPage();
        $extraItems = [];

        if ($dataPage) {
            if ($dataPage->age_start()->isNotEmpty())
                $start = $dataPage->age_start()->value();

            if ($dataPage->age_end()->isNotEmpty())
                $end = $dataPage->age_end()->value();

            $extraItems = $dataPage->age_structure()->toStructure();
        }

        for ($i = 0; $i <= ($end - $start); $i++) {
            $_structure->add(new Kirby\Cms\StructureObject([
                'id'        => strval($start + $i),
                'content'   => ['desc' => strval($start + $i)]
            ]));
        }

        foreach ($extraItems as $structureItem) {
            $_structure->add(new Kirby\Cms\StructureObject([
                'id'        => $structureItem->desc()->slug(),
                'content'   => ['desc' => $structureItem->desc()]
            ]));
        }

        if ($invert)
            $_structure = $_structure->flip();

        return $_structure;
    }
}


/************
 * 
 * PLUGIN
 * 
 * **********/

Kirby::plugin('2av/jds-pagemodel', [
    'options' => [
        'dateFormat' => 'Y.m.j, D H:i:s T',
        'dateFormatIntl' => 'yyyy.MM.dd, E HH:mm:ss',
        'dateCreated' => 'dateCreatedEpoch',
        'dateModified' => 'dateModifiedEpoch',
        'updateLogs' => 'updateLogs',
        'createdByUser'  => 'createdByUser',
        'modifiedByUser'  => 'modifiedByUser'
    ],
    'fieldMethods' => [
        'epoch2date' => function ($field, $format = '') {
            date_default_timezone_set('Europe/Berlin');

            $fmt = datefmt_create(
                'de_DE',
                IntlDateFormatter::LONG,
                IntlDateFormatter::LONG,
                'Europe/Berlin',
                IntlDateFormatter::GREGORIAN,
                $format == '' ? option('2av.jds-pagemodel.dateFormatIntl') : $format,
            );
            $ldString = datefmt_format($fmt, $field->value());

            return $ldString;
            //return date($format, $field->value());
        },
    ],
    'hooks' => [
        'page.create:after' => function ($page) {
            $page->updateAtCreation();
        },
    ],
    'pageModels' => [
        'jdspage' => 'JDSPage'
    ],

]);

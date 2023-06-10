<?php

/**
 * 
 * JDSPage
 * Basic class for all other models (template classes). It contains many features
 * that are used by all relevant models, like save-history logging, sepcial
 * URL slug handling
 * 
 **/

use Kirby\Cms\CustomContentLock;
use Kirby\Cms\Page;

class JDSPage extends Page
{
    /**
     * Gets called once after page was created
     *
     * @param  array $data
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

    /**
     * update
     * Overrides the update function
     * https://github.com/getkirby/kirby/blob/3.9.3/src/Cms/PageActions.php#L942
     * Save-history logic and some slug change manipulation if title was changed
     * @param  array $input
     * @param  string $languageCode
     * @param  bool $validate
     * @return Kirby\Cms\Page
     */
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

        /* if (isset($input['title']) && $input['title'] != $this->title()) {
            $input['delayedSlugChange'] = true;
        } */

        /* if the page is inside a workshop (it is an exhibit, exhibition or participant)
        *  and the workshop was alrady cleaned, set it again as uncleaned
        */
        if ($this->parent() && $this->parent()->intendedTemplate() == 'c_workshop') {
            if ($this->parent()->clean()->toBool()) {
                $wsupdate = $this->parent()->update([
                    'clean' => false
                ]);
            }
        }

        return parent::update($input);
    }

    /**
     * changeSlugOnly
     * changes only the slug without updating anything else
     * @param  string $slug
     * @return Kirby\Cms\Page
     */
    public function changeSlugOnly(string $slug)
    {
        return parent::changeSlug($slug);
    }

    /**
     * changeSlug
     * Overrides the changeSlug function to also call an empty update so the history log gets triggered
     * https://github.com/getkirby/kirby/blob/3.9.3/src/Cms/PageActions.php#L154
     * @param  string $slug
     * @param  string $languageCode
     * @return Kirby\Cms\Page
     */
    public function changeSlug(string $slug, string $languageCode = null)
    {
        $this->update();
        return parent::changeSlug($slug, $languageCode);
    }

    /**
     * checkSlugIndex
     * Used for pages that might has colliding slugs, like exhibits and exhibitions. If a slug already exists
     * inside the workshop, it adds an index number as a suffix
     * @param  string $_slug
     * @return string
     */
    public function checkSlugIndex($_slug)
    {
        $existingPages = $this->parent()->childrenAndDrafts();
        $existingIds = [];

        if ($existingPages->isNotEmpty()) {
            foreach ($existingPages as $ep) {
                array_push($existingIds, $ep->slug());
            }
        }

        $tempslugOri = $_slug;
        $indexedSlug = $_slug;
        $index = 1;

        while (in_array($indexedSlug, $existingIds)) {

            $indexedSlug = $tempslugOri . '-' . $index;
            $index++;
        }

        return $indexedSlug;
    }

    /**
     * changeTitle
     * https://github.com/getkirby/kirby/blob/3.9.3/src/Cms/PageActions.php#L399
     * Overrides the chanteTitle function to trigger an empty update
     * so the history log writes once. Relevant for logging in the
     * admin area
     * @param  mixed $title
     * @param  mixed $languageCode
     * @return Kirby\Cms\Page
     */
    public function changeTitle(string $title, string $languageCode = null)
    {
        $this->update();
        return parent::changeTitle($title, $languageCode);
    }

    /**
     * changeSlugAfterTitleChange
     * Automatically changes the slug after a title change. Relevant for the admin area
     * @param  mixed $newTitle
     * @param  mixed $oldTitle
     * @return void
     */
    public function changeSlugAfterTitleChange($newTitle, $oldTitle)
    {
        $changeSlug = $newTitle != $oldTitle;

        if ($changeSlug) {
            $indexedSlug = $this->checkSlugIndex(Str::slug($newTitle));
            $this->changeSlugOnly($indexedSlug);
        }
    }

    // INFO: the following function have 'pin' in the name since participants login using their ID and PIN

    /**
     * pinMe
     * Handles PIN sub-authentification for participants inside workshop area
     * @param  Kirby\Cms\Page $workshopPage
     * @param  mixed $data
     * @return array
     */
    public function pinMe($workshopPage, $data)
    {
        if ($workshopPage && $data) {
            try {
                $target_participant = $workshopPage->find($data['participant_url']);
                $entered_pin = $data['first_pin'] . $data['second_pin'] . $data['third_pin'] . $data['fourth_pin'];

                //if ($target_page->pin() == $entered_pin) {
                if (password_verify($entered_pin, $target_participant->pin())) {

                    kirby()->session()->set([
                        'participantID' => strtolower($data['participant_url']),
                        'participantLogged'  => true
                    ]);
                    $alert[] = "PIN Richtig!";
                    $target_participant->go();
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

    /**
     * unpinMe
     * Handles participant soft-logout, meaning the group account is still logged in
     * but a new participant can now enter his/her PIN in the workshop lobby area
     * @param  Kirby\Cms\Page $workshopPage
     * @return array
     */
    public function unpinMe($workshopPage)
    {
        if ($workshopPage) {
            try {

                $participant = $workshopPage->find(kirby()->session()->get('participantID'));
                if($participant && $exhibition = $participant->linked_exhibition()->toPageOrDraft()){
                    $exhibition->unlockMe();
                }

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

    /**
     * checkPin
     * Checks if the session variable for the participant ID is the same as
     * the current page's that calls the function
     * @return bool
     */
    public function checkPin()
    {
        return kirby()->session()->get('participantID') == $this->slug();
    }

    /**
     * getPinStatus
     * Returns true or false if a participant is logged in with an ID
     * (function not used until now but might be relevant)
     * @return bool
     */
    public function getPinStatus()
    {
        return kirby()->session()->get('participantLogged');
    }

    /**
     * lock
     * overrides lock function to return our CustomLock
     * https://github.com/getkirby/kirby/blob/3.9.3/src/Cms/ModelWithContent.php#L311
     * Creates a lock for the page so others cannot edit it (at this point
     * only relevant for exhibitions)
     * @return void
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

    /**
     * lockMe
     * Locks the page taking into account special logic
     * @param  \Kirby\Cms\Page $curatorPage
     * @return void
     */
    public function lockMe($curatorPage)
    {
        $username = $curatorPage->intendedTemplate() == 'c_curator' ? $curatorPage->username()->toString() : 'Leiterkonto';
        $this->lock()->createWithCurator($curatorPage->slug(), $username);
    }

    /**
     * unlockMe
     * Unlocks the page in a more forcefull way
     * than only $contentlock->unlock() so we are
     * sure it is unlocked. This is due to many curators
     * sharing the group account
     * @return void
     */
    public function unlockMe()
    {
        $this->lock()->remove();
        $this->lock()->resolve();
    }

    /**
     * checkLock
     * Similar to isLocked, but our CustomLock checks for other parameters
     * https://github.com/getkirby/kirby/blob/3.9.3/src/Cms/ModelWithContent.php#L287
     * @param  string $curator_slug
     * @return bool
     */
    public function checkLock($curator_slug): bool
    {
        $lock = $this->lock();
        return $lock && $lock->isBlocked($curator_slug) === true;
    }


    /**
     * callPopulateAge
     * Calls the global function populateAge. Needed in order to use this function
     * inside YAML blueprints
     * 
     * @param  int $start
     * @param  int $end
     * @param  bool $invert
     * @return void
     */
    public function callPopulateAge(int $start = 1, int $end = 99, $invert = false)
    {
        return populateAge($start, $end, $invert);
    }
}


/************
 * 
 * Define the plugin
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

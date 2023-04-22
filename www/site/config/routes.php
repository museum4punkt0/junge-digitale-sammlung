<?php

/**
 * ROUTES
 * Routes can handle some given logic when a specific URL is called.
 * Also, they can deliver data or virtual pages. For more information:
 * https://getkirby.com/docs/guide/routing
 */

return [
    // logout
    [
        'pattern' => 'logout',
        'action'  => function () {

            if ($user = kirby()->user()) {
                kirby()->session()->clear();
                $user->logout();
            }

            go(kirby()->url() . '/login');
        }
    ],

    // virtual pdf page (for list of participants)
    [
        'pattern' => '(:num)/teilnehmerliste',
        'action'  => function ($ws) {

            if ($user = kirby()->user()) {
                return Page::factory([
                    'slug' => 'workshop-' . time(),
                    'template' => 'v_pdf_data',
                    'content' => [
                        'title' => 'Workshop Information',
                        'target_page' => $ws,
                    ]
                ]);
            } else {
                go(kirby()->url());
            }
        }
    ],

    // virtual json country list (for populating the dropdowns)
    [
        'pattern' => 'countries',
        'action'  => function () {

            $dataPage = site()->data_populators_pick()->toPage();

            $sourceIsOnline = $dataPage->json_countries_sourceIsOnline()->toBool();

            if ($sourceIsOnline)
                $jsonFile = Json::read($dataPage->json_countries_url()->url());
            else
                $jsonFile = Json::read($dataPage->json_countries_file()->toFile());

            $valuesMapedArray = array();
            if ($dataPage->json_countries_id()->isNotEmpty() && $dataPage->json_countries_label()->isNotEmpty()) {
                $rewriteKeys = array($dataPage->json_countries_label()->value() => 'label', $dataPage->json_countries_id()->value() => 'value');
                $keys = array($dataPage->json_countries_label(), $dataPage->json_countries_id());
                $valuesMapedArray = array();
                foreach ($jsonFile as $value) {
                    $subarray = array();
                    foreach ($value as $key => $val) {
                        if (in_array($key, $keys)) {
                            $subarray[$rewriteKeys[$key]] = $val;
                        }
                    }
                    $valuesMapedArray[] = $subarray;
                }
            } else {
                $valuesMapedArray = $jsonFile; // json needs to have value and label as params
            }

            $countriesExtra = $dataPage->countries_extra()->yaml();

            $completeList = array_merge($valuesMapedArray, $countriesExtra);
            $result = json_encode($completeList);

            return $result;
        }
    ],
];

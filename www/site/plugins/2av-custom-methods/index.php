<?php
Kirby::plugin('2av/custom-methods', [
    'collectionMethods' => [],
    'fieldMethods' => [
        'toPagesAndDrafts' => function ($field) {
            $result = new Pages();
            if ($field && $field->isNotEmpty()) {
                $selectedPages = $field->yaml();

                foreach ($selectedPages as $page) {
                    $result->add(kirby()->page($page));
                }
            }
            return $result;
        },

        'toPageOrDraft' => function ($field) {
            if ($field && $field->isNotEmpty()) {
                $selectedPages = $field->yaml();
                foreach ($selectedPages as $page) {
                    $result = kirby()->page($page);
                    if ($result) {
                        return $result;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        },

        'checkImpulse' => function ($field) {
            $result = new Pages();
            if ($field && $field->isNotEmpty()) {
                $selectedPages = $field->yaml();

                foreach ($selectedPages as $page) {
                    $result->add(kirby()->page($page));
                }
            }
            return $result;
        },

        'mapValueToLabel' => function ($field) {
            //$options = site()->data_populators_pick()->toPage()->content()->get($field->key())->yaml();
            $options = site()->data_populators_pick()->toPage()->content()->get($field->key())->toStructure();
            if ($field->value() > -1) {
                //$value = $options[$field->value()]['desc'];
                $value = $options->find($field->value());
                if ($value) {
                    $value = $value->desc();
                }
            } else {
                $value = null;
            }

            return $value;
        },

        'userStatus' => function ($field) {
            $value = "Kein Benutzer";

            if ($field->isNotEmpty()) {
                $user = $field->toUser();
                $value = $user->active()->toBool() ? 'Aktiv' : 'Inaktiv';
            }

            return $value;
        },

        'userStatusTheme' => function ($field) {
            $value = "";

            if ($field->isNotEmpty()) {
                $user = $field->toUser();
                $value = $user->active()->toBool() ? 'positive' : 'negative';
            }

            return $value;
        },

        'brToN' => function ($field) {
            $value = "";

            if ($field->isNotEmpty()) {
                $txt = $field->text();
                $breaks = array("<br />", "<br>", "<br/>", "<br />", "&lt;br /&gt;", "&lt;br/&gt;", "&lt;br&gt;");
                $value = str_replace($breaks, "\n", $txt);
                $li = array("<li>");
                $value = str_replace($li, "", $value);
                $liend = array("</li>");
                $value = str_replace($liend, "\n", $value);
            }

            return $value;
        },

        'multiselectGroup' => function ($field) {
            $elements = $field->toStructure();
            $arr = [];
            foreach ($elements as $el) {
                array_push($arr, [
                    'id' => $el->id(),
                    'desc' => $el->desc()
                ]);
                if ($suboptions = $el->sub_options()->toStructure()) {
                    foreach ($suboptions as $s) {
                        array_push($arr, [
                            'id' => $el->id() . '-' . $s->id(),
                            'desc' => $s->desc()
                        ]);
                    }
                }
            }

            return $arr;
        },

        'multiselectGroupPanel' => function ($field) {
            $elements = $field->toStructure();
            $arr = [];
            foreach ($elements as $el) {
                if ($suboptions = $el->sub_options()->toStructure()) {
                    if ($suboptions->isEmpty()) {
                        array_push($arr, [
                            'id' => $el->id(),
                            'desc' => $el->desc()
                        ]);
                    }
                    foreach ($suboptions as $s) {
                        array_push($arr, [
                            'id' => $el->id() . '-' . $s->id(),
                            'desc' => $s->desc()
                        ]);
                    }
                }
            }

            return $arr;
        },
    ],
    'fileMethods' => [
        'responsiveImg' => function ($srcset = false) {
            if ($srcset) {
                $tag = '<img src="' . $this->placeholderUri() . '" data-srcset="' . $this->srcset($srcset) . '" alt="' . $this->alt() . '" data-lazyload>';
            } else {
                $tag = '<img src="' . $this->placeholderUri() . '" data-srcset="' . $this->srcset() . '" alt="' . $this->alt() . '" data-lazyload>';
            }
            return $tag;
        },
        'findModelFromImage' => function ($input = false) {
            $filename = $input ? $input : $this->name();
            $filenameQuery = explode('-', $filename)[0]; // get first occurrence, filenames must not have - for this to work
            //$filenameQuery = substr($filename, 0, strrpos($filename, '-'));
            kirbylog($filenameQuery);
            $models = $this->page()->getModels(); // we assume its a c_exhibit page and call the function directly

            $modelResult = $models->filter(function ($model) use ($filenameQuery) {
                $result = false;
                $modelname = explode('-', $model->name())[0];
                if ($modelname == $filenameQuery) {
                    $result = true;
                }
                return $result;
            });

            return $modelResult->first();
        },
        'getExposure' => function () {
            //$cleanName = $this->name();
            $exposure = explode('_-_exp-', $this->name());

            if (sizeOf($exposure) > 1) {
                $exposure = $exposure[1];
            }

            return floatval($exposure) ?? 1;
            //return $this->name();
        },
    ],
]);

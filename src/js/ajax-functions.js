/**
 * IMPORTANT:::
 * AJAX functions work together with kirby regular controllers and JSON controllers.
 * 
 * Regular call
 * For instance calling of a form submit
 * 
 * 
 * JSON call
 * We call a JSON URL and kirby handles in the
 * targeted controller the data and returns the required information. 
 */
import JDSForm from "./components/JDSForm";


/* function ajaxCall(type, url, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == XMLHttpRequest.DONE && xmlhttp.status == 200) {
            var data = xmlhttp.responseText;
            if (callback) callback(data);
        }
    };
    xmlhttp.open(type, url, true);
    xmlhttp.send();
}
Object.assign(window, { ajaxCall }); */


/**
 * Sends a form via ajax by finding the forms submit button
 * or sending the form directly.
 * @param {HTMLElement} targetformID 
 */
function sendAjaxForm(targetformID) {
    let targetform = document.getElementById(targetformID);
    let btn_submit = targetform.querySelector('button[type="submit"]');

    // if form has type=submit or only button
    if (btn_submit) {
        btn_submit.click();
    }
    else {
        targetform.submit();
    }
}
Object.assign(window, { sendAjaxForm });


/**
 * Validates if the impulse (topic) of the user's exhibit coincides with
 * the impulse of the exhibition's impulse of which the exhibit is part of.
 * Then sets validation icons
 * @param {HTMLElement} ajaxTarget 
 * @returns 
 */
async function checkImpulseRelationships(ajaxTarget) {
    let impulse = ajaxTarget.value;
    let page = ajaxTarget.form.parentElement.getAttribute('data-pageid');

    if (!page) {
        return;
    }

    let url = location.protocol + '//' + location.host + location.pathname;
    url = `${url}.json/?impulseCheck=${impulse}&pageID=${page}`;

    try {
        const response = await fetch(url);
        const { impulseResult } = await response.json();

        // we work with valid/invalid functions and then extra with 'warning' icon
        let check = ajaxTarget.form.querySelector('.checks[data-for="' + ajaxTarget.id + '"]');
        let error = ajaxTarget.form.querySelector('.errors[data-for="' + ajaxTarget.id + '"]');
        let warning = ajaxTarget.form.querySelector('.warnings[data-for="' + ajaxTarget.id + '"]');
        let input = ajaxTarget.form.querySelector('#' + ajaxTarget.id);

        if (impulseResult == 'result_success') {
            if (input && error && check && warning) {
                isValid(input, check, error);
                warning.classList.add('d-none');
            }
        }
        else if (impulseResult == 'result_warning') {
            if (input && error && check && warning) {
                error.classList.add('d-none');
                warning.classList.remove('d-none');
                check.classList.add('d-none');
                input.classList.remove('filled');
            }
        }
        else if (impulseResult == 'result_error') {
            if (input && error && check && warning) {
                isInvalid(input, check, error);
                warning.classList.add('d-none');
            }
        }
        // default behaviour if no answer, sets valid or invalid if value empty or not
        else {
            if (impulse != '') {
                warning.classList.add('d-none');
                isValid(input, check, error);
            }
            else {
                warning.classList.add('d-none');
                isInvalid(input, check, error);
            }
        }

    } catch (error) {
        console.log('Fetch error: ', error);
    }
}
Object.assign(window, { checkImpulseRelationships });

/**
 * Validates for relationships in areas where the user has a
 * list of the participants of an exhibition. Then sets validation icons
 * @param {HTMLElement} ajaxTarget 
 * @returns 
 */
async function checkImpulseRelationshipsList(ajaxTarget) {

    let curator = ajaxTarget.value;

    // find right parent that contains data, since we work with tab-panes and modals
    let parent = ajaxTarget.form.parentElement;
    while (!parent.classList.contains('modal') && !parent.classList.contains('tab-pane')) {
        parent = parent.parentElement;
    }

    let page = parent.getAttribute('data-pageid');
    if (!page) {
        return;
    }

    let url = location.protocol + '//' + location.host + location.pathname;
    url = `${url}.json/?curatorID=${curator}&exhibitionID=${page}`;

    try {
        const response = await fetch(url);
        const { impulseResult } = await response.json();

        // we work with valid/invalid functions and then extra with 'warning' icon
        let check = ajaxTarget.form.querySelector('.checks[data-for="' + ajaxTarget.id + '"]');
        let error = ajaxTarget.form.querySelector('.errors[data-for="' + ajaxTarget.id + '"]');
        let warning = ajaxTarget.form.querySelector('.warnings[data-for="' + ajaxTarget.id + '"]');
        let input = ajaxTarget.form.querySelector('#' + ajaxTarget.id);

        if (impulseResult == 'result_success') {
            if (input && error && check && warning) {
                isValid(input, check, error);
                warning.classList.add('d-none');
            }
        }
        else if (impulseResult == 'result_warning') {
            if (input && error && check && warning) {
                error.classList.add('d-none');
                warning.classList.remove('d-none');
                check.classList.add('d-none');
                input.classList.remove('filled');
            }
        }
        else if (impulseResult == 'result_error') {
            if (input && error && check && warning) {
                isInvalid(input, check, error);
                warning.classList.add('d-none');
            }
        }
        else {
            if (input && error && check && warning) {
                error.classList.add('d-none');
                check.classList.add('d-none');
                warning.classList.add('d-none');
                input.classList.remove('filled');
            }
        }

    } catch (error) {
        console.log('Fetch error: ', error);
    }
}
Object.assign(window, { checkImpulseRelationshipsList });

/**
 * Fetches the data for an exhibition before the exhibition modal (curator leader)
 * or the tab-pane (curator) Bootstrap elements are displayed. It then refreshes
 * the form data, since maybe another user has updated the exhibition in the meantime.
 * @param {HTMLElement} context 
 */
async function fetchDynamicExhibitionData(context) {
    if (context.id == 'pane-exhibition' || context.classList.contains('modal')) {
        let page = context.getAttribute('data-pageid');
        let url = location.protocol + '//' + location.host + location.pathname;
        url = `${url}.json/?exhibitionpage=${page}`;
        console.log(url);
        try {
            const response = await fetch(url);
            const { isCuratorLeader, exhibitionDataResult, curatorInExhibition } = await response.json();

            if (isCuratorLeader || (exhibitionDataResult && curatorInExhibition)) {
                let form = context.querySelector('form');
                let dynamicPartForm = form.querySelector('.dynamic__content');
                dynamicPartForm.innerHTML = exhibitionDataResult;
                form = new JDSForm(form);
            }
            else {
                /* in case the user was removbed from the exhibition, shows a warning. After browser refresh
                the "Exhibition" tab would have disappeared for the curator */
                context.classList.add('user-removed');
                context.innerHTML = "Sie wurden angeblich von den Ausstellungen entfernt. Bitte Fenster neu laden.";
            }

            document.dispatchEvent(new CustomEvent('done_fetching_information', { detail: { context: context } }));

        } catch (error) {
            console.log('Fetch error: ', error);
        }
    }

}
Object.assign(window, { fetchDynamicExhibitionData });

/**
 * Simple ajax to call scraper of embeds in controller and return the whole object
 * @param {string} url 
 * @returns object
 */
async function scrapEmbed(url) {
    let result = false;

    if (url) {
        try {
            console.log('fetch:: ' + url);
            const response = await fetch(url);
            const { embed } = await response.json();
            result = embed;
            console.log(result);
        } catch (er) {
            console.log('Fetch error: ', er);
        }
    }

    return result;
}
Object.assign(window, { scrapEmbed });

/**
 * Scrapes for the image file of the embed url and returns a finished img tag (relevant for tiktok,
 * since the preview images are short lived and we must always load the new ones). If the URL cant
 * be scrapped, it returns a X-icon as visual error.
 * @param {string} url 
 * @returns string
 */
async function scrapFreshEmbedImage(url) {
    let result;

    await scrapEmbed(url).then(embed => {
        if (embed.data) {
            result = {
                imgTag: '<img src="' + embed.data.image + '" alt="' + embed.data.title + '">',
                imgWidth: embed.data.imageWidth,
                imgHeight: embed.data.imageHeight,
            };
        }
        else
            result = {
                imgTag: '<p class="single-exhibit text-danger">Unable to load<span class="empty"><i icon-name="x-circle" class="icon-only"></i></span></p>',
            }
    });

    return result;
}
Object.assign(window, { scrapFreshEmbedImage });

/**
 * Scrapes the embed URL to see if its valid and handles data.
 * Then injects the embed into the DOM and sets validation icons.
 * @param {HTMLElement} ajaxTarget 
 */
function scrapWorkshopEmbed(ajaxTarget) {
    let urlvalue = ajaxTarget.value;
    urlvalue = urlvalue.split('?')[0].replace(/\/$/, "");
    ajaxTarget.value = urlvalue;
    let embedurl = location.protocol + '//' + location.host + location.pathname;
    embedurl = `${embedurl}.json/?embedurl=${urlvalue}`;

    // if this container exists, cookies were allowed
    let embed_preview = document.getElementById('embed__preview');
    if (urlvalue && embed_preview) {
        //try {
        /* const response = await fetch(url);
        const { embed } = await response.json();
 */
        scrapEmbed(embedurl).then(embed => {
            let check = ajaxTarget.form.querySelector('.checks[data-for="' + ajaxTarget.id + '"]');
            let error = ajaxTarget.form.querySelector('.errors[data-for="' + ajaxTarget.id + '"]');
            let input = ajaxTarget.form.querySelector('#' + ajaxTarget.id);

            if (embed.data) {
                if (input && error && check) {
                    isValid(input, check, error);
                }

                embed_preview.innerHTML = embed.data.code;
                embed_preview.setAttribute('class', 'mx-auto ' + embed.data.providerName.toLowerCase());

                if (window.twttr) {
                    window.twttr.widgets.load();
                }
                else if (embed.data.providerName.toLowerCase() == 'twitter') {
                    const embed = document.createElement('script');
                    embed.src = 'https://platform.twitter.com/widgets.js';
                    document.body.appendChild(embed);
                }

                if (window.instgrm) {
                    window.instgrm.Embeds.process();
                }
                else if (embed.data.providerName.toLowerCase() == 'instagram') {
                    const embed = document.createElement('script');
                    embed.src = 'https://www.instagram.com/embed.js';
                    document.body.appendChild(embed);
                }

                // sadly no obvious way to reinitialize tiktokEmbed, so we readded everytime
                if (embed.data.providerName.toLowerCase() == 'tiktok') {
                    const embed = document.createElement('script');
                    embed.src = 'https://www.tiktok.com/embed.js';
                    document.body.appendChild(embed);
                }
            }
            else {
                let embed_preview = document.querySelector('#embed__preview');
                embed_preview.innerHTML = '<p>Es ist ein Fehler aufgetretten oder der Link ist ungültig und konnte nicht geladen werden.</p>';

                if (input && error && check) {
                    isInvalid(input, check, error);
                }
            }
        });


        /* } catch (er) {
            console.log('Fetch error: ', er);
        } */
    }
    else {
        let check = ajaxTarget.form.querySelector('.checks[data-for="' + ajaxTarget.id + '"]');
        let error = ajaxTarget.form.querySelector('.errors[data-for="' + ajaxTarget.id + '"]');
        let input = ajaxTarget.form.querySelector('#' + ajaxTarget.id);
        if (input && error && check) {
            error.classList.remove('d-none');
            check.classList.add('d-none');
            input.classList.remove('filled');
        }
    }

}
Object.assign(window, { scrapWorkshopEmbed });


/**
 * Handles file uploading via ajax so we can show a progressbar.
 * If everything goes well we reload the browser
 * @param {Event} ev 
 */
function fileUpload(ev) {
    var btn = ev.currentTarget;
    var form = btn.form;
    var progress = form.querySelector(".progress-bar");
    var fileInput = form.querySelector(".files");
    let url = location.protocol + '//' + location.host + location.pathname + '.json';
    var file = fileInput.files[0];

    var formData = new FormData();
    formData.append(fileInput.id, file);
    formData.set(btn.name, btn.name);

    var ajax = new XMLHttpRequest();
    ajax.responseType = 'json';
    ajax.open("POST", url, true);

    ajax.upload.onprogress = function (e) {
        if (e.lengthComputable) {
            var percentage = Math.round((e.loaded / e.total) * 100);
            progress.style.width = percentage + "%";
            progress.innerHTML = percentage + "%";
            progress.setAttribute('aria-valuenow', percentage);
        }
        else {
            console.log("Unable to compute progress information since the total size is unknown");
        }
    }

    ajax.onload = function (e) {
        // if successfull, set a message in session for an alert and reload page 
        if (this.readyState == 4 && this.status == 200) {
            sessionStorage.setItem('alert', this.response.alert);
            location.reload();
        }
    };

    ajax.send(formData);
}
Object.assign(window, { fileUpload });

/**
 * Validation for the image selection in the form for physical-object
 * based on a radio button group. Checks if one is selected and then
 * sets as valid if 'not empty' is selected.
 * @param {HTMLElement} ajaxTarget 
 */
function checkRadioGroupGallery(ajaxTarget) {
    var radios = document.getElementsByName(ajaxTarget.id);
    var radiosArray = Array.prototype.slice.call(radios);

    radiosArray = radiosArray.filter(function (radio) {
        return radio.tagName !== "DIV";
    });

    var firstRadio = radiosArray[0]; // we use the first radio as a reference, since we know it always will exist (empty value)
    var checkedRadio = radiosArray.find((radio) => radio.checked);

    let check = firstRadio.form.querySelector('.checks[data-for="' + ajaxTarget.id + '"]');
    let error = firstRadio.form.querySelector('.errors[data-for="' + ajaxTarget.id + '"]');
    let input = ajaxTarget;

    if (checkedRadio) {
        input.setAttribute('value', checkedRadio.value);

        if (checkedRadio.value == "") {
            isInvalid(input, check, error);
        }
        else {
            isValid(input, check, error);
        }
    }
    else {
        firstRadio.checked = true;
        isInvalid(input, check, error);
    }
}
Object.assign(window, { checkRadioGroupGallery });


var usernameField = document.querySelector('input#username');
let usernameDelayTimer;
let usernameDelay = 300;
/**
 * Validates the username to see if it's unique in the whole system
 * @param {HTMLElement} element 
 */
function checkUsername(element) {

    const form = element.form;
    let check = form.querySelector('.checks[data-for="' + element.name + '"]');
    let error = form.querySelector('.errors[data-for="' + element.name + '"]');

    clearTimeout(usernameDelayTimer);
    if (!usernameField.form.hasAttribute('user-error')) {
        if (element.value != '') {
            usernameDelayTimer = setTimeout(async function () {

                if (element.getAttribute('statuschanged') != null) {
                    let _path = location.protocol + '//' + location.host + location.pathname + '.json?username=' + element.value;
                    try {
                        const response = await fetch(_path);
                        const { usernameExists } = await response.json();

                        if (usernameExists) {
                            console.log("user-exists");
                            isInvalid(element, check, error);
                        }
                        else {
                            isValid(element, check, error);
                        }

                        element.removeAttribute('first-run-username');
                    }
                    catch (error) {
                        console.log('Fetch error: ', error);
                    }
                }
                else {
                    isValid(element, check, error);
                }

            }.bind(element), usernameDelay);
        }
        else {
            isInvalid(element, check, error);
        }
    }
    else {
        isInvalid(element, check, error);
        usernameField.form.removeAttribute('user-error');
    }
}
Object.assign(window, { checkUsername });

/**
 * Handles a exhibit type change, since this requires removing
 * all files (like videos, etc.) for avoiding data garbage. After
 * a change is triggered a Bootstrap modal is opened via 'createPhantomToggle'
 * @param {HTMLElement} element 
 */
function handleExhibitTypeChange(element) {
    let form = element.form;
    createPhantomToggle({
        headline: 'Achtung',
        message: 'Wenn Sie den Objekttyp verändern wird das Objekt gespeichert und manche Daten werden zurückgesetzt. Sind Sie sicher?',
        func: 'sendAjaxForm',
        data: form.id,
        contextCancel: '#' + form.id,
        funcCancel: 'resetElement',
        dataCancel: element.id,
        confirmLabel: 'Ja, ändern',
    });
}
Object.assign(window, { handleExhibitTypeChange });


/**
 * Sets the visual validation for valid, invalid and if the input
 * is shown as filled. The warning icon is not included and must be done
 * separately for special cases
 * @param {HTMLElement} element 
 * @param {HTMLElement} check 
 * @param {HTMLElement} error 
 */
function isValid(element, check, error) {
    check.classList.remove('d-none');
    error.classList.add('d-none');
    element.classList.add('filled');
}

/**
 * Sets the visual invalidation for valid, invalid and if the input
 * is shown as filled. The warning icon is not included and must be done
 * separately for special cases
 * @param {HTMLElement} element 
 * @param {HTMLElement} check 
 * @param {HTMLElement} error 
 */
function isInvalid(element, check, error) {
    check.classList.add('d-none');
    error.classList.remove('d-none');
    element.classList.remove('filled');
}

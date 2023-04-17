/**
 * Handles all forms in pages and inside tabs
 * and also some form elements
 */


import DragnDrop from "../components/DragnDrop";
import JDSForm from "../components/JDSForm";


/**
 * Init all forms that have a change watchdog
 */
export function initForms() {

    let watchdog_forms = document.querySelectorAll('.watchdog__form');
    watchdog_forms.forEach(element => {
        element = new JDSForm(element);
    });

    initFormTracking();
    initPinInputs();
    initDnDForms();
}


let current_form;
/**
 * Tracks which form is currently active,
 * if it has been change and save via keyboard (ctrl + s)
 */
function initFormTracking() {
    // waits for current form event
    document.addEventListener("current_form", (event) => {
        current_form = event.detail.context;
    }, true);

    // if ctrl+s and form was changed, save form
    document.addEventListener("keydown", function (e) {
        if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.keyCode == 83) {
            e.preventDefault();

            if (current_form && current_form.getAttribute('form_changed') == 'true') {
                sendAjaxForm(current_form.id);
            }
        }
    }, false);

    // waits for form changed event
    document.addEventListener("form_changed", (event) => {        
        let formUnsaved = event.detail.state;
        let paneId = event.detail.paneID;
        let tab = document.querySelector('button[aria-controls="'+paneId+'"]');

        if(formUnsaved){
            tab.classList.add('unsaved');
        }
        else{
            tab.classList.remove('unsaved');
        }        
    }, true);
}

/**
 * Inits the form in the current container
 * @param {HTMLElement} container 
 */
export function initFormOnShow(container) {
    let forms = container.querySelectorAll('form.dynamic-content.watchdog__form');
    forms.forEach(form => {
        form.initSelectGroupWatchdog();
    });
}

/**
 * Resets all forms to its defaults, if they were changed
 * @param {HTMLElement} container   e.g. a tab-pane or modal
 */
export function resetForms(container) {
    let forms = container.querySelectorAll('form.watchdog__form');
    forms.forEach(form => {
        form.resetToDefault();
    });
}

/**
 * Checks if some forms were changed and return result + amount
 * @param {HTMLElement} container   e.g. a tab-pane or modal
 * @returns {boolean}
 */
export function checkForms(container) {
    let forms = container.querySelectorAll('form.watchdog__form');
    let changed = 0;
    let statusChanged = false;

    forms.forEach(form => {
        if (form.formChanged) {
            changed++;
        }
        statusChanged = statusChanged || form.formChanged;
    });

    var result = {
        changed: changed,
        statusChanged: statusChanged,
    };

    return result;
}

/**
 * Disables all forms in the container
 * @param {HTMLElement} container  e.g. a tab-pane or modal
 */
export function disableForms(container) {
    let forms = container.querySelectorAll('form.watchdog__form');

    forms.forEach(element => {
        element.disableForm();
    });
}

/**
 * Enables all forms in the container
 * @param {HTMLElement} container  e.g. a tab-pane or modal
 */
export function enableForms(container) {
    let forms = container.querySelectorAll('form.watchdog__form');

    forms.forEach(element => {
        if (element.formInitialized)
            element.enableForm();
    });
}

let pinForms;

/**
 * Inits 4-digit forms that auto focus sibling elements
 */
function initPinInputs() {
    pinForms = document.querySelectorAll('.pin__form');

    pinForms.forEach(pinform => {
        let inputs = pinform.querySelectorAll('.field__pin');
        let hasAutoFocus = pinform.classList.contains('autofocus');

        inputs.forEach((input, key) => {

            if (hasAutoFocus) {
                inputs[0].focus();
            }

            input.addEventListener("keyup", function (event) {
                if (event.keyCode == 8 || event.key === "Backspace" || event.key === "Delete" || event.keyCode == 37) {
                    if (key > 0) {
                        inputs[key - 1].focus();
                        inputs[key - 1].select();
                    }
                    return;
                }
                
                if (input.value || event.keyCode == 39) {                      
                    if (key === (inputs.length - 1)) {
                        let submit = this.form.querySelector('input[type="submit"]');
                        submit.focus();
                    } else {
                        inputs[key + 1].focus();
                        inputs[key + 1].select();
                    }
                }
            });
        });
    });
}

/**
 * Inits Drag n Drop forms
 */
function initDnDForms() {
    let dndForms = document.querySelectorAll('.upload-form');

    dndForms.forEach(element => {
        new DragnDrop(element);
    });
}



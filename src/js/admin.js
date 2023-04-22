import { initForms, resetForms, checkForms, enableForms, disableForms, initFormOnShow } from './controllers/forms';

const cross_data = document.querySelector('#cross-data');
const page_id = cross_data.getAttribute('page-slug');

/**
 * inits the admin logic in the relevant page
 */
function initAdmin() {
    console.log('init admin');

    initForms();
    initBSElements();
    rehashUrl();
}
Object.assign(window, { initAdmin });


let url;
/**
 * Places a hash after a refresh so we jump to the right bootstrap tab
 */
function rehashUrl() {
    url = location.href.replace(/\/$/, "");

    if (location.hash) {
        const hash = url.split("#");
        let triggerEl = document.querySelector('button[data-bs-target="#' + hash[1] + '"]');

        if (triggerEl) {
            const tabTrigger = new Tab(triggerEl);
            tabTrigger.show();
            url = location.href.replace(/\/#/, "#");

            let forms = document.querySelectorAll('form');
            forms.forEach(element => {
                element.setAttribute('action', url);
            });

            history.replaceState(null, null, url);
        }

        setTimeout(() => {
            window.scrollTo(0, 0);
        }, 100);
    }
}

/**
 * Handles the confirm button of dynamic modals
 * @param {Event} ev 
 */
function defaultModalHandler(ev) {
    let btn_confirm = ev.target;
    let context = document.querySelector(btn_confirm.getAttribute('context')) ?? window;
    executeFunctionByName(btn_confirm.getAttribute('callback'), context, btn_confirm.getAttribute('data'));

    confirmationModal.hide();
}

/**
 * Handles the cancel button of dynamic modals
 * @param {Event} ev 
 */
function defaultModalCancelHandler(ev) {
    let btn_cancel = ev.target;
    let context = document.querySelector(btn_cancel.getAttribute('context-cancel')) ?? window;
    executeFunctionByName(btn_cancel.getAttribute('callback-cancel'), context, btn_cancel.getAttribute('data-cancel'));

    confirmationModal.hide();
}

/**
 * Calls a function by its name in the given context and passes an argument
 * @param {string} functionName 
 * @param {HTMLElement} context 
 * @param {string} args 
 * @returns mixed
 */
function executeFunctionByName(functionName, context, args) {
    var args = Array.prototype.slice.call(arguments, 2);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for (var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return context[func].apply(context, args);
}

/**
 * Resets a form inside a tab after changes were made
 * @param {string} data 
 */
function resetFormModal(data) {
    let tabActiveEl = document.querySelector('button[data-bs-toggle="tab"].active');
    let tabPane = tabActiveEl.getAttribute('data-bs-target');
    resetForms(document.querySelector(tabPane));
}
Object.assign(window, { resetFormModal });

/**
 * Triggers a confirm-modal after changes were made in
 * a tab and the changes were not saved
 * @param {string} data 
 */
function handleChangedFormModal(data) {
    // reset form
    let tabActiveEl = document.querySelector('button[data-bs-toggle="tab"].active');
    let tabPane = tabActiveEl.getAttribute('data-bs-target');
    resetForms(document.querySelector(tabPane));

    let triggerEl = document.querySelector('button[data-bs-target="' + data + '"]');
    const tabTrigger = new Tab(triggerEl);
    tabTrigger.show();
}
Object.assign(window, { handleChangedFormModal });


let confirmationModal;
let confirmationModalEl = document.getElementById('confirmationModal');
if (confirmationModalEl) {
    confirmationModal = new Modal('#confirmationModal');
}

const CHANGED_FORM_MODAL = 'handleChangedFormModal';
/**
 * Inits the bootstrap elements, specially tabs and modals and sets some event listeners
 */
function initBSElements() {

    /** TAB TOGGLES ***/
    console.log('init bs');
    // event listener for tab exhibition; when it is done, handle locking
    document.addEventListener('done_fetching_information', handleCurrentActiveForm, true);

    // bootstrap toggles are 'tab buttons'
    let toggles = document.querySelectorAll('button[data-bs-toggle="tab"]');
    toggles.forEach(element => {
        // tab is shown
        element.addEventListener("shown.bs.tab", async function (ev) {
            let newUrl;
            const hash = this.getAttribute("data-bs-target");
            // workaround for home and stripping of hash
            if (hash == "#home") {
                newUrl = url.split("#")[0];
            } else {
                newUrl = url.split("#")[0] + hash;
            }

            let forms = document.querySelectorAll('form');
            forms.forEach(element => {
                element.setAttribute('action', newUrl);
            });
            history.replaceState(null, null, newUrl);

            /* if the tab for exhibition is being called, fetch eventual changes
            made to the exhibition by someone else and refresh the tab, then
            re-init the form inside. curators have the exhibition inside a tab.*/
            let tabPaneID = ev.target.getAttribute('data-bs-target');
            let tabPane = document.querySelector(tabPaneID);

            if (tabPane.id == 'pane-exhibition') {
                fetchDynamicExhibitionData(tabPane);
                initFormOnShow(tabPane);
            }
        });
        // last tab is going to change to new tab
        element.addEventListener("hide.bs.tab", async function (ev) {
            let tabPaneID = ev.target.getAttribute('data-bs-target');
            let tabPane = document.querySelector(tabPaneID);
            let tabFormsCheck = checkForms(tabPane);
            let destinationTab = ev.relatedTarget.getAttribute('data-bs-target');

            // check if old tab has unsaved changes
            if (tabFormsCheck.statusChanged) {
                // if yes prevent from changing and trigger confirmation modal
                ev.preventDefault();
                createPhantomToggle({
                    headline: 'Achtung',
                    message: 'Einige Änderungen wurden nicht gespeichert. Änderungen verwerfen?',
                    func: CHANGED_FORM_MODAL,
                    data: destinationTab,
                    confirmLabel: 'Verwerfen',
                });
            }
            else {
                /* if not then just do nothing except if it was the exhibition
                 tab, in this case we have to unlock it. curators have exhibitions
                 inside a tab*/
                if (tabPane.id == 'pane-exhibition') {
                    handleLastActiveForm(tabPane);
                }
            }
        });
    });

    // bs modals
    let modals = document.querySelectorAll('.modal');
    modals.forEach(element => {
        /* when it hides reset the forms inside and eventually
        unlock the page that was being edited via that form*/
        element.addEventListener('hidden.bs.modal', event => {
            resetForms(event.target);
            handleLastActiveForm(event.target);
        });

        /* fetch some new information in case the page was being
        edited by someone else (exhibition) and re-init the forms.
        curator leader have exhibitions in modals. Do this only
        if it was not a confirmation modal*/
        element.addEventListener('show.bs.modal', event => {
            if (event.target.id != 'confirmationModal') {
                fetchDynamicExhibitionData(event.target);
                initFormOnShow(event.target);
            }
        });
    });

    // logic for confirmation modal
    if (confirmationModalEl) {
        confirmationModalEl.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            let headline;
            let message;
            let context;
            let contextCancel;
            let func;
            let funcCancel;
            let data;
            let dataCancel;
            let confirmLabel;
            let cancelLabel;

            if (button) {
                headline = button.getAttribute('data-bs-headline');
                message = button.getAttribute('data-bs-message');
                context = button.getAttribute('data-bs-context');
                contextCancel = button.getAttribute('data-bs-context-cancel');
                func = button.getAttribute('data-bs-func');
                funcCancel = button.getAttribute('data-bs-func-cancel');
                data = button.getAttribute('data-bs-data');
                dataCancel = button.getAttribute('data-bs-data-cancel');
                confirmLabel = button.getAttribute('data-bs-confirm-label');
                cancelLabel = button.getAttribute('data-bs-cancel-label');
            }

            // Update the modal's content.
            const modalTitle = confirmationModalEl.querySelector('.modal-title');
            const modalBodyMessage = confirmationModalEl.querySelector('.modal-body p');
            const modalBtnConfirm = confirmationModalEl.querySelector('button.modal-confirm');
            const modalBtnCancel = confirmationModalEl.querySelector('button.modal-cancel');

            if (headline)
                modalTitle.textContent = headline;
            if (message)
                modalBodyMessage.innerHTML = message;
            if (context)
                modalBtnConfirm.setAttribute('context', context);
            if (func)
                modalBtnConfirm.setAttribute('callback', func);
            if (data)
                modalBtnConfirm.setAttribute('data', data);
            if (contextCancel)
                modalBtnCancel.setAttribute('context-cancel', contextCancel);
            if (funcCancel)
                modalBtnCancel.setAttribute('callback-cancel', funcCancel);
            if (dataCancel)
                modalBtnCancel.setAttribute('data-cancel', dataCancel);
            if (confirmLabel)
                modalBtnConfirm.innerHTML = confirmLabel;
            if (cancelLabel)
                modalBtnCancel.innerHTML = cancelLabel;

            // set the dynamic callbacks to be triggered
            modalBtnConfirm.addEventListener('click', defaultModalHandler);
            modalBtnCancel.addEventListener('click', defaultModalCancelHandler);
        });
    }

    // init tooltips for status      
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl, { container: 'body', html: true }));
}

/**
 * Prepares the form that is going to be edited (specially unlocking that page)
 * Mainly relevant for exhibitions, since many people can edit them.
 * @param {mixed} targetElement 
 */
async function handleCurrentActiveForm(targetElement) {

    // dynamicinit function from indexjs
    dynamicInit();

    //reinit tooltips for this form
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl, { container: 'body' }));

    // check if targetElement was an Event and if not that means it was an HTMLElement
    // and we retrieve the element from the .detail from the CustomEvent (see ajax-functions.js)
    if (targetElement.target) {
        targetElement = targetElement.detail.context;
    }
    // there is a possiblity, that the user was removed from the exhibition, so we check for that
    if (!targetElement.classList.contains('user-removed')) {
        const pageToLock = targetElement.getAttribute("data-pageid");

        if (pageToLock) {
            let _path = location.protocol + '//' + location.host + location.pathname + '.json?lockMe=' + pageToLock;
            console.log(_path);
            try {
                const response = await fetch(_path);
                const { lockactionstatus, lockedBy, overlayCode } = await response.json();

                if (lockactionstatus == 'already_locked') {
                    //console.log('WAS ALREADY Locked by ' + lockedBy);
                    disableForms(targetElement);
                    targetElement.setAttribute('blocked-by', lockedBy);

                    targetElement.classList.add('blocked');

                    let overlay_container = targetElement.querySelector('.overlay_container');
                    overlay_container.innerHTML = overlayCode;
                }
                else {
                    enableForms(targetElement);
                    targetElement.setAttribute('blocked-by', page_id);
                    targetElement.classList.remove('blocked');

                    let overlay_container = targetElement.querySelector('.overlay_container');
                    if (overlay_container) {
                        overlay_container.innerHTML = '';
                    }
                }
            }
            catch (error) {
                console.log('Fetch error: ', error);
            }
        }
    }
}

/**
 * Does some cleanup in the form that was edited (specially unlocking that page)
 * Mainly relevant for exhibitions, since many people can edit them.
 * @param {HTMLElement} targetElement 
 */
async function handleLastActiveForm(targetElement) {
    const pageToUnlock = targetElement.getAttribute("data-pageid");

    if (pageToUnlock) {
        let isBlockedBy = targetElement.getAttribute('blocked-by');
        let blockedParam = isBlockedBy != null ? '&blockedBy=' + isBlockedBy : '';
        let _path = location.protocol + '//' + location.host + location.pathname + '.json?unlockMe=' + pageToUnlock + blockedParam;
        console.log(_path);
        try {
            const response = await fetch(_path);
            const { lockactionstatus, lockedBy } = await response.json();

            if (lockactionstatus == 'was_already_locked') {
                //console.log('IS STILL ALREADY Locked by ' + lockedBy);
            }
            else {
                if (page_id == lockedBy || !lockedBy) {
                    targetElement.setAttribute('blocked-by', '');
                }
            }
        }
        catch (error) {
            console.log('Fetch error: ', error);
        }
    }
}

/**
 * createPhantomToggle
 * creates a virutal button to trigger a bootstrap model
 * that then reads the information passed and dynamically
 * sets the content and callback functions.
 * @param {Object} data 
 */
function createPhantomToggle(data) {
    let phantomToggle = document.createElement("button");
    phantomToggle.setAttribute('data-bs-headline', data.headline ?? '');
    phantomToggle.setAttribute('data-bs-message', data.message ?? '');
    phantomToggle.setAttribute('data-bs-context', data.context ?? '');
    phantomToggle.setAttribute('data-bs-func', data.func ?? '');
    phantomToggle.setAttribute('data-bs-data', data.data ?? '');
    phantomToggle.setAttribute('data-bs-context-cancel', data.contextCancel ?? '');
    phantomToggle.setAttribute('data-bs-func-cancel', data.funcCancel ?? '');
    phantomToggle.setAttribute('data-bs-data-cancel', data.dataCancel ?? '');
    phantomToggle.setAttribute('data-bs-confirm-label', data.confirmLabel ?? 'Bestätigen');
    phantomToggle.setAttribute('data-bs-cancel-label', data.cancelLabel ?? 'Abbrechen');
    confirmationModal.show(phantomToggle);
}
Object.assign(window, { createPhantomToggle });

initAdmin();




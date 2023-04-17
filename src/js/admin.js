import { initForms, resetForms, checkForms, enableForms, disableForms, initFormOnShow } from './controllers/forms';

const cross_data = document.querySelector('#cross-data');
const page_id = cross_data.getAttribute('page-slug');

function initAdmin() {
    console.log('init admin');

    initForms();
    initBSElements();
    rehashUrl();
}
Object.assign(window, { initAdmin });


let url;
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

function defaultModalHandler(ev) {
    let btn_confirm = ev.target;
    let context = document.querySelector(btn_confirm.getAttribute('context')) ?? window;
    executeFunctionByName(btn_confirm.getAttribute('callback'), context, btn_confirm.getAttribute('data'));

    confirmationModal.hide();
}

function defaultModalCancelHandler(ev) {
    let btn_cancel = ev.target;
    let context = document.querySelector(btn_cancel.getAttribute('context-cancel')) ?? window;
    executeFunctionByName(btn_cancel.getAttribute('callback-cancel'), context, btn_cancel.getAttribute('data-cancel'));

    confirmationModal.hide();
}

function executeFunctionByName(functionName, context, args) {
    var args = Array.prototype.slice.call(arguments, 2);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for (var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return context[func].apply(context, args);
}

function resetFormModal(data) {
    let tabActiveEl = document.querySelector('button[data-bs-toggle="tab"].active');
    let tabPane = tabActiveEl.getAttribute('data-bs-target');
    resetForms(document.querySelector(tabPane));
}
Object.assign(window, { resetFormModal });

function handleChangedFormModal(data) {
    // reset form
    let tabActiveEl = document.querySelector('button[data-bs-toggle="tab"].active');
    let tabPane = tabActiveEl.getAttribute('data-bs-target');
    resetForms(document.querySelector(tabPane));

    let triggerEl = document.querySelector('button[data-bs-target="' + data + '"]');
    //const tabTrigger = new bootstrap.Tab(triggerEl);
    const tabTrigger = new Tab(triggerEl);
    tabTrigger.show();
}
Object.assign(window, { handleChangedFormModal });
/** END Functions executable by modals **/


let confirmationModal;
let confirmationModalEl = document.getElementById('confirmationModal');
if (confirmationModalEl) {
    //confirmationModal = new bootstrap.Modal('#confirmationModal');
    confirmationModal = new Modal('#confirmationModal');
}

const CHANGED_FORM_MODAL = 'handleChangedFormModal';
function initBSElements() {

    /** TAB TOGGLES ***/
    console.log('init bs');
    // event listener for tab exhibition; when it is done, handle locking
    document.addEventListener('done_fetching_information', handleCurrentActiveForm, true);

    let toggles = document.querySelectorAll('button[data-bs-toggle="tab"]');
    toggles.forEach(element => {
        element.addEventListener("shown.bs.tab", async function (ev) {
            let newUrl;
            const hash = this.getAttribute("data-bs-target");
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


            let tabPaneID = ev.target.getAttribute('data-bs-target');
            let tabPane = document.querySelector(tabPaneID);

            if (tabPane.id == 'pane-exhibition') {
                fetchDynamicExhibitionData(tabPane);
                initFormOnShow(tabPane);
            }
        });

        element.addEventListener("hide.bs.tab", async function (ev) {

            let tabPaneID = ev.target.getAttribute('data-bs-target');
            let tabPane = document.querySelector(tabPaneID);
            let tabFormsCheck = checkForms(tabPane);
            let destinationTab = ev.relatedTarget.getAttribute('data-bs-target');

            if (tabFormsCheck.statusChanged) {
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
                if (tabPane.id == 'pane-exhibition') {
                    handleLastActiveForm(tabPane);
                }
            }
        });
    });


    //modals
    let modals = document.querySelectorAll('.modal');
    modals.forEach(element => {
        element.addEventListener('hidden.bs.modal', event => {
            console.log('modal closed');
            resetForms(event.target);
            handleLastActiveForm(event.target);
        });

        element.addEventListener('show.bs.modal', event => {
            console.log('modal open');
            if (event.target.id != 'confirmationModal') {
                fetchDynamicExhibitionData(event.target);
                initFormOnShow(event.target);
            }
        });
    });

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

            modalBtnConfirm.addEventListener('click', defaultModalHandler);
            modalBtnCancel.addEventListener('click', defaultModalCancelHandler);
        });
    }

    //tooltips curators status      
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    //const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, { container: 'body' }));
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl, { container: 'body' }));
}

async function handleCurrentActiveForm(targetElement) {

    dynamicInit();

    //reinit tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    //const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, { container: 'body' }));
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl, { container: 'body' }));

    if (targetElement.target) {
        targetElement = targetElement.detail.context;
    }

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
                    /******** TODO
                     * 
                     * 
                     * REPOPULATE FIELDS *******/
                    //console.log('WAS now unlocked by ' + lockedBy);
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
            //console.log(lockactionstatus);
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




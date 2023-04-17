//import { mapToStyles } from '@popperjs/core/lib/modifiers/computeStyles';

/**
 * Form class that ahs its own watchdog for tracking changes,
 * reseting to default values after changes, setting statuses
 * for inputs, populating selects from json, and more
 * 
 * Selects and multiselects are based on virtual-select.js
 */

export default class JDSForm {

    constructor(element, hasWatchdog = true) {
        this.formInitialized = false;
        this.formDisabled = false;
        this.nativeElement = element;
        this.submit = this.nativeElement.querySelector('button[type="submit"]');
        this.reset = this.nativeElement.querySelector('button.btn-reset[type="button"]');
        this.hasWatchdog = hasWatchdog;
        this.formChanged = false;
        this.jsonPopulatesIndex = 0;
        this.formsMap = new Map();
        this.selectGroups = new Map();
        this.selectGroupsValues = new Map();
        this.selects = [];
        this.alwaysDisabledOptions = [];
        this.formEls;

        this.virtualSelectConf = {
            placeholder: 'Bitte wählen',
            noOptionsText: 'Keine Optionen',
            noSearchResultsText: 'Keine Ergebnisse',
            searchPlaceholderText: 'Suchen...',
            allOptionsSelectedText: 'Alle',
            optionsSelectedText: 'ausgewählt',
            optionSelectedText: 'ausgewählt',
            moreText: 'mehr...',
            emptyValue: "",

            search: true,
            disableSelectAll: true,
            silentInitialValueSet: true,
            autoSelectFirstOption: false,
            disableOptionGroupCheckbox: true,
        };

        this.init();
    }

    /**
     * Init the form
     */
    init() {
        this.submit.disabled = true;

        if (this.reset)
            this.reset.disabled = true;

        this.initRegularSelects();
        this.initJsonPopulators();

        this.selects = this.nativeElement.querySelectorAll('.select-has-group');

        this.selects.forEach(element => {
            element.addEventListener('change', this.onSelectChange.bind(this.nativeElement));
        });

        this.nativeElement.selects = this.selects;
        this.nativeElement.virtualSelectConf = this.virtualSelectConf;
    }

    /**
     * Init group of selects with shared disabled options
     */
    initSelectGroupWatchdog() {
        console.log('init form groups');

        // create list of selected items
        this.selects.forEach(element => {
            this.setSelectedOptions(element);
        });

        // create list of original disabled items from DOM excluding selected options from this group
        this.selects.forEach(element => {
            element.permaDisabledOptions = [];

            let disabledItems = element.getDisabledOptions();
            let targetGroup = element.getAttribute('data-select-group');
            let groupArray = this.selectGroupsValues.get(targetGroup);

            disabledItems.forEach(item => {
                if (groupArray.indexOf(item) < 0) {
                    element.permaDisabledOptions.push(item);
                }
                //element.setAlwaysDisabledOptions(element.permaDisabledOptions);                
            });

            this.alwaysDisabledOptions = element.permaDisabledOptions;
        });
    }

    /**
     * Handles value change of a select/multiselect that are part of a group
     * @param {Event} ev 
     */
    onSelectChange(ev) {
        let targetGroup = ev.target.getAttribute('data-select-group');
        this.selectGroups.set(targetGroup, '');

        // insert all values
        this.selects.forEach(element => {
            this.setSelectedOptions(element);
        });

        // then refresh disabled options
        this.selects.forEach(element => {
            this.setDisabledOptions(element);
        });
    }

    /**
     * Sets the selected values of the select/multiselect that are part of a group
     * @param {HTMLElement} element 
     */
    setSelectedOptions(element) {
        let group = element.getAttribute('data-select-group');
        let groupArray = this.selectGroups.get(group);
        let groupArrayValues = this.selectGroupsValues.get(group);

        if (groupArray && groupArray.length > 0) {
            this.selectGroups.set(group, groupArray.concat([element.getSelectedOptions()]));
            this.selectGroupsValues.set(group, groupArrayValues.concat([element.value]));
        }
        else {
            this.selectGroups.set(group, [element.getSelectedOptions()]);
            this.selectGroupsValues.set(group, [element.value]);
        }
    }

    /**
     * Sets the disabled options of the select/multiselect that are part of a group
     * @param {HTMLElement} element 
     */
    setDisabledOptions(element) {
        let group = element.getAttribute('data-select-group');
        let _disOptions = this.selectGroups.get(group);
        let disOptions = _disOptions.concat();

        for (var i = 0; i < disOptions.length; i++) {
            disOptions[i] = disOptions[i] ? disOptions[i].value : '';
        }

        if (this.alwaysDisabledOptions && this.alwaysDisabledOptions.length > 0) {
            element.setDisabledOptions(disOptions.concat(this.alwaysDisabledOptions), true);
        }
        else {
            element.setDisabledOptions(disOptions, true);
        }
    }

    /**
     * Sets all options as enabled of the select/multiselect that are part of a group
     */
    setEnabledOptions() {
        this.selects = this.querySelectorAll('.select-has-group');
        this.selects.forEach(element => {
            element.setEnabledOptions(true);
        });
    }

    /**
     * Inits Json populators, in case select/multiselect has external source
     */
    initJsonPopulators() {
        var jsonElements = this.nativeElement.querySelectorAll('div[json-path]');

        jsonElements.forEach(element => {
            this.loadJson(element.getAttribute('json-path'), element.id, element.getAttribute('value'), jsonElements.length);
        });

        /* if (jsonElements.length > 0 && this.hasWatchdog) {
        }
        else if (this.hasWatchdog) {
            this.initFormWatchdog();
        } */


        if (jsonElements.length <= 0 && this.hasWatchdog) {
            this.initFormWatchdog();
        }
    }

    /**
     * Fetches the json data
     * @param {string} dataurl 
     * @param {string} elID 
     * @param {string} currentValue 
     * @param {number} lengthToWait 
     */
    loadJson(dataurl, elID, currentValue, lengthToWait) {
        fetch(dataurl)
            .then((response) => response.json())
            .then((json) => this.initJsonSelect(json, elID, currentValue, lengthToWait));
    }

    /**
     * Inits regular virtual-selects
     */
    initRegularSelects() {
        let conf = Object.assign({
            ele: 'select',
            additionalClasses: 'regular-select',
        }, this.virtualSelectConf);

        VirtualSelect.init(conf);
    }

    /**
     * Inits json populated virtual-select after the data has been fetched.
     * It checks if all json virtual-selects are done and then finally
     * initializes the form watchdog
     * @param {json} jsonData 
     * @param {string} id 
     * @param {string} value 
     * @param {number} lengthToWait 
     */
    initJsonSelect(jsonData, id, value, lengthToWait) {
        let conf = Object.assign({
            ele: '#' + id,
            options: jsonData,
            selectedValue: value.replaceAll(" ", "").split(','),
        }, this.virtualSelectConf);

        VirtualSelect.init(conf);

        if (++this.jsonPopulatesIndex == lengthToWait && this.hasWatchdog) {
            this.watchdogEnabled = true;
            this.initFormWatchdog();
        }
    }

    /**
     * Inits the watchdog to track changes of the elements inside the form
     * after storing default values and setting up event listeners on all elements
     */
    initFormWatchdog() {
        var wdForm = this.nativeElement;
        this.formEls = wdForm.querySelectorAll('input:not([type="submit"],.vscomp-search-input,#dseHidden,.radioimage-radio), textarea, select, .radioimages');

        this.formEls.forEach(formEl => {

            if (formEl.tagName.toLowerCase() === 'input' && formEl.getAttribute('type') === 'checkbox') {
                this.formsMap.set(formEl.getAttribute('name'), formEl.checked);
            }
            else {
                if (formEl.hasAttribute('original-data')) {
                    this.formsMap.set(formEl.getAttribute('name'), formEl.getAttribute('original-data'));
                }
                else if (formEl.value) {
                    this.formsMap.set(formEl.getAttribute('name'), formEl.value);
                }
                else {
                    this.formsMap.set(formEl.getAttribute('name'), formEl.getAttribute('value'));
                }
            }

            //console.log(this.formsMap);

            /**
             * Sets a function to check for simple validacy of field (empty or not empty).
             * More complex validation happens via ajax calls, if the input element has
             * that HTML property declared (please refer to input_element.php snippet)
             * @returns {string}
             */
            formEl.fieldValid = function () {
                var response = 'neutral';
                let label = document.querySelector('label[for="' + this.name + '"]');

                if (label) {
                    if (label.classList.contains('is-required') && this.value == '') {
                        var response = 'error';
                    }
                    else if (this.value != '') {
                        var response = 'valid';
                    }
                }

                return response;
            };

            // sets initial icon for valid, not valid, or warning 
            this.setElementCheck(this.nativeElement, formEl);

            let eventTarg = this.nativeElement.querySelector('#' + formEl.getAttribute('name'));

            // add listeners for validation and change tracking to the element
            // depending if its a div or actual input
            if (eventTarg.tagName == 'DIV') {
                eventTarg.addEventListener('change', function (ev) {

                    let input = ev.target.querySelector('input');
                    if (ev.currentTarget.classList.contains('radioimages')) {
                        input = ev.currentTarget;
                        ev.currentTarget.resetRadioGroup = function (val) {
                            let radios = this.querySelectorAll('input');

                            radios.forEach(element => {
                                if (element.value != val)
                                    element.checked = false;
                                else
                                    element.checked = true;
                            });
                        }
                    }
                    if (ev.target.value != this.formsMap.get(ev.target.getAttribute('name'))) {
                        this.setElementChanged(this.nativeElement, input);
                    }
                    else {
                        this.unsetElementChanged(input);
                    }
                    this.setElementCheck(this.nativeElement, input);

                }.bind(this));
            }
            else {
                eventTarg.addEventListener('input', function (ev) {
                    if (ev.target.value != this.formsMap.get(ev.target.id)) {
                        this.setElementChanged(this.nativeElement, ev.target);
                    }
                    else {
                        this.unsetElementChanged(ev.target);
                    }
                    this.setElementCheck(this.nativeElement, ev.target);

                }.bind(this));
            }

        });

        // form is done initializing
        this.formInitialized = true;

        // bind some class functions and properties to native element
        this.bindToDOMElement();
    }

    /**
     * Sets icon for valid, not valid, or warning
     * @param {HTMLElement} scope // function gets called inside class or on native element
     * @param {HTMLElement} el // input element to be handled
     */
    setElementCheck(scope, el) {
        if (el) {
            let inputTarget = scope.querySelector('#' + el.getAttribute('name'));
            let ajaxHandler = inputTarget.getAttribute('data-ajax-handler');

            // if element has ajax handler for validation, call that function instead
            // of the simple one here below
            if (inputTarget && ajaxHandler) {
                if (ajaxHandler) {
                    window[ajaxHandler](inputTarget);
                }
            } else {
                let check = scope.querySelector('.checks[data-for="' + el.name + '"]');
                let error = scope.querySelector('.errors[data-for="' + el.name + '"]');
                let input = scope.querySelector('#' + el.name);

                if (input && error && check) {
                    if (el.fieldValid() == 'valid') {
                        error.classList.add('d-none');
                        check.classList.remove('d-none');
                        input.classList.add('filled');
                    }
                    else if (el.fieldValid() == 'error') {
                        error.classList.remove('d-none');
                        check.classList.add('d-none');
                        input.classList.remove('filled');
                    }
                    else {
                        error.classList.add('d-none');
                        check.classList.add('d-none');
                        input.classList.remove('filled');
                    }
                }
            }
        }
    }

    /**
     * Sets an attribute change for element and dispatches an event
     * for other pieces of code to handle an input change. It also
     * updates the form overall change-status
     * @param {HTMLElement} el 
     */
    setElementChanged(scope, el) {
        if (el) {
            let inputTarget = scope.querySelector('#' + el.getAttribute('name'));
            let changeHandler = inputTarget.getAttribute('data-change-handler');

            if (inputTarget && changeHandler) {
                if (changeHandler) {
                    window[changeHandler](inputTarget);
                }
            }

            el.setAttribute('statusChanged', '');
            this.updateFormStatus();
        }

        document.dispatchEvent(new CustomEvent('current_form', { detail: { context: this.nativeElement } }));
    }

    /**
     * Removes the status change form the element, meaning it has original value (again)
     * and updates form overall change-status
     * @param {HTMLElement} el 
     */
    unsetElementChanged(el) {
        if (el) {
            el.removeAttribute('statusChanged');
            this.updateFormStatus();
        }
    }

    /**
     * Updates the form status by checking the status of all elements inside.
     * Dispatches an event if the form was changed for other pieces of code
     * to handle changes in the form
     */
    updateFormStatus() {
        let formChanged = false;
        this.formEls.forEach(formEl => {
            let status = formEl.hasAttribute('statusChanged');
            formChanged = formChanged || status;
        });

        this.formChanged = formChanged;

        if (this.nativeElement) {
            this.nativeElement.formChanged = this.formChanged;
            this.nativeElement.setAttribute('form_changed', this.formChanged);
        }

        this.submit.disabled = !this.formChanged;
        if (this.reset)
            this.reset.disabled = !this.formChanged;        

        let containerpane = window.findParentContainer(this.nativeElement ? this.nativeElement.parentNode : this.parentNode, 'tab-pane');
        document.dispatchEvent(new CustomEvent('form_changed', { detail: { paneID: containerpane.id, state: this.formChanged } }));
    }

    /**
     * Enables all form elements and the form
     */
    enableForm() {
        this.formEls.forEach(formEl => {
            let eventTarg = this.querySelector('#' + formEl.getAttribute('name'));

            if (eventTarg.classList.contains('vscomp-ele')) {
                if (!eventTarg.classList.contains('force-disabled')) {
                    eventTarg.enable();
                }
            }
            else {
                if (!formEl.classList.contains('force-disabled')) {
                    formEl.disabled = false;
                }
            }
        });

        this.formDisabled = false;
    }

    /**
     * Disables all form elements and the form
     */
    disableForm() {
        this.formEls.forEach(formEl => {
            let eventTarg = this.querySelector('#' + formEl.getAttribute('name'));

            if (eventTarg.classList.contains('vscomp-ele')) {
                if (!eventTarg.classList.contains('force-disabled')) {
                    eventTarg.disable();
                }
            }
            else {
                if (!eventTarg.classList.contains('force-disabled')) {
                    formEl.disabled = true;
                }
            }
        });

        this.formDisabled = true;
    }

    /**
     * Resets the element to the default values
     * @param {HTMLElement} el 
     */
    resetElement(element) {
        if (typeof element == 'string')
            element = document.querySelector('input[name="' + element + '"');

        let val = this.formsMap.get(element.getAttribute('name'));

        if (element.classList.contains('vscomp-hidden-input')) {
            let form = element.form;
            let el = form.querySelector('#' + element.getAttribute('name'));
            el.setValue(val.split(','), true);
        }
        else if (element.classList.contains('radioimages')) {
            element.value = val;
            element.resetRadioGroup(val);
        }
        else if (this.tagName.toLowerCase() === 'input' && element.getAttribute('type') === 'checkbox') {
            element.checked = val;
        }
        else {
            element.value = val;
        }

        this.setElementCheck(this, element);
        element.removeAttribute('statusChanged');

        this.updateFormStatus();
    }
    /**
     * Resets the form to default values and removes all status changes
     */
    resetToDefault() {
        if (this.formChanged) {

            this.selectGroups = new Map();
            this.selectGroupsValues = new Map();

            this.setEnabledOptions();

            this.formEls.forEach(formEl => {
                this.resetElement(formEl);
                /* let val = this.formsMap.get(formEl.getAttribute('name'));

                if (formEl.classList.contains('vscomp-hidden-input')) {
                    let form = formEl.form;
                    let el = form.querySelector('#' + formEl.getAttribute('name'));
                    el.setValue(val.split(','), true);
                }
                else if (formEl.classList.contains('radioimages')) {
                    formEl.value = val;
                    formEl.resetRadioGroup(val);
                }
                else if (formEl.tagName.toLowerCase() === 'input' && formEl.getAttribute('type') === 'checkbox') {
                    formEl.checked = val;
                }
                else {
                    formEl.value = val;
                }

                this.setElementCheck(this, formEl);
                formEl.removeAttribute('statusChanged'); */
            });

            this.formChanged = false;
            this.setAttribute('form_changed', false);
            this.submit.disabled = true;

            if (this.reset)
                this.reset.disabled = true;
        }

        let containerpane = window.findParentContainer(this.parentNode, 'tab-pane');
        document.dispatchEvent(new CustomEvent('form_changed', { detail: { paneID: containerpane.id, state: this.formChanged } }));
    }


    /**
     * Binds some class functions and properties to the native element
     */
    bindToDOMElement() {

        this.nativeElement.resetToDefault = this.resetToDefault;
        this.nativeElement.initSelectGroupWatchdog = this.initSelectGroupWatchdog;
        this.nativeElement.setSelectedOptions = this.setSelectedOptions;
        this.nativeElement.setDisabledOptions = this.setDisabledOptions;
        this.nativeElement.setEnabledOptions = this.setEnabledOptions;
        this.nativeElement.onSelectChange = this.onSelectChange;
        this.nativeElement.setElementCheck = this.setElementCheck;
        this.nativeElement.disableForm = this.disableForm;
        this.nativeElement.enableForm = this.enableForm;
        this.nativeElement.resetElement = this.resetElement;
        this.nativeElement.updateFormStatus = this.updateFormStatus;
        //this.nativeElement.findParentContainer = this.findParentContainer;

        this.nativeElement.formInitialized = this.formInitialized;
        this.nativeElement.formDisabled = this.formDisabled;
        this.nativeElement.selectGroups = this.selectGroups;
        this.nativeElement.selectGroupsValues = this.selectGroupsValues;
        this.nativeElement.formEls = this.formEls;
        this.nativeElement.formsMap = this.formsMap;
        this.nativeElement.formChanged = this.formChanged;
        this.nativeElement.submit = this.submit;
        this.nativeElement.reset = this.reset;
        //this.nativeElement.selects = this.selects;
    }


}



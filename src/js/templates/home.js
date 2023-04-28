
//import Autocomplete from '@trevoreyre/autocomplete-js';
import autoComplete from "@tarekraafat/autocomplete.js";
import DynamicContainer from '../components/DynamicContainer';
import { Collapse } from "bootstrap";

initSelects();

const bod = document.body;
const btn_btl = document.getElementById('btl');
const btl_revert_time = 2500;

/**
 * Main init for home
 */
function initHome() {
  console.log('init home');
  const introText = new DynamicContainer(document.querySelector('.home-intro'), document.getElementById('scroll__container'));

  // setup virtual-selects
  selectorImpulse.addEventListener('change', switchImpulse);
  selectorCollection.addEventListener('change', switchCollection);
  filterSelects.forEach(fselect => {
    fselect.addEventListener('change', filterChange);
  });

  //back to left for desktop    
  btn_btl.addEventListener('click', leftFunction);
  scrollLeftStop(revert_btl, btl_revert_time);

  initScroller();

  document.addEventListener("focus", focusChange, true);

  sessionStorageGet();
}

/**
 * Handles back-to-left button hidding and showing when container scrolls
 */

function scrollHFunction() {
  if (scrollContainer.scrollLeft > window.innerWidth/2) {
    btn_btl.classList.add("showing");
  } else {
    btn_btl.classList.remove("showing");
  }
}

/**
* Handles back to top function
*/
function leftFunction() {
  scrollContainer.scroll({
    left: 0,
    behavior: 'smooth'
  })
}

/**
* Hides back to left button
*/
function revert_btl() {
  btn_btl.classList.remove("showing");
}

/**
 * Handles stop after user was scrolling horizontally so button can stay delayed on screen
 * @param {Function} callback 
 * @param {number} refresh 
 * @returns 
 */
function scrollLeftStop(callback, refresh = 66) {
  // Make sure a valid callback was provided
  if (!callback || typeof callback !== 'function') return;

  let isScrolling;
  scrollContainer.addEventListener('scroll', function (event) {
    window.clearTimeout(isScrolling);
    isScrolling = setTimeout(callback, refresh);
  }, false);
}

/**
 * Init horizontal scrolling functionality 
 */
function initScroller() {
  if (!scrollerInitialised) {

    if (!is_touch_enabled()) {
      // listen to 'wheel' event so that the container only scrolls horizontally when we scroll directly on it
      document.addEventListener("wheel", (evt) => {
        evt.preventDefault();
        // dont react if its a list item or elements with 'ignoreWheel' class, and some others
        let tag = evt.target.tagName;
        let classlist = evt.target.classList;
        if (tag == 'LI' || classlist.contains('vscomp-option') || classlist.contains('vscomp-option-text') || classlist.contains('ignoreWheel')) {
          return
        }
        if (!document.body.classList.contains('nav-open') && !document.body.classList.contains('filter-open')) {
          if (Math.abs(evt.deltaY) > Math.abs(evt.deltaX)) {
            scrollContainer.scrollLeft += evt.deltaY;
          }
          else {
            scrollContainer.scrollLeft += evt.deltaX;
          }
          scrollHFunction();
          lastFocusItem = null;
        }
      });
    }

    scrollerInitialised = true;
  }
}

/* *******
 * 
 * LOAD MORE
 * 
 * ********/
const collectionContainer = document.querySelector('.load-more-container');
const podestContainer = document.querySelector('.podest-container');
const scrollContainer = document.querySelector("#scroll__container");
const loadmoreTrigger = document.querySelector(".loadmore-trigger");
const selectorCollection = document.querySelector('.select-collection');
const selectorImpulse = document.querySelector('.select-impulse');
const selectorCuratorState = document.querySelector('#curator_state_selector');
const selectorSchoolClass = document.querySelector('#schoolclass_selector');
const filterSelects = document.querySelectorAll('.filter__select');
const limit = parseInt(collectionContainer.getAttribute('data-limit'));

let currentCollection = collectionContainer.getAttribute('collection-type');
let currentImpulse = selectorImpulse.value;

let offset = 0;
let scrollerInitialised = false;
let filterquery = [];
let filterqueryString = '';
let lastOffset = -1;
/**
 * Fetch loadmore data for home
 * @returns 
 */
const fetchProjects = async () => {

  if (lastOffset == offset)
    return;

  if (!sessionInitialized)
    return;

  lastOffset = offset;
  let url = `${window.location.href.split('?')[0]}`;
  url = `${url}home.json?limit=${limit}&offset=${offset}&currentCollection=${currentCollection}&currentImpulse=${currentImpulse}&${filterqueryString}`;

  console.log(url);

  try {
    const response = await fetch(url);
    const { html, more, comment } = await response.json();
    loadmoreTrigger.hidden = !more;
    podestContainer.innerHTML += html;
    offset += limit;

    console.log("JSON COMMENT: " + offset);

    dynamicInit();
    focusLastItem(parseInt(lastOffset));

    if (typeof twttr !== 'undefined') {
      let newTweets = document.querySelectorAll('blockquote.twitter-tweet');
      newTweets.forEach(element => {
        twttr.widgets.load(element);
      });
    }
    else {
      console.log('no twitter');
    }

  } catch (error) {
    console.log('Fetch error: ', error);
  }
}

/* ********
 * 
 * TAB NAVIGATION THROUGH EXHIBITS OR EXHIBITIONS
 * 
 * ********/

// save the current focus. The 'wheel' event resets the last item
let lastFocusItem = null;
/**
 * Checks if the last focused item was an exhibit or exhibition
 * in order to handle tab navigation inside loadmore
 * @param {event} event 
 * @returns 
 */
function focusChange(event) {
  if (!event.target.classList) {
    return;
  }
  if (event.target.classList.contains('exhibit-link') || event.target.classList.contains('exhibition-link'))
    lastFocusItem = event.target;
  else
    lastFocusItem = null;
}

/**
 * Focuses the last element in loadmore in
 * case user was navigating with keyboard
 * @param {number} lastOffset 
 */
function focusLastItem(lastOffset) {
  if (lastFocusItem) {
    let loadmore_items = podestContainer.querySelectorAll('.loadmore-element');

    if (loadmore_items[lastOffset - 1]) {
      loadmore_items[lastOffset - 1].querySelector('a').focus();
      console.log(loadmore_items[lastOffset - 1]);
    }
  }
}


/* ********
 * 
 * FILTER
 * 
 * ********/

const collapseElementList = document.getElementById('dd-filter'); //dropdown for filters
const collapseList = new Collapse(collapseElementList, { // init the list programatically with bootstrap
  toggle: false
});
const btn_dd = document.querySelector('.filter-toggle'); //dropdown button for filters

// handle states of elements for opening, closing, and click outside
document.addEventListener('click', function (e) {
  let targetEl = e.target; // clicked element 

  if (e.target == btn_dd) {
    if (btn_dd.classList.contains('collapsed')) {
      collapseList.show();
      btn_dd.classList.remove('collapsed');
      btn_dd.setAttribute('aria-expanded', 'true');
    }
    else {
      collapseList.hide();
      btn_dd.classList.add('collapsed');
      btn_dd.setAttribute('aria-expanded', 'false');
    }

    return;
  }

  let status = false;
  do {
    if (targetEl == collapseElementList) {
      status = true;
      break;
    }
    if (status)
      break;
    // Go up the DOM and keep looking
    targetEl = targetEl.parentNode;
  } while (targetEl);

  if (!status) {
    if (!btn_dd.classList.contains('collapsed')) {
      collapseList.hide();
      btn_dd.classList.add('collapsed');
      btn_dd.setAttribute('aria-expanded', 'false');
    }
  }
});

/**
 * Initialize the virtual-selects in home
 */
function initSelects() {
  // Common parameters
  let vsSettings = {
    noOptionsText: 'Keine Optionen',
    noSearchResultsText: 'Keine Ergebnisse',
    searchPlaceholderText: 'Suchen...',
    allOptionsSelectedText: 'Alle',
    optionsSelectedText: 'ausgewählt',
    optionSelectedText: 'ausgewählt',
    moreText: 'mehr...',
    additionalClasses: 'regular-select',
    emptyValue: "",
    disableSelectAll: true,
    silentInitialValueSet: true,
    autoSelectFirstOption: false,
    disableOptionGroupCheckbox: true,
    search: false,
    showDropboxAsPopup: false,
  };
  let conf;

  // Collection selector
  conf = Object.assign({
    ele: '#collection_selector',
    placeholder: 'Bitte wählen',
    hideClearButton: true,
    optionAutoHeight: true,
  }, vsSettings);
  VirtualSelect.init(conf);

  // Impulse selector
  conf = Object.assign({
    ele: '#impulse_selector',
    placeholder: 'Bitte wählen',
    hideClearButton: true,
    optionAutoHeight: true,
  }, vsSettings);
  VirtualSelect.init(conf);

  // State selector
  conf = Object.assign({
    ele: '.select-curator_state',
    placeholder: 'Bundesland...',
  }, vsSettings);
  VirtualSelect.init(conf);

  // Class selector
  conf = Object.assign({
    ele: '.select-schoolclass',
    placeholder: 'Klasse/Stuffe...',
  }, vsSettings);
  VirtualSelect.init(conf);
}

/**
 * Handle changing collection (exhibits or exhibitions)
 * @param {event} evt 
 */
function switchCollection(evt) {
  currentCollection = evt.target.value;
  //reset data
  offset = 0;
  lastOffset = -1;
  collectionContainer.setAttribute('collection-type', currentCollection);
  podestContainer.innerHTML = '';
  input_search.value = "";
  sessionStorageSet('current_collection', currentCollection);
  fetchProjects();
}

/**
 * Handle changing impulse (topic)
 * @param {event} evt 
 */
function switchImpulse(evt) {
  currentImpulse = selectorImpulse.value;

  //reset data
  offset = 0;
  lastOffset = -1;
  podestContainer.innerHTML = '';
  sessionStorageSet('current_impulse', currentImpulse);
  fetchProjects();
}

/**
 * Triggered everytime a virtual-select's value is changed
 * @param {Event} evt 
 */
function filterChange(evt) {
  let param = evt.target.name;
  let value = evt.target.value;

  if (value != '') {
    // set the value in sessionStorage
    filterquery[param] = value;
    sessionStorageSet(param, filterquery[param]);
  }
  else {
    // clear the value in sessionStorage
    delete filterquery[param];
    sessionStorageSet(param, '');
  }

  collapseList.hide();
  btn_dd.classList.add('collapsed');
  btn_dd.setAttribute('aria-expanded', 'false');

  serializeFilterQuery();

  //reset data
  offset = 0;
  lastOffset = -1;
  podestContainer.innerHTML = '';
  fetchProjects();

  refreshFilterBadge(evt);
}

let filtersActive = [];
let badge_filters = document.querySelector('.filter-toggle .badge');
/**
 * Refreshes the bootstrap Badge displaying the
 * number of active filters
 * @param {Event} evt 
 */
function refreshFilterBadge(evt) {
  let currentFilter = evt.target;
  if (!currentFilter.value || currentFilter.value == "") {
    const index = filtersActive.indexOf(currentFilter);
    const x = filtersActive.splice(index, 1);
  }
  else {
    if (!filtersActive.includes(currentFilter)) {
      filtersActive.push(currentFilter);
    }
  }

  if (filtersActive.length > 0) {
    badge_filters.classList.remove('d-none');
  }
  else {
    badge_filters.classList.add('d-none');
  }
  badge_filters.querySelector('.visual-information').innerHTML = filtersActive.length;
}

/**
 * Creates a string query from the Array of parameters in 'filterquery'
 */
function serializeFilterQuery() {
  filterqueryString = '';

  for (const key in filterquery) {
    filterqueryString += key + "=" + filterquery[key] + '&';
  }
}

let intersectionObserver;
/**
 * Init IntersectionObserver to trigger loadmore functionality
 */
function initLoadmore() {
  intersectionObserver = new IntersectionObserver(entries => {
    if (entries[0].intersectionRatio <= 0) return;
    fetchProjects();
  });
  // start observing for position of the loadmoreTrigger
  intersectionObserver.observe(loadmoreTrigger);
}

/* ***********
 * 
 * FILTER MENU
 * 
 * **********/
const filter_btn = document.querySelector(".filter__btn");
const filter_area = document.querySelector(".filter__container");
const filter_close_btn = document.querySelector(".x-btn");
const filter_close_overlay = document.querySelector(".filter__overlay");

filter_btn.addEventListener("click", openFilterMenu);
filter_close_btn.addEventListener("click", closeFilterMenu);
filter_close_overlay.addEventListener("click", closeFilterMenu);

/**
 * Opens filter dropdown
 * @param {Event} e 
 */
function openFilterMenu(e) {
  filter_btn.classList.toggle("active");
  filter_area.classList.toggle("active");
  bod.classList.toggle("filter-open");
}
/**
 * Closes filter dropdown
 * @param {Event} e 
 */
function closeFilterMenu(e) {
  filter_btn.classList.remove("active");
  filter_area.classList.remove("active");
  bod.classList.remove("filter-open");
}


/* ***********
 * 
 * SEARCH
 * 
 * ***********/
let btn_search_submit = document.querySelector('#btn__search_submit');
let badge_autocomplete = document.querySelector('#autocomplete .badge');
let input_search = document.querySelector('#autocomplete .autocomplete-input');

/**
 * Custom search engine returning just the record and not
 * re-searching as default behaviour for autoComplete
 * @param {string} query 
 * @param {string} record 
 * @returns {string}
 */
function searchEngine(query, record) {
  return record;
}

const autoCompleteJS = new autoComplete({
  selector: "#autocomplete .autocomplete-input",
  placeHolder: "",
  threshold: 2,
  searchEngine: searchEngine, // Strict, loose or custom search engine
  data: {
    src: async (query) => {
      try {
        let url = location.protocol + '//' + location.host + location.pathname;
        url = `${url}home.json?searchQuery=${query}&currentCollection=${currentCollection}&currentImpulse=${currentImpulse}`;

        // Set count of results to placeholder '...' in the bootstrap Badge
        badge_autocomplete.querySelector('.visual-information').innerHTML = '...';

        // Fetch Data from external Source
        const source = await fetch(url);
        const data = await source.json();
        return data.searchResults;
      } catch (error) {
        console.log('Fetch error: ', error);
        return error;
      }
    },
    // Gets the value for the specified key from api's response
    keys: ["title"],
  },
  resultsList: {
    maxResults: 300,
    noResults: true,
  },
  resultItem: {
    highlight: true,
    tag: 'li',
    class: 'list-group-item',
    element: (item, data) => {
      // Set attributes for result items
      item.setAttribute("href", data.value.url);
      item.setAttribute("role", 'listitem');
    }
  },
  events: {
    input: {
      focus: (event) => {
        autoCompleteJS.open();
        badge_autocomplete.classList.remove('d-none');
      },
      blur: (event) => {
        autoCompleteJS.close();
        badge_autocomplete.classList.add('d-none');
      },
      results: (event) => {
        let results = event.detail.results;
        // resfresh count of results in bootstrap Badge
        badge_autocomplete.querySelector('.visual-information').innerHTML = results.length;
      },
      selection: (event) => {
        // Go to selection's URL
        window.location.href = event.detail.selection.value.url;
      }
    }
  }
});

/* ***********
 * 
 * LOCAL STORAGE
 * 
 * **********/

/**
 * Saves the values for the key in the browser session for
 * rebuilding home filters after leaving to another page
 * @param {string} key 
 * @param {string} value 
 */
function sessionStorageSet(key, value) {
  sessionStorage.setItem(key, value);
}

let sessionInitialized = false;
/**
 * Retreives the session parameters to rebuild home filters
 */
function sessionStorageGet() {
  let collection = sessionStorage.getItem('current_collection');
  let impulse = sessionStorage.getItem('current_impulse');
  let curator_state = sessionStorage.getItem('curator_state');
  let school_class = sessionStorage.getItem('schoolclass');

  if (collection) {
    selectorCollection.setValue(collection);
  }

  if (impulse) {
    selectorImpulse.setValue(impulse);
  }

  if (curator_state)
    selectorCuratorState.setValue(curator_state);

  if (school_class)
    selectorSchoolClass.setValue(school_class);

  // selects need a short delay
  setTimeout(() => {
    sessionInitialized = true;
    initLoadmore();
  }, 100);
}

/* ************
 * 
 * UTILITIES
 * 
 * ************/

/**
 * 
 * @returns {boolean}
 */
function is_touch_enabled() {
  return ('ontouchstart' in window) ||
    (navigator.maxTouchPoints > 0) ||
    (navigator.msMaxTouchPoints > 0);
}

initHome();




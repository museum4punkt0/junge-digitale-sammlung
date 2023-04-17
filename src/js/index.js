import Loadeer from "loadeer";
import Alert from "./components/Alert";
import '@kuzorov/smoothscroll-polyfill';
import { Tab, Modal, Tooltip } from 'bootstrap';
import { initEmbedLogic } from "./controllers/embeds";
import { setupVideos } from './controllers/video-handler';
import { initAccessibility } from "./controllers/accessibility";
import { InactivityCountdownTimer } from 'inactivity-countdown-timer/dist/main';
import { createIcons, Save, Eye, Upload, Trash2, Info, CheckCircle2, AlertCircle, XCircle, X, Pencil, History, Contact, Users, Box, LayoutGrid, Undo2, Filter, Files, Download, HelpCircle, Slash, Cookie } from 'lucide';


const $body = document.body;
const instance = new Loadeer();
const btn_btt = document.getElementById("btt");
const btt_revert_time = 2500;
const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector("ul.navbar-nav");
//const h = document.documentElement;


/* const aniTime = 400;
const aniDelay = 250; */

/**
 * Inits the main index
 */
function init() {
    console.log("init");

    // tab accessibility for menu items
    initAccessibility();

    // cookies
    $body.addEventListener('cookies:saved', handleCookies);

    // dynamic init that can be triggered after some other events (resets, etc.)
    dynamicInit();

    // navi
    if (hamburger) {
        hamburger.addEventListener("click", handleMobileMenu);
    }

    //btt    
    btn_btt.addEventListener('click', topFunction);
    window.onscroll = function () { scrollFunction() };
    scrollStop(revert_btt, btt_revert_time);

    // init alert animations, if alerts exists
    initAlerts();
}

// timer for logout
const cross_data = document.querySelector('#cross-data');
const LOGOUT_TIME = cross_data.getAttribute('timeout-time') ? cross_data.getAttribute('timeout-time') : 10;

/**
 * Inits the InactivityCountdownTimer to hanlde the auto logout
 * when a user is logged in
 */
function initLogoutTimer() {

    let updateElement = document.getElementById('logout__counter');

    function countDownCallback(timeRemaining) {
        if (updateElement) {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            function padTo2Digits(num) {
                return num.toString().padStart(2, '0');
            }

            updateElement.innerHTML = `(${padTo2Digits(minutes)}:${padTo2Digits(seconds)})`;
        }
    }

    let settings = {
        idleTimeoutTime: LOGOUT_TIME * 60 * 1000 + 1, // needs to be bigger than start
        startCountDownTimerAt: LOGOUT_TIME * 60 * 1000,
        timeoutCallback: timeoutCallback,
        countDownCallback: countDownCallback,
    };

    new InactivityCountdownTimer(settings).start();
}

/**
 * Handles the timeout of the auto logout by saving an active form,
 * if it was changed and then logging the user out
 */
function timeoutCallback() {
    let unsavedform = document.querySelector('form[form_changed=true]');

    if (unsavedform) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "logout-after-save";
        input.id = "logout-after-save";
        input.value = true;
        unsavedform.appendChild(input);

        sendAjaxForm(unsavedform.id);
    }
    else {
        window.location.href = location.protocol + '//' + location.host + location.pathname + '/?logout=true';
    }
}


/**
 * Dynamic Init that gets called after specific events, for example
 * after more items were loaded in the loadmore
 */
function dynamicInit() {
    // lay-loading
    instance.observe();

    // setup vlite videos
    setupVideos();

    //init lucide icons
    initIcons();

    // dynamic embeds
    initEmbedLogic();
}


/**
 * Handles a cookie settings change by reloading the browser to load
 * or unload desired/undesired parts. Please refer to kirby plugin cookie-banner
 * @param {Event} e 
 */
function handleCookies(e) {
    var newcookies = e.detail.join();
    var oldcookies = decodeURIComponent(document.cookie).split('cookie_status=')[1];

    if (newcookies != oldcookies) {
        location.reload(true);
    }
}

/**
 * Inits the Lucide library icons
 */
function initIcons() {
    createIcons({
        icons: {
            Save,
            Eye,
            Upload,
            Trash2,
            Info,
            CheckCircle2,
            AlertCircle,
            X,
            XCircle,
            Pencil,
            History,
            Contact,
            Users,
            Box,
            LayoutGrid,
            Undo2,
            Filter,
            Files,
            Download,
            HelpCircle,
            Slash,
            Cookie,
        },
    });
}




/**
 * Handles mobile menu for opening and closing
 */
function handleMobileMenu() {
    console.log("clicking");
    hamburger.classList.toggle("active");
    navMenu.classList.toggle("active");
    $body.classList.toggle("nav-open");
}

/**
 * Handles back-to-top button hidding and showing when page scrolls
 */
function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        btn_btt.classList.add("showing");
    } else {
        btn_btt.classList.remove("showing");
    }
}

/**
 * Handles back to top function
 */
function topFunction() {
    document.querySelector('body').scrollIntoView({ behavior: 'smooth' });
}

/**
 * Hides back to top button
 */
function revert_btt() {
    btn_btt.classList.remove("showing");
}

/**
 * Hanldes stop after user was scrolling so button can stay delayed on screen
 * @param {Function} callback 
 * @param {number} refresh 
 * @returns 
 */
function scrollStop(callback, refresh = 66) {
    // Make sure a valid callback was provided
    if (!callback || typeof callback !== 'function') return;

    let isScrolling;
    window.addEventListener('scroll', function (event) {
        window.clearTimeout(isScrolling);
        isScrolling = setTimeout(callback, refresh);
    }, false);
}

/**
 * Init alert elements, if any exists. It also checks if
 * alerts might exist in sessionStorage, for instance after
 * ajax + window.reload call
 */
function initAlerts() {
    let alerts = document.querySelectorAll('.alert.auto');
    alerts.forEach(alert => {
        alert = new Alert(alert);
    });

    let alertMsgs = sessionStorage.getItem('alert');
    if (alertMsgs && alertMsgs != '') {
        let alert = new Alert(null, 3, alertMsgs.split(','));
        sessionStorage.removeItem('alert');
    }
}





/**
* Checks, finds and returns parent container that has the targeted class
* @param {HTMLElement} startingNode 
* @param {string} targetClass 
* @returns {HTMLElement}
*/
function findParentContainer(startingNode, targetClass) {
    let node = startingNode;

    while (!node.classList.contains(targetClass)) {
        node = node.parentNode;
    }

    return node;
}


// make functions available everywhere
Object.assign(window, { init, dynamicInit, initIcons, initLogoutTimer, findParentContainer, Tab, Modal, Tooltip });

init();


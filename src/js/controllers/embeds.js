
/**
 * Inits embed logic for handling embeds after loading.
 * It checks if the original DOMelement has something
 * else inserted, like a blockquote is replaced by an iframe.
 * For some services it scrapes the new images and sets them dynamically
 * via javascript
 */

let baseembedurl = `${window.location.href.split('?')[0]}`;
var lastChar = baseembedurl.substr(-1); // Selects the last character
if (lastChar == '/')         // If the last character is a slash
    baseembedurl = baseembedurl + 'home.json'; // we are in home and need to call the home json so its not /.json but home.json
else
    baseembedurl = baseembedurl + '.json'; // otherwise call normally the controller for every other template
export function initEmbedLogic() {
    // TWITTER
    let tweetsLoading = document.querySelectorAll('.twitter-container'); // until now only twitter
    tweetsLoading.forEach(element => {
        element.addEventListener('DOMNodeInserted', tweetLoaded);
    });


    // SERVICES THAT HAVE SHORT LIVED IMG URLs (tiktok, instagram)
    let dynamicEmbedImgs = document.querySelectorAll('.load-embed-img');
    dynamicEmbedImgs.forEach(element => {
        let imgUrl = element.getAttribute('href');
        let sendembedurl = `${baseembedurl}?embedurl=${imgUrl}`;
        scrapFreshEmbedImage(sendembedurl).then(result => {
            element.classList.remove('load-embed-img');
            element.classList.add('loaded-embed-img');
            element.innerHTML = result.imgTag;

            if (result.imgHeight > result.imgWidth) // we only want to scale the portrait imgs
                element.parentNode.style.cssText = "--embed-w:" + result.imgWidth + "; --embed-h:" + result.imgHeight + "";
        });
    });
}


/**
 * Handles the element after a change was made in the DOM.
 * It checks for new iframes and then disables accessibility
 * properties. Then it waits for load and checks the dimensions
 * to set a CSS scaling variable to scale down the tweet depending
 * on the height. After that the ResizeObserver disconnects
 * @param {Event} ev 
 */
function tweetLoaded(ev) {
    let target = ev.target;
    if (target.tag == "IFRAME") {
        console.log('isiframe');
    }
    else {
        target = target.querySelector('iframe');
    }
    target.setAttribute('tabindex', '-1');
    target.setAttribute('aria-hidden', 'true');

    target.addEventListener('load', function () {
        new ResizeObserver(function (entries) {
            entries.forEach(entry => {

                var _w = entry.target.getBoundingClientRect().width;
                var _h = entry.target.getBoundingClientRect().height;
                if (_w > 0 && _h > 0) {
                    this.disconnect();
                    w = _w;
                    h = _h;
                    let container = findParentContainer(entry.target.parentNode, 'twitter-container');
                    container.style.cssText = '--scaleFactor: ' + w / h;
                    container.classList.add('loaded');

                    let parentContainer = container.parentNode;
                    let factor = getComputedStyle(container).getPropertyValue('--scale');
                    console.log(factor);
                    parentContainer.style.width = factor * w * (w / h) + 'px';
                    parentContainer.style.height = factor * h * (w / h) + 'px';

                    let spinner = container.querySelector('.spinner-border');
                    if (spinner)
                        container.removeChild(spinner);

                }
            });
        }).observe(this);
    });
}
var w, h;

/**
 * Resizes the tweet so it fits on a podest
 * @param {HTMLElement} target 
 * @param {number} factor 
 */
function resizeTwitter(target, factor) {
    if (w > 0 && h > 0) {
        let container = findParentContainer(target.parentNode, 'twitter-container');
        container.classList.add('loaded');

        let parentContainer = container.parentNode;
        parentContainer.style.width = factor * w * (w / h) + 'px';
        parentContainer.style.height = factor * h * (w / h) + 'px';
        parentContainer.classList.add('loaded');

        let spinner = container.querySelector('.spinner-border');
        if (spinner)
            container.removeChild(spinner);
    }
}

// JS media queries
const mediaQueryMin = window.matchMedia('(min-width: 1024px)');
const mediaQueryMax = window.matchMedia('(max-width: 1024px)');

/**
 * JS Mediaquery to resize and crop the scaled twitter embed
 * @param {Event} e 
 */
function handleTabletChangeMin(e) {
    if (e.matches) {
        let targets = document.querySelectorAll('.twitter-container iframe');
        targets.forEach(target => {
            resizeTwitter(target, 1);
        });
    }
}

/**
 * JS Mediaquery to resize and crop the scaled twitter embed
 * @param {Event} e 
 */
function handleTabletChangeMax(e) {
    if (e.matches) {
        let targets = document.querySelectorAll('.twitter-container iframe');
        targets.forEach(target => {
            resizeTwitter(target, 0.66);
        });
    }
}

mediaQueryMin.addListener(handleTabletChangeMin);
mediaQueryMax.addListener(handleTabletChangeMax);
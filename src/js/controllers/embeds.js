
/**
 * Inits embed logic for handling embeds after loading.
 * It checks if the original DOMelement has something
 * else inserted, like a blockquote is replaced by an iframe
 */
export function initEmbedLogic() {
    let tweetsLoading = document.querySelectorAll('.twitter-container'); // until now only twitter
    tweetsLoading.forEach(element => {
        element.addEventListener('DOMNodeInserted', tweetLoaded);
    });
}

/**
 * Handles the element after a change was made in the DOM.
 * It checks for new iframes and then disables accessibility
 * properties. Then it waits for load and checks the dimensions
 * to set a CSS scaling variable to scale down the tweet depending
 * on the height.
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
                var w = entry.target.getBoundingClientRect().width;
                var h = entry.target.getBoundingClientRect().height;
                if (w > 0 && h > 0) {
                    let container = findParentContainer(entry.target.parentNode, 'twitter-container');
                    container.style.cssText = '--scaleFactor: ' + w/h;
                    container.classList.add('loaded');
                }
            });
        }).observe(this);
    });

}
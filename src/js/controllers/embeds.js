
/**
 * Inits embed logic for handling embeds after loading.
 * It checks if the original DOMelement has something
 * else inserted, like a blockquote is replaced by an iframe.
 * For tiktok it scrapes the new images and sets them dynamically
 * via javascript
 */
/* let newTiktokCounter = 0;
let dynamicEmbedImgs = []; */
const baseembedurl = `${window.location.href.split('?')[0]}.json`;
console.log(baseembedurl);
//const baseembedurl = location.protocol + '//' + location.host + location.pathname + '.json';
export function initEmbedLogic() {
    // TWITTER
    let tweetsLoading = document.querySelectorAll('.twitter-container'); // until now only twitter
    tweetsLoading.forEach(element => {
        element.addEventListener('DOMNodeInserted', tweetLoaded);
    });

    // TIKTOK    
    let dynamicEmbedImgs = document.querySelectorAll('.load-embed-img');


    /* if (dynamicEmbedImgs.length > 0)
        loadTiktokAtATime(dynamicEmbedImgs); */

    dynamicEmbedImgs.forEach(element => {
        let imgUrl = element.getAttribute('href');
        let sendembedurl = `${baseembedurl}?embedurl=${imgUrl}`;
        scrapFreshEmbedImage(sendembedurl).then(imgTag => {
            element.classList.remove('load-embed-img');
            element.classList.add('loaded-embed-img');
            element.innerHTML = imgTag;
        });
    });
}

/*  function loadTiktokAtATime(dynamicEmbedImgs) {
    let imgUrl = dynamicEmbedImgs[newTiktokCounter].getAttribute('href');
    let sendembedurl = `${baseembedurl}?embedurl=${imgUrl}`;
    console.log(dynamicEmbedImgs[newTiktokCounter]);
    console.log(imgUrl);
     scrapFreshEmbedImage(sendembedurl).then(imgTag => {
        dynamicEmbedImgs[newTiktokCounter].classList.remove('load-embed-img');
        dynamicEmbedImgs[newTiktokCounter].classList.add('loaded-embed-img');
        dynamicEmbedImgs[newTiktokCounter].innerHTML = imgTag;
        newTiktokCounter++;
        if(newTiktokCounter < dynamicEmbedImgs.length){
            loadTiktokAtATime(dynamicEmbedImgs);
        }
        else{
            newTiktokCounter = 0;
            dynamicEmbedImgs = [];
        }        
    });
} */


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
                    container.style.cssText = '--scaleFactor: ' + w / h;
                    container.classList.add('loaded');

                    let spinner = container.querySelector('.spinner-border');
                    if (spinner)
                        container.removeChild(spinner);

                }
            });
        }).observe(this);
    });

}
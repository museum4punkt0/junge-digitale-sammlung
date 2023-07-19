import PhotoSwipeLightbox from '../../../node_modules/photoswipe/dist/photoswipe-lightbox.esm.js';
import PhotoSwipe from '../../../node_modules/photoswipe/dist/photoswipe.esm';

let model;
let model_progress;
let largeImg;

/**
 * Inits the exhibit page depending on existing data
 */
function init() {
    model = document.querySelector('model-viewer');
    largeImg = document.querySelector('#gallery');

    if (model)
        init3DLogic(); 

    if (largeImg)
        initLargeImage();
}

/**
 * Inits logic for handling loading of 3D model 
 */
function init3DLogic() {
    model = document.querySelector('model-viewer');
    model_progress = model.querySelector('.viewer-progress');
    model.addEventListener('load', modelLoaded);
    model.addEventListener('progress', updateProgress);
}

/**
 * Updates the loading progress of the 3D model
 * @param {Event} ev 
 */
function updateProgress(ev) {
    model_progress.style.width = ev.detail.totalProgress * 100 + '%';
}

/**
 * Starts and removes relevant listeners after model loaded
 * @param {Event} ev 
 */
function modelLoaded(ev) {
    model_progress.style.width = 100 + '%';
    model_progress.classList.add('loaded');

    model.removeEventListener('load', modelLoaded);
    model.removeEventListener('progress', updateProgress);
    model.addEventListener('camera-change', modelInteracted);
}

/**
 * After first interaction with 3D model, it deactivates the
 * auto-rotation of the model
 * @param {Event} ev 
 */
function modelInteracted(ev) {
    model.removeEventListener('camera-change', modelInteracted);
    model.autoRotate = false;
}

/**
 * Inits large image as a PhotoSwipeLightbox so the
 * user can scale the image
 */
function initLargeImage() {
    const lightbox = new PhotoSwipeLightbox({
        // may select multiple "galleries"
        gallery: '#gallery',

        // Elements within gallery (slides)
        children: 'a',
        pswpModule: PhotoSwipe,
    });
    lightbox.init();
}

init();

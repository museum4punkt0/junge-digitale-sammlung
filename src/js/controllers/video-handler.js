import VLite from '../components/VLite';

var vliteMap = new Map();

/**
 * Manage all videos in the page
 */

export function setupVideos() {

    var vids = document.querySelectorAll('.vlite-js');
    vids.forEach(element => {
        vliteMap.set(element.id, new VLite(element));
    });
}

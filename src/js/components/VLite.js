import Vlitejs from 'vlitejs';
import VlitejsYoutube from 'vlitejs/dist/providers/youtube';
import VlitejsVimeo from 'vlitejs/dist/providers/vimeo';

Vlitejs.registerProvider('youtube', VlitejsYoutube);
Vlitejs.registerProvider('vimeo', VlitejsVimeo);

/**
 * VLite video player class
 */

export default class VLite {

    constructor(dom_element) {
        this.container = dom_element;
        this.player;
        this.setupPlayer();
    }

    setupPlayer() {

        if (!this.container)
            return;

        let provider = this.container.getAttribute('provider');
        let settings;
        // youtube or vimeo
        if (provider != null) {
            settings = {
                provider: provider,
                options: {
                    controls: true,
                    autoplay: false,
                    playPause: true,
                    progressBar: true,
                    time: true,
                    volume: true,
                    fullscreen: false,
                    bigPlay: true,
                    playsinline: true,
                    loop: false,
                    muted: false,
                    autoHide: true,
                    providerParams: {
                        'controls': 0,
                        'autoplay': 0
                    },
                },
                onReady: function (player) {
                }
            };
        }
        // regular mp4
        else {
            settings = {
                options: {
                    controls: true,
                    autoplay: false,
                    playPause: true,
                    progressBar: true,
                    time: true,
                    volume: true,
                    fullscreen: false,
                    bigPlay: true,
                    playsinline: false,
                    loop: false,
                    muted: false,
                    autoHide: true,
                },
                onReady: function (player) {
                }
            };
        }

        this.player = new Vlitejs(this.container, settings);
    }

    play() {
        this.player.player.pause();
    }

    pause() {
        this.player.player.pause();
    }

    async reset() {
        // bug in youtube if the video was not playing already
        let time = await this.player.player.getCurrentTime();
        
        if(time > 0){
            this.player.player.pause();
            this.player.player.seekTo(0);
        } 
    }
}


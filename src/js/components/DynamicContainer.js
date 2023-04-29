/**
 * DynamicContainer that overflows when size is bigger than parent
 * (might not be used at the end - discussing with designer if
 * text might have a max value for admins so it fits every screen)
 */

export default class DynamicContainer {

    constructor(nativeElement, parent = null) {
        this.resizeW = false;
        this.resizeH = false;
        this.maxWidthPerc = 1;
        this.maxHeightPerc = 0.54; // 55 in CSS

        this.nativeElement = nativeElement;

        if (!parent)
            this.parent = this.nativeElement.parentNode;
        else
            this.parent = parent;

        this.init();
    }

    init() {
        window.addEventListener("resize", (event) => {
            this.checkDimensions();
            this.checkOverflow();
            this.applyResize();
        });

        this.checkDimensions();
        this.applyResize();
    }

    checkDimensions() {
        if (this.nativeElement.offsetWidth > this.parent.offsetWidth * this.maxWidthPerc) {
            console.log('width bigger');
            this.resizeW = true;
        }
        else {
            this.resizeW = false;
        }

        if (this.nativeElement.offsetHeight > this.parent.offsetHeight * this.maxHeightPerc) {
            console.log('height bigger');
            this.resizeH = true;
        }
        else {
            this.resizeH = false;
        }
    }

    checkOverflow() {

        const eleRect = this.nativeElement.getBoundingClientRect();
        const targetRect = this.parent.getBoundingClientRect();

        if (eleRect.right > targetRect.right) {
            console.log('outside right');
            this.resizeW = true;
        }
        else if (eleRect.left < targetRect.left) {
            console.log('outside left');
            this.resizeW = true;
        }
        else {
            this.resizeW = false;
        }

        if (eleRect.bottom > targetRect.bottom) {
            console.log('outside bot');
            this.resizeH = true;
        }
        else if (eleRect.top < targetRect.top) {
            console.log('outside top');
            this.resizeH = true;
        }
        else {
            this.resizeH = false;
        }
    }

    applyResize() {
        if (this.resizeH || this.resizeW) {
            //csstyle += "overflow-y: scroll;";
            this.nativeElement.classList.add('container-overflown');
            this.nativeElement.classList.add('ignoreWheel');
        }
        else {
            //csstyle += "overflow-y: auto;";
            this.nativeElement.classList.remove('container-overflown');
            this.nativeElement.classList.remove('ignoreWheel');
        }      
    }

}



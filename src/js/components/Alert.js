/**
 * Alert class for creating dynamic feedbacks via js (static feedbacks are created in PHP after saving pages)
 */
export default class Alert {

    constructor(nativeElement, delay = 6, text = null) {

        this.alertsDelay = delay; // in seconds
        this.alertsAnimation = 0.3; // in seconds
        this.alertsTimeout;
        this.alertsTimeoutDestroy;
        this.text = text;

        this.nativeElement = nativeElement;
        this.init();
    }

    init() {
        if (this.text)
            this.create();

        // prepare transition IN
        this.nativeElement.classList.add('showing');
        this.nativeElement.style.transitionDuration = this.alertsAnimation + "s";

        // prepare transition OUT when click
        this.nativeElement.addEventListener('click', function (ev) {
            this.destroy();
        }.bind(this));

        // prepare transition OUT
        if (this.alertsTimeout)
            clearTimeout(this.alertsTimeout);

        this.alertsTimeout = setTimeout(() => {
            this.destroy();
        }, this.alertsDelay * 1000);
    }

    create() {
        let container = document.querySelector('#wrapper .container');

        let alertHTML = document.createElement("div");
        alertHTML.classList.add("alert", 'position-fixed', 'auto');

        if(this.text.length > 1){
            let ulEl = document.createElement("ul");            
            this.text.forEach(txt => {       
                let pEl =  document.createElement("p");        
                let liEl = document.createElement("li");
                let textContent = document.createTextNode(txt);
                pEl.appendChild(textContent);
                liEl.appendChild(pEl);
                ulEl.appendChild(liEl);
            });
            alertHTML.appendChild(ulEl);
        }
        else{
            let textContent = document.createTextNode(this.text);        
            alertHTML.appendChild(textContent);
        }
        
        container.appendChild(alertHTML);

        this.nativeElement = alertHTML;
    }

    destroy() {
        this.nativeElement.classList.remove('showing');

        if (this.alertsTimeoutDestroy)
            clearTimeout(this.alertsTimeoutDestroy);

        this.alertsTimeoutDestroy = setTimeout(() => {
            this.nativeElement.remove();
        }, this.alertsAnimation * 1000);
    }
}




/**
 * Create accessibility for dropdown items in main
 * navigation so they can be navigated via tab (keyboard)
 */
export function initAccessibility() {
    var menuItems = document.querySelectorAll('.nav-item.dropdown');
    Array.prototype.forEach.call(menuItems, function (el, i) {
        el.querySelector('a').addEventListener("click", function (event) {
            if (event.screenX == 0 && event.screenY == 0) {
                if (!this.parentNode.classList.contains("open")) {
                    this.parentNode.classList.add("open");
                    this.setAttribute('aria-expanded', "true");
                    event.preventDefault();
                } else {
                    this.parentNode.classList.remove("open");
                    this.setAttribute('aria-expanded', "false");
                }
            }

            return false;
        });
    });
}
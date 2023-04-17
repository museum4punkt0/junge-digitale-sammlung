import Alert from "./Alert";

/**
 * Drag n Drop Input Element for uploads
 */
export default class DragnDrop {

    constructor(nativeElement) {

        this.MSG_DRAG = 'Datei rein ziehen';
        this.MSG_DROP = 'Los lassen';
        this.MSG_WARNING = 'Maximal 1 Datei erlaubt';
        this.MSG_ERROR = 'Dateityp nicht erlaubt: .';
        this.MSG_ALLOWED_TYPES = "Erlaubte Dateien: ";

        this.nativeElement = nativeElement;

        this.dragArea = this.nativeElement.querySelector(".drag-area");
        this.dropArea = this.nativeElement.querySelector(".drop-area");
        this.mediaContainer = this.dragArea.querySelector(".media-container");
        this.dragText = this.dragArea.querySelector("header");
        this.input = this.dragArea.querySelector("input");
        this.submit = this.nativeElement.querySelector('button[type="submit"]');
        this.extensions = this.input.getAttribute('accept');
        this.fileType = this.nativeElement.id;
        this.file;

        this.init();
    }

    init() {
        this.submit.disabled = true;      
        this.submit.addEventListener("click", function (e) {
            e.preventDefault();

            fileUpload(e);
        }.bind(this));

        this.input.addEventListener("change", function (e) {
            //getting user select file and [0] this means if user select multiple files then we'll select only the first one
            this.file = e.target.files[0];
            this.dropArea.classList.add("active");
            this.showFile(); //calling function
        }.bind(this));

        //If user Drag File Over dragArea
        this.dragArea.addEventListener("dragover", function (event) {
            event.preventDefault(); //preventing from default behaviour
            this.dropArea.classList.add("active");
            this.dragText.textContent = this.MSG_DROP;
        }.bind(this));

        //If user leave dragged File from dragArea
        this.dragArea.addEventListener("dragleave", function (event) {
            this.dropArea.classList.remove("active");
            this.dragText.textContent = this.MSG_DRAG;
        }.bind(this));

        //If user drop File on dragArea
        this.dragArea.addEventListener("drop", function (event) {
            event.preventDefault(); //preventing from default behaviour
            this.dragText.textContent = this.MSG_DRAG;
            this.dropArea.classList.remove("active");

            if (event.dataTransfer.files.length > 1) {
                let alert = new Alert(null, 3, [this.MSG_WARNING]);
            } else {
                this.file = event.dataTransfer.files[0];
                this.input.files = event.dataTransfer.files;
                this.showFile(); //calling function                
            }

        }.bind(this));
    }

    showFile() {
        let fileType = this.file.name.split('.').pop().toLowerCase(); //getting selected file extension
        let validExtensions = this.extensions.replaceAll(' ', '').replaceAll('.', '').split(','); //adding valid image extensions in array from 'accept' string

        if (validExtensions.includes(fileType)) { //if user selected file has valid type

            this.submit.disabled = false;

            let fileReader = new FileReader(); //creating new FileReader object
            fileReader.onload = () => {
                let fileURL = fileReader.result; //passing user file source in fileURL variable
                let tag;
                
                if (this.fileType == 'exhibit_preview')
                    //creating an img tag and passing user selected file source inside src attribute
                    tag = `<img src="${fileURL}" alt="image">`;
                else if (this.fileType == 'threed_model')
                    //creating a model tag and passing user selected file source inside src attribute
                    tag = `<model-viewer class="exhibit-3d" id="reveal" loading="eager" camera-controls touch-action="pan-y" interaction-prompt="none" auto-rotate src="${fileURL}" shadow-intensity="1" style="background-color: unset;"></model-viewer>`;
                else if (this.fileType == 'digital_asset') {
                    // creating video tag
                    tag = `<video crossorigin class="vlite-js modal__video" src=""></video>`;
                }

                this.submit.click();
            }
            fileReader.readAsDataURL(this.file);
        } else {
            let warningFileExt = new Alert(null, 10, [this.MSG_ERROR + fileType, this.MSG_ALLOWED_TYPES + this.extensions]);
            this.dropArea.classList.remove("active");
            this.dragText.textContent = this.MSG_DRAG;
        }
    }
}






/**
 * A class to create then destroy a modal.
 * @param configs - an object to override default modal properties
 * @param callback - optional callback function
 *
 * If no callback is specific, only a confirm button will be displayed. Otherwise, both confirm and cancel are display.
 * Elements of the array in the params property will be passed back to the callback, in the oder they're given.
 * The inputNames property is an array of the names of inputs included in contentHtml; values entered will be added, in order listed in inputNames, to the params property before all are passed to callback.
 */
export class CentaModal {
    constructor(configs = {},callback = false) {
        //Set defaults for configs, override any that are passed in configs param
        Object.assign(this, {
            titleText: 'Modal title',
            contentHtml: '<p>Modal content.</p>',
            params: [],
            inputNames: [],
            cancelText: 'Cancel',
            confirmText: 'OK',
        }, configs);
        //Strip HTML from titleText
        this.titleText = $('<p>').html(this.titleText).text();

        //Set additional properties
        this.callback = callback;

        //Define modal template
        this.template = `<div style="display:none">
    <div class="modal-background">
      <div class="modal-container">
        <div class="modal-title">
          <div class="modal-closer">
            <i class="fas fa-times"></i>
          </div>
          <div class="modal-title-text">${this.titleText}</div>
        </div>
        <div class="modal-content">
          ${this.contentHtml}
        </div>
        <div class="centum">
          <div class="cell btn-wrap align-center">
            <div class="btn modal-confirm">${this.confirmText}</div>`;
        if(this.callback) {
            this.template += `
            <div class="btn modal-cancel">${this.cancelText}</div>`;
        }
      this.template += `
            </div>
        </div>
      </div>
    </div>
</div>`;
    }

    render() {
        const modalTemplate = $(this.template);

        //Stop clicks on main container from closing modal
        modalTemplate.find('.modal-container').on('click',e => {
            e.stopPropagation();
        });

        //Make clicks on closer elements close modal
        modalTemplate.find('.modal-background,.modal-cancel,.modal-closer,.modal-confirm').on('click',() => {
            modalTemplate.fadeOut(400,() => modalTemplate.remove());
        });

        //Exec callback on confirm, if there's a callback
        if(this.callback) {
            const confirmButton = modalTemplate.find('.modal-confirm');
            confirmButton.on('click',() => {
                this.inputNames.forEach(name => {
                    const matchingInput = $('input[name="' + name + '"]');
                    if(matchingInput.length) {
                        this.params.push(matchingInput.val());
                    }
                    const matchingTextarea = $('textarea[name="' + name + '"]');
                    if(matchingTextarea.length) {
                        this.params.push(matchingTextarea.val());
                    }
                });
                this.callback(...this.params);
            });

            modalTemplate.find('input').on('keydown',e => {
                if(13 === e.keyCode) {
                    confirmButton.click();
                }
            });
        }

        //Add modal to body element
        $('body').append(modalTemplate);
        modalTemplate.fadeIn({
            duration: 400,
            complete: () => { //Focus on first input element
                modalTemplate.find('input,textarea').first().focus();
            }
        });
    }
}

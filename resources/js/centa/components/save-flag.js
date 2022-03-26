/**
 * Show save flag if it doesn't already exist
 * @param form
 */
export const maybeShowSaveFlag = form => {
  const existingFlag = $('#saveFlag');
  if(!existingFlag.length && form.hasClass('flagSaver')) {
      showSaveFlag(form);
      enableCtrlS(form);
      form.on('submit',() => {
          showSaveInProgress();
      });
  }
};

/**
 * Show save flag when form item's value changes
 */
export const pickUpFlagSaverChanges = () => {
    $('.flagSaver').each((index,el) => {
        const form = $(el);
        form.find('input,textarea').each((childIndex, childEl) => {
            const formItem = $(childEl);
            formItem.on('keydown', () => {
                formItem.oldVal = formItem.val().trim();
            }).on('keyup', () => {
                const newVal = formItem.val().trim();
                if(newVal !== formItem.oldVal) {
                    maybeShowSaveFlag(form);
                }
            });
        });
        form.find('input[type="checkbox"],select').each((childIndex, childEl) => {
            $(childEl).on('change', () => {
                maybeShowSaveFlag(form);
            });
        });
    });
};

/**
 * Make Ctrl-S (or Cmd-S) submit the form
 * @param form
 */
const enableCtrlS = form => {
    //Enable CTRL-S for form submit
    $(document).on('keydown',function(e) {
        if ((e.metaKey || e.ctrlKey) && ('s' === String.fromCharCode(e.which).toLowerCase())) {
            e.preventDefault();
            form.submit();
        }
    });
};

/**
 * Create and display HTML elements for save flag
 * @param form
 */
const showSaveFlag = form => {
    const flagEl = $('<div>');
    flagEl.html(`<div class="centum" id="saveFlag">
    <div class="cell">
        <b>Unsaved changes</b><br>Click or Ctrl-S to save
    </div>
</div>`);
    flagEl.on('click',() => {
        form.submit();
    });
    $('main').prepend(flagEl);
};

/**
 * Change save flag to loader as form submits
 */
const showSaveInProgress = () => {
    $('#saveFlag').html(`<div class="cell"><span class="loader"></span></div>`);
};

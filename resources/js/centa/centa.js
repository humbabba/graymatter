//Define the callback for changes in text-editor
//Set to false for no callback
export const textEditorOnChangeCallback = () => {
    const modalMaster = $('.modal-master');
    if(!modalMaster.length || 'none' === modalMaster.css('display')) { //We don't want this running when the modal is visible
        console.log('showUnsavedFlag(documentForm)');
    }
};

//Included components
require('./components/alerts');
require('./components/modal');
require('./components/nav');
require('./components/shift');
require('./components/sorters');
require('./components/text-editor');

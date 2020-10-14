//Define path to modal configs
window.modalConfigsPath = '/centa/modal.json';

//Define the callback for changes in text-editor
//Set to false for no callback
window.textEditorOnChangeCallback = () => {
    if('none' === $('.modal-background').css('display')) { //We don't want this running when the modal is visible
        console.log('showUnsavedFlag(documentForm)');
    }
};

//Define the callback for insertImage command in text-editor. Callback should return image URL.
//Set to false for no callback
window.textEditorInsertImageCallback = () => console.log('Inserting!');

//Included components
require('./components/alerts');
require('./components/modal');
require('./components/nav');
require('./components/shift');
require('./components/sorters');
require('./components/text-editor');

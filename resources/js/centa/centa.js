//Import functions

//Define path to modal configs
export const modalConfigsPath = '/centa/modal.json';

//Define the callback for changes in text-editor
//Set to false for no callback
export const textEditorOnChangeCallback = () => {
    if('none' === $('.modal-background').css('display')) { //We don't want this running when the modal is visible
        console.log('showUnsavedFlag(documentForm)');
    }
};

//Define the callback for insertImage command in text-editor. Callback should return image URL.
window.textEditorInsertImageCallback = () => console.log('Inserting!');

//Included components
require('./components/alerts');
require('./components/modal');
require('./components/nav');
require('./components/shift');
require('./components/sorters');
require('./components/text-editor');

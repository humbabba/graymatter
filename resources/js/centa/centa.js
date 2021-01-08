//Import functions
import { insertOpenAndCloseMarkers } from './components/text-editor.js';
import { renderModal } from './components/modal.js';

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

//Define the callback for createLink command in text-editor. Callback should return URL.

window.textEditorCreateLinkCallback = editArea => {
    let range = window.getSelection().getRangeAt(0);
    insertOpenAndCloseMarkers(range);
    console.log('Famous original MUTATED editArea.html()');
    console.log(editArea.html());
    const inputConfigs = {
        title: 'Link destination',
        content: '<p>Enter URL:</p><p><input type=\'text\' name=\'url\' /></p>',
        paramDisplay: [],
        paramInput: ['url'],
        cancelText: 'Cancel',
        confirmText: 'Go',
        confirmFunction: 'execCreateLinkModal'
    };
    const params = [editArea,editArea.html()];
    renderModal(inputConfigs,params);
};

//Included components
require('./components/alerts');
require('./components/modal');
require('./components/nav');
require('./components/shift');
require('./components/sorters');
require('./components/text-editor');

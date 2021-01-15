/**
 * Imports
 */
import {insertLinkViaUi, insertImageViaUi, insertTextColorViaUi, insertOpenAndCloseMarkers} from "./components/text-editor";
import {CentaModal} from "./components/modal";

/**
 * Define the callback for changes in text-editor.
 * Set to false for no callback.
 */
export const textEditorOnChangeCallback = () => {
    const modalMaster = $('.modal-master');
    if(!modalMaster.length || 'none' === modalMaster.css('display')) { //We don't want this running when the modal is visible
        console.log('showUnsavedFlag(documentForm)');
    }
};

/**
 * Creates UI for link insertion.
 * Must call back insertLinkViaUi in text-editor.js with params editArea, editAreaHtml, and url.
 * Param editAreaHtml may be set to false if no manipulation is done and editArea focus is not changed - otherwise it's necessary to restore selection in insertLinkViaUi.
 * @param editArea
 */
export const renderInsertLinkUi = editArea => {
    const range = window.getSelection().getRangeAt(0);
    //Bail if range collapsed
    if(range.collapsed) {
        return;
    }
    insertOpenAndCloseMarkers(range);
    const modalConfigs = {
        titleText: 'Link destination',
        contentHtml: '<p>Enter URL:</p><p><input type="text" name="url" /></p>',
        params: [editArea,editArea.html()],
        inputNames: ['url'],
        cancelText: 'Cancel',
        confirmText: 'Go'
    };
    const modal = new CentaModal(modalConfigs,insertLinkViaUi);
    modal.render();
};
/**
 * Browser-prompt version of the above, by way of example of an alternate UI for link insertion.
 * @param editArea
 */
// export const renderInsertLinkUi = editArea => {
//     const url = prompt('Enter URL:');
//     if('' !== url) {
//         insertLinkViaUi(editArea,false,url);
//     }
// };

/**
 * Creates UI for image insertion.
 * Must call back insertImageViaUi in text-editor.js with params editArea, editAreaHtml, and url.
 * Param editAreaHtml may be set to false if no manipulation is done and editArea focus is not changed - otherwise it's necessary to restore selection in insertImageViaUi.
 * @param editArea
 */
export const renderInsertImageUi = editArea => {
    const range = window.getSelection().getRangeAt(0);

    insertOpenAndCloseMarkers(range);
    const modalConfigs = {
        titleText: 'Insert image',
        contentHtml: '<p>Enter a URL for your image, or upload one from your device:</p><p><input type="text" name="url" placeholder="Image URL"/></p><p><label><span class="btn">Upload image</span><input type="file" name="upload" data-send-url-to="url" style="display:none" /></label></p>',
        params: [editArea,editArea.html()],
        inputNames: ['url'],
        fileInputNames: ['upload'],
        cancelText: 'Cancel',
        confirmText: 'Go'
    };
    const modal = new CentaModal(modalConfigs,insertImageViaUi);
    modal.render();
};

/**
 * Browser-prompt version of the above, by way of example of an alternate UI for image insertion.
 * @param editArea
 */
// export const renderInsertImageUi = editArea => {
//     const url = prompt('Enter URL:');
//     if('' !== url) {
//         insertImageViaUi(editArea,false,url);
//     }
// };

/**
 * Creates UI for text color.
 * Must call back insertTextColorViaUi in text-editor.js with params editArea, editAreaHtml, and color.
 * Param editAreaHtml may be set to false if no manipulation is done and editArea focus is not changed - otherwise it's necessary to restore selection in insertTextColorViaUi.
 * @param editArea
 */
export const renderTextColorUi = editArea => {
    const range = window.getSelection().getRangeAt(0);
    //Bail if range collapsed
    if(range.collapsed) {
        return;
    }
    insertOpenAndCloseMarkers(range);
    const modalConfigs = {
        titleText: 'Change text color',
        contentHtml: '<p>Enter desired color in hex, rgb, or rgba format:</p><p><input type="text" name="color" value="#000000"/></p>',
        params: [editArea,editArea.html()],
        inputNames: ['color'],
        cancelText: 'Cancel',
        confirmText: 'Go'
    };
    const modal = new CentaModal(modalConfigs,insertTextColorViaUi);
    modal.render();
};

/**
 * Browser-prompt version of the above, by way of example of an alternate UI for text-color.
 * @param editArea
 */
// export const renderTextColorUi = editArea => {
//     const color = prompt('Enter color hex:','#000000');
//     if('' !== color) {
//         insertTextColorViaUi(editArea,false,color);
//     }
// };

/**
 * Included components
 */
require('./components/alerts');
require('./components/modal');
require('./components/nav');
require('./components/shift');
require('./components/sorters');
require('./components/text-editor');

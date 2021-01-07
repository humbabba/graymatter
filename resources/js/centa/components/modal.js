let modalBackground = $('.modal-background').first(); //Only get first in case multipls present. There shouldn't be.
let modalTriggers = $('[class*="modal+"]');
let modalConfigs = null; //This is overwritten in configureModal if valid modal configs are found
let modalContainer = modalBackground.find('.modal-container');
let modalTitleText = modalBackground.find('.modal-title-text');
let modalCloser = modalBackground.find('.modal-closer');
let modalContent = modalBackground.find('.modal-content');
let modalCancel = modalBackground.find('.modal-cancel');
let modalConfirm = modalBackground.find('.modal-confirm');

import { initTextEditors } from './text-editor.js';
import { modalConfigsPath } from '../centa.js';

$.ajaxSetup({ cache: false });

const checkForModalTriggers = () => {
  //Confirm elements are Centa modalBackground triggers, then process
  if(modalTriggers.length) {
    modalTriggers.each(function(index,element) {
      let el = $(element);
      var classes = el.prop('class').split(/\s+/);
      $(classes).each(function(index,elClass) {
          if(elClass.startsWith('modal+')) {
            let elModalClass = elClass.split('+');
            let modalFunctionDefinesArr = elModalClass.splice(1); //Get an array of everything after first +, assuming there will be more than one due to URL encoding in the view
            let modalFunctionDefines = modalFunctionDefinesArr.join('+'); //Rejoin items with URL encode +
            el.on('click',function(e) {
              e.preventDefault();
              console.log('modalFunctionDefines');
              console.log(modalFunctionDefines);
              configureModal(modalFunctionDefines);
            });
          }
        });
    });
  }
};

//Click handlers
const addModalClickHandlers = () => {
  modalContainer.on('click',function(e) {
    e.stopPropagation();
  });
  modalBackground.add(modalCloser).add(modalCancel).on('click',function() {
    hideModal();
  });
};

//Init
checkForModalTriggers();
addModalClickHandlers();

//Show or hide
const showModal = () => modalBackground.addClass('fade-in').css('display','');

export const hideModal = () => modalBackground.removeClass('fade-in').fadeOut(400,function() { modalCancel.css('display','inline-block'); }); //We reset modalCanel to inline-block display in case it was set to none by false cancelText in modal configs

//Configure this specific instance
const configureModal = (defines) => {
  let params = '';
  let configName = defines.replace(/\(.*\)/,'');

  params = defines.match(/\((.*)\)/)[1].split(',');

  //Remove URL encoding
  params.forEach(function(item,index) {
    this[index] = decodeURIComponent(item).replace('+',' ');
  },params);

  //Fetch the configs for the modal
  $.getJSON(modalConfigsPath,configName,function(data) {
    let remoteConfigs = data[configName];
    if('undefined' !== typeof remoteConfigs) { //Don't overwrite modalConfigs till we find good remoteConfigs
      modalConfigs = remoteConfigs;
    }
  })
  .always(function() {
    renderModal(modalConfigs,params);
  });
};

export const renderModal = (configs,params) => {
  if(configs) {
      console.log('configs');
      console.log(configs);
      console.log('params');
      console.log(params);
    //Make sure all configs are present
    let neededValues = ['title','content','paramDisplay','paramInput','confirmFunction','confirmText','cancelText'];
    for(let x of neededValues) {
      if('undefined' === typeof configs[x]) {
        console.log('Centa modal error: Requried value "' + x + '" missing from modal config.');
        return;
      }
    }

    //Set modal values
    modalTitleText.html(configs.title);
    modalContent.html(configs.content);
    modalConfirm.html(configs.confirmText);
    if(configs.cancelText) {
      modalCancel.html(configs.cancelText);
    } else {
      modalCancel.css('display','none'); //Hide cancel button; its display is reset to inline-block by hideModal
    }

    //Now let's see if any changes to the modal content to display parameters are called for
    let paramDisplay = configs.paramDisplay;
    if(paramDisplay.length && params.length) {
      paramDisplay.forEach(function(item,index) {
        $('.' + item).html(params[index]);
      });
    }

    //Check for special inputs in the modal content
    let paramInput = configs.paramInput;
    if(paramInput.length) {
      //Add each paramInput to an array
      let paramInputArray = [];
      paramInput.forEach(function(item,index) {
        paramInputArray.push(modalContent.find('*[name="' + item + '"]'));
      });
      //Add said array to params for passing to confirm function
      params.push(paramInputArray);
    }

    //Set click handler
    modalConfirm.on('click',function(e) {
      try {
          window[configs.confirmFunction].apply(null,params);
      } catch(e) {
          console.log('Centa modal confirmFunction error:\r\n' + e);
      }
    });

    //Finally, display it
    showModal();

    //Init text editors in case there's one in the modal
    initTextEditors();

  } else {
    console.log('Centa modal error:\r\nEither the modal configs where not found or the JSON is invalid.');
  }
};

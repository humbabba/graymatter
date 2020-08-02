let modalBackground = $('.modal-background').first(); //Only get first in case multipls present. There shouldn't be.
let modalTriggers = $('[class*="modal+"]');
let modalConfigs = null; //This is overwritten in configureModal if valid modal configs are found
let modalContainer = modalBackground.find('.modal-container');
let modalTitle = modalBackground.find('.modal-title');
let modalCloser = modalBackground.find('.modal-closer');
let modalContent = modalBackground.find('.modal-content');
let modalCancel = modalBackground.find('.modal-cancel');
let modalConfirm = modalBackground.find('.modal-confirm');

//Init
checkForModalTriggers();
addModalClickHandlers();

function checkForModalTriggers() {
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
            el.on('click',function() {
              console.log('modalFunctionDefines');
              console.log(modalFunctionDefines);
              configureModal(modalFunctionDefines);
            });
          }
        });
    });
  }
}

//Click handlers
function addModalClickHandlers() {
  modalContainer.on('click',function(e) {
    e.stopPropagation();
  });
  modalBackground.add(modalCloser).add(modalCancel).on('click',function() {
    hideModal();
  });
}

//Show or hide
showModal = () => modalBackground.addClass('fade-in').css('display','');

hideModal = () => modalBackground.removeClass('fade-in').fadeOut(400);

//Configure this specific instance
configureModal = (defines) => {
  console.log('defines');
  console.log(defines);
  let params = '';
  let parts = '';
  let configName = defines.replace(/\(.*\)/,'');
  console.log('configName');
  console.log(configName);

  params = defines.match(/\((.*)\)/)[1].split(',');

  //Remove URL encoding
  params.forEach(function(item,index) {
    this[index] = decodeURIComponent(item).replace('+',' ');
  },params);

  console.log('params');
  console.log(params);

  //Fetch the configs for the modal
  $.getJSON(window.modalConfigsPath,configName,function(data) {
    let remoteConfigs = data[configName];
    if('undefined' !== typeof remoteConfigs) { //Don't overwrite modalConfigs till we find good remoteConfigs
      modalConfigs = remoteConfigs;
    }
  })
  .always(function() {
    renderModal(modalConfigs,params);
  });
}

renderModal = (configs,params) => {
  if(configs) {

    //Make sure all configs are present
    let neededValues = ['title','content','paramDisplay','confirm','confirmText','cancel','cancelText'];
    for(let x of neededValues) {
      if('undefined' === typeof configs[x]) {
        console.log('Centa modal error: Requried value "' + x + '" missing from modal config.');
        return;
      }
    }

    //Set modal values
    modalTitle.html(configs.title);
    modalContent.html(configs.content);
    modalConfirm.html(configs.confirmText);
    modalCancel.html(configs.cancelText);

    //Now let's see if any changes to the modal content to display parameters are called for
    let paramDisplay = configs.paramDisplay;
    if(paramDisplay.length && params.length) {
      paramDisplay.forEach(function(item,index) {
        $('.' + item).html(params[index]);
      });
    }

    //Set click handlers
    modalConfirm.on('click',function() {
      window[configs.confirm](params);
    });
    modalCancel.on('click',function() {
      window[configs.cancel]();
    });

    //Finally, display it
    showModal();
  } else {
    console.log('Centa modal error:\r\nEither the modal configs where not found or the JSON is invalid.');
  }
}

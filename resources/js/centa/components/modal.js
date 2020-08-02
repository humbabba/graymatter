let modalBackground = $('.modal-background').first(); //Only get first in case multipls present. There shouldn't be.
let modalTriggers = $('[class*="modal+"]');
let modalDefaultConfigs = {"title":"Are you sure?","content": "Please confirm that you wish to do this.","confirm": "modalClose","cancel": "modalClose"};
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
            if(2 === elModalClass.length) {
              let modalFunctionDefines = elModalClass.splice(1,1)[0];
              el.on('click',function() {
                console.log('modalFunctionDefines');
                console.log(modalFunctionDefines);
                configureModal(modalFunctionDefines);
                showModal();
              });
            }
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
  let params = '';
  let parts = defines.split('.');
  let configName = parts[0];
  console.log('configName');
  console.log(configName);
  if(1 < parts.length) {
    let paramsArr = parts.splice(1,1);
    params = paramsArr.join(',');
  }

  //Fetch the configs for the modal
  $.getJSON(window.modalConfigsPath,configName,function(data) {
    let configs = data[configName];
    console.log('configs');
    console.log(configs);
    if('undefined' === typeof configs) {
    console.log('nossur');
      configs = modalDefaultConfigs; //Use the default if we cannot find any matching configs
      console.log('configs');
      console.log(configs);
    }
    modalTitle.html(configs.title);
    modalContent.html(configs.content);
  });

}

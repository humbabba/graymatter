let modal = $('.modal-background').first(); //Only get first in case multipls present. There shouldn't be.
let modalTriggers = $('[class*="modal+"]');
let modalFunction = 'default';

checkForModalTriggers();

function checkForModalTriggers() {
  //Confirm elements are Centa modal triggers, then process
  if(modalTriggers.length) {
    modalTriggers.each(function(index,element) {
      let el = $(element);
      var classes = el.prop('class').split(/\s+/);
      $(classes).each(function(index,elClass) {
          if(elClass.startsWith('modal+')) {
            let elModalClass = elClass.split('+');
            if(2 === elModalClass.length) {
              modalFunction = elModalClass[1];
              console.log(modalFunction);
              addModalTriggerClickHandlers();
            }
          }
        });
    });
  }
}

function addModalTriggerClickHandlers() {
  $(this).on('click',function() {
    showModal();
  });
}

function showModal() {
  modal.fadeIn(400);
}

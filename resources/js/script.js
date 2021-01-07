//App-specific JS goes here

import { hideModal } from './centa/components/modal.js';

window.suspendUser = (id,name,formId,paramInput) => {
  console.log('paramInput');
  console.log(paramInput);
  let form = $('#' + formId);
  //Match the modal inputs to the hidden form fields with the same names, set their values based on modal input
  paramInput.forEach(function(item,index) {
    let hiddenFormField = form.find('[name="' + item.prop('name') + '"]');
    console.log('hiddenFormField');
    console.log(hiddenFormField);
    hiddenFormField.val(item.val());
  });
  hideModal();
  form.submit();
};

window.deleteUser = (id,name,formId) => {
  hideModal();
  $('#' + formId).submit();
};

//App-specific JS goes here

import { CentaModal } from './centa/components/modal.js';

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

//Test button on /test

const thing = url => alert('I got: ' + url);

$('#testButton').on('click',() => {
    let modal = new CentaModal({
        titleText:'<h1>Link destination</h1>',
        contentHtml: '<label>Enter URL:</label><input type="text" name="url" />',
        inputNames: ['url']
    }
    ,thing);
    modal.render();
});

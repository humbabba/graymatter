//App-specific JS goes here
window.suspendUser = (id,name,formId,paramInput) => {
  console.log('id');
  console.log(id);
  console.log('name');
  console.log(name);
  console.log('formId');
  console.log(formId);
  let form = $('#' + formId);
  console.log('form');
  console.log(form);
  let suspendedDaysInput = form.find('input[name="suspendedDays"]');
  console.log('suspendedDaysInput');
  console.log(suspendedDaysInput);
  console.log('paramInput');
  console.log(paramInput);
  suspendedDaysInput.val(paramInput.val());
  hideModal();
  form.submit();
}

window.deleteUser = (id,name,formId) => {
  hideModal();
  $('#' + formId).submit();
}

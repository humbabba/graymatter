//App-specific JS goes here
window.suspendUser = (id,name,formId,paramInput) => {
  let form = $('#' + formId);
  let suspendedDaysInput = form.find('input[name="suspendedDays"]');
  console.log('suspendedDaysInput');
  console.log(suspendedDaysInput);
  console.log('paramInput');
  console.log(paramInput);
  suspendedDaysInput.val(paramInput.val());
  // hideModal();
  // form.submit();
}

window.deleteUser = (id,name,formId) => {
  hideModal();
  $('#' + formId).submit();
}

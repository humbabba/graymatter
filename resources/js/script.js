//App-specific JS goes here
window.suspendUser = (params) => {
  console.log('id');
  console.log(params[0]);
  console.log('name');
  console.log(params[1]);
  console.log('Suspending user with ID: ' + params[0] + ' (' + params[1] + ') soon!');
  hideModal();
}

//App-specific JS goes here
window.suspendUser = (id,name) => {
  console.log('Suspending user with ID: ' + id + ' (' + name + ') soon!');
  hideModal();
}

window.deleteUser = (id,name) => {
  console.log('Deleting user with ID: ' + id + ' (' + name + ') soon!');
  hideModal();
}

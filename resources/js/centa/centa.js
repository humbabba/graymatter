//Define path to modal configs
window.modalConfigsPath = '/centa/modal.json';

//Define the default callback for changes in text-editor
//Set to false for no callback
window.textEditorDefaultCallback = () => showUnsavedFlag(documentForm);

//Included components
require('./components/alerts');
require('./components/modal');
require('./components/nav');
require('./components/shift');
require('./components/sorters');
require('./components/text-editor');

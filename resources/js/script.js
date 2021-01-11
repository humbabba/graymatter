/**
 * App-specific JS goes here
 */

//Imports
import { CentaModal } from './centa/components/modal.js';

//User suspension
const suspendUser = (userId,form,suspendedDays,suspendedMessage) => {
    console.log('Suspend func stuff');
    console.log(userId);
    console.log(form);
    console.log(suspendedDays);
    console.log(suspendedMessage);
    form.find('input[name="suspendedDays"]').val(suspendedDays);
    form.find('input[name="suspendedMessage"]').val(suspendedMessage);
    form.submit();
};

$('.suspendUser').each((index,el) => {
    const button = $(el);
    const form = button.closest('form');
    const userName = form.find('input[name="suspendedUserName"]').val();
    const userId = form.find('input[name="suspendUserId"]').val();
    const modalContent = `<p>Suspend user <span class="username">${userName}</span> (ID: <span class="userId">${userId}</span>) for:</p>
<p><input type="number" name="suspendedDays" class="short-field" step="1" min="0" value="1"></p>
<p>days.</p>
<p>Suspension message for user:</p>
<input type="hidden" name="suspendedMessage" class="text-editor">`;
    button.on('click',(e) => {
        e.preventDefault();
        const modal = new CentaModal({
            titleText: 'Suspend this user?',
            contentHtml: modalContent,
            params: [userId,form],
            inputNames: ['suspendedDays','suspendedMessage'],
            cancelText: 'Cancel',
            confirmText: 'Suspend',
        },suspendUser);
        modal.render();
    });

});

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

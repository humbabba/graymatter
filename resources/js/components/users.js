/**
 * Script for users index page
 */

//Imports
import { CentaModal } from './modal';

//User suspension
const suspendUser = (form,suspendedDays,suspendedMessage) => {
    form.find('input[name="suspendedDays"]').val(suspendedDays);
    form.find('input[name="suspendedMessage"]').val(suspendedMessage);
    form.submit();
};
//User suspension modal
$('.suspendUser').each((index,el) => {
    const button = $(el);
    const form = button.closest('form');
    const userName = form.find('input[name="suspendedUserName"]').val();
    const userId = form.find('input[name="suspendUserId"]').val();
    const modalContent = `<p>Suspend user <span class="username">${userName}</span> (ID: <span class="userId">${userId}</span>) for:</p>
<p><input type="number" name="suspendedDays" class="short-field" step="1" min="0" value="1"></p>
<p>days.</p>
<p>Suspension message for user:</p>
<input type="hidden" name="suspendedMessage" class="textEditor">`;
    button.on('click',(e) => {
        e.preventDefault();
        const modal = new CentaModal({
            titleText: 'Suspend this user?',
            contentHtml: modalContent,
            params: [form],
            inputNames: ['suspendedDays','suspendedMessage'],
            cancelText: 'Cancel',
            confirmText: 'Suspend',
        },suspendUser);
        modal.render();
    });

});

//User deletion
const deleteUser = form => {
    form.submit();
};
//User deletion modal
$('.deleteUser').each((index,el) => {
    const button = $(el);
    const form = button.closest('form');
    const userName = form.find('input[name="suspendedUserName"]').val();
    const userId = form.find('input[name="suspendUserId"]').val();
    const modalContent = `<p>Do you really want to delete user <span class="username">${userName}</span> (ID: <span class="userId">${userId}</span>)?</p>`;
    button.on('click',(e) => {
        e.preventDefault();
        const modal = new CentaModal({
            titleText: 'Delete this user?',
            contentHtml: modalContent,
            params: [form],
            inputNames: [],
            cancelText: 'Cancel',
            confirmText: 'Delete user',
        },deleteUser);
        modal.render();
    });

});

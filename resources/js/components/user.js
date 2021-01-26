/**
 * Script for user create/edit page
 */

const passwordInputDiv = $('#passwordInputDiv');
const passwordResetButton = passwordInputDiv.find('button');
const passwordTemplateDiv = $('#passwordTemplateDiv');

//Handlers
passwordResetButton.on('click',(e) => {
    e.preventDefault();
    showPasswordResetInputs();
});

const showPasswordResetInputs = () => {
    passwordInputDiv.removeClass('align-center').html(passwordTemplateDiv.html());
};

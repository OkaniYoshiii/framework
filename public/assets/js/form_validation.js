import { FormValidation } from "./Validations/FormValidation.js";
const forms = document.querySelectorAll('form') ?? [];
forms.forEach((form) => {
    const formValidation = new FormValidation(form);
    form.addEventListener('onValidationSuccess', () => {
        console.log('s', formValidation.inputValidations.succeeded);
        console.log('s', formValidation.inputValidations.failed);
    });
    form.addEventListener('onValidationFail', () => {
        console.log('f', formValidation.inputValidations.succeeded);
        console.log('f', formValidation.inputValidations.failed);
    });
});

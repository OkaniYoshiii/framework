import { ValidationFactory } from "../Factories/ValidationFactory.js";
export { FormValidation };
class FormValidation {
    _element;
    _initialInputs;
    _inputs;
    _labels;
    _excludedInputTypes = ['hidden', 'submit'];
    _events;
    _inputValidations = { succeeded: [], failed: [] };
    constructor(form) {
        this._element = form;
        this._events = { validationSuccess: new Event('onValidationSuccess'), validationFail: new Event('onValidationFail') };
        this._initialInputs = Array.from(form.querySelectorAll('input'))
            .map((input) => input.cloneNode(true))
            .filter((input) => input instanceof HTMLInputElement && !this._excludedInputTypes.includes(input.type));
        this._inputs = Array.from(form.elements)
            .filter((input) => input instanceof HTMLInputElement && !this._excludedInputTypes.includes(input.type));
        this._labels = this._inputs
            .map((input) => {
            if (input.labels === null || input.labels.length !== 1)
                throw new Error('Each input on the form need to have a single label. Found ' + input.labels?.length ?? 0 + ' input.labels');
            return input.labels[0];
        });
        this.init();
    }
    init() {
        this._inputs.forEach((input) => input.addEventListener('change', this.validate.bind(this)));
        this._element.addEventListener('submit', this.validate.bind(this));
    }
    validate(ev) {
        this._inputValidations = { succeeded: [], failed: [] };
        for (let i = 0; i < this._inputs.length; i++) {
            const validation = ValidationFactory.create(this._initialInputs[i], this._inputs[i], this._labels[i]);
            (validation.errors.length > 0) ? this._inputValidations.failed.push(validation) : this._inputValidations.succeeded.push(validation);
        }
        const isValidated = this._inputValidations.failed.length === 0;
        const event = (isValidated) ? this._events.validationSuccess : this._events.validationFail;
        this._element.dispatchEvent(event);
        if (ev instanceof SubmitEvent && !isValidated)
            ev.preventDefault();
    }
    get inputValidations() {
        return this._inputValidations;
    }
}

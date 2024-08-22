export { InputValidation };
class InputValidation {
    _input;
    _label;
    _value;
    _readonly;
    _required;
    _errors = [];
    constructor(input, value, label) {
        this._input = input;
        this._label = label.textContent || '';
        this._value = value;
        this._required = input.required;
        this._readonly = input.readOnly;
        if (this._required && this._value === '')
            this._errors.push('Le champ ' + this._label + ' est requis');
    }
    get errors() {
        return this._errors;
    }
}

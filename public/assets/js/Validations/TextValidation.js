import { InputValidation } from "./InputValidation.js";
export { TextValidation };
class TextValidation extends InputValidation {
    _maxLength;
    _minLength;
    _pattern;
    constructor(input, value, label) {
        super(input, value, label);
        this._maxLength = (input.maxLength > 0) ? input.maxLength : null;
        this._minLength = (input.minLength > 0) ? input.minLength : null;
        this._pattern = (input.pattern) ? new RegExp(input.pattern) : null;
        this.validate();
    }
    validate() {
        if (this._minLength !== null && this._value.length < this._minLength)
            this.errors.push('Le champ "' + this._label + '" doit faire au minimum ' + this._minLength + ' caractères.');
        if (this._maxLength !== null && this._value.length > this._maxLength)
            this.errors.push('Le champ "' + this._label + '" doit faire au maximum ' + this._maxLength + ' caractères.');
        if (this._pattern !== null && !this._pattern.test(this._value))
            this.errors.push(this._input.title || 'Le champ ' + this._label + ' ne correspond pas au modèle demandé.');
    }
}

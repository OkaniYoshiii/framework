import { InputValidation } from "./InputValidation.js";
export { NumberValidation };
class NumberValidation extends InputValidation {
    _min;
    _max;
    _step;
    constructor(input, value, label) {
        super(input, Number(value), label);
        this._min = (Number(input.min) > 0) ? Number(input.min) : null;
        this._max = (Number(input.max) > 0) ? Number(input.max) : null;
        this._step = (this._step) ? Number(input.step) : null;
        this.validate();
    }
    validate() {
        if (this._min !== null && this._value < this._min)
            this.errors.push('Le champ ' + this._label + ' doit faire être un chiffre avec au minimum ' + this._min + ' décimales.');
        if (this._max !== null && this._value > this._max)
            this.errors.push('Le champ ' + this._label + ' doit faire être un chiffre avec au maximum ' + this._max + ' décimales.');
        if (this._step !== null && this._value > this._step)
            this.errors.push('Le champ ' + this._label + ' doit faire être un chiffre avec au maximum ' + this._max + ' décimales.');
    }
}

import { InputValidation } from "./InputValidation.js";
export { DateValidaton };
class DateValidaton extends InputValidation {
    _max;
    _min;
    constructor(input, value, label) {
        super(input, new Date(Date.parse(value)), label);
        this._max = (input.max) ? new Date(Date.parse(input.max)) : null;
        this._min = (input.max) ? new Date(Date.parse(input.min)) : null;
        this.validate();
    }
    validate() {
        if (this._min !== null && this._value < this._min)
            this.errors.push('Le champ ' + this._label + ' doit être une date commencant avant le ' + this._min);
        if (this._max !== null && this._value > this._max)
            this.errors.push('Le champ ' + this._label + ' doit être une date commencant après le ' + this._max);
    }
}

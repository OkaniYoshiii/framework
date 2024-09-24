import { InputValidation } from "./InputValidation.js";

export { DateValidaton };

class DateValidaton extends InputValidation
{
    private _max : Date|null;
    private _min : Date|null;

    constructor(input : HTMLInputElement, value : string, label : HTMLLabelElement) 
    {
        super(input, new Date(Date.parse(value)), label);

        this._max = (input.max) ? new Date(Date.parse(input.max)) : null;
        this._min = (input.max) ? new Date(Date.parse(input.min)) : null;
        
        this.validate();
    }

    protected validate() 
    {
        if(this._min !== null && this._value < this._min) this.errors.push('Le champ ' + this._label + ' doit être une date commencant avant le ' + this._min);
        if(this._max !== null && this._value > this._max) this.errors.push('Le champ ' + this._label + ' doit être une date commencant après le ' + this._max);
    }
}
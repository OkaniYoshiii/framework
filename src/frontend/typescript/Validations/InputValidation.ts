export { InputValidation };

abstract class InputValidation
{
    protected _input : HTMLInputElement;
    protected _label : string;

    protected _value : any;

    protected _readonly : boolean;
    protected _required : boolean;

    protected _errors : string[] = [];

    constructor(input : HTMLInputElement, value : any, label : HTMLLabelElement)
    {
        this._input = input;
        this._label = label.textContent || '';

        this._value = value;

        this._required = input.required;
        this._readonly = input.readOnly;

        if(this._required && this._value === '') this._errors.push('Le champ ' + this._label + ' est requis');
    }

    public get errors() : string[]
    {
        return this._errors;
    }

    protected abstract validate() : void;
}
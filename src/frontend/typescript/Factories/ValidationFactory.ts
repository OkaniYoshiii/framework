import { DateValidaton } from "../Validations/DateValidation.js";
import { EmailValidation } from "../Validations/EmailValidation.js";
import { NumberValidation } from "../Validations/NumberValidation.js";
import { TextValidation } from "../Validations/TextValidation.js";
import { InputValidation } from "../Validations/InputValidation.js";

export { ValidationFactory };

class ValidationFactory
{
    public static create(input : HTMLInputElement, sentInput : HTMLInputElement, label : HTMLLabelElement) : InputValidation
    {
        switch(input.type) {
            case 'text' :
            case 'password' : 
                return new TextValidation(input, sentInput.value, label)
                break;
            case 'number' : 
                return new NumberValidation(input, sentInput.value, label)
                break;
            case 'email' : 
                return new EmailValidation(input, sentInput.value, label)
                break;
            case 'date' : 
            case 'datetime-local' : 
                return new DateValidaton(input, sentInput.value, label)
                break;
            default : 
                throw new Error('Cannot create Validation object with input type of ' + input.type);
        }
    }
}
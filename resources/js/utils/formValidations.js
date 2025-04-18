const formValidations = {

    validateEmail(email){

        if(email === undefined || email === null || email === ''){
            return false;
        }

        return String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    },

    validateInternationalMobileNumber(mobile) {
        if (mobile === undefined || mobile === null || mobile === '') {
            return false;
        }

        return String(mobile)
            .match(
                /^\+[0-9]+$/
            );
    },

    validateMobileNumber(mobile) {
        if (mobile === undefined || mobile === null || mobile === '') {
            return false;
        }

        return String(mobile)
            .match(/^[0-9]+$/) && mobile.length <= 11;
    },

    emailConfirmation(email, emailConfirmation){
        return email === emailConfirmation;
    },

    passwordConfirmation(password, passwordConfirmation){
        if(password !== "" || passwordConfirmation !== ""){
            return password === passwordConfirmation;
        }
    },

    validateCharacterLength(char, min = null, max = null){
        if(char !== "" && char !== null){
            if(min !== null && max !== null){
                return char.length >= min && char.length <= max;
            }else if(min !== null){
                return char.length >= min;
            }else if(max !== null){
                return char.length <= max;
            }
        }
    },

    validateAmount(amount, min = null, max = null){
        if(amount !== "" && amount !== null){

            // amount must be a positive number
            if (!isNaN(amount) && amount > 0) {
                return amount;
            }

            if(min !== null && max !== null){
                return amount >= min && amount <= max;
            }else if(min !== null){
                return amount >= min;
            }else if(max !== null){
                return amount <= max;
            }
        }
    }

}

export default formValidations;

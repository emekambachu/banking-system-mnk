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

    validateMobileNumber(mobile) {
        if (mobile === undefined || mobile === null || mobile === '') {
            return false;
        }

        return String(mobile)
            .match(
                /^\+[0-9]+$/
            );
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

    validateFileType(file, allowedExtensions = []) {
        const fileName = file.name;
        const fileExtension = fileName.split('.').pop().toLowerCase();
        return allowedExtensions.includes(fileExtension);
    },

    validateFileSize(file, maxSize) {
        return file.size <= maxSize;
    },

}

export default formValidations;

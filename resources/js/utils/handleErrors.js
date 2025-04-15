let handleErrors = {

    hideErrorInProduction(log_name, log_data) {
        if(import.meta.env.DEV){
            console.log(log_name, log_data);
        }
    }

};

export default handleErrors;

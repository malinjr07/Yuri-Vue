//global functions for tempo

export default {
    install: (tempoApp) => {

        const tempoUtils = {
            testGlobalFunction: (input) => {
                alert('global yea!');
            },
            tempoDateFormat1: (inputDate) => {
                return tempoApp.config.globalProperties.$_moment(inputDate, 'YYYY-MM-DD').format('ddd Do MMM YYYY');
                
            },
            tempoDateTimeFormat1: (inputDateTime) => {
                return tempoApp.config.globalProperties.$_moment(inputDateTime, 'YYYY-MM-DD HH:mm:ss').format('ddd Do MMM YYYY h:ma');
                
            }
        }

        tempoApp.config.globalProperties.$_tempoUtils = tempoUtils;

    }
}

/**
export const tempoUtils = {

    testGlobalFunc: (input) => {
        alert(input);
    },
    testVariable: 'test var',
    tempoDateFormat1: (inputDate) => {
        //return 'hello';//$_moment().format(inputDate, 'MMMM Do YYYY, h:mm:ss a');
        return tempoApp.config.globalProperties.$_moment.format(inputDate, 'MMMM Do YYYY, h:mm:ss a');
    }

}

**/
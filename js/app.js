$(() => {
    $('.btn-next').click(function() {
        let currentStep = $(this).closest('.form-step');

        let nextStep = currentStep.next('.form-step');
        currentStep.removeClass('active');
        nextStep.addClass('active');
    });

    $('.btn-prev').click(function() {
        let currentStep = $(this).closest('.form-step');
        let prevStep = currentStep.prev('.form-step');

        currentStep.removeClass('active');
        prevStep.addClass('active');
    });

    let $password = $('#password');

    $('password').on('input',function(){
        let pwd = $(this).val();

        if (pwd.length >=8){
            $('#rule-length').addClass('valid');
        } else {
            $('#rule-length').removeClass('valid')
        }

        // At least one uppercase
        if (/[A-Z]/.test(pwd)) {
            $('#rule-upper').addClass('valid');
        } else {
            $('#rule-upper').removeClass('valid');
        }

        // At least one lowercase
        if (/[a-z]/.test(pwd)) {
            $('#rule-lower').addClass('valid');
        } else {
            $('#rule-lower').removeClass('valid');
        }

        // At least one number
        if (/\d/.test(pwd)) {
            $('#rule-number').addClass('valid');
        } else {
            $('#rule-number').removeClass('valid');
        }

        // At least one special char    
        if (/[@$!%*?&]/.test(pwd)) {
            $('#rule-special').addClass('valid');
        } else {
            $('#rule-special').removeClass('valid');
        }
    });
});
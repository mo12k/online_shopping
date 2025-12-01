$(() => {
    
    
    // Helper: update progress bar based on which .form-step is active
    function updateProgressBar() {
        const activeStepIndex = $('.form-step.active').index(); // 0, 1, or 2
        
        $('.progress-bar .step').removeClass('active').attr('aria-current', null);
        $('.progress-bar .step')
            .eq(activeStepIndex)
            .addClass('active')
            .attr('aria-current', 'step');
    }

    // NEXT button
    $('.btn-next').click(function (e) {
        e.preventDefault(); // Prevent form submission if button is inside a form
        let currentStep = $(this).closest('.form-step');
        let nextStep    = currentStep.next('.form-step');

        if (nextStep.length) {
            currentStep.removeClass('active');
            nextStep.addClass('active');
            updateProgressBar();           // ← update progress bar
        }
    });

    // PREVIOUS button
    $('.btn-prev').click(function (e) {
        e.preventDefault(); // Prevent form submission if button is inside a form
        let currentStep = $(this).closest('.form-step');
        let prevStep    = currentStep.prev('.form-step');

        if (prevStep.length) {
            currentStep.removeClass('active');
            prevStep.addClass('active');
            updateProgressBar();           // ← update progress bar
        }
    });

    // Optional: initialize correctly on page load (in case you reload on step 2/3)
    updateProgressBar();

    // ——— Your existing password validation (unchanged) ———
   let $password = $('#password');

    $password.on('input',function(){
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
        if (/[^A-Za-z0-9]/.test(pwd)) {
            $('#rule-special').addClass('valid');
        } else {
            $('#rule-special').removeClass('valid');
        }
    });
});


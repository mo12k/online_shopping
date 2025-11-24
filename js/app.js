$(() => {
    $('.btn-next').click(function() {
        var currentStep = $(this).closest('.form-step');
        var requiredFields = currentStep.find('.required-field[required]');
        var allFilled = true;

        requiredFields.each(function() {
            if(!this.checkValidity()) {
                allFilled = false;
                this.reportValidity();
                return false; // Break the loop
            }
        });

        if (!allFilled) {
            return;
        }

        var nextStep = currentStep.next('.form-step');
        currentStep.removeClass('active');
        nextStep.addClass('active');
    });

    $('.btn-prev').click(function() {
        var currentStep = $(this).closest('.form-step');
        var prevStep = currentStep.prev('.form-step');

        currentStep.removeClass('active');
        prevStep.addClass('active');
    });

});
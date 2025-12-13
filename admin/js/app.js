// ============================================================================
// General Functions
// ============================================================================
    // auto close alert-clode 
$(document).on('click', '.alert-close', function() {
    $(this).closest('.alert-success-fixed').fadeOut(300);
});

$(function() {
    const $input   = $('input[name=photo]');
    const $preview = $('#preview');
    const $text    = $('#upload-text');
    const $cancel  = $('#cancel-photo');
    const $hint    = $('#new-photo-hint');

    $input.on('change', function() {
        if (this.files && this.files[0]) {
            $preview.attr('src', URL.createObjectURL(this.files[0]));
            $text.hide();
            $cancel.show();
            $hint.text('Using by update picture');
        }
    });

    $cancel.on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();

        $input.val('');
        $preview.attr('src', '/images/no-photo.jpg');
        $text.show();
        $cancel.hide();
        $hint.text('Using by current picture');
    });

    $hint.text('Using by current picture');
});


// Insert product =Photo Preview Script 
$(function() {
    // Photo preview with fallback & reset
    $('label.upload-label input[type=file]').on('change', function(e) {
        const file = e.target.files[0];
        const $img = $(this).siblings('.preview-wrapper').find('img#preview');

        // Save original image source
        if (!$img.data('original-src')) {
            $img.data('original-src', $img.attr('src'));
        }

        if (file && file.type.startsWith('image/')) {
            $img.attr('src', URL.createObjectURL(file));
            $img.closest('.preview-wrapper').find('.upload-text').hide();
        } else {
            // Reset to original placeholder
            $img.attr('src', $img.data('original-src'));
            $(this).val('');
            $img.closest('.preview-wrapper').find('.upload-text').show();
        }
    });
});
// ============================================================================
// General Functions
// ============================================================================



// ============================================================================
// Page Load (jQuery)
// ============================================================================

$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();
    
    // Confirmation message
    $('[data-confirm]').on('click', e => {
        const text = e.target.dataset.confirm || 'Are you sure?';
        if (!confirm(text)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

    // Photo preview
    $('label.upload input[type=file]').on('change', e => {
        const f = e.target.files[0];
        const img = $(e.target).siblings('img')[0];

        if (!img) return;

        img.dataset.src ??= img.src;   // /images/photo.jpg

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);  //create preview thumbail
        }
        else {
            img.src = img.dataset.src; // reset back to origninal  /imags/photo.jpg
            e.target.value = '';
        }
    });




});


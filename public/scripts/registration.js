$(document).ready(() => {

    $('.txtPhone').mask('000-000-0000', { placeholder: '___-___-____' });

    //Masque poue la BETA KEY
    $('.txtxPostalCode').mask('ABC-BCB', {
        placeholder: '___-___',
        translation: {
            A: { pattern: /[A-CEGHJ-NPR-TVXYa-ceghj-npr-tvxy]/ },
            B: { pattern: /[0-9]/ },
            C: { pattern: /[A-CEGHJ-NPR-TV-Za-ceghj-npr-tv-z]/ }
        }
    });
    $('.txtxPostalCode').keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });

});
//Post Validation

$(() => {
    $('#tx1').blur((e) => {
    const input = e.currentTarget;
        if (input.value=='') {
            $(e.currentTarget).css('background-color', 'red');
        } else {
            $(e.currentTarget).css('background-color', 'white');
        }
        return;
    });
    $('#tx2').change((e) => {
        const $text_input = $('#tx2').val();
        alert($text_input);
        if ($text_input != '') {
            $('#submitPost').prop('disabled', false);
        } else {
            $('#submitPost').prop('disabled', true);
        }
        return;
    });
});


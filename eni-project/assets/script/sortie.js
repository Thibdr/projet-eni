$(document).on('change', '#sortie_ville', function() {
    let $field = $(this)
    let $form = $field.closest('form')
    let data = {}

    data[$field.attr('name')] = $field.val()
    $.post($form.attr('action'), data).then(function(data) {
        let $input = $(data).find('#sortie_lieu')
        $('#sortie_lieu').replaceWith($input)
    })
});
$(document).on('change', '#sortie_ville', () => {
    let form = $(this).closest('form')
    let action = form.attr('action')
    let data = {}

    data[$(this).attr('name')] = $(this).val()

    $.post(action, data).then(function(data) {
        let input = $(data).find('#sortie_lieu')
        $('#sortie_lieu').replaceWith(input)
    })
});
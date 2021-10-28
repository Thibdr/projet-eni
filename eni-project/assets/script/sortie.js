/*$(document).on('change', '#sortie_ville', function() {
    let $field = $(this)
    let $form = $field.closest('form')
    let data = {}

    data[$field.attr('name')] = $field.val()
    $.post($form.attr('action'), data).then(function(data) {
        let $input = $(data).find('#sortie_lieu')
        $('#sortie_lieu').replaceWith($input)
    })

});*/

/*
FONCTIONNE

$(document).on('change', '#sortie_ville', function() {
    let $field = $(this)
    let $form = $field.closest('form')
    let data = {}

    data[$field.attr('name')] = $field.val()
    $.post($form.attr('action'), data, function( data ) {
        $('#sortie_lieu option:not(:first)').remove();


        $.each(data, function(index, lieu) {
            $("#sortie_lieu").append('<option value="' + lieu.id + '">' + lieu.nom + '</option>')
        });
    });
   });
 */

var $city = $("#sortie_ville")
var $token = $("#sortie_token")

$city.change(function() {
    var $form = $(this).closest('form')
    var data = {}

    data[$token.attr('name')] = $token.val()
    data[$city.attr('name')] = $city.val()

    $.post($form.attr('action'), data).then(function (response) {
        $("#sortie_lieu").replaceWith(
            $(response).find("#sortie_lieu")
        )
    })
})
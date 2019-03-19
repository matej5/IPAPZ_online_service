var $receipt_form_office = $("#receipt_form_office")
var $token = $("#post_token")

$receipt_form_office.change(function () {
    var $form = $(this).closest('form')

    var data = {}

    data[$token.attr('name')] = $token.val()
    data[$receipt_form_office.attr('name')] = $receipt_form_office.val()

    $.post($form.attr('action'), data).then(function (response) {
        $("#receipt_form_worker").replaceWith(
            $(response).find("#receipt_form_worker")
        )
    })
    
})

$receipt_form_worker.change(function () {
    var $form = $(this).closest('form')

    var data = {}

    data[$token.attr('name')] = $token.val()
    data[$receipt_form_office.attr('name')] = $receipt_form_office.val()

    $.post($form.attr('action'), data).then(function (response) {
        $("#receipt_form_worker").replaceWith(
            $(response).find("#receipt_form_worker")
        )
    })

})
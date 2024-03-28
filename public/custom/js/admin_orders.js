"use strict";

$(document).ready(function() {

    $('#closed').on('click', function(event) {
        event.preventDefault();
        $('#modal-payment-method').modal('show');
        $('#payment_url').val($(this).attr('href'));
    });

    $('#payment_finish').on('click', function() {
        let url = $('#payment_url').val();
        var paymentMethod = $('#payment_method').val();
        url = url + '?payment_method=' + paymentMethod;
        $('#modal-payment-method').modal('hide');
        window.location.href = url;
    })

});

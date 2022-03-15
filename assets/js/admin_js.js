jQuery( document ).ready(function($) {
    //Get Param from url
    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results[1] || 0;
    }

    // Refund request
    $('.eupago-refund-request').on('click', function() {
        var refund_name     = $('input[name="refund_name"].eupago-field').val();
        var refund_iban     = $('input[name="refund_iban"].eupago-field').val();
        var refund_bic      = $('input[name="refund_bic"].eupago-field').val();
        var refund_amount   = $('input[name="refund_amount"].eupago-field').val();
        var refund_reason   = $('input[name="refund_reason"].eupago-field').val();
        var refund_order    = $.urlParam('post');
        var site_url        = $('.eupago-site-url').text();
        $('.eupago-refund-response').empty();

        $.ajax({
            type:'post',
            url: MYajax.ajax_url,
            data : {
                action : 'refund',
                refund_order: refund_order, refund_name: refund_name, refund_iban: refund_iban, refund_bic: refund_bic, refund_amount: refund_amount, refund_reason: refund_reason
            },
            success : function(response) {
                $( '.eupago-refund-response').append(response);
            }
        });
    });

    // Eupago settings
    $('input[name="sms_enable"]').on('change', function() {
        if (this.checked) {
            $('.eupago-sms-notifications').addClass('active');
        } else {
            $('.eupago-sms-notifications').removeClass('active');
        }
    });
});
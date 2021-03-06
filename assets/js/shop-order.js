jQuery(function ($) {
    $(document.body).on('wc-init-tabbed-panels', function(){
        $('#topdrop_name label > .optional').remove();
        $('#topdrop_phone_field label > .optional').remove();
    });

    $('.wc-customer-search').on('change', function (e) {
        customer = $('#customer_user').val();

        if (customer) {
            get_dropship(customer);
        }
    });

    $('#topdrop-load-dropship-data').on('click', function (e) {
        customer = $('#customer_user').val();
        $('.topbli-loader').show();

        if (customer) {
            get_dropship(customer);
            return;
        }

        alert('No dropship information saved for Guest.');
        console.log('No dropship information saved for Guest.');
        $('.topbli-loader').hide();
    });

    function get_dropship(customer) { 
        $.ajax({
            type: 'POST',
            url: topdrop.url,
            data: {
                customer: customer,
                action: 'topdrop_get_dropship',
                topdrop_nonce: topdrop.nonce
            },
            success: function (response) {
                if (response && response.success == true) {
                    $('#topdrop_name').val(response.results.name);
                    $('#topdrop_phone').val(response.results.phone);
                } else { 
                    alert(response.message);
                }

                $('.topbli-loader').hide();
            }
        });
    }
});
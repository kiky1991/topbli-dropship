jQuery(function ($) {
    $('#topdrop-load-dropship-data').on('click', function (e) {
        customer = $('#customer_user').val();
        $('.topbli-loader').show();

        if (customer) {
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
            return;
        }

        alert('Customer not valid.');
        console.log('Customer not valid.');
    });
});
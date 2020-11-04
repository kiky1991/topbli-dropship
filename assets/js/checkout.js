jQuery(function ($) {
    $(document.body).on('update_checkout', function(){
        $('#topdrop_privilege_field label > .optional').remove();
        $('#topdrop_phone_field label > .optional').remove();
    });

    $(document).on('ready', function () {
        check_dropship();
    });

    $("input#topdrop_privilege").click(function () {
        check_dropship();
    });

    function check_dropship() { 
        if ( $("input#topdrop_privilege").checked ){
            $(".topdrop-dropship-information").slideToggle(100);
        } else {
            $(".topdrop-dropship-information").slideToggle(0);
        }
    }
});
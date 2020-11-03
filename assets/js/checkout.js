jQuery(function ($) {
    $(document.body).on('update_checkout', function(){
        $('#topdrop_privilege_field label > .optional').remove();
        $('#topdrop_phone_field label > .optional').remove();
    });

    $("input#topdrop_privilege").click(function () {
        if ( this.checked ){
            $(".topdrop-dropship-information").slideToggle(100);
        } else {
            $(".topdrop-dropship-information").slideToggle(0);
        }
    });
});
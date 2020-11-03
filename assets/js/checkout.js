jQuery(function ($) {
    $("input#topdrop_privilege").click(function () {
        if ( this.checked ){
            $(".topdrop-dropship-information").slideToggle(100);
        } else {
            $(".topdrop-dropship-information").slideToggle(0);
        }
    });
});
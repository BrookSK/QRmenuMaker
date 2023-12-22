"use strict";
function openNav() {
    $("#cartSideNav").addClass('sidenav-cart-open').removeClass('sidenav-cart-close');
}

function closeNav() {
   $("#cartSideNav").addClass('sidenav-cart-close').removeClass('sidenav-cart-open');
}

$(window).scroll(function(){
    if ($(window).scrollTop() >= 1) {
        $('#navbar-main').addClass('custom-nav');
        $('#topDarkLogo').show();
        $('#topLightLogo').hide();
    }
    else {
        $('#navbar-main').removeClass('custom-nav');
        $('#topDarkLogo').hide();
        $('#topLightLogo').show();
    }
});

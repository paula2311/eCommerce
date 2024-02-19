$(function(){
'use strict';
    // switch between login & signup
    $('.login-page h1 span').click(function(){
        $(this).addClass('active').siblings().removeClass('active');
        $('.login-page form').hide();
        $('.' + $(this).data('class')).fadeIn(100);
    });

    // confirm message on btn delete
    $('.confirm').click( function () {
        return confirm('Are You Sure?');
    });

    // صفحة اضافة اعلان 
    $('.live-name').keyup(function(){
        $('.live-preview .caption h3').text($(this).val());
    });
    $('.live-desc').keyup(function(){
        $('.live-preview .caption p').text($(this).val());
    });
    $('.live-price').keyup(function(){
        $('.live-preview .price-tag').text( '$'+ $(this).val());
    });
    
});

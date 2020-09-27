$( document ).ready(function() {

    $("#reload").click(function(event) {
        event.preventDefault();
        $("#captcha").animate({
            opacity:'0.1'
        });
        $('#spinner').animate({
            opacity:'1'
        });
        $("#captcha").attr("src",AppPath+"captcha?"+(Math.random()*6));
        $('#spinner').animate({
            opacity:'0'
        });
        $("#captcha").animate({
            opacity:'1'
        });
    });

});
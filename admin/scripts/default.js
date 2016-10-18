$(document).ready(function () {

    $(window).load(function () {
        $("body").removeClass("preload");
    });

    var box = $('#navigation');
    var navShown = false;
    $('#button').on('click', function () {
        if (navShown) {
            box.toggleClass('hide-nav');
            navShown = false;
        } else {
            box.toggleClass('show-nav');
            navShown = true;
        }
    });
});
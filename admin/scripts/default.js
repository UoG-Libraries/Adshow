$(document).ready(function () {

    /*Navigation initialization*/
    var navigation = $('#navigation');
    var navShown = false;
    $('#nav-controller').on('click', function () {
        if (navShown) {
            navigation.removeClass('show-nav');
            navigation.addClass('hide-nav');
            navShown = false;
        } else {
            navigation.removeClass('hide-nav');
            navigation.addClass('show-nav');
            navShown = true;
        }
    });

    /*Bootstrap Popover initialization*/
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
});